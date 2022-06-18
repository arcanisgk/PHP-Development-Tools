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

use ArcanisGK\PhpDevelopmentTool\ExposeData\ExposeEngine;

/**
 * @param ...$data
 * @return void
 * @noinspection PhpUnused
 */

function sd(...$data)
{
    foreach ($data as $var) {
        ExposeEngine::getInstance(['highlight' => false, 'end' => false])->varDump($var);
    }
}

/**
 * @param ...$data
 * @return void
 * @noinspection PhpUnused
 */

function sda(...$data)
{
    foreach ($data as $var) {
        ExposeEngine::getInstance(['highlight' => true])->varDump($var);
    }
}
