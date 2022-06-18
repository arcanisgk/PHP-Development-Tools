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

use ArcanisGK\PhpDevelopmentTool\WebCLIDetector;

require_once __DIR__ . "/../vendor/autoload.php";

$wc_detector = new WebCLIDetector();

if ($wc_detector->isCLI()) {
    echo 'Running from CLI' . PHP_EOL;
}

if ($wc_detector->isWEB()) {
    echo 'Running from WEB<br>';
}

echo 'Get Raw Environment: ' . $wc_detector->getEnvironment();
