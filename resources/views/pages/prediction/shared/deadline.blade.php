<div class="alert border-accent shadow-lg flex flex-col justify-center items-center">
    <h3 class="font-bold text-center">
        Modifica il pronostico entro la data di inizio dell'incontro
    </h3>
    <div class="w-full flex justify-center items-center">
        <x-partials.countdown.main bgColor="bg-accent/80" :date="$startedAt"/>
    </div>
</div>
