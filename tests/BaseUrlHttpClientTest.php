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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 * @coversDefaultClass \CoiSA\Http\Client\BaseUrlHttpClient
 */
final class BaseUrlHttpClientTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $client;

    private ObjectProphecy $baseUriFactory;

    private ObjectProphecy $uriFactory;

    private ObjectProphecy $requestFactory;

    private ObjectProphecy $request;

    private ObjectProphecy $response;

    private ObjectProphecy $uri;

    private BaseUrlHttpClient $baseUrlHttpClient;

    protected function setUp(): void
    {
        $this->client         = $this->prophesize(ClientInterface::class);
        $this->baseUriFactory = $this->prophesize(BaseUriFactory::class);
        $this->uriFactory     = $this->prophesize(UriFactoryInterface::class);
        $this->requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $this->request        = $this->prophesize(RequestInterface::class);
        $this->response       = $this->prophesize(ResponseInterface::class);
        $this->uri            = $this->prophesize(UriInterface::class);

        $this->baseUriFactory->createUri(Argument::type('string'))->willReturn($this->uri->reveal());

        $this->baseUrlHttpClient = new BaseUrlHttpClient(
            $this->baseUriFactory->reveal(),
            $this->client->reveal(),
            $this->uriFactory->reveal(),
            $this->requestFactory->reveal()
        );
    }

    /**
     * @coversNothing
     */
    public function testClassImplementsHttpClientInterface(): void
    {
        parent::assertInstanceOf(HttpClientInterface::class, $this->baseUrlHttpClient);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructWithStringBaseUrlWillCreateNewBaseUriFactory(): void
    {
        $baseUrlHttpClient = new BaseUrlHttpClient('http://example.com');

        static::assertInstanceOf(
            BaseUriFactory::class,
            $baseUrlHttpClient->getRequestFactory()->getBaseUriFactory()
        );
    }

    /**
     * @covers ::__construct
     * @covers ::getRequestFactory
     */
    public function testGetRequestFactoryWillReturnBaseUrlRequestFactoryWithGivenBaseUriFactory(): void
    {
        $requestFactory = $this->baseUrlHttpClient->getRequestFactory();

        parent::assertInstanceOf(BaseUrlRequestFactory::class, $requestFactory);
        parent::assertSame($this->baseUriFactory->reveal(), $requestFactory->getBaseUriFactory());
    }

    /**
     * @covers ::__construct
     * @covers ::createRequest
     */
    public function testCreateRequestWillProxyCreateRequestToBaseUrlRequestFactory(): void
    {
        $method = uniqid('method');
        $uri    = uniqid('uri');

        $this->requestFactory->createRequest($method, $this->uri->reveal())
            ->willReturn($this->request->reveal())
            ->shouldBeCalledOnce()
        ;

        $this->baseUrlHttpClient->createRequest($method, $uri);
    }

    /**
     * @covers ::__construct
     * @covers ::sendRequest
     */
    public function testSendRequestWillRecreateUriAndProxySendRequestToGivenClient(): void
    {
        $uri = uniqid('uri');

        $this->uri->__toString()->willReturn($uri)->shouldBeCalledOnce();
        $this->request->getUri()->willReturn($this->uri->reveal())->shouldBeCalledOnce();
        $this->baseUriFactory->createUri($uri)->willReturn($this->uri->reveal())->shouldBeCalledOnce();
        $this->request->withUri($this->uri->reveal())->willReturn($this->request->reveal())->shouldBeCalledOnce();
        $this->client->sendRequest($this->request->reveal())
            ->willReturn($this->response->reveal())
            ->shouldBeCalledOnce()
        ;

        $response = $this->baseUrlHttpClient->sendRequest($this->request->reveal());

        parent::assertSame($this->response->reveal(), $response);
    }
}
