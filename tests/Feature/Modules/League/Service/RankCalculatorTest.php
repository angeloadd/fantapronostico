<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\League\Service;

use App\Helpers\Ranking\RankingCalculator;
use App\Helpers\Ranking\RankingCalculatorInterface;
use App\Models\Game;
use App\Models\GameGoal;
use App\Models\Player;
use App\Models\Prediction;
use App\Models\Tournament;
use App\Modules\Auth\Models\User;
use App\Modules\League\Models\League;
use App\Modules\Tournament\Models\Team;
use Illuminate\Support\Collection;
use Tests\TestCase;

final class RankCalculatorTest extends TestCase
{
    private Tournament $tournament;

    private League $league;

    /**
     * @var Collection<int, User>
     */
    private Collection $users;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Tournament $tournament */
        $this->tournament = Tournament::factory()
            ->create();
        $this->league = League::create([
            'tournament_id' => $this->tournament->id,
            'name' => 'Test',
        ]);
        $this->users = User::factory(4)->create();
        $this->league->users()->attach($this->users, ['status' => 'accepted']);
    }

    public function test_calculate(): void
    {
        /** @var Game $game */
        $game = Game::factory()->create([
            'tournament_id' => $this->tournament->id,
            'status' => 'finished',
            'started_at' => today()->startOfDay(),
        ]);
        $homeTeam = Team::factory()
            ->create(['is_national' => true]);
        $awayTeam = Team::factory()
            ->create(['is_national' => true]);

        /** @var int $scorerId */
        $scorerId = Player::factory()->create(['national_id' => $homeTeam->id])->id;
        Player::factory()->create(['national_id' => $awayTeam->id]);

        $game->teams()->attach($homeTeam, ['is_away' => false]);
        $game->teams()->attach($awayTeam, ['is_away' => true]);
        GameGoal::factory()->create([
            'game_id' => $game->id,
            'player_id' => $scorerId,
        ]);

        Prediction::factory([
            'game_id' => $game->id,
            'sign' => 'x',
            'home_score' => '1',
            'away_score' => '0',
            'home_scorer_id' => $scorerId,
            'away_scorer_id' => 0,
            'user_id' => $this->users[0]->id,
        ])->create();
        Prediction::factory([
            'game_id' => $game->id,
            'sign' => 'x',
            'home_score' => '1',
            'away_score' => '0',
            'home_scorer_id' => 0,
            'away_scorer_id' => -1,
            'user_id' => $this->users[1]->id,
        ])->create();
        Prediction::factory([
            'game_id' => $game->id,
            'sign' => '2',
            'home_score' => '0',
            'away_score' => '1',
            'home_scorer_id' => 0,
            'away_scorer_id' => $game->away_team?->players->first()->id,
            'user_id' => $this->users[2]->id,
        ])->create();
        Prediction::factory([
            'game_id' => $game->id,
            'sign' => '2',
            'home_score' => '0',
            'away_score' => '1',
            'home_scorer_id' => $scorerId,
            'away_scorer_id' => 0,
            'user_id' => $this->users[3]->id,
        ])->create();

        $homeTeam->players->map(static fn (Player $player) => $player->games()->attach($game));
        $awayTeam->players->map(static fn (Player $player) => $player->games()->attach($game));

        $calculator = app(RankingCalculatorInterface::class);
        $this->assertInstanceOf(RankingCalculator::class, $calculator);
        $calculator->calculate($this->league);
        $rank = $calculator->get($this->league);

        $this->assertSame(8, $rank[0]->total());
        $this->assertSame($this->users[0]->id, $rank[0]->userId());
        $this->assertSame(4, $rank[1]->total());
        $this->assertSame($this->users[1]->id, $rank[1]->userId());
        $this->assertSame(4, $rank[2]->total());
        $this->assertSame($this->users[3]->id, $rank[2]->userId());
        $this->assertSame(0, $rank[3]->total());
        $this->assertSame($this->users[2]->id, $rank[3]->userId());
    }

    public function test_calculate_for_group(): void
    {
        /** @var Game $game */
        $game = Game::factory()->create([
            'tournament_id' => $this->tournament->id,
            'status' => 'finished',
        ]);
        $homeTeam = Team::factory()
            ->create(['is_national' => true]);
        $awayTeam = Team::factory()
            ->create(['is_national' => true]);

        /** @var int $scorerId */
        $scorerId = Player::factory()->create(['national_id' => $homeTeam->id])->id;
        Player::factory()->create(['national_id' => $awayTeam->id]);

        $game->teams()->attach($homeTeam, ['is_away' => false]);
        $game->teams()->attach($awayTeam, ['is_away' => true]);
        GameGoal::factory()->create([
            'game_id' => $game->id,
            'player_id' => $scorerId,
        ]);

        Prediction::factory([
            'game_id' => $game->id,
            'sign' => 'x',
            'home_score' => '1',
            'away_score' => '0',
            'home_scorer_id' => null,
            'away_scorer_id' => null,
            'league_id' => $this->league->id,
            'user_id' => $this->users[0]->id,
        ])->create();
        Prediction::factory([
            'game_id' => $game->id,
            'sign' => '1',
            'home_score' => '1',
            'away_score' => '0',
            'home_scorer_id' => null,
            'away_scorer_id' => null,
            'league_id' => $this->league->id,
            'user_id' => $this->users[1]->id,
        ])->create();
        Prediction::factory([
            'game_id' => $game->id,
            'sign' => '2',
            'home_score' => '0',
            'away_score' => '1',
            'home_scorer_id' => null,
            'away_scorer_id' => null,
            'league_id' => $this->league->id,
            'user_id' => $this->users[2]->id,
        ])->create();
        Prediction::factory([
            'game_id' => $game->id,
            'sign' => '1',
            'home_score' => '0',
            'away_score' => '1',
            'home_scorer_id' => null,
            'away_scorer_id' => null,
            'league_id' => $this->league->id,
            'user_id' => $this->users[3]->id,
        ])->create();

        $homeTeam->players->map(static fn (Player $player) => $player->games()->attach($game));
        $awayTeam->players->map(static fn (Player $player) => $player->games()->attach($game));

        $calculator = app(RankingCalculatorInterface::class);
        $calculator->calculate($this->league);
        $rank = $calculator->get($this->league);

        $this->assertSame(5, $rank[0]->total());
        $this->assertSame($this->users[1]->id, $rank[0]->userId());
        $this->assertSame(4, $rank[1]->total());
        $this->assertSame($this->users[0]->id, $rank[1]->userId());
        $this->assertSame(1, $rank[2]->total());
        $this->assertSame($this->users[3]->id, $rank[2]->userId());
        $this->assertSame(0, $rank[3]->total());
        $this->assertSame($this->users[2]->id, $rank[3]->userId());
    }
}
