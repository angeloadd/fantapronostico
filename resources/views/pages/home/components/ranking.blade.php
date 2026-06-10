<x-home::shared.card title="{{ __('messages.home.ranking_title') }}">
    <div class="overflow-auto space-y-2">
    @foreach($ranking as $position => $rank)
        @if($position > 10)
            <div class="flex items-center justify-center px-3 py-2 text-base-content/60">
                <span class="tracking-widest text-xs select-none">• • •</span>
            </div>
        @endif
        <div @class([
            'flex justify-between gap-2 px-3 py-2 rounded-2xl',
            'bg-accent/20 hover:bg-accent/25' => Auth::user()?->id === $rank->userId(),
            'hover:bg-base-200' => Auth::user()?->id !== $rank->userId(),
        ])>
            <div class="space-x-2">
                <x-partials.ranking.badge :$position/>
                <span>{{$rank->userName()}}
                    @if(Auth::user()?->id === $rank->userId())
                        <span class="text-accent text-xs">{{ __('messages.common.you') }}</span>
                    @endif
                            </span>
            </div>
            <div class="flex items-center gap-2 font-bold tabular-nums">
                {{$rank->total()}} {{ __('messages.ranking.points_abbr_lower') }}
            </div>
        </div>
    @endforeach
    </div>
</x-home::shared.card>
