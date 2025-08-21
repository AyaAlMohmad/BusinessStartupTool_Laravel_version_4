@php
    // تحويل البيانات إلى شكل قابل للمعالجة
    $data = is_string($data) ? json_decode($data, true) ?? $data : $data;
    $isArray = is_array($data) && array_values($data) === $data;
    $isObject = is_array($data) || is_object($data);
@endphp

@if(!empty($data))
<div class="mb-2 last:mb-0">
    @if($title)<div class="font-medium text-gray-700">{{ $title }}:</div>@endif
    
    <div class="text-gray-600 text-sm">
        @if($isArray)
            @foreach($data as $item)
                <div class="flex">
                    <span class="mr-1">•</span>
                    @if(is_array($item) || is_object($item))
                        <span class="font-mono">@json($item)</span>
                    @else
                        <span>{{ $item }}</span>
                    @endif
                </div>
            @endforeach
        @elseif($isObject)
            @foreach($data as $key => $value)
                <div class="flex">
                    <span class="mr-2 font-medium">{{ $key }}:</span>
                    @if(is_array($value) || is_object($value))
                        <span class="font-mono">@json($value)</span>
                    @else
                        <span>{{ $value }}</span>
                    @endif
                </div>
            @endforeach
        @else
            {{ $data }}
        @endif
    </div>
</div>
@endif