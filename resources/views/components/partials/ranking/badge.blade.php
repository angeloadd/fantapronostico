<span
    @class([
        'inline-flex items-center justify-center size-8 rounded-full font-bold text-sm',
        'bg-gradient-to-br from-amber-300 to-yellow-600' => $position === 0,
        'bg-gradient-to-br from-secondary to-gray-400 text-secondary-content' => $position === 1,
        'bg-gradient-to-br from-amber-700 to-amber-900 text-seo-content' => $position === 2,
        'bg-gradient-to-br from-primary to-gray-500 text-primary-content' => $position >= 3 && $position <= 5,
        'bg-gradient-to-br from-accent/30 to-accent/60 text-secondary-content' => $position === 7 || $position === 6,
        'border-accent/30 border-1' => $position > 7,
    ])
>{{$position + 1}}</span>
