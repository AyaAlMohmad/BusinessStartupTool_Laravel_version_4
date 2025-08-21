@if(!empty($data))
<div class="mb-4 last:mb-0">
    @if($title)<div class="font-semibold text-gray-700 mb-1">{{ $title }}:</div>@endif
    <div class="text-gray-600">
        @foreach((array)$data as $item)
            <div class="flex items-start">
                <span class="mr-2">•</span>
                <span class="break-words">
                    @if(is_array($item) || is_object($item))
                        @json($item)
                    @else
                        {{ is_bool($item) ? ($item ? 'Yes' : 'No') : $item }}
                    @endif
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif