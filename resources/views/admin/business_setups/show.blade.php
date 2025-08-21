@extends('layouts.app')
@section('content')
<div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Legal Requirements Details #{{ $setup->id }}</h1>

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
                @foreach ($setup->getAttributes() as $key => $newValue)
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
                    @endphp
                    <tr class="@if($isDifferent) bg-yellow-50 hover:bg-yellow-100 @else hover:bg-gray-50 @endif transition-colors">
                        <td class="border px-4 py-2 font-medium break-words">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
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
                        <td class="border px-4 py-2 break-words" style="{{ $isDifferent ? 'background-color:#d1fae5; color:#065f46; font-weight:bold;' : '' }}">
                            {{ $displayNew }}
                        </td>
                        <td class="border px-4 py-2 break-words" style="{{ $isDifferent ? 'background-color:#fee2e2; color:#991b1b; font-weight:bold;' : '' }}">
                            {{ $displayOld }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($setup->tasks && $setup->tasks->count())
            @php
                $relatedTasks = $setup->tasks->filter(function ($task) use ($setup) {
                    return \Carbon\Carbon::parse($task->created_at)->format('Y-m-d H:i') === $setup->updated_at->format('Y-m-d H:i');
                });
            @endphp

            @if($relatedTasks->count())
                <h3 class="text-lg font-semibold mt-6 mb-2">📝 Tasks Related to Last Update</h3>
                <table class="table-auto w-full border mb-4">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2">Task</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Deadline</th>
                            <th class="px-4 py-2">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($relatedTasks as $task)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $task->title ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $task->description ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $task->status ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $task->deadline ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $task->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif


        <h2 class="text-xl font-semibold mb-4">🕘 Change Log:</h2>
        <div class="space-y-4">
            @foreach ($auditLogs as $index => $log)
            <div class="border rounded-lg">
                <a href="#log-{{ $index }}" 
                   class="accordion-link block p-4 bg-gray-50 hover:bg-gray-100 cursor-pointer"
                   onclick="toggleLog(event, '{{ $index }}')">
                    <div class="flex justify-between items-center">
                        <span>Edited on {{ $log->created_at->format('Y-m-d H:i') }} by {{ optional($log->user)->name }}</span>
                        <span id="arrow-{{ $index }}">▼</span>
                    </div>
                </a>
                <div id="log-{{ $index }}" class="hidden log-details">
                    <div class="p-4">
                        <table class="table-auto w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2">Field</th>
                                    <th class="px-4 py-2">New</th>
                                    <th class="px-4 py-2">Old</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($log->new_data as $key => $newVal)
                                    @php
                                        $oldVal = $log->old_data[$key] ?? null;
                                        $newVal = is_string($newVal) && json_decode($newVal) ? json_decode($newVal, true) : $newVal;
                                        $oldVal = is_string($oldVal) && json_decode($oldVal) ? json_decode($oldVal, true) : $oldVal;

                                        $displayNew = is_array($newVal) ? implode(', ', $newVal) : $newVal;
                                        $displayOld = is_array($oldVal) ? implode(', ', $oldVal) : $oldVal;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-4 py-2">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
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
                                        <td class="border px-4 py-2" style="background-color: #d1fae5;">{{ $displayNew }}</td>
                                        <td class="border px-4 py-2" style="background-color: #fee2e2;">{{ $displayOld }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($setup->tasks && $setup->tasks->count())
    @php
        $relatedTasks = $setup->tasks->filter(function ($task) use ($log) {
            return \Carbon\Carbon::parse($task->created_at)->format('Y-m-d H:i') === $log->created_at->format('Y-m-d H:i');
        });
    @endphp

    @if($relatedTasks->count())
        <h3 class="text-lg font-semibold mt-6 mb-2">📝 Tasks Related to This Change</h3>
        <table class="table-auto w-full border mb-4">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Task</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Deadline</th>
                    <th class="px-4 py-2">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($relatedTasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $task->title ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $task->description ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $task->status ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $task->deadline ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $task->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endif

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

    document.querySelectorAll('.log-details').forEach((item, i) => {
        if (item.id !== `log-${index}`) {
            item.classList.add('hidden');
            const otherArrow = item.previousElementSibling.querySelector(`#arrow-${item.id.split('-')[1]}`);
            if (otherArrow) otherArrow.innerHTML = '▼';
        }
    });

    details.classList.toggle('hidden');
    arrow.innerHTML = details.classList.contains('hidden') ? '▼' : '▲';
}
</script>
@endsection
