<div class="text-primary/70 text-center">
    <p class="font-bold">
        {{str($date->timezone('Europe/Rome')->isoFormat('D MMMM YYYY'))->title()}}
    </p>
    <p class="text-4xl">
        {{$date->format('H:i')}}
    </p>
</div>
