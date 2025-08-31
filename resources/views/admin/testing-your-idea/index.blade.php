@extends('layouts.app')
@section('content')
<style>
    /* من الواجهة المعتمدة */
    .modal-content{border-radius:10px;border:1px solid #e1e1e1;box-shadow:0 4px 10px rgba(0,0,0,.1);background:#f9f9f9}
    .modal-header{background:#007bff;color:#fff;border-bottom:1px solid #ddd;padding:1.25rem;border-radius:10px 10px 0 0}
    .modal-body{padding:1.5rem}
    .modal-footer{background:#f1f1f1;border-top:1px solid #ddd;padding:1rem}

    .btn-icon-only{width:2rem;height:2rem;display:inline-flex;align-items:center;justify-content:center;padding:0}
    .btn-rounded{border-radius:50% !important}

    .btn-outline-success{color:#28a745;border-color:#28a745}
    .btn-outline-success:hover{background:#28a745;color:#fff}
    .btn-outline-danger{color:#dc3545;border-color:#dc3545}
    .btn-outline-danger:hover{background:#dc3545;color:#fff}
    .btn-outline-primary{color:#007bff;border-color:#007bff}
    .btn-outline-primary:hover{background:#007bff;color:#fff}

    .table td p{margin-bottom:0}
    .cell-scroll{max-height:220px;overflow:auto}
    .w-actions{width:7.5rem}
</style>

<div class="row">
    <div class="col-12">
        <div class="card border shadow-xs mb-4">
            <div class="card-header border-bottom pb-0">
                <div class="d-sm-flex align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-semibold text-lg mb-0">Testing Your Ideas List</h6>
                        <p class="text-sm mb-sm-0">Manage desirability, feasibility, and viability checks</p>
                    </div>

                    <div class="ms-auto d-flex">
                        <div class="btn-group ms-2">
                            <button type="button" class="btn btn-sm btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="exportExcel">Excel</a></li>
                                <li><a class="dropdown-item" href="#" id="exportPDF">PDF</a></li>
                            </ul>
                        </div>

                        <div class="btn-group ms-2">
                            <a href="{{ route('admin.testing-your-idea.analysis') }}" class="btn btn-sm btn-dark">
                                <i class="fas fa-chart-bar me-1"></i> View Analysis
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 py-0">
                <div class="table-responsive p-0">
                    <table class="table align-items-center justify-content-center mb-0">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7">#</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Business</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">User</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Desirability</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Feasibility</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Viability</th>
                                <th class="text-center text-secondary text-xs font-weight-semibold opacity-7 w-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ideas as $idea)
                                <tr>
                                    <td><p class="text-sm mb-0">{{ $idea->id }}</p></td>
                                    <td><p class="text-sm mb-0">{{ $idea->business->name ?? '-' }}</p></td>
                                    <td><p class="text-sm mb-0">{{ $idea->user->name ?? '-' }}</p></td>

                                    {{-- Desirability --}}
                                    <td>
                                        <div class="cell-scroll">
                                            @include('partials.display-data', [
                                                'data' => $idea->solves_problem,
                                                'title' => 'Solves Problem',
                                                'type' => 'boolean'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->problem_statement,
                                                'title' => 'Problem Statement'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->existing_solutions_used,
                                                'title' => 'Existing Solutions Used',
                                                'type' => 'boolean'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->current_solutions_details,
                                                'title' => 'Current Solutions'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->switch_reason,
                                                'title' => 'Switch Reasons'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->desirability_notes,
                                                'title' => 'Notes'
                                            ])
                                        </div>
                                    </td>

                                    {{-- Feasibility --}}
                                    <td>
                                        <div class="cell-scroll">
                                            @include('partials.display-data', [
                                                'data' => $idea->required_skills,
                                                'title' => 'Required Skills'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->qualifications_permits,
                                                'title' => 'Qualifications'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->feasibility_notes,
                                                'title' => 'Notes'
                                            ])
                                        </div>
                                    </td>

                                    {{-- Viability --}}
                                    <td>
                                        <div class="cell-scroll">
                                            @include('partials.display-data', [
                                                'data' => $idea->payment_possible,
                                                'title' => 'Payment Possible'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->profitability,
                                                'title' => 'Profitability'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->finances_details,
                                                'title' => 'Finances Details'
                                            ])
                                            @include('partials.display-data', [
                                                'data' => $idea->viability_notes,
                                                'title' => 'Notes'
                                            ])
                                        </div>
                                    </td>

                                    <td class="align-middle text-center">
                                        <div class="d-flex justify-content-center">
                                            <!-- View -->
                                            <a href="{{ route('admin.testing-your-idea.show', $idea->id) }}"
                                               class="btn btn-sm btn-icon-only btn-rounded btn-outline-success me-2"
                                               data-bs-toggle="tooltip" data-bs-title="View Record">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Delete -->
                                            <form action="{{ route('admin.testing-your-idea.destroy', $idea->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('Are you sure you want to delete this record?')"
                                                        class="btn btn-sm btn-icon-only btn-rounded btn-outline-danger"
                                                        data-bs-toggle="tooltip" data-bs-title="Delete Record">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- (اختياري) Pagination إذا كانت المجموعة مقسمة صفحات --}}
                {{--
                @if($ideas->hasPages())
                <div class="border-top py-3 px-3 d-flex align-items-center">
                    @if ($ideas->previousPageUrl())
                        <a href="{{ $ideas->previousPageUrl() }}" class="btn btn-sm btn-white d-sm-block d-none mb-0">Previous</a>
                    @else
                        <button class="btn btn-sm btn-white d-sm-block d-none mb-0" disabled>Previous</button>
                    @endif

                    <nav aria-label="..." class="ms-auto">
                        <ul class="pagination pagination-light mb-0">
                            @foreach ($ideas->getUrlRange(1, $ideas->lastPage()) as $page => $url)
                                <li class="page-item {{ $ideas->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link border-0 font-weight-bold" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                    @if ($ideas->nextPageUrl())
                        <a href="{{ $ideas->nextPageUrl() }}" class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto">Next</a>
                    @else
                        <button class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto" disabled>Next</button>
                    @endif
                </div>
                @endif
                --}}
            </div>
        </div>
    </div>
</div>

<!-- مكتبات التصدير كما في الواجهة المعتمدة -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    $(function () {
        // Export to Excel
        $('#exportExcel').on('click', function (e) {
            e.preventDefault();
            const data = [], headers = [];
            $('table thead th').each(function(){ headers.push($(this).text().trim()); });
            data.push(headers);

            $('table tbody tr').each(function(){
                const rowData = [];
                $(this).find('td').each(function(){ rowData.push($(this).text().trim()); });
                data.push(rowData);
            });

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, 'TestingIdeas');
            XLSX.writeFile(wb, 'testing_your_ideas.xlsx');
        });

        // Export to PDF
        $('#exportPDF').on('click', function (e) {
            e.preventDefault();
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const headers = [], body = [];
            $('table thead th').each(function(){ headers.push($(this).text().trim()); });
            $('table tbody tr').each(function(){
                const rowData = [];
                $(this).find('td').each(function(){ rowData.push($(this).text().trim()); });
                body.push(rowData);
            });

            doc.autoTable({ head: [headers], body, styles: { fontSize: 8 }, margin: { top: 20 } });
            doc.save('testing_your_ideas.pdf');
        });

        // Tooltips
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
          .map(function (el) { return new bootstrap.Tooltip(el); });
    });
</script>
@endsection
