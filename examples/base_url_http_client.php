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

use CoiSA\Http\Client\BaseUrlHttpClient;
use CoiSA\Logger\LoggerFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$baseUriClient = new BaseUrlHttpClient('https://httpbin.org/');

$logger = LoggerFactory::createLogger();
$baseUriClient->setLogger($logger);

$request  = $baseUriClient->createRequest('GET', '/get?test=123');
$response = $baseUriClient->sendRequest($request);

echo $response->getBody()->getContents();
