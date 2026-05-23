<?php

declare(strict_types=1);

namespace App\Events;

use App\Modules\League\Models\League;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

final class GameGoalsUpdated implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly League $league) {}

    public function handle(GameGoalsUpdated $event): void
    {
        Artisan::call('fp:ranking:calculate', ['--leagueId' => $event->league->id]);

        Log::channel('worker')->info('ranking for league '.$event->league->name.'[id='.$event->league->id.'] updated');
    }
}
