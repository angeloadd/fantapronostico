<div class="flex-1 flex flex-col items-center justify-center gap-4 w-full">
    <fieldset>
        <legend class="text-center text-sm">{{__('auth.request_password_reset.paragraph')}}</legend>
        <x-auth::shared.form
                action="{{route('password.email')}}"
                method="POST"
                prefix="reset-password"
                :formControls="[
        [
            'name' => 'email',
            'type' => 'email',
            'placeholder' => 'Email',
        ]
        ]"
                btnText="{{__('auth.request_password_reset.btn')}}"
        />
    </fieldset>
</div>
