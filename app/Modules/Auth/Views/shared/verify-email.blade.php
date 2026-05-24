<x-partials.notifications.toast-message sessionKey="status"/>

<div class="flex-1 flex flex-col items-center justify-center gap-4 w-full text-center">
    <h2 class="text-2xl">{{__('auth.verify_email.heading')}}</h2>
    <span class="text-lg">
        {{__('auth.verify_email.paragraph', ['email' => Auth::user()->email])}}
    </span>
    <form action="{{route('verification.send')}}" method="POST">
        @csrf
        <span class="text-xs text-base-content/70">
            {{__('auth.verify_email.paragraph2', ['email' => Auth::user()->email])}}
        </span>
        <button class="btn btn-primary w-full mt-2">{{__('auth.verify_email.btn')}}</button>
    </form>
</div>
