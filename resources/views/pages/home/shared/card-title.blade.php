<h2 class="shrink-0 text-xs font-medium text-base-content/50 uppercase tracking-wider">
    {{$slot}}
    @if($hint ?? false)
        <span class="text-xs lowercase text-base-content/20">(solo tempi regolamentari)</span>
    @endif
</h2>
