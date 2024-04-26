<div class="w-full max-w-sm divide-y divide-zinc-200">
    @foreach(config('devdojo.auth.providers') as $provider)
        <div class="flex relative justify-between items-center py-5 space-x-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10">
                    @if(isset($provider['svg']) && !empty(trim($provider['svg'])))
                        {!! $provider['svg'] !!}
                    @else
                        <span class="block w-full h-full rounded-full bg-zinc-200"></span>
                    @endif
                </div>
                <div class="relative">
                    <h4 class="text-base font-bold">{{ $provider['name'] }}</h4>
                    <p class="text-sm">slug: {{ $provider['slug'] }}</p>
                </div>
            </div>
            <div class="relative right">
                @if(isset($provider['client_id']) && !empty(trim($provider['client_id'])) && isset($provider['client_secret']) && !empty(trim($provider['client_secret'])))
                    <span class="flex justify-center items-center w-7 h-7 text-green-500 bg-green-100 rounded-full border-2 border-green-500">
                        <svg class="w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M15.75 1.5a6.75 6.75 0 0 0-6.651 7.906c.067.39-.032.717-.221.906l-6.5 6.499a3 3 0 0 0-.878 2.121v2.818c0 .414.336.75.75.75H6a.75.75 0 0 0 .75-.75v-1.5h1.5A.75.75 0 0 0 9 19.5V18h1.5a.75.75 0 0 0 .53-.22l2.658-2.658c.19-.189.517-.288.906-.22A6.75 6.75 0 1 0 15.75 1.5Zm0 3a.75.75 0 0 0 0 1.5A2.25 2.25 0 0 1 18 8.25a.75.75 0 0 0 1.5 0 3.75 3.75 0 0 0-3.75-3.75Z" clip-rule="evenodd" /></svg>
                    <span>
                @else
                    <span class="flex justify-center items-center w-7 h-7 text-red-500 bg-red-100 rounded-full border-2 border-red-500">
                        <svg class="w-4 h-4 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M15.75 1.5a6.75 6.75 0 0 0-6.651 7.906c.067.39-.032.717-.221.906l-6.5 6.499a3 3 0 0 0-.878 2.121v2.818c0 .414.336.75.75.75H6a.75.75 0 0 0 .75-.75v-1.5h1.5A.75.75 0 0 0 9 19.5V18h1.5a.75.75 0 0 0 .53-.22l2.658-2.658c.19-.189.517-.288.906-.22A6.75 6.75 0 1 0 15.75 1.5Zm0 3a.75.75 0 0 0 0 1.5A2.25 2.25 0 0 1 18 8.25a.75.75 0 0 0 1.5 0 3.75 3.75 0 0 0-3.75-3.75Z" clip-rule="evenodd" /></svg>
                    <span>
                @endif
            </div>  
        </div>
    @endforeach
</div>