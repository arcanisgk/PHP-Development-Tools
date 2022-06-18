<?php

/**
 * PHP Development Tools.
 * PHP Version required 7.4.* or higher
 *
 * @see https://github.com/arcanisgk/PHP-Development-Tools
 *
 * @author    Walter Nuñez (arcanisgk/original founder)
 * @email     icarosnet@gmail.com
 * @copyright 2020 - 2022 Walter Nuñez/Icaros Net S.A.
 * @license   For the full copyright and licence information, please view the LICENSE
 * @note      This program is distributed in the hope that it will be useful
 *            WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *            or FITNESS FOR A PARTICULAR PURPOSE.
 */

declare(strict_types=1);

namespace ArcanisGK\PhpDevelopmentTool\ExposeData;

/**
 * ExposeEngine Class.
 */
class ExposeEngine
{
    /**
     * @var string
     */

    private string $data;

    /**
     * @var bool|mixed
     */

    private bool $highlight = true;

    /**
     * @var bool|mixed
     */

    private bool $end = true;

    /**
     * @var array
     */

    private array $options;

    /**
     * Description: instantiate Class Static.
     * @var ExposeEngine|null $instance
     */

    private static ?ExposeEngine $instance = null;

    /**
     * @param array $data
     * @return ExposeEngine
     */

    public static function getInstance(array $data): ExposeEngine
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($data);
        }

        return self::$instance;
    }

    /**
     * @noinspection PhpUnused
     * @param array $options
     */

    public function __construct(array $options = [])
    {
        if (!empty($options)) {
            if (isset($options['highlight'])) {
                $this->highlight = $options['highlight'];
            }
            if (isset($options['end'])) {
                $this->end = $options['end'];
            }
        }
        $this->options = $options;
    }

    /**
     * @param $var
     * @return void
     */

    public function varDump($var): void
    {
        $this->clean();
        if ($this->highlight) {
            $this->Explain($var);
        } else {
            $this->capture($var);
        }
        $this->output();
        if ($this->end) {
            die;
        }
    }

    /**
     * @return void
     */

    private function clean(): void
    {
        if (ob_get_contents() || ob_get_length()) {
            ob_end_clean();
            flush();
        }
    }

    /**
     * @param $var
     * @return void
     */

    private function Explain($var)
    {
        echo 'Under Development<br>';
        $this->capture($var);
    }

    /**
     * @param $var
     * @return void
     */

    private function capture($var): void
    {
        ob_start();
        var_dump($var);
        $this->data = ob_get_contents();
        ob_end_clean();
    }

    /**
     * @return void
     */

    private function output(): void
    {
        echo '<pre>' . $this->data . '</pre>';
    }
}
