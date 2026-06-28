<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Game;
use App\Models\Player;
use App\Models\Prediction;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;

final class InsertPredictionWidget extends Widget implements HasSchemas
{
    use InteractsWithSchemas;

    /** @var array<string, mixed> */
    public array $data = [];

    protected string $view = 'filament.widgets.insert-prediction-widget';

    protected int|string|array $columnSpan = 'full';

    public function mount(): void
    {
        $this->form->fill([
            'league_id' => League::first()?->id,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('league_id')
                    ->label('Liga')
                    ->options(League::all()->pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(static function (Set $set): void {
                        $set('user_id', null);
                        $set('game_id', null);
                        $set('home_scorer_id', null);
                        $set('away_scorer_id', null);
                    }),

                Select::make('user_id')
                    ->label('Utente')
                    ->options(static function (Get $get): array {
                        $leagueId = $get('league_id');
                        if (!$leagueId) {
                            return [];
                        }

                        return League::find($leagueId)
                            ?->users()
                            ->wherePivot('status', 'accepted')
                            ->pluck('name', 'users.id')
                            ->toArray() ?? [];
                    })
                    ->required()
                    ->live(),

                Select::make('game_id')
                    ->label('Partita')
                    ->options(static function (Get $get): array {
                        $leagueId = $get('league_id');
                        if (!$leagueId) {
                            return [];
                        }

                        $league = League::find($leagueId);

                        return $league?->tournament
                            ->games()
                            ->with('teams')
                            ->get()
                            ->mapWithKeys(static fn (Game $game): array => [
                                $game->id => sprintf(
                                    '%s vs %s (%s)',
                                    $game->home_team?->name ?? '?',
                                    $game->away_team?->name ?? '?',
                                    $game->stage,
                                ),
                            ])
                            ->toArray() ?? [];
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(static function (Set $set): void {
                        $set('home_scorer_id', null);
                        $set('away_scorer_id', null);
                    }),

                Radio::make('sign')
                    ->label('Segno')
                    ->options(['1' => '1', 'X' => 'X', '2' => '2'])
                    ->inline()
                    ->required(),

                TextInput::make('home_score')
                    ->label('Gol casa')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                TextInput::make('away_score')
                    ->label('Gol ospite')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Select::make('home_scorer_id')
                    ->label('Marcatore casa')
                    ->options(fn (Get $get): array => $this->buildScorerOptions($get('game_id'), 'home'))
                    ->hidden(fn (Get $get): bool => $this->isGroupStageOrEmpty($get('game_id')))
                    ->required(fn (Get $get): bool => !$this->isGroupStageOrEmpty($get('game_id'))),

                Select::make('away_scorer_id')
                    ->label('Marcatore ospite')
                    ->options(fn (Get $get): array => $this->buildScorerOptions($get('game_id'), 'away'))
                    ->hidden(fn (Get $get): bool => $this->isGroupStageOrEmpty($get('game_id')))
                    ->required(fn (Get $get): bool => !$this->isGroupStageOrEmpty($get('game_id'))),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (Prediction::where('game_id', $data['game_id'])
            ->where('user_id', $data['user_id'])
            ->where('league_id', $data['league_id'])
            ->exists()) {
            Notification::make()
                ->title('Pronostico già esistente per questo utente e partita')
                ->danger()
                ->send();

            return;
        }

        Prediction::create([
            'game_id' => $data['game_id'],
            'user_id' => $data['user_id'],
            'league_id' => $data['league_id'],
            'sign' => $data['sign'],
            'home_score' => $data['home_score'],
            'away_score' => $data['away_score'],
            'home_scorer_id' => $data['home_scorer_id'] ?? null,
            'away_scorer_id' => $data['away_scorer_id'] ?? null,
        ]);

        Notification::make()
            ->title('Pronostico inserito con successo')
            ->success()
            ->send();

        $this->form->fill(['league_id' => $data['league_id']]);
    }

    private function isGroupStageOrEmpty(int|string|null $gameId): bool
    {
        if (!$gameId) {
            return true;
        }

        $game = Game::find($gameId);

        return !$game || $game->isGroupStage();
    }

    /**
     * @return array<int|string, string>
     */
    private function buildScorerOptions(int|string|null $gameId, string $side): array
    {
        if (!$gameId) {
            return [];
        }

        $game = Game::with('teams')->find($gameId);
        if (!$game) {
            return [];
        }

        /** @var Team|null $team */
        $team = 'home' === $side ? $game->home_team : $game->away_team;
        if (!$team) {
            return [];
        }

        $options = [-1 => 'Autogol', 0 => 'NoGol'];
        $team->players->each(static function (Player $player) use (&$options): void {
            $options[$player->id] = $player->displayed_name;
        });

        return $options;
    }
}
