<div class="w-full flex justify-between items-center py-2 md:py-8">
    <x-bar.link
            link="{{route('prediction.previous-from-ref', compact('game'))}}"
            svg="previous"
            disabled="{{$game->isFirstGame()}}"
    />
    <x-bar.heading :games="$games" :game="$game"/>
    <x-bar.link
            link="{{route('prediction.next-from-ref', compact('game'))}}"
            svg="next"
            disabled="{{$game->isFinal()}}"
    />
</div>
