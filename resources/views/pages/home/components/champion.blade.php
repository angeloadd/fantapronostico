@php
    $openingDate = $tournamentStartedAt->subDays(2);
    $isPastPredictionDateOrStillNotOpen = (null === $champion && $tournamentStartedAt->isPast()) || $openingDate->isFuture();
    $isOpenForPrediction = $tournamentStartedAt->isFuture() && $openingDate->isPast();
@endphp

<x-home::shared.card :grayed="$isPastPredictionDateOrStillNotOpen" title="{{ __('messages.home.champion_title') }}">
    <div class="my-auto gap-4 flex flex-col">
        @if(null !== $champion)
            <x-home::shared.info-tile label="{{ __('messages.common.winner') }}" :value="__($champion->team->name)" :isPredictionSuccessful="$isChampionPredictionSuccessful ?? false">
                <x-partials.svgs.rank/>
            </x-home::shared.info-tile>
            <x-home::shared.info-tile label="{{ __('messages.common.top_scorer') }}" :value="$champion->player->displayed_name" :isPredictionSuccessful="$isTopScorerPredictionSuccessful ?? false">
                <x-partials.svgs.boot/>
            </x-home::shared.info-tile>
            @if($isOpenForPrediction)
                <a href="{{ route('champion.index') }}" class="btn btn-primary btn-lg rounded-2xl w-full mt-auto shrink-0">{{ __('messages.prediction.edit') }}</a>
            @endif
        @else
            @if($isOpenForPrediction)
                <div class="flex-1 flex flex-col items-center justify-center gap-3 text-center">
                    <div class="ml-auto">
                        <x-partials.countdown.main :date="$tournamentStartedAt"/>
                    </div>
                    <div class="m-auto bg-accent/30 text-accent size-12 rounded-full flex items-center justify-center">
                        <x-partials.svgs.rank/>
                        <x-partials.svgs.boot/>
                    </div>
                    <span class="font-bold text-base-content/60">{{ __('messages.champion.selection_pending') }}</span>
                    <a href="{{ route('champion.index') }}" class="btn btn-primary btn-lg rounded-2xl w-full mt-auto shrink-0">{{ __('messages.prediction.create') }}</a>
                </div>
            @elseif($tournamentStartedAt->isPast())
                <div class="ml-auto">
                    <x-partials.countdown.main :isExpired="true"/>
                </div>
                <div class="flex-1 flex flex-col items-center justify-center gap-4">
                    <div class="bg-base-300 size-12 rounded-full flex items-center justify-center text-base-content/50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <span class="font-bold text-base-content/60">{{ __('messages.champion.not_selected') }}</span>
                    <span class="text-center">{{ __('messages.champion.too_late') }}</span>
                </div>
            @else
                <div class="ml-auto">
                    <x-partials.countdown.main :date="$openingDate" :isOpen="false"/>
                </div>
                <div class="flex-1 flex flex-col items-center justify-center gap-3 text-center">
                    <div class="bg-base-300 size-12 rounded-full flex items-center justify-center text-base-content/50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <span class="font-bold text-base-content/60">{{ __('messages.champion.not_selected') }}</span>
                    <span class="text-center">{{ __('messages.champion.not_open') }}</span>
                </div>
            @endif
        @endif
    </div>

</x-home::shared.card>
