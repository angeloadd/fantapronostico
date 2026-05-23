<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Helpers\Ranking\RankingCalculatorInterface;
use App\Modules\Auth\Models\User;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

final class LeaguesWidget extends BaseWidget
{
    use Table\Concerns\HasQuery;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->with(['leagues'])->whereHas('leagues'))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('leagues')
                    ->state(static fn (Model $model) => $model->leagues->first()?->name),
                TextColumn::make('status')
                    ->badge()
                    ->state(static fn (Model $model) => $model->leagues->first()?->pivot->status)->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Action::make('accept')
                    ->disabled(static fn (User $user) => 'accepted' === $user->leagues->first()?->pivot->status)
                    ->action(
                        static function (User $user): void {
                            $league = $user->leagues->first();
                            if (null === $league) {
                                throw new InvalidArgumentException('Could not find a league to accept the user in');
                            }

                            $user->leagues()->updateExistingPivot($league->id, ['status' => 'accepted']);
                            $user->save();

                            Cache::forget('league-'.$league->id.'-rank');
                            app(RankingCalculatorInterface::class)->calculate($league);
                        }
                    ),
            ]);
    }
}
