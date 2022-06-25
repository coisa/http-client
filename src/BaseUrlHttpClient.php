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

namespace CoiSA\Http\Client;

use CoiSA\Http\Message\BaseUriFactory;
use CoiSA\Http\Message\BaseUrlRequestFactory;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class BaseUrlHttpClient implements HttpClientInterface
{
    private ClientInterface $client;

    private BaseUrlRequestFactory $requestFactory;

    /**
     * @param BaseUriFactory|string|UriInterface $baseUrl
     */
    public function __construct(
        $baseUrl,
        ClientInterface $client = null,
        UriFactoryInterface $uriFactory = null,
        RequestFactoryInterface $requestFactory = null
    ) {
        if (!$baseUrl instanceof BaseUriFactory) {
            $baseUrl = new BaseUriFactory($baseUrl, $uriFactory);
        }

        $this->client         = $client ?? Psr18ClientDiscovery::find();
        $this->requestFactory = new BaseUrlRequestFactory($baseUrl, $requestFactory);
    }

    public function getRequestFactory(): RequestFactoryInterface
    {
        return $this->requestFactory;
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->requestFactory->createRequest($method, $uri);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $baseUriFactory = $this->requestFactory->getBaseUriFactory();

        $newUri     = $baseUriFactory->createUri((string) $request->getUri());
        $newRequest = $request->withUri($newUri);

        return $this->client->sendRequest($newRequest);
    }
}
