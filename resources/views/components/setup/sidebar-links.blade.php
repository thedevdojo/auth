<nav class="px-4 mt-1">
    <ul role="list" class="space-y-3">
        <x-auth::setup.sidebar-link-item
            pageLink="auth/setup"
            icon="house"
            text="Home"
        ></x-auth::setup.sidebar-link-item>
        <li>
            <div class="px-1 text-xs font-semibold leading-6 text-gray-400">Configure</div>
            <ul role="list" class="mt-2 space-y-1">
                <x-auth::setup.sidebar-link-item
                    pageLink="auth/setup/appearance"
                    icon="paint-bucket"
                    text="Appearance"
                ></x-auth::setup.sidebar-link-item>
                
                <x-auth::setup.sidebar-link-item
                    pageLink="auth/setup/providers"
                    icon="user-circle-plus"
                    text="Social Providers"
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="auth/setup/language"
                    icon="globe-hemisphere-east"
                    text="Language"
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    pageLink="auth/setup/settings"
                    icon="gear"
                    text="Settings"
                ></x-auth::setup.sidebar-link-item>
            </ul>
        </li>
        
        <li>
            <div class="px-1 text-xs font-semibold leading-6 text-gray-400">Resources</div>
            <ul role="list" class="mt-2 space-y-1">
                <x-auth::setup.sidebar-link-item
                    newTab="true"
                    pageLink="https://github.com/thedevdojo/auth"
                    icon="github-logo"
                    text="Github Repo"
                ></x-auth::setup.sidebar-link-item>
                <x-auth::setup.sidebar-link-item
                    newTab="true"
                    pageLink="https://devdojo.com/auth/docs"
                    icon="notebook"
                    text="Documentation"
                ></x-auth::setup.sidebar-link-item>
            </ul>
        </li>
    </ul>

</nav>