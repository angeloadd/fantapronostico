<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Client;

use App\Modules\ApiSport\Exceptions\ExternalSystemUnavailableException;
use App\Modules\ApiSport\Exceptions\InvalidApisportTokenException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

final readonly class ApiSportClient implements ApiSportClientInterface
{
    private const API_SPORT_AUTH_HEADER = 'x-apisports-key';

    private const API_SPORT_INVALID_TOKEN_KEY = 'errors.token';

    private const RESPONSE_KEY = 'response';

    public function __construct(
        private string $host,
        private string $apiToken
    ) {}

    /**
     * @param  array<string, int|string>  $query
     * @return array<string, array<string, int|string>>
     *
     * @throws ConnectionException
     * @throws InvalidApisportTokenException
     */
    public function get(string $endpoint, array $query = []): array
    {
        $response = Http::baseUrl($this->host)
            ->withHeaders([self::API_SPORT_AUTH_HEADER => $this->apiToken])
            ->get($endpoint, $query);

        /** @var array<string, array<string, int|string>> $json */
        $json = $response->json();

        if (Arr::has($json, self::API_SPORT_INVALID_TOKEN_KEY)) {
            throw InvalidApisportTokenException::create();
        }

        if ( ! Arr::has($json, self::RESPONSE_KEY)) {
            throw ExternalSystemUnavailableException::fromResponse((string) $response);
        }

        return $json;
    }
}
