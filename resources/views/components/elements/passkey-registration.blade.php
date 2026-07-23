@assets
    @if(config('devdojo.auth.settings.dev_mode'))
        @vite(['vendor/devdojo/auth/resources/js/passkeys.js'])
    @else
        <script src="{{ asset('/auth/build/assets/passkeys.js') }}"></script>
    @endif
@endassets

<div
        x-data="{
        supported: false,
        showForm: false,
        name: '',
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        getDefaultPasskeyName() {
            const ua = navigator.userAgent;

            const browser = [
                { pattern: /Edg|Edge/, name: 'Edge' },
                { pattern: /OPR|Opera|OPiOS/, name: 'Opera' },
                { pattern: /Firefox|FxiOS/, name: 'Firefox' },
                { pattern: /Chrome|CriOS/, name: 'Chrome' },
                { pattern: /Safari/, name: 'Safari' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            const os = [
                { pattern: /iPhone/, name: 'iPhone' },
                { pattern: /iPad|Macintosh(?=.*Mobile)/, name: 'iPad' },
                { pattern: /Android/, name: 'Android' },
                { pattern: /Mac/, name: 'Mac' },
                { pattern: /Windows/, name: 'Windows' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            return [browser, os].filter(Boolean).join(' on ') || '';
        },
        init() {
            this.name = this.getDefaultPasskeyName();
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async register() {
            if (!this.name.trim()) return;

            this.loading = true;
            this.error = null;

            try {
                await window.Passkeys.register({ name: this.name });
                this.name = this.getDefaultPasskeyName();
                this.showForm = false;
                await $wire.loadPasskeys();
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
        cancel() {
            this.showForm = false;
            this.name = this.getDefaultPasskeyName();
            this.error = null;
        },
    }"
>
    <template x-if="!supported">
        <p class="text-sm text-zinc-500 dark:text-zinc-400">Passkeys are not supported in this browser.</p>
    </template>

    <template x-if="supported && !showForm">
        <div class="flex justify-end">
            <x-button type="button" x-on:click="showForm = true">
                Add passkey
            </x-button>
        </div>
    </template>

    <template x-if="supported && showForm">
        <div class="space-y-4 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
            <div>
                <label for="passkey-name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">Passkey name</label>
                <input
                        id="passkey-name"
                        type="text"
                        x-model="name"
                        x-ref="passkeyNameInput"
                        x-init="$nextTick(() => $refs.passkeyNameInput?.focus())"
                        x-on:keydown.enter.prevent="register()"
                        placeholder="e.g., MacBook Pro, iPhone"
                        class="mt-1 block w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-xs focus:border-zinc-500 focus:outline-hidden focus:ring-1 focus:ring-zinc-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-100"
                />
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Give this passkey a name to help you identify it later.</p>
            </div>

            <p x-show="error" x-text="error" x-cloak class="text-sm text-red-600 dark:text-red-400"></p>

            <div class="flex justify-end gap-2">
                <x-button type="button" x-on:click="register()" x-bind:disabled="loading || !name.trim()">
                    <span x-show="!loading">Register passkey</span>
                    <span x-show="loading" x-cloak>Registering...</span>
                </x-button>
                <x-button type="button" color="gray" x-on:click="cancel()">
                    Cancel
                </x-button>
            </div>
        </div>
    </template>
</div>
