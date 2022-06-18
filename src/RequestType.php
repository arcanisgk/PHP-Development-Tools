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

namespace ArcanisGK\PhpDevelopmentTool;

/**
 * RequestType Class.
 */
class RequestType
{
    /**
     * @var RequestType|null
     */

    private static ?RequestType $instance = null;

    /**
     * @return RequestType
     */

    public static function getInstance(): RequestType
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return string
     */

    public function getRequestType(): string
    {
        $var = $_GET ?? $_POST;
        if ((isset($_POST) || isset($_GET)) && !empty($var)) {
            if (count($var) >= 1) {
                return $this->isJson($var) ? 'json' : 'plain';
            }
        }

        return 'plain';
    }

    /**
     * @param array $var
     * @return bool
     */

    private function isJson(array $var): bool
    {
        $var = reset($var);
        if (is_string($var)) {
            json_decode($var);
        }

        return json_last_error() === JSON_ERROR_NONE;
    }
}
