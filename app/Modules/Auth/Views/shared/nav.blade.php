<div class="flex border-b border-base-300">
    <x-auth::shared.tab name="login" text="{{__('auth.login.nav')}}"/>
    <x-auth::shared.tab name="register" text="{{__('auth.register.nav')}}"/>
</div>
<div class="flex-1 flex items-center justify-center p-6">
    {{$slot}}
</div>
