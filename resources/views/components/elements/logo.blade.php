<x-auth::elements.link href="/" class="block w-auto" style="height:{{ $height ?? '30' }}px">
    @if($isImage)
        <img src="{{ url($imageSrc) }}" class="w-auto h-full" />
    @else
        {!! str_replace('<svg', '<svg class="w-auto h-full"', $svgString) !!}
    @endif
</x-auth::elements.link>