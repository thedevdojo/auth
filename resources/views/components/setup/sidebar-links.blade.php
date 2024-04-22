<nav class="px-5 mt-3">
    <ul role="list" class="space-y-3">
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">Configure</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <x-auth::setup.sidebar-link-item
                    pageLink="login"
                    icon="key"
                    text="Login"
                    :$page
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="register"
                    icon="user-circle-plus"
                    text="Register"
                    :$page
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="verify"
                    icon="shield-check"
                    text="Verify"
                    :$page
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="password-request"
                    icon="cursor-text"
                    text="Password Reset Request"
                    :$page
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="password-reset"
                    icon="password"
                    text="Password Reset"
                    :$page
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="password-confirmation"
                    icon="lock-key"
                    text="Password Confirmation"
                    :$page
                ></x-auth::setup.sidebar-link-item>
            </ul>
        </li>
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">Customize</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <x-auth::setup.sidebar-link-item
                    pageLink="branding"
                    icon="swatches"
                    text="Branding"
                    :$page
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="appearance"
                    icon="paint-bucket"
                    text="Appearance"
                    :$page
                ></x-auth::setup.sidebar-link-item>
            </ul>
        </li>
    </ul>
</nav>