<nav class="px-5 mt-3">
    <ul role="list" class="space-y-3">
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">Configure</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <x-auth::setup.sidebar-link-item
                    href="/auth/setup/login"
                    icon="key"
                    text="Login"
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    href="/"
                    icon="user-circle-plus"
                    text="Register"
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    href="/"
                    icon="shield-check"
                    text="Verify"
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    href="/"
                    icon="cursor-text"
                    text="Password Reset Request"
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    href="/"
                    icon="password"
                    text="Password Reset"
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    href="/"
                    icon="lock-key"
                    text="Password Confirmation"
                ></x-auth::setup.sidebar-link-item>
            </ul>
        </li>
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">Customize</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <x-auth::setup.sidebar-link-item
                    href="/"
                    icon="swatches"
                    text="Branding"
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    href="/"
                    icon="paint-bucket"
                    text="Appearance"
                ></x-auth::setup.sidebar-link-item>
            </ul>
        </li>
    </ul>
</nav>