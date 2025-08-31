@extends('layouts.app')
@section('content')
@php
    $fieldNames = [
        'id' => 'ID',
        'name' => 'Plan Name',
        'is_migrant' => 'Are you a migrant or a refugee?',
        'years_here' => 'How long you have been here?',
        'english_level'=>'English Level',
        'is_business_old'=>'Have you owned a business before?',
        'products_services'=>'Products or Services',
        'about_me'=>'About Me',
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
            Business Plan Details
            <span class="badge badge-key">#{{ $business->id }}</span>
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
              @foreach ($business->getAttributes() as $key => $newValue)
                @php
                    // new value
                    $processedNew = is_string($newValue) ? json_decode($newValue, true) ?? $newValue : $newValue;
                    $displayNew = is_array($processedNew)
                        ? implode(', ', array_map(fn($v)=>html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $processedNew))
                        : html_entity_decode($processedNew, ENT_QUOTES, 'UTF-8');
                    // old value
                    $oldValue = $oldData[$key] ?? null;
                    $processedOld = is_string($oldValue) ? json_decode($oldValue, true) ?? $oldValue : $oldValue;
                    $displayOld = is_array($processedOld)
                        ? implode(', ', array_map(fn($v)=>html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $processedOld))
                        : html_entity_decode($processedOld ?? '', ENT_QUOTES, 'UTF-8');

                    $isDifferent = trim((string)$displayNew) !== trim((string)$displayOld);
                    $label = $fieldNames[$key] ?? $key;
                @endphp
                <tr class="{{ $isDifferent ? 'table-warning' : '' }}">
                  <td class="wrap-cell"><span class="badge badge-key">{{ $label }}</span></td>
                  <td class="wrap-cell {{ $isDifferent ? 'diff-new' : '' }}">{{ $displayNew }}</td>
                  <td class="wrap-cell {{ $isDifferent ? 'diff-old' : '' }}">{{ $displayOld }}</td>
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

                              // decode JSON-like
                              $newVal = (is_string($newVal) && json_decode($newVal)) ? json_decode($newVal, true) : $newVal;
                              $displayNew = is_array($newVal)
                                  ? implode(', ', array_map(fn($v)=>html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $newVal))
                                  : html_entity_decode($newVal, ENT_QUOTES, 'UTF-8');

                              $displayOld = is_array($oldVal)
                                  ? implode(', ', array_map(fn($v)=>html_entity_decode($v, ENT_QUOTES, 'UTF-8'), $oldVal))
                                  : html_entity_decode($oldVal ?? '', ENT_QUOTES, 'UTF-8');

                              // ترميم تسمية الحقل حسب خريطتك
                              $translatedKey = $fieldNamesReversed[$key] ?? $key;
                              $label = $fieldNames[$translatedKey] ?? $translatedKey;
                          @endphp

                          <tr>
                            <td class="wrap-cell"><span class="badge badge-key">{{ $label }}</span></td>
                            <td class="wrap-cell diff-new">{{ $displayNew }}</td>
                            <td class="wrap-cell diff-old">{{ $displayOld }}</td>
                          </tr>
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

{{-- مكتبات التصدير --}}
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
      XLSX.writeFile(wb, 'business_plan_last_modified.xlsx');
    });

    // Last Modified → PDF
    $('#exportLastPDF').on('click', function(e){
      e.preventDefault();
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p','pt');
      doc.text('Business Plan — Last Modified', 40, 40);
      doc.autoTable({ html:'#lastModifiedTable', startY:60, styles:{fontSize:8} });
      doc.save('business_plan_last_modified.pdf');
    });

    // All expanded Logs → PDF
    $('#exportLogsPDF').on('click', function(e){
      e.preventDefault();
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p','pt');
      let y = 40;
      doc.text('Business Plan — Change Logs (expanded)', 40, y);
      y += 20;

      // افتح كل الأكورديونات مؤقتًا لالتقاط الجداول الظاهرة
      $('.accordion-collapse').addClass('show');
      setTimeout(function(){
        $('.audit-table').each(function(i, tbl){
          if (i > 0) { y += 10; }
          doc.autoTable({ html: tbl, startY: y, styles:{fontSize:8} });
          y = doc.lastAutoTable.finalY + 20;
        });
        doc.save('business_plan_change_logs.pdf');
        $('.accordion-collapse').removeClass('show');
      }, 120);
    });
  });
</script>

{{-- Bootstrap JS (لو غير محمّل ضمن layout) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
