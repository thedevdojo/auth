<nav class="px-5 mt-3">
    <ul role="list" class="space-y-3">

        <x-auth::setup.sidebar-link-item
                    pageLink="welcome"
                    icon="house"
                    text="Welcome"
                    class="-mx-2"
                    :$page
                ></x-auth::setup.sidebar-link-item>
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">Configure</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <x-auth::setup.sidebar-link-item
                    pageLink="branding"
                    icon="swatches"
                    text="Customizations"
                    :$page
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="providers"
                    icon="user-circle-plus"
                    text="Social Providers"
                    :$page
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="settings"
                    icon="gear"
                    text="Settings"
                    :$page
                ></x-auth::setup.sidebar-link-item>
            </ul>
        </li>
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400">Customize</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
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