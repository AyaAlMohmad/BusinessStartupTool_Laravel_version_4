@extends('layouts.app')
@section('content')
@php
function resolveFieldName($key)
{
    // أسماء ثابتة
    $fieldNames = [
        'user_id' => 'User Name',
        'business_id' => 'Business Name',
        'options' => 'Considering the discussion we had, list three features that can make your solution unique in the market?',
        'notes' => 'Notes',
    ];

    // إذا كان موجودًا في القائمة مباشرة
    if (isset($fieldNames[$key])) {
        return $fieldNames[$key];
    }

    // اكتشاف حملات Campaign بشكل ديناميكي
    if (preg_match('/^campaign_(\d+)_(goal|audience|format|channels|notes)$/', $key, $matches)) {
        $num = $matches[1];
        $type = $matches[2];

        $questions = [
            'goal' => 'What is the Goal/Message I want to deliver?',
            'audience' => 'Who is my Audience for this campaign?',
            'format' => 'What format do you use?',
            'channels' => 'What channels you will use?',
            'notes' => 'Notes',
        ];

        return "Campaign {$num} - " . ($questions[$type] ?? ucfirst($type));
    }

    // fallback
    return ucfirst(str_replace('_', ' ', $key));
}
@endphp

<div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Marketing Details #{{ $feature->id }}</h1>

        <h2 class="text-xl font-semibold mb-2">🔄 Last Modified:</h2>
        <table class="table-auto w-full mb-6 border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Field</th>
                    <th class="px-4 py-2">New Value</th>
                    <th class="px-4 py-2">Old Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($combinedData as $key => $newValue)
                    @php
                        $processedNew = is_string($newValue) ? json_decode($newValue, true) ?? $newValue : $newValue;
                        $displayNew = is_array($processedNew) 
                            ? implode(', ', array_map(fn($v) => html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $processedNew))
                            : html_entity_decode($processedNew, ENT_QUOTES, 'UTF-8');
        
                        $oldValue = $oldData[$key] ?? null;
                        $processedOld = is_string($oldValue) ? json_decode($oldValue, true) ?? $oldValue : $oldValue;
                        $displayOld = is_array($processedOld) 
                            ? implode(', ', array_map(fn($v) => html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $processedOld))
                            : html_entity_decode($processedOld ?? '', ENT_QUOTES, 'UTF-8');
        
                        $isDifferent = trim((string)$displayNew) !== trim((string)$displayOld);
                        
                        $fieldName = str_replace(['.', '_'], ' ', $key);
                        $fieldName = ucwords($fieldName);
                    @endphp
        
                    <tr class="transition-colors @if($isDifferent) bg-yellow-50 hover:bg-yellow-100 @else hover:bg-gray-50 @endif cursor-pointer"
                        onclick="showModal('{{ addslashes($key) }}', `{{ addslashes($displayNew) }}`, `{{ addslashes($displayOld) }}`)">
                        <td class="border px-4 py-2 font-medium break-words max-w-xs">{{ resolveFieldName($key) }}</td>
                        @php
                        $oldVal = $oldData[$key] ?? null;

                        if ($key === 'user_id') {
                            $user = \App\Models\User::find($newValue);
                            $displayNew = optional($user)->name;

                            $oldUser = \App\Models\User::find($oldVal);
                            $displayOld = optional($oldUser)->name;
                        } elseif ($key === 'business_id') {
                            $biz = \App\Models\Business::find($newValue);
                            $displayNew = optional($biz)->name;

                            $oldBiz = \App\Models\Business::find($oldVal);
                            $displayOld = optional($oldBiz)->name;
                        }
                    @endphp
                        <td class="border px-4 py-2 break-words max-w-xs" 
                            style="{{ $isDifferent ? 'background-color:#d1fae5; color:#065f46; font-weight:bold; padding:8px; border-radius:6px;' : '' }}">
                            {{ $displayNew }}
                        </td>
                        <td class="border px-4 py-2 break-words max-w-xs" 
                            style="{{ $isDifferent ? 'background-color:#fee2e2; color:#991b1b; font-weight:bold; padding:8px; border-radius:6px;' : '' }}">
                            {{ $displayOld }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <h2 class="text-xl font-semibold mb-4">🕘 Change Log:</h2>

        <div class="space-y-4">
            @foreach ($auditLogs as $index => $log)
            <div class="border rounded-lg">
                <a href="#log-{{ $index }}" 
                   class="accordion-link block p-4 bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer"
                   onclick="toggleLog(event, '{{ $index }}')">
                    <div class="flex justify-between items-center">
                        <span>
                            Edited on {{ $log->created_at->format('Y-m-d H:i') }} 
                            by {{ optional($log->user)->name }}
                        </span>
                        <span class="transform transition-transform duration-200" id="arrow-{{ $index }}">▼</span>
                    </div>
                </a>
                
                <div id="log-{{ $index }}" class="hidden log-details">
                    <div class="p-4">
                        <table class="table-auto w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2">Field</th>
                                    <th class="px-4 py-2">New Value</th>
                                    <th class="px-4 py-2">Old Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($log->new_data as $key => $newVal)
                                    @php
                                        $oldVal = $log->old_data[$key] ?? null;
                                        
                                        // Handling new values
                                        if (is_string($newVal) && json_decode($newVal)) {
                                            $newVal = json_decode($newVal, true);
                                        }
                                        $displayNew = is_array($newVal) 
                                            ? implode(', ', array_map(fn($v) => html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $newVal)) 
                                            : html_entity_decode($newVal, ENT_QUOTES, 'UTF-8');
                                        
                                        // Handling old values
                                        $displayOld = is_array($oldVal) 
                                            ? implode(', ', array_map(fn($v) => html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $oldVal)) 
                                            : html_entity_decode($oldVal ?? '', ENT_QUOTES, 'UTF-8');
                                    @endphp
                                    
                                    @if ($displayNew != $displayOld)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="font-medium text-gray-700">
                                                {{ resolveFieldName($key) }}
                                            </td>
                                            <td class="border px-4 py-2" 
                                                style="background-color: #d1fae5; color: #065f46; font-weight: bold; padding: 8px; border-radius: 6px;">
                                                {{ $displayNew }}
                                            </td>
                                            <td class="border px-4 py-2" 
                                                style="background-color: #fee2e2; color: #991b1b; font-weight: bold; padding: 8px; border-radius: 6px;">
                                                {{ $displayOld }}
                                            </td>
                                        </tr>
                                    @else
                                        <!-- Display values even if they are the same -->
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="font-medium text-gray-700">
                                                {{ resolveFieldName($key) }}
                                            </td>
                                            @php
                                            $oldVal = $oldData[$key] ?? null;
                    
                                            if ($key === 'user_id') {
                                                $user = \App\Models\User::find($newValue);
                                                $displayNew = optional($user)->name;
                    
                                                $oldUser = \App\Models\User::find($oldVal);
                                                $displayOld = optional($oldUser)->name;
                                            } elseif ($key === 'business_id') {
                                                $biz = \App\Models\Business::find($newValue);
                                                $displayNew = optional($biz)->name;
                    
                                                $oldBiz = \App\Models\Business::find($oldVal);
                                                $displayOld = optional($oldBiz)->name;
                                            }
                                        @endphp
                                            <td class="border px-4 py-2">{{ $displayNew }}</td>
                                            <td class="border px-4 py-2">{{ $displayOld }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
        
        
        </div>
    </div>
</div>

<script>
function toggleLog(event, index) {
    event.preventDefault();
    const details = document.getElementById(`log-${index}`);
    const arrow = document.getElementById(`arrow-${index}`);
    
    // Close all other details
    document.querySelectorAll('.log-details').forEach(item => {
        if (item.id !== `log-${index}`) {
            item.classList.add('hidden');
            // Fix here: look inside the correct item
            const otherArrow = item.previousElementSibling.querySelector(`#arrow-${item.id.split('-')[1]}`);
            if (otherArrow) otherArrow.innerHTML = '▼';
        }
    });
    
    // Toggle current state
    details.classList.toggle('hidden');
    arrow.innerHTML = details.classList.contains('hidden') ? '▼' : '▲';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
