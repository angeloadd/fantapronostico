<x-layouts.with-drawer>
    <div class="md:card flex-1 min-h-0 w-full">
        <div class="md:card-body md:shadow-lg md:rounded-2xl md:border md:bg-base-100 min-h-0 grow p-4 flex flex-col my-auto md:border-accent/30">

            <div class="w-full space-y-1">

                {{-- Header: hidden on mobile, visible on sm+ --}}
                <div class="hidden sm:grid grid-cols-[2rem_1fr_auto_3rem] items-center gap-x-3 px-3 py-2 text-xs text-base-content/60 tracking-wider">
                    <div></div>
                    <div class="pl-1">{{ __('messages.ranking.player') }}</div>
                    <div class="grid grid-cols-5 gap-3 w-44 text-center">
                        <span>R</span><span>S</span><span>G</span><span>{{ __('messages.ranking.winner_initial') }}</span><span>{{ __('messages.ranking.top_scorers_initial') }}</span>
                    </div>
                    <div class="text-right">{{ __('messages.ranking.points_abbr_upper') }}</div>
                </div>

                @foreach($ranking as $position => $rank)
                    <div @class([
                    'grid grid-cols-[2rem_1fr_3rem] sm:grid-cols-[2rem_1fr_auto_3rem] items-center gap-x-3 gap-y-2 px-3 py-2.5 rounded-2xl',
                    'bg-accent/20 hover:bg-accent/25' => Auth::user()?->id === $rank->userId(),
                    'hover:bg-base-200' => Auth::user()?->id !== $rank->userId(),
                ])>

                        {{-- Rank badge --}}
                        <x-partials.ranking.badge :$position/>

                        {{-- Player name --}}
                        <span class="truncate">
                        {{ $rank->userName() }}
                            @if(Auth::user()?->id === $rank->userId())
                                <span class="text-accent text-xs ml-1.5">{{ __('messages.common.you') }}</span>
                            @endif
                    </span>

                        {{-- Stats: hidden on mobile, inline on sm+ --}}
                        <div class="hidden sm:grid grid-cols-5 gap-3 w-44 items-center justify-items-center text-sm">
                            <span class="tabular-nums">{{ $rank->results() }}</span>
                            <span class="tabular-nums">{{ $rank->signs() }}</span>
                            <span class="tabular-nums">{{ $rank->scorers() }}</span>
                            <span>
                            @if($rank->winner())
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-accent">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                                @else
                                    <span class="text-base-content/30">—</span>
                                @endif
                        </span>
                            <span>
                            @if($rank->topScorer())
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-accent">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                                @else
                                    <span class="text-base-content/30">—</span>
                                @endif
                        </span>
                        </div>

                        {{-- Points: always visible, right edge --}}
                        <div class="font-bold tabular-nums text-right">{{ $rank->total() }}</div>

                        {{-- Stats on mobile: second row spanning full width --}}
                        <div class="col-span-3 sm:hidden grid grid-cols-5 gap-1 items-center justify-items-center text-xs border-t border-base-300 pt-2">
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="text-base-content/40">R</span>
                                <span class="tabular-nums">{{ $rank->results() }}</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="text-base-content/40">S</span>
                                <span class="tabular-nums">{{ $rank->signs() }}</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="text-base-content/40">G</span>
                                <span class="tabular-nums">{{ $rank->scorers() }}</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="text-base-content/40">V</span>
                                @if($rank->winner())
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-accent">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                    </svg>
                                @else
                                    <span class="text-base-content/30">—</span>
                                @endif
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="text-base-content/40">C</span>
                                @if($rank->topScorer())
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-accent">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                    </svg>
                                @else
                                    <span class="text-base-content/30">—</span>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </div>
</x-layouts.with-drawer>
