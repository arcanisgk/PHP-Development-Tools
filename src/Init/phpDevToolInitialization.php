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
 * @license   For the full copyright and licence information, please view the LICENCE
 * @note      This program is distributed in the hope that it will be useful
 *            WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *            or FITNESS FOR A PARTICULAR PURPOSE.
 */

declare(strict_types=1);

namespace ArcanisGK\PhpDevelopmentTool\Init;

use ArcanisGK\PhpDevelopmentTool\BugCatcher;
use ArcanisGK\PhpDevelopmentTool\WebCLIDetector;

/**
 * phpDevToolInitialization Class.
 */
class phpDevToolInitialization
{
    /**
     * @var phpDevToolInitialization|null
     */

    private static ?phpDevToolInitialization $instance = null;

    /**
     * @return phpDevToolInitialization
     */

    public static function getInstance(): phpDevToolInitialization
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $template
     * @param $directive_explained
     * @return void
     */
    private function renderTemplate($template, $directive_explained)
    {
        $template_reader = file_get_contents(dirname(__DIR__) . '/src/template/' . $template);
        $instruction = str_replace("{config}", highlight_string($directive_explained, true), $template_reader);
        echo $instruction;
        exit();
    }

    /**
     * @return void
     */
    public function run()
    {
        if (WebCLIDetector::getInstance()->isWEB()) {
            $dir_root = $_SERVER['DOCUMENT_ROOT'];

            $path = dirname(__DIR__) . '/src/';

            $directive_reader = file_get_contents(dirname(__DIR__) . '/../src/directive/php.txt');

            $directive_explained = str_replace("{path}", $path, $directive_reader);

            if (!file_exists($dir_root . '.htaccess')) {
                $this->renderTemplate('missing_file.html', $directive_explained);
            }

            $htaccess_reader = file_get_contents($dir_root . '.htaccess');

            if (strpos($htaccess_reader, 'BugCatcher') === false) {
                $this->renderTemplate('missing_file.html', $directive_explained);
            }

            if (!isset($_SERVER['HTACCESS']) && !file_exists($dir_root . '.user.ini')) {
                $this->renderTemplate('unsupported_htaccess.html', '"auto_prepend_file = ' . $path . 'BugCatcher.php"');
            }

            $user_ini_reader = file_get_contents($dir_root . '.user.ini');

            if (strpos($user_ini_reader, 'BugCatcher') === false) {
                $this->renderTemplate(
                    'missing_directive_user.html',
                    '"auto_prepend_file = ' . $path . 'BugCatcher.php"'
                );
            }
        }
    }
}

ob_start();

phpDevToolInitialization::getInstance()->run();

BugCatcher::getInstance(['dir_log' => '/']);

