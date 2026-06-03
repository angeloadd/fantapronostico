<div class="w-full flex justify-center items-center py-2 md:py-8">
    <div class="join">
        <x-bar.link
                link="{{route('prediction.previous-from-ref', compact('game'))}}"
                img="previous.svg"
                alt="{{ __('messages.accessibility.backward_arrow') }}"
                side="left"
                condition="{{!$game->isFirstGame()}}"
        />
        <x-bar.dropdown :games="$games" :game="$game" btnClasses="btn-primary mx-1 rounded-none"/>
        <x-bar.link
                link="{{route('prediction.next-from-ref', compact('game'))}}"
                img="next.svg"
                alt="{{ __('messages.accessibility.forward_arrow') }}"
                side="right"
                condition="{{!$game->isFinal()}}"
        />
    </div>
</div>
