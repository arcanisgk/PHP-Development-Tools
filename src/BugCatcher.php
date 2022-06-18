<?php

declare(strict_types=1);

namespace ArcanisGK\PhpDevelopmentTool;

class BugCatcher
{

    /**
     * @var BugCatcher|null
     */

    private static ?BugCatcher $instance = null;
    /**
     * @var string
     */

    private string $line_separator;
    /**
     * @var bool
     */

    private bool $display_error;
    /**
     * @var bool
     */

    private bool $to_log;

    private string $dir_log;


    /**
     * this class is armed automatically at the moment it is instantiated and remains active.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setLineSeparator($this->detectLineSeparator());

        $this->setDisplayError($this->isDisplayErrors());

        $this->setToLog($this->isDisplayErrors());

        $this->setDirLog($data['dir_log']);

        register_shutdown_function([$this, "shutdownHandler"]);

        set_exception_handler([$this, "exceptionHandler"]);

        set_error_handler([$this, "errorHandler"]);
    }

    /**
     * @param string $line_separator
     */

    private function setLineSeparator(string $line_separator): void
    {
        $this->line_separator = $line_separator;
    }

    /**
     * @return string
     */

    private function detectLineSeparator(): string
    {
        return WebCLIDetector::getInstance()->isCLI() ? PHP_EOL : '<br>';
    }

    /**
     * @param array $data
     * @return BugCatcher
     */

    public static function getInstance(array $data): BugCatcher
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($data);
        }

        return self::$instance;
    }

    /**
     * @param bool $display_error
     */

    private function setDisplayError(bool $display_error): void
    {
        $this->display_error = $display_error;
    }

    /**
     * @return bool
     */

    private function isDisplayErrors(): bool
    {
        return ini_get('display_errors') == 1;
    }

    /**
     * @param bool $to_log
     */

    private function setToLog(bool $to_log): void
    {
        $this->to_log = $to_log;
    }

    /**
     * @return string
     */
    public function getDirLog(): string
    {
        return $this->dir_log;
    }

    /**
     * @param string $dir_log
     */
    public function setDirLog(string $dir_log): void
    {
        $this->dir_log = $dir_log;
    }


    /**
     * @return void
     */

    public function shutdownHandler(): void
    {
        $error = error_get_last();
        if (!is_null($error)) {
            $this->cleanOutput();
            $trace = array_reverse(debug_backtrace());
            array_pop($trace);
            $error_array = [
                'class' => 'ShutdownHandler',
                'type' => $error['type'],
                'description' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
                'trace' => $trace,
            ];
            $error_array['trace_msg'] = $this->getBacktrace($error_array);
            $this->output($error_array);
        }
    }

    /**
     * @return void
     */

    private function cleanOutput(): void
    {
        if (ob_get_contents() || ob_get_length()) {
            ob_end_clean();
            flush();
        }
    }

    /**
     * @param array $error_array
     * @return string
     */

    private function getBacktrace(array $error_array): string
    {
        $smg = [];
        if (!empty($error_array['trace'])) {
            foreach ($error_array['trace'] as $track) {
                if (!isset($track['file']) && !isset($track['line'])) {
                    $route = 'Magic Call Method: (' . $track['class'] . ')->';
                } else {
                    $route = $track['file'] . ' ' . $track['line'] . ' calling Method: ';
                }
                $smg[] = '  ' . $route . $track['function'] . '()';
            }
        } else {
            $smg[] = 'No backtrace data in the ' . $error_array['class'] . '.';
        }

        return implode($this->getLineSeparator(), $smg);
    }

    /**
     * @return string
     */

    private function getLineSeparator(): string
    {
        return $this->line_separator;
    }

    /**
     * @param array $error_array
     * @return void
     */

    private function output(array $error_array): void
    {
        $this->toLog($error_array);

        $rqType = RequestType::getInstance()->getRequestType();

        if (WebCLIDetector::getInstance()->isCLI()) {
            CliOutput::getInstance()->cliOutputError($error_array);
        } elseif ($rqType == 'plain') {
            $error_skin = dirname(__DIR__) . '/src/template/error/handler_error.php';
            $source = show_source(
                $error_array['file'],
                true
            );
            require_once $error_skin;
        } else {
            header('Content-Type: application/json');
            echo json_encode($error_array);
        }
        $this->clearLastError();
    }

    /**
     * @param array $error_array
     * @return void
     */

    private function toLog(array $error_array)
    {
        $trace = preg_replace("/\r|\n|\r\n/", "", $error_array['trace_msg']);

        $error_smg_log = time() . ' ' . date(
                'Y-m-d H:i:s'
            ) . ' ' . $error_array['description'] .
            ' Trace: ' . $trace . PHP_EOL;

        error_log($error_smg_log, 3, 'error_log.log');
    }

    /**
     * @return void
     */

    private function clearLastError()
    {
        error_clear_last();
        exit();
    }

    /**
     * @param $e
     * @return void
     */

    public function exceptionHandler($e): void
    {
        $this->cleanOutput();
        $error_array = [
            'class' => 'ExceptionHandler',
            'type' => ($e->getCode() == 0 ? 'Not Set' : $e->getCode()),
            'description' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
        ];
        $error_array['trace_msg'] = $this->getBacktrace($error_array);
        $this->output($error_array);
        die();
    }

    /**
     * @param $error_level
     * @param $error_desc
     * @param $error_file
     * @param $error_line
     * @return void
     */

    public function errorHandler($error_level = null, $error_desc = null, $error_file = null, $error_line = null): void
    {
        $this->cleanOutput();
        $trace = array_reverse(debug_backtrace());
        array_pop($trace);
        $error_array = [
            'class' => 'ErrorHandler',
            'type' => $error_level,
            'description' => $error_desc,
            'file' => $error_file,
            'line' => $error_line,
            'trace' => $trace,
        ];
        $error_array['trace_msg'] = $this->getBacktrace($error_array);
        $this->output($error_array);
        die();
    }

    /**
     * @return bool
     */

    private function getDisplayError(): bool
    {
        return $this->display_error;
    }

    /**
     * @return bool
     */

    private function getToLog(): bool
    {
        return $this->to_log;
    }
}