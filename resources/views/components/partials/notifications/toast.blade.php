<div
    x-transition
    x-init="setTimeout(() => $el.remove(), $el.firstElementChild.innerText.length * 100)"
    @class([
        'alert max-w-xs overflow-scroll md:overflow-visible w-full md:max-w-xl md:flex md:justify-center',
        'alert-warning' => $type === 'warning',
        'alert-error' => $type === 'error',
        'alert-success' => $type === 'success',
        'alert-info' => $type === 'info',
    ])
>
    <span>{{__($text ?? "Success")}}</span>
</div>
