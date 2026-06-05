<x-auth::shared.nav>
    <x-auth::shared.form
        action="{{route('register')}}"
        method="POST"
        prefix="register"
        :formControls="[
            [
                'name' => 'email',
                'type' => 'email',
                'placeholder' => 'Email',
            ],
            [
                'name' => 'name',
                'type' => 'text',
                'placeholder' => 'Username',
            ],
            [
                'name' => 'password',
                'type' => 'password',
                'placeholder' => 'Password',
            ],
        ]"
        btnText="{{__('auth.register.btn')}}"
        :showTec="true"
    />
</x-auth::shared.nav>
