<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\ApiSport\Mapper;

use App\Modules\ApiSport\Dto\ApiSportDto;
use App\Modules\ApiSport\Exceptions\NoMapperStrategyFoundException;
use App\Modules\ApiSport\Mapper\ApiSportMapper;
use App\Modules\ApiSport\Mapper\Strategy\MapperStrategyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\UnitTestCase;

final class ApiSportMapperTest extends UnitTestCase
{
    private MapperStrategyInterface&MockObject $mapperStrategy;

    private ApiSportMapper $apiSportMapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapperStrategy = $this->createMock(MapperStrategyInterface::class);
        $this->apiSportMapper = new ApiSportMapper($this->mapperStrategy);
    }

    public function test_map_returns_a_dto(): void
    {
        $this->mapperStrategy
            ->expects($this->once())->method('supports')
            ->with(['get' => 'teams'])
            ->willReturn(true);

        $this->mapperStrategy
            ->expects($this->once())
            ->method('map')
            ->with(['get' => 'teams'])
            ->willReturn(new class() implements ApiSportDto {});
        $this->apiSportMapper = new ApiSportMapper($this->mapperStrategy);
        $this->apiSportMapper->map(['get' => 'teams']);
    }

    public function test_map_throws_exception_if_no_strategy_supports(): void
    {
        $this->expectException(NoMapperStrategyFoundException::class);
        $this->mapperStrategy->expects($this->once())
            ->method('supports')
            ->with(['get' => 'teams'])
            ->willReturn(false);

        $this->apiSportMapper->map(['get' => 'teams']);
    }

    public function test_map_throws_if_no_strategy_provided(): void
    {
        $this->expectException(NoMapperStrategyFoundException::class);

        new ApiSportMapper()->map(['get' => 'teams']);
    }
}
