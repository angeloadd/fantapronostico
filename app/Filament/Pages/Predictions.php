<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\InsertPredictionWidget;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

final class Predictions extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Pronostici';

    protected string $view = 'filament.pages.predictions';

    protected function getHeaderWidgets(): array
    {
        return [InsertPredictionWidget::class];
    }
}
