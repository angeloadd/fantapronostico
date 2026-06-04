<div class="text-primary/70 text-center">
    <p class="font-bold">
        {{str($date->avoidMutation()->timezone('Europe/Rome')->isoFormat('D MMMM YYYY'))->title()}}
    </p>
    <p class="text-4xl">
        {{$date->avoidMutation()->timezone('Europe/Rome')->format('H:i')}}
    </p>
</div>
