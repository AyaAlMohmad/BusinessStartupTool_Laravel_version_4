@if(!empty($data))
<div class="mb-4 last:mb-0">
    @if($title)<div class="font-semibold text-gray-700 mb-1">{{ $title }}:</div>@endif
    <div class="text-gray-600">
        @foreach((array)$data as $key => $value)
            <div class="flex items-start">
                <span class="mr-2 font-medium">{{ ucfirst($key) }}:</span>
                <span class="break-words">
                    @if(is_array($value) || is_object($value))
                        @foreach((array)$value as $item)
                            <div class="ml-2">• {{ $item }}</div>
                        @endforeach
                    @else
                        {{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}
                    @endif
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif