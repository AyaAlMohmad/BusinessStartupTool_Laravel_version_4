@extends('layouts.app')
@section('content')
@php
    $fieldNames = [
        'your_idea' => 'What is your chosen Business Idea?',
        'solves_problem' => 'Do you think your solution is solving a real problem?',
        'problem_statement' => 'What problem are you solving?',
        'existing_solutions_used' => 'Are your target customers currently using any other solutions?',
        'current_solutions_details' => 'What solution are they using now?',
        'switch_reason' => 'Why do you think your customers would switch to your solution?',
        'desirability_notes' => 'Notes',
        'required_skills' => 'Do you think you (or your team) have the required skills for this business?',
        'qualifications_permits' => 'Does this business require any qualifications/permits?',
        'feasibility_notes' => 'Notes',
        'payment_possible' => 'Do you think people will pay for it?',
        'profitability' => 'Do you think it will be Profitable?',
        'finances_details' => 'Do you think you have the finances to establish this business/ or start in a small way?',
        'viability_notes' => 'Notes',

        'user_id' => 'User Name',
           'business_id' => 'landing page name',
    ];
@endphp

 
<div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Testing Your Ideas Details #{{ $idea->id }}</h1>

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
                @foreach ($idea->getAttributes() as $key => $newValue)
                    @php
                        $displayNew = $idea->formatForDisplay($newValue);
                        $displayOld = $idea->formatForDisplay($oldData[$key] ?? null);
                        $isDifferent = $idea->isDifferent($displayNew, $displayOld);
                    @endphp
        
                    <tr class="transition-colors @if($isDifferent) bg-yellow-50 hover:bg-yellow-100 @else hover:bg-gray-50 @endif cursor-pointer"
                        onclick="showModal('{{ addslashes($key) }}', `{!! addslashes($displayNew) !!}`, `{!! addslashes($displayOld) !!}`)">
                        @php
                        $label = $fieldNames[$key] ?? $key;
                    @endphp
                    <td class="border px-4 py-2 font-medium break-words max-w-xs">
                        {{ $label }}
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
                        <td class="border px-4 py-2 break-words max-w-xs" 
                            style="{{ $isDifferent ? 'background-color:#d1fae5; color:#065f46; font-weight:bold; padding:8px; border-radius:6px;' : '' }}">
                            {!! $displayNew !!}
                        </td>
                        <td class="border px-4 py-2 break-words max-w-xs" 
                            style="{{ $isDifferent ? 'background-color:#fee2e2; color:#991b1b; font-weight:bold; padding:8px; border-radius:6px;' : '' }}">
                            {!! $displayOld !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2 class="text-xl font-semibold mb-4">🕘 Change Log:</h2>
<div class="space-y-4">
    @foreach ($auditLogs as $index => $log)
    <div class="border rounded-lg">
        <a href="#log-{{ $index}}" 
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
                                $displayNew = $idea->formatForDisplay($newVal);
                                $displayOld = $idea->formatForDisplay($log->old_data[$key] ?? null);
                                $isDifferent = $idea->isDifferent($displayNew, $displayOld);
                            @endphp
                            
                            <tr class="hover:bg-gray-50 transition-colors">
                                @php
                                $label = $fieldNames[$key] ?? $key;
                            @endphp
                            <td class="border px-4 py-2 font-medium break-words max-w-xs">
                                {{ $label }}
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
                                <td class="border px-4 py-2 break-words max-w-xs" 
                                    style="{{ $isDifferent ? 'background-color:#d1fae5; color:#065f46; font-weight:bold; padding:8px; border-radius:6px;' : '' }}">
                                    {!! $displayNew !!}
                                </td>
                                <td class="border px-4 py-2 break-words max-w-xs" 
                                    style="{{ $isDifferent ? 'background-color:#fee2e2; color:#991b1b; font-weight:bold; padding:8px; border-radius:6px;' : '' }}">
                                    {!! $displayOld !!}
                                </td>
                            </tr>
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
    
    document.querySelectorAll('.log-details').forEach(item => {
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