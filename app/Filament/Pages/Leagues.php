<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\LeaguesWidget;
use BackedEnum;
use Filament\Pages\Page;

final class Leagues extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.leagues';

    protected function getHeaderWidgets(): array
    {
        return [
            LeaguesWidget::class,
        ];
    }
}
