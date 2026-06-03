@php
    $results=__('messages.prediction.result').' <span class="sm:hidden">%s - %s</span><span class="hidden sm:block">%s - %s</span>';
@endphp

<x-table.head
    :heads="[
    ['text' => __('messages.table.rank'), 'class' => 'w-12'],
    ['text' => __('messages.table.name')],
    ['text' => __('messages.table.sign')],
    ['text' => sprintf($results, $game->home_team->code, $game->away_team->code, __($game->home_team->name), __($game->away_team->name))],
    ['text' => __('messages.table.goals_no_goals').' '.__($game->home_team->name), 'class' => ['hidden' => $game->isGroupStage()]],
    ['text' => __('messages.table.goals_no_goals').' '.__($game->away_team->name), 'class' => ['hidden' => $game->isGroupStage()]],
]"/>
