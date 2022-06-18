<?php

declare(strict_types=1);


namespace ArcanisGK\PhpDevelopmentTool;

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