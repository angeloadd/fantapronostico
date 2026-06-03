    <x-home::shared.card title="{{ __('messages.home.next_game_title') }}">
        @if(null !== $game && !$hasFinalStarted)
            <div class="ml-auto">
                <x-partials.countdown.main :isOpen="true" :date="$game->started_at"/>
            </div>
        @endif
        <div @class([
            'my-auto flex w-full items-center',
            'justify-center'  => !isset($game) || $hasFinalStarted,
            'justify-between' => isset($game) && !$hasFinalStarted,
        ])>
            @if(null !== $game && !$hasFinalStarted)
                <x-home::shared.team-display :teamCode="$game->home_team->code" :teamName="$game->home_team->name"/>
                <div class="flex flex-col items-center gap-3">
                    <x-home::shared.game-date :date="$game->started_at"/>
                    <a href="{{ route('prediction.create', ['game' => $game]) }}" class="btn bg-accent btn-lg rounded-2xl">
                        {{ __('messages.common.predict') }}
                    </a>
                </div>
                <x-home::shared.team-display :teamCode="$game->away_team->code" :teamName="$game->away_team->name"/>
            @else
                <div class="flex flex-col justify-center items-center gap-3 w-full">
                    <div class="ml-auto">
                        <x-partials.countdown.main :isExpired="true" :isOpen="false"/>
                    </div>
                    <div class="flex-1 flex flex-col items-center justify-center gap-3 text-center">
                        <div class="bg-base-300 size-12 rounded-full flex items-center justify-center text-base-content/50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        @if($hasFinalStarted)
                            <span class="font-bold text-base-content/60">{{ __('messages.prediction.final_in_progress') }}</span>
                            <span class="text-center">{{ __('messages.prediction.wait_final') }}</span>
                        @else
                            <span class="font-bold text-base-content/60">{{ __('messages.prediction.no_next_game') }}</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </x-home::shared.card>
