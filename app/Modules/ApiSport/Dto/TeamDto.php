<?php

declare(strict_types=1);

namespace App\Modules\ApiSport\Dto;

final readonly class TeamDto
{
    public function __construct(
        public int $apiId,
        public string $name,
        public string $code,
        public string $logo,
        public bool $isNational,
    ) {}

    /**
     * @return array{
     *     api_id: int,
     *     name: string,
     *     code: string,
     *     logo: string,
     *     is_national: bool,
     * }
     */
    public function toArray(): array
    {
        return [
            'api_id' => $this->apiId,
            'name' => $this->name,
            'code' => $this->code,
            'logo' => $this->logo,
            'is_national' => $this->isNational,
        ];
    }
}
