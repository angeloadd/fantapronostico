<x-home::shared.card title="{{ __('messages.home.ranking_title') }}">
    <div class="overflow-auto sm:overflow-visible w-full">
        @foreach($ranking as $position => $rank)
            <div @class([
                            'flex justify-between gap-2 px-3 py-2 my-2 rounded-2xl hover:bg-accent/20',
                            'bg-accent/20 hover:bg-accent/25' => Auth::user()?->id === $rank->userId(),
                            'hover:bg-base-200' => Auth::user()?->id !== $rank->userId(),
                        ])>
                <div class="space-x-2">
                           <span
                            @class([
                                    'inline-flex items-center justify-center size-8 rounded-full font-bold text-sm',
                                    'bg-gradient-to-br from-amber-300 to-yellow-600' => $position === 0,
                                    'bg-gradient-to-br from-secondary to-gray-400 text-secondary-content' => $position === 1,
                                    'bg-gradient-to-br from-amber-700 to-amber-900 text-seo-content' => $position === 2,
                                    'bg-gradient-to-br from-primary to-gray-500 text-base-content-content' => $position >= 3 && $position <= 5,
                                    'bg-gradient-to-br from-accent/30 to-accent/60 text-secondary-content' => $position === 7 || $position === 6,
                                    'border-accent/30 border-1' => $position > 7,
                                ])
                           >{{$position + 1}}</span>
                    <span>{{$rank->userName()}}
                        @if(Auth::user()?->id === $rank->userId())
                            <span class="text-accent text-xs">{{ __('messages.common.you') }}</span>
                        @endif
                            </span>
                </div>
                <div class="flex items-center gap-2 font-bold">
                    {{$rank->total()}} {{ __('messages.ranking.points_abbr_lower') }}
                </div>
            </div>
        @endforeach
    </div>
</x-home::shared.card>
