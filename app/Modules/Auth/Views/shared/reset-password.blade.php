<fieldset class="flex-1 flex flex-col items-center justify-center gap-4 w-full">
    <legend class="text-center text-sm">{{__('auth.request_password_reset.paragraph')}}</legend>
    <x-auth::shared.form
        action="{{route('password.update')}}"
        method="POST"
        prefix="reset-password"
        :formControls="[
        [
            'name' => 'password',
            'type' => 'password',
            'placeholder' => 'Nuova Password',
        ],
        [
            'name' => 'token',
            'type' => 'hidden',
            'placeholder' => 'Token',
            'value' => request()->token
        ],
        [
            'name' => 'email',
            'type' => 'hidden',
            'placeholder' => 'Email',
            'value' => request()->email
        ]
    ]"
        btnText="{{__('auth.reset-password.btn')}}"
    />
</fieldset>
