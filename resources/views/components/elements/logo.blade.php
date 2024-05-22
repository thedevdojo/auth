<x-auth::elements.link href="/" style="height:{{ $height ?? '30' }}px; width:auto; display:block">
    @if($isImage)
        <img src="{{ url($imageSrc) }}" style="height:100%; width:auto" />
    @else
        {!! str_replace('<svg', '<svg style="height:100%; width:auto"', $svgString) !!}
    @endif
</x-auth::elements.link>