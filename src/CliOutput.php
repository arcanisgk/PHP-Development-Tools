<?php

namespace ArcanisGK\PhpDevelopmentTool;

class CliOutput
{

    /**
     * @var CliOutput|null
     */

    private static ?CliOutput $instance = null;
    /**
     * @var array
     */

    private array $cli_square;
    /**
     * @var string
     */

    private string $retrieve_data;

    public function __construct()
    {
        $this->setCliSquare();
    }

    /**
     * @return CliOutput
     */

    public static function getInstance(): CliOutput
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param array $error_array
     * @return void
     */

    public function cliOutputError(array $error_array): void
    {
        $this->outputCli(
            $this->getStringFromErrorArray($error_array),
            true,
            1,
            0,
            true
        );
        echo $this->getRetrieveData();
    }

    /**
     * @param string $var
     * @param bool $retrieve
     * @param int $header
     * @param int $footer
     * @param bool $highlight
     * @param int $limiter
     * @return void
     */

    private function outputCli(
        string $var,
        bool $retrieve = false,
        int $header = 0,
        int $footer = 0,
        bool $highlight = false,
        int $limiter = 0
    ): void {
        $data = $this->toBoxCli($var, $header, $footer, $highlight, $limiter);
        if ($retrieve) {
            $this->setRetrieveData($data);
        } else {
            echo $data;
        }
    }

    /**
     * @param $source
     * @param int $n_header
     * @param int $n_footer
     * @param bool $highlight
     * @param int $limit_len
     * @return string
     */

    private function toBoxCli(
        $source,
        int $n_header = 0,
        int $n_footer = 0,
        bool $highlight = false,
        int $limit_len = 0
    ): string {
        $result = '';
        if ($limit_len == 0) {
            $limit_len = $this->getWindowLimit();
        }
        $source = (is_string($source) ? preg_split('/\r\n|\r|\n/', rtrim($source)) : $source);
        $cli_c_hf = chr(27) . '[1;42m';
        $cli_c_r = chr(27) . '[1;32m';
        $cli_EOL = chr(27) . '[0m';
        $reg = '#\\e[[][^A-Za-z]*[A-Za-z]#';
        $len = max(
            array_map(function ($el) use ($reg) {
                return mb_strlen(preg_replace($reg, '', $el));
            }, $source)
        );
        $max_len = (max($len, $limit_len));
        $check_fit = $this->checkIsFit($max_len, $limit_len);
        if ($check_fit) {
            $draw = (object)$this->getCliSquare();
            $i = 0;
            $n_lines = count($source);
            $calc_footer = $n_lines - $n_footer;
            $f_top = $draw->tl . str_repeat($draw->h, $max_len + 2) . $draw->tr;
            $f_button = $draw->bl . str_repeat($draw->h, $max_len + 2) . $draw->br;
            $result .= ($highlight && 0 != $n_header) ? $cli_c_hf . $f_top . $cli_EOL . PHP_EOL : $cli_c_r . $f_top . $cli_EOL . PHP_EOL;
            foreach ($source as $line) {
                $addEmpty = '';
                $line_txt = '';
                $len = mb_strlen(preg_replace($reg, '', $line));
                if (0 != $n_header && $i < $n_header) {
                    $line = str_pad($line, $max_len, ' ', STR_PAD_BOTH);
                    $line_txt .= $cli_c_hf . $draw->v . ' ' . $line . ' ' . $draw->v . $cli_EOL;
                } elseif (0 != $n_footer && $calc_footer == $i) {
                    $line = str_pad($line, $max_len, ' ', STR_PAD_BOTH);
                    $line_txt .= $cli_c_hf . $draw->v . ' ' . $line . ' ' . $draw->v . $cli_EOL;
                } elseif ($len <= $max_len) {
                    $addEmpty = str_repeat(' ', $max_len - $len);
                    $line_txt .= $cli_c_r . $draw->v . ' ' . $line . $addEmpty . ' ' . $draw->v . $cli_EOL;
                }
                if (0 != $n_header && $i == $n_header - 1) {
                    $line_txt .= PHP_EOL . $cli_c_hf . $draw->ls . str_repeat(
                            $draw->hs,
                            $max_len + 2
                        ) . $draw->rs . $cli_EOL . PHP_EOL;
                } elseif (0 != $n_footer && $calc_footer - 1 == $i) {
                    $line_txt .= PHP_EOL . $cli_c_r . $draw->v . str_repeat(' ', $max_len + 2) . $draw->v . $cli_EOL;
                    $line_txt .= PHP_EOL . $cli_c_hf . $draw->ls . str_repeat(
                            $draw->hs,
                            $max_len + 2
                        ) . $draw->rs . $cli_EOL . PHP_EOL;
                } else {
                    $line_txt .= PHP_EOL;
                }
                $result .= $line_txt;
                ++$i;
            }
            $result .= ($highlight && 0 != $n_header) ? $cli_c_hf . $f_button . $cli_EOL : $cli_c_r . $f_button . $cli_EOL . PHP_EOL;
        }

        return $result;
    }

    /**
     * @return int
     */
    private function getWindowLimit(): int
    {
        $WidthReal = shell_exec('MODE 2> null') ?? shell_exec('tput cols');
        if (strlen($WidthReal) > 5) {
            preg_match('/CON.*:(\n[^|]+?){3}(?<cols>\d+)/', $WidthReal, $match);
            $WidthReal = $match['cols'] ?? 80;
        }
        return ((int)$WidthReal - 10);
    }

    /**
     * @param $max_len
     * @param $limit_len
     * @return bool
     */

    private function checkIsFit($max_len, $limit_len): bool
    {
        if ($max_len <= $limit_len) {
            return true;
        } else {
            echo '!!!Your Terminal Windows is to Narrow Resize It!!!' . PHP_EOL .
                '==> Minimum Expected: ' . $max_len . PHP_EOL .
                '==> Given Size:       ' . $limit_len . PHP_EOL . PHP_EOL .
                'If you can not Resize the window;' . PHP_EOL .
                'You can Output the data to a file and avoid this error:' . PHP_EOL .
                'php script.php -f="filename"' . PHP_EOL;
            die;
        }
    }

    /**
     * @return array
     */
    public function getCliSquare(): array
    {
        return $this->cli_square;
    }

    public function setCliSquare(): void
    {
        $cli_square = [];
        foreach (
            [
                'tl' => '╔',
                'tr' => '╗',
                'bl' => '╚',
                'br' => '╝',
                'v' => '║',
                'h' => '═',
                'hs' => '─',
                'ls' => '╟',
                'rs' => '╢',
            ] as $key => $value
        ) {
            $cli_square[$key] = html_entity_decode($value, ENT_NOQUOTES, 'UTF-8');
        }
        $this->cli_square = $cli_square;
    }

    /**
     * @param array $error_array
     * @return string
     */

    private function getStringFromErrorArray(array $error_array): string
    {
        return $error_array['class'] . PHP_EOL .
            'File: ' . $error_array['file'] . PHP_EOL .
            'Line: ' . $error_array['line'] . ' Level: ' . $error_array['type'] . PHP_EOL .
            'Description: ' . $error_array['description'] . PHP_EOL .
            'BackTrace Log: ' . $error_array['trace_msg'] . PHP_EOL;
    }

    /**
     * @return string
     */
    public function getRetrieveData(): string
    {
        return $this->retrieve_data;
    }

    /**
     * @param string $retrieve_data
     */
    public function setRetrieveData(string $retrieve_data): void
    {
        $this->retrieve_data = $retrieve_data;
    }


}