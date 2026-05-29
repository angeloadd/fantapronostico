<div class="card h-full w-full shadow-md rounded-lg border border-accent/50 bg-base-100">
    <div class="card-body overflow-scroll">
        <div class="w-full flex justify-between items-center">
            <h2 class="card-title pb-2 text-2xl">{{$title}}</h2>
            @if(! empty($link ?? null))
                <a
                        href="{{$link}}"
                        role="button"
                        class="link link-accent text-md text-right lg:text-lg"
                >{{$linkText}}</a>
            @elseif($dropdown ?? false)
               <x-bar.dropdown :$games :$game btnClasses="btn-outline btn-primary" :right="true"/>
            @endif
        </div>
        {{$slot}}
    </div>
</div>
