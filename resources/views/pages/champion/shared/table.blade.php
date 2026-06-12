<div class="overflow-auto sm:overflow-visible w-full">
    <table class="table table-zebra w-full">
        <x-champion::shared.table-head/>
        <tbody>
            @foreach($champions as $key => $champion)
                <x-champion::shared.table-row :$champion :$key :lastUpdate="$champion->updated_at->avoidMutation()->timezone(Auth::user()?->timezone ?? 'Europe/Rome')->format('d/m/Y \o\r\e H:i:s \e u \m\s')"/>
            @endforeach
        </tbody>
    </table>
</div>
