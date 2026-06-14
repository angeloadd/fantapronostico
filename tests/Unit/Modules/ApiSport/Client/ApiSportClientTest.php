<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\ApiSport\Client;

use App\Modules\ApiSport\Client\ApiSportClient;
use App\Modules\ApiSport\Exceptions\ExternalSystemUnavailableException;
use App\Modules\ApiSport\Exceptions\InvalidApisportTokenException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;
use Tests\Unit\UnitTestCase;

final class ApiSportClientTest extends UnitTestCase
{
    public const string HOST_SUCCESSFUL = 'successful/*';

    public const string HOST_MISSING_TOKEN = 'missing/token/*';

    public const string HOST_MISSING_RESPONSE = 'missing/response/*';

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            self::HOST_SUCCESSFUL => Http::response(['response' => 'successful']),
            self::HOST_MISSING_TOKEN => Http::response(['errors' => ['token' => 'missing']], 401),
            self::HOST_MISSING_RESPONSE => Http::response(['test' => 'exception'], 500),
        ]);
    }

    public function test_get_return_response(): void
    {
        $this->getSportClient(self::HOST_SUCCESSFUL)->get('test', ['query' => 'value']);
        Http::assertSent(
            static fn (Request $request) => $request->hasHeader('x-apisports-key', 'token') &&
                $request->url() === self::HOST_SUCCESSFUL.'/test?query=value'
        );
    }

    public function test_get_throws_if_missing_token(): void
    {
        $this->expectException(InvalidApisportTokenException::class);
        $this->getSportClient(self::HOST_MISSING_TOKEN)->get('test', ['query' => 'value']);
    }

    public function test_get_if_missing_response(): void
    {
        $this->expectException(ExternalSystemUnavailableException::class);
        $this->getSportClient(self::HOST_MISSING_RESPONSE)->get('test', ['query' => 'value']);
    }

    private function getSportClient(string $uri): ApiSportClient
    {
        return new ApiSportClient($uri, 'token', $this->createStub(LoggerInterface::class));
    }
}
