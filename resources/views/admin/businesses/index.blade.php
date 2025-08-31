@extends('layouts.app')
@section('content')
<style>
    /* نفس تنسيقات المودال والازرار من واجهة Users List */
    .modal-content {
        border-radius: 10px;
        border: 1px solid #e1e1e1;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
    }
    .modal-header {
        background-color: #007bff;
        color: #fff;
        border-bottom: 1px solid #ddd;
        padding: 1.25rem;
        border-radius: 10px 10px 0 0;
    }
    .modal-header h5 { font-size: 1.25rem; font-weight: bold; }
    .modal-body { padding: 1.5rem; }
    .modal-footer { background-color: #f1f1f1; border-top: 1px solid #ddd; padding: 1rem; }
    .btn-icon-only { width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .btn-rounded { border-radius: 50% !important; }
    .btn-outline-success { color: #28a745; border-color: #28a745; }
    .btn-outline-success:hover { background-color: #28a745; color: white; }
    .btn-outline-danger { color: #dc3545; border-color: #dc3545; }
    .btn-outline-danger:hover { background-color: #dc3545; color: white; }
</style>

<div class="row">
    <div class="col-12">
        <div class="card border shadow-xs mb-4">
            <div class="card-header border-bottom pb-0">
                <div class="d-sm-flex align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-semibold text-lg mb-0">Landing Page List</h6>
                        <p class="text-sm mb-sm-0">Manage landing pages for businesses</p>
                    </div>
                    <div class="ms-auto d-flex">
                        <div class="btn-group ms-2">
                            <button type="button" class="btn btn-sm btn-dark dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-export me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="exportExcel">Excel</a></li>
                                <li><a class="dropdown-item" href="#" id="exportPDF">PDF</a></li>
                            </ul>
                        </div>
                        <div class="btn-group ms-2">
                            <a href="{{ route('admin.landing-page.analysis') }}" class="btn btn-sm btn-dark">
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
                                <th class="text-secondary text-xs font-weight-semibold opacity-7">ID</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">User</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Business</th>
                                <th class="text-center text-secondary text-xs font-weight-semibold opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($businesses as $business)
                                <tr>
                                    <td><p class="text-sm mb-0">{{ $business->id }}</p></td>
                                    <td><p class="text-sm mb-0">{{ $business->user->name ?? 'New Business' }}</p></td>
                                    <td><p class="text-sm mb-0">{{ $business->name ?? '' }}</p></td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex justify-content-center">
                                            <!-- View -->
                                            <a href="{{ route('admin.landing-page.show', $business->id) }}"
                                               class="btn btn-sm btn-icon-only btn-rounded btn-outline-success me-2"
                                               data-bs-toggle="tooltip" data-bs-title="View Landing Page">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!-- Delete -->
                                            <form action="{{ route('admin.landing-page.destroy', $business->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-icon-only btn-rounded btn-outline-danger"
                                                    data-bs-toggle="tooltip" data-bs-title="Delete Landing Page"
                                                    onclick="return confirm('Are you sure you want to delete this landing page?')">
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
                {{-- بإمكانك لاحقًا إضافة Pagination مثل واجهة Users --}}
            </div>
        </div>
    </div>
</div>

<!-- Scripts for Export -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    $(document).ready(function() {
        $('#exportExcel').click(function(e) {
            e.preventDefault();
            const data = [], headers = [];
            $('table thead th').each(function() { headers.push($(this).text().trim()); });
            data.push(headers);
            $('table tbody tr').each(function() {
                const rowData = [];
                $(this).find('td').each(function() { rowData.push($(this).text().trim()); });
                data.push(rowData);
            });
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(data);
            XLSX.utils.book_append_sheet(wb, ws, "LandingPages");
            XLSX.writeFile(wb, "landing_pages.xlsx");
        });

        $('#exportPDF').click(function(e) {
            e.preventDefault();
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const headers = [];
            $('table thead th').each(function() { headers.push($(this).text().trim()); });
            const data = [];
            $('table tbody tr').each(function() {
                const rowData = [];
                $(this).find('td').each(function() { rowData.push($(this).text().trim()); });
                data.push(rowData);
            });
            doc.autoTable({ head: [headers], body: data, styles: { fontSize: 8 }, margin: { top: 20 } });
            doc.save('landing_pages.pdf');
        });

        // tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });
    });
</script>
@endsection
