<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\ApiSport\Console;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use JsonException;

trait GetMockResponseTrait
{
    /**
     * @return array<string, array<string, int|string>>
     *
     * @throws FileNotFoundException
     * @throws JsonException
     */
    private function getResponse(string $fileName): array
    {
        $response = (new Filesystem())->get(
            base_path('tests/mocks/'.$fileName)
        );

        return (array) json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
