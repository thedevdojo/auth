<div class="flex flex-col justify-center items-center w-full max-w-md h-full">
    <div class="px-5 w-full">
        <h2 class="mb-2 text-lg font-semibold">Let's configure your authentication</h2>
        <p class="mb-6 text-sm text-gray-700">These configurations can be changed anytime in the authentication setup page.</p>
    </div>
    <div class="w-full">
        <div class="p-[3px] w-full rounded-xl bg-zinc-200/60">
            <div class="overflow-hidden mx-auto bg-white rounded-xl border border-zinc-300">
                <div class="relative p-5">
                    <label class="block text-sm font-medium leading-5 text-gray-700 dark:text-gray-300">
                        How to display email and password
                    </label>
                    <div class="flex items-center space-x-5 border-b border-zinc-200">
                        <button class="flex flex-col w-1/2 rounded-md border-2 border-blue-500">
                            <strong>Same Screen</strong>
                            <span class="text-[0.6rem]">Ask for login and password on the same screen</span>
                        </button>
                        <button class="flex flex-col w-1/2 rounded-md border-2 border-zinc-200">
                            <strong>Individual Screen</strong>
                            <span class="text-[0.6rem]">Ask for login and password on the individual screens</span>
                        </button>
                    </div>
                </div>
                <div class="relative divide-y divide-zinc-200">
                    <div class="px-5 py-3 text-sm font-semibold text-gray-700">Sign in options</div>
                    <x-auth::setup.checkbox-row icon="envelope" text="Email" />
                    <x-auth::setup.checkbox-row icon="phone" text="Phone Number" />
                    <x-auth::setup.checkbox-row icon="user" text="Username" />
                    <x-auth::setup.checkbox-row icon="google-logo" text="Google" />
                    <x-auth::setup.checkbox-row icon="facebook-logo" text="Facebook" />
                    <x-auth::setup.checkbox-row icon="apple-logo" text="Apple" />
                    <x-auth::setup.checkbox-row icon="github-logo" text="Github" />
                </div>
            </div>
            <div class="flex justify-between items-center px-3 py-3 text-sm">
                <button class="px-4 py-2 w-auto font-medium rounded-lg bg-zinc-300/40 text-zinc-600">Reset to Default</button>
                <x-auth::setup.button>Save</x-auth::setup.button>
            </div>
        </div>
    </div>
</div>