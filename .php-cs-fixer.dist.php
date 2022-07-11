<?php

declare(strict_types=1);

/**
 * This file is part of coisa/http-client.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/http-client
 * @copyright Copyright (c) 2022 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

$paths = [
    __FILE__,
    __DIR__,
];

$header = file_get_contents(__DIR__ . '/.docheader');

return CoiSA\PhpCsFixer\PhpCsFixer::create($paths, (string) $header);
