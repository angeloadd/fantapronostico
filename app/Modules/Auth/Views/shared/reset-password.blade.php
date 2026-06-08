<div class="flex-1 flex flex-col items-center justify-center gap-4 w-full">
    <x-auth::shared.form
        action="{{route('password.update')}}"
        method="POST"
        prefix="reset-password"
        :formControls="[
        [
            'name' => 'password',
            'type' => 'password',
            'placeholder' => 'Nuova Password',
            'label' => __('auth.request_password_reset.paragraph'),
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
        btnText="{{__('auth.reset_password.btn')}}"
    />
</div>
