@extends('layouts.app')
@section('content')
@php
    $fieldNames = [
        'id' => 'ID',
        'user_id' => 'User',
        'title' => 'Title',
        'educational' => 'Educational Content',
        'my_story' => 'My Story',
        'country' => 'Country',
        'aim' => 'Aim',
        'game' => 'Game',
        'who_am_i' => 'Who Am I',
        'image' => 'Image',
        'link' => 'Link'
    ];
    $fieldNamesReversed = array_flip($fieldNames);
@endphp

<style>
  .card{border-radius:12px}
  .table thead th{background:#f8f9fa}
  .wrap-cell{word-break:break-word}
  .diff-new{background:#d1fae5;color:#065f46;font-weight:600;border-radius:6px;padding:6px}
  .diff-old{background:#fee2e2;color:#991b1b;font-weight:600;border-radius:6px;padding:6px}
  .badge-key{background:#eef2ff;color:#3730a3}
</style>

<div class="row py-4">
  <div class="col-12 col-xxl-10 mx-auto">
    <div class="card shadow-xs">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-1">
            Story Details
            <span class="badge badge-key">#{{ $story->id }}</span>
          </h5>
          <div class="text-muted small">Review latest changes and full audit history</div>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-white border">
            <i class="fas fa-arrow-left me-1"></i> Back
          </a>
          <div class="btn-group">
            <button class="btn btn-sm btn-dark dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fas fa-file-export me-1"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="#" id="exportLastExcel">Last Modified → Excel</a></li>
              <li><a class="dropdown-item" href="#" id="exportLastPDF">Last Modified → PDF</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#" id="exportLogsPDF">All Logs (expanded) → PDF</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="card-body">

        {{-- Last Modified --}}
        <h6 class="mb-3"><i class="fas fa-clock me-2"></i>Last Modified:</h6>
        <div class="table-responsive">
          <table id="lastModifiedTable" class="table table-sm align-middle mb-4">
            <thead>
              <tr>
                <th>Field</th>
                <th>New Value</th>
                <th>Old Value</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($story->getAttributes() as $key => $newValue)
                @php
                    // normalize new
                    $processedNew = is_string($newValue) ? json_decode($newValue, true) ?? $newValue : $newValue;
                    $displayNew = is_array($processedNew)
                        ? implode(', ', array_map(fn($v)=>html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $processedNew))
                        : html_entity_decode($processedNew, ENT_QUOTES, 'UTF-8');

                    // normalize old
                    $oldValue = $oldData[$key] ?? null;
                    $processedOld = is_string($oldValue) ? json_decode($oldValue, true) ?? $oldValue : $oldValue;
                    $displayOld = is_array($processedOld)
                        ? implode(', ', array_map(fn($v)=>html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $processedOld))
                        : html_entity_decode($processedOld ?? '', ENT_QUOTES, 'UTF-8');

                    // relations (user)
                    $oldValId = $oldData[$key] ?? null;
                    if ($key === 'user_id') {
                        $u = \App\Models\User::find($newValue);
                        $displayNew = optional($u)->name ?? $displayNew;
                        $uOld = \App\Models\User::find($oldValId);
                        $displayOld = optional($uOld)->name ?? $displayOld;
                    }

                    // friendly rendering for media/links
                    if ($key === 'image' && $displayNew)  $displayNew = '<img src="'.e($displayNew).'" alt="image" style="max-height:60px;border-radius:6px">';
                    if ($key === 'image' && $displayOld)  $displayOld = '<img src="'.e($displayOld).'" alt="image" style="max-height:60px;border-radius:6px">';
                    if ($key === 'link'  && $displayNew)  $displayNew = '<a href="'.e($displayNew).'" target="_blank" rel="noopener">'.e($displayNew).'</a>';
                    if ($key === 'link'  && $displayOld)  $displayOld = '<a href="'.e($displayOld).'" target="_blank" rel="noopener">'.e($displayOld).'</a>';

                    $isDifferent = trim(strip_tags((string)$displayNew)) !== trim(strip_tags((string)$displayOld));
                    $label = $fieldNames[$key] ?? $key;
                @endphp

                <tr class="{{ $isDifferent ? 'table-warning' : '' }}">
                  <td class="wrap-cell"><span class="badge badge-key">{{ $label }}</span></td>
                  <td class="wrap-cell {!! $isDifferent ? 'diff-new' : '' !!}">{!! $displayNew !!}</td>
                  <td class="wrap-cell {!! $isDifferent ? 'diff-old' : '' !!}">{!! $displayOld !!}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Change Log --}}
        <h6 class="mt-4 mb-3"><i class="fas fa-history me-2"></i>Change Log:</h6>
        <div class="accordion" id="auditAccordion">
          @foreach ($auditLogs as $index => $log)
            <div class="accordion-item mb-2">
              <h2 class="accordion-header" id="heading-{{ $index }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $index }}" aria-expanded="false"
                        aria-controls="collapse-{{ $index }}">
                  <div class="w-100 d-flex justify-content-between align-items-center">
                    <span>Edited on {{ $log->created_at->format('Y-m-d H:i') }} by {{ optional($log->user)->name }}</span>
                    <span class="text-muted small"><i class="fas fa-chevron-down"></i></span>
                  </div>
                </button>
              </h2>
              <div id="collapse-{{ $index }}" class="accordion-collapse collapse"
                   aria-labelledby="heading-{{ $index }}" data-bs-parent="#auditAccordion">
                <div class="accordion-body">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-3 audit-table">
                      <thead>
                        <tr>
                          <th>Field</th>
                          <th>New Value</th>
                          <th>Old Value</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($log->new_data as $key => $newVal)
                          @php
                              $oldVal = $log->old_data[$key] ?? null;
                              // decode-like
                              $newVal = (is_string($newVal) && json_decode($newVal)) ? json_decode($newVal, true) : $newVal;
                              $displayNew = is_array($newVal)
                                  ? implode(', ', array_map(fn($v)=>html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $newVal))
                                  : html_entity_decode($newVal, ENT_QUOTES, 'UTF-8');
                              $displayOld = is_array($oldVal)
                                  ? implode(', ', array_map(fn($v)=>html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $oldVal))
                                  : html_entity_decode($oldVal ?? '', ENT_QUOTES, 'UTF-8');

                              // relations (user)
                              if ($key === 'user_id') {
                                  $u = \App\Models\User::find($log->new_data[$key] ?? null);
                                  $displayNew = optional($u)->name ?? $displayNew;
                                  $uOld = \App\Models\User::find($log->old_data[$key] ?? null);
                                  $displayOld = optional($uOld)->name ?? $displayOld;
                              }

                              // media/links in logs too
                              if ($key === 'image' && $displayNew)  $displayNew = '<img src="'.e($displayNew).'" alt="image" style="max-height:60px;border-radius:6px">';
                              if ($key === 'image' && $displayOld)  $displayOld = '<img src="'.e($displayOld).'" alt="image" style="max-height:60px;border-radius:6px">';
                              if ($key === 'link'  && $displayNew)  $displayNew = '<a href="'.e($displayNew).'" target="_blank" rel="noopener">'.e($displayNew).'</a>';
                              if ($key === 'link'  && $displayOld)  $displayOld = '<a href="'.e($displayOld).'" target="_blank" rel="noopener">'.e($displayOld).'</a>';

                              $label = $fieldNames[$key] ?? $key;
                          @endphp
                          @if (trim(strip_tags($displayNew)) != trim(strip_tags($displayOld)))
                          <tr>
                            <td class="wrap-cell"><span class="badge badge-key">{{ $label }}</span></td>
                            <td class="wrap-cell diff-new">{!! $displayNew !!}</td>
                            <td class="wrap-cell diff-old">{!! $displayOld !!}</td>
                          </tr>
                          @endif
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Export libs --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
  $(function () {
    // Excel
    $('#exportLastExcel').on('click', function(e){
      e.preventDefault();
      const headers = [];
      $('#lastModifiedTable thead th').each(function(){ headers.push($(this).text().trim()); });
      const rows = [];
      $('#lastModifiedTable tbody tr').each(function(){
        const cols = [];
        $(this).find('td').each(function(){ cols.push($(this).text().trim()); });
        rows.push(cols);
      });
      const data = [headers, ...rows];
      const wb = XLSX.utils.book_new();
      const ws = XLSX.utils.aoa_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'LastModified');
      XLSX.writeFile(wb, 'story_last_modified.xlsx');
    });

    // PDF (last modified)
    $('#exportLastPDF').on('click', function(e){
      e.preventDefault();
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p','pt');
      doc.text('Story — Last Modified', 40, 40);
      doc.autoTable({ html:'#lastModifiedTable', startY:60, styles:{fontSize:8} });
      doc.save('story_last_modified.pdf');
    });

    // PDF (logs expanded)
    $('#exportLogsPDF').on('click', function(e){
      e.preventDefault();
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p','pt');
      let y = 40;
      doc.text('Story — Change Logs (expanded)', 40, y);
      y += 20;

      // افتح كل الأكورديونات مؤقتًا
      $('.accordion-collapse').addClass('show');
      setTimeout(function(){
        $('.audit-table').each(function(i, tbl){
          if (i > 0) { y += 10; }
          doc.autoTable({ html: tbl, startY: y, styles:{fontSize:8} });
          y = doc.lastAutoTable.finalY + 20;
        });
        doc.save('story_change_logs.pdf');
        $('.accordion-collapse').removeClass('show');
      }, 120);
    });
  });

  // (احتياطي) لو احتجت نفس سلوك التوجّل خارج الأكورديون
  function toggleLog(event, index) {
      event.preventDefault();
      const details = document.getElementById(`log-${index}`);
      const arrow = document.getElementById(`arrow-${index}`);
      document.querySelectorAll('.log-details').forEach(item => {
          if (item.id !== `log-${index}`) {
              item.classList.add('hidden');
              const otherArrow = item.previousElementSibling?.querySelector(`#arrow-${item.id.split('-')[1]}`);
              if (otherArrow) otherArrow.innerHTML = '▼';
          }
      });
      details.classList.toggle('hidden');
      arrow.innerHTML = details.classList.contains('hidden') ? '▼' : '▲';
  }
</script>

{{-- Bootstrap JS (لو غير محمّل ضمن layout) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
