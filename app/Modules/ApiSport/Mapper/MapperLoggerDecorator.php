<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Mapper;

use App\Modules\ApiSport\Dto\ApiSportDto;
use App\Modules\ApiSport\Exceptions\ApiSportParsingException;
use App\Modules\ApiSport\Exceptions\NoMapperStrategyFoundException;
use Psr\Log\LoggerInterface;

final readonly class MapperLoggerDecorator implements MapperInterface
{
    public function __construct(private MapperInterface $mapper, private LoggerInterface $logger) {}

    /**
     * {@inheritdoc}
     */
    public function map(array $externalResponse): ApiSportDto
    {
        try {
            return $this->mapper->map($externalResponse);
        } catch (NoMapperStrategyFoundException|ApiSportParsingException $e) {
            $this->logger->error(
                $e->getMessage(),
                [
                    'trace' => $e->getTraceAsString(),
                    'response' => $externalResponse,
                ]
            );

            throw $e;
        }
    }
}
