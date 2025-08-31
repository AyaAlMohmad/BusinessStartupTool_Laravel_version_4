@extends('layouts.app')
@section('content')
<style>
    /* ستايل موحد كما في الواجهة المعتمدة */
    .card { border-radius: 12px; }
    .table thead th { background: #f8f9fa; }
    .diff-new  { background-color:#d1fae5; color:#065f46; font-weight:600; }
    .diff-old  { background-color:#fee2e2; color:#991b1b; font-weight:600; }
    .badge-key { background:#eef2ff; color:#3730a3; }
    .accordion-button:not(.collapsed){ background:#f8fafc; }
    .w-actions { width: 7.5rem }
    .wrap-cell { word-break: break-word; }
</style>

<div class="row">
  <div class="col-12">
    <div class="card shadow-xs mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-1">Legal Requirements Details <span class="badge badge-key ms-1">#{{ $setup->id }}</span></h5>
          <div class="text-muted small">Review latest changes and full audit history</div>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ url()->previous() }}" class="btn btn-sm btn-white border"><i class="fas fa-arrow-left me-1"></i>Back</a>
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

                  // Map العلاقات لإظهار الأسماء بدلاً من الـ IDs
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

                  $isDifferent = trim((string)$displayNew) !== trim((string)$displayOld);
                @endphp

                <tr class="{{ $isDifferent ? 'table-warning' : '' }}">
                  <td class="wrap-cell"><span class="badge badge-key">{{ ucfirst(str_replace('_', ' ', $key)) }}</span></td>
                  <td class="wrap-cell {{ $isDifferent ? 'diff-new' : '' }}">{{ $displayNew }}</td>
                  <td class="wrap-cell {{ $isDifferent ? 'diff-old' : '' }}">{{ $displayOld }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Tasks related to last update --}}
        @if($setup->tasks && $setup->tasks->count())
          @php
            $relatedTasks = $setup->tasks->filter(function ($task) use ($setup) {
                return \Carbon\Carbon::parse($task->created_at)->format('Y-m-d H:i') === $setup->updated_at->format('Y-m-d H:i');
            });
          @endphp

          @if($relatedTasks->count())
            <h6 class="mt-4 mb-3"><i class="fas fa-tasks me-2"></i>Tasks Related to Last Update</h6>
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-4">
                <thead>
                  <tr>
                    <th>Task</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Notes</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($relatedTasks as $task)
                    <tr>
                      <td class="wrap-cell">{{ $task->title ?? '-' }}</td>
                      <td class="wrap-cell">{{ $task->description ?? '-' }}</td>
                      <td class="wrap-cell">{{ $task->status ?? '-' }}</td>
                      <td class="wrap-cell">{{ $task->deadline ?? '-' }}</td>
                      <td class="wrap-cell">{{ $task->notes ?? '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        @endif

        {{-- Change Log --}}
        <h6 class="mt-4 mb-3"><i class="fas fa-history me-2"></i>Change Log:</h6>

        <div class="accordion" id="auditAccordion">
          @foreach ($auditLogs as $index => $log)
            <div class="accordion-item mb-2">
              <h2 class="accordion-header" id="heading-{{ $index }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $index }}" aria-expanded="false" aria-controls="collapse-{{ $index }}">
                  <div class="w-100 d-flex justify-content-between align-items-center">
                    <span>Edited on {{ $log->created_at->format('Y-m-d H:i') }} by {{ optional($log->user)->name }}</span>
                    <span class="text-muted small"><i class="fas fa-chevron-down"></i></span>
                  </div>
                </button>
              </h2>
              <div id="collapse-{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}" data-bs-parent="#auditAccordion">
                <div class="accordion-body">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-3 audit-table">
                      <thead>
                        <tr>
                          <th>Field</th>
                          <th>New</th>
                          <th>Old</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($log->new_data as $key => $newVal)
                          @php
                            $oldVal = $log->old_data[$key] ?? null;

                            // فك JSON لو كان نص JSON
                            $newVal = is_string($newVal) && json_decode($newVal) ? json_decode($newVal, true) : $newVal;
                            $oldVal = is_string($oldVal) && json_decode($oldVal) ? json_decode($oldVal, true) : $oldVal;

                            $displayNew = is_array($newVal) ? implode(', ', $newVal) : $newVal;
                            $displayOld = is_array($oldVal) ? implode(', ', $oldVal) : $oldVal;

                            // عرض أسماء المستخدم/البيزنس بدل الـ ID
                            $oldIdForRel = $log->old_data[$key] ?? null;
                            if ($key === 'user_id') {
                                $u = \App\Models\User::find($log->new_data[$key] ?? null);
                                $displayNew = optional($u)->name;
                                $uOld = \App\Models\User::find($oldIdForRel);
                                $displayOld = optional($uOld)->name;
                            } elseif ($key === 'business_id') {
                                $b = \App\Models\Business::find($log->new_data[$key] ?? null);
                                $displayNew = optional($b)->name;
                                $bOld = \App\Models\Business::find($oldIdForRel);
                                $displayOld = optional($bOld)->name;
                            }
                          @endphp
                          <tr>
                            <td class="wrap-cell"><span class="badge badge-key">{{ ucfirst(str_replace('_', ' ', $key)) }}</span></td>
                            <td class="wrap-cell diff-new">{{ $displayNew }}</td>
                            <td class="wrap-cell diff-old">{{ $displayOld }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>

                  @if($setup->tasks && $setup->tasks->count())
                    @php
                      $relatedTasks = $setup->tasks->filter(function ($task) use ($log) {
                          return \Carbon\Carbon::parse($task->created_at)->format('Y-m-d H:i') === $log->created_at->format('Y-m-d H:i');
                      });
                    @endphp
                    @if($relatedTasks->count())
                      <h6 class="mt-3 mb-2"><i class="fas fa-tasks me-1"></i>Tasks Related to This Change</h6>
                      <div class="table-responsive">
                        <table class="table table-sm align-middle mb-2">
                          <thead>
                            <tr>
                              <th>Task</th>
                              <th>Description</th>
                              <th>Status</th>
                              <th>Deadline</th>
                              <th>Notes</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($relatedTasks as $task)
                              <tr>
                                <td class="wrap-cell">{{ $task->title ?? '-' }}</td>
                                <td class="wrap-cell">{{ $task->description ?? '-' }}</td>
                                <td class="wrap-cell">{{ $task->status ?? '-' }}</td>
                                <td class="wrap-cell">{{ $task->deadline ?? '-' }}</td>
                                <td class="wrap-cell">{{ $task->notes ?? '-' }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    @endif
                  @endif

                </div>
              </div>
            </div>
          @endforeach
        </div>

      </div>
    </div>
  </div>
</div>

{{-- مكتبات التصدير كما في الواجهة المعتمدة --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
  $(function () {
    // Last Modified → Excel
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
      XLSX.writeFile(wb, 'legal_setup_last_modified.xlsx');
    });

    // Last Modified → PDF
    $('#exportLastPDF').on('click', function(e){
      e.preventDefault();
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p', 'pt');
      doc.text('Legal Requirements — Last Modified', 40, 40);
      doc.autoTable({ html: '#lastModifiedTable', startY: 60, styles: { fontSize: 8 } });
      doc.save('legal_setup_last_modified.pdf');
    });

    // All expanded Logs → PDF
    $('#exportLogsPDF').on('click', function(e){
      e.preventDefault();
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p', 'pt');
      let y = 40;
      doc.text('Legal Requirements — Change Logs (expanded)', 40, y);
      y += 20;

      // افتح جميع الأكورديونات مؤقتًا (لنأخذ الجداول الظاهرة)
      $('.accordion-collapse').addClass('show');
      setTimeout(function(){
        $('.audit-table').each(function(i, tbl){
          if (i > 0) { y += 10; }
          doc.autoTable({ html: tbl, startY: y, styles: { fontSize: 8 } });
          y = doc.lastAutoTable.finalY + 20;
        });
        doc.save('legal_setup_change_logs.pdf');
        // إرجاع الحالة حسب الحاجة (اختياري)
        $('.accordion-collapse').removeClass('show');
      }, 100);
    });
  });
</script>
@endsection
