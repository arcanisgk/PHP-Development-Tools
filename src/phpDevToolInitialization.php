<?php

declare(strict_types=1);


use ArcanisGK\PhpDevelopmentTool\BugCatcher;
use ArcanisGK\PhpDevelopmentTool\WebCLIDetector;

function renderTemplate($template, $directive_explained)
{
    $template_reader = file_get_contents(dirname(__DIR__) . '/src/template/' . $template);
    $instruction = str_replace("{config}", highlight_string($directive_explained, true), $template_reader);
    echo $instruction;
    exit();
}


if (WebCLIDetector::getInstance()->isWEB()) {
    $dir_root = $_SERVER['DOCUMENT_ROOT'];

    $path = dirname(__DIR__) . '/src/';

    $directive_reader = file_get_contents(dirname(__DIR__) . '/src/directive/php.txt');

    $directive_explained = str_replace("{path}", $path, $directive_reader);

    if (!file_exists($dir_root . '.htaccess')) {
        renderTemplate('missing_file.html', $directive_explained);
    }

    $htaccess_reader = file_get_contents($dir_root . '.htaccess');

    if (strpos($htaccess_reader, 'BugCatcher') === false) {
        renderTemplate('missing_file.html', $directive_explained);
    }

    if (!isset($_SERVER['HTACCESS']) && !file_exists($dir_root . '.user.ini')) {
        renderTemplate('unsupported_htaccess.html', '"auto_prepend_file = ' . $path . 'BugCatcher.php"');
    }

    $user_ini_reader = file_get_contents($dir_root . '.user.ini');

    if (strpos($user_ini_reader, 'BugCatcher') === false) {
        renderTemplate('missing_directive_user.html', '"auto_prepend_file = ' . $path . 'BugCatcher.php"');
    }
}

ob_start();
BugCatcher::getInstance(['dir_log' => '/']);

function dop($var)
{
    echo '<pre>';
    echo var_export($var);
    echo '</pre>';
}
