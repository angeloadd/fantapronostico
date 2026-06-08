<div class="flex-1 flex flex-col items-center justify-center gap-4 w-full text-center">
    <svg style="stroke: var(--color-accent)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-16">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    <h2 class="text-2xl">{{__('auth.verify_email.heading')}}</h2>
    <span class="text-lg">
        {!! __('auth.verify_email.paragraph', ['email' => Auth::user()->email]) !!}
    </span>
    <x-auth::shared.form
            action="{{route('verification.send')}}"
            method="POST"
            :formControls="[]"
            btnText="{{__('auth.verify_email.btn')}}"
            hint="{{__('auth.verify_email.paragraph2', ['email' => Auth::user()->email])}}"
    />
</div>
