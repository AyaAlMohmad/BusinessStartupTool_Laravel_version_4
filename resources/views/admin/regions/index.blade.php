@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border shadow-xs mb-4">
            <div class="card-header border-bottom pb-0">
                <div class="d-sm-flex align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-semibold text-lg mb-0">Regions List</h6>
                        <p class="text-sm mb-sm-0">Manage your regions</p>
                    </div>
                    <div class="ms-auto d-flex">
                        <div class="ms-auto d-flex">

                            {{-- <div class="input-group input-group-sm ms-auto me-2">
                                <span class="input-group-text text-body">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                                    </svg>
                                </span>
                                <input type="text" class="form-control form-control-sm" placeholder="Search regions" id="searchInput">
                            </div> --}}


                            <div class="btn-group ms-2">
                                <button type="button" class="btn btn-sm btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="me-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" id="exportExcel">Excel</a></li>
                                    <li><a class="dropdown-item" href="#" id="exportPDF">PDF</a></li>
                                </ul>
                            </div>
    <div class="btn-group ms-2">
                            <a href="{{ route('admin.regions.create') }}"      class="btn btn-sm btn-dark ">
                                <span class="btn-inner--icon">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="me-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </span>
                                <span class="btn-inner--text">Add Region</span>
                            </a>
                            </div>
                        </div>

                </div>
            </div>
            <div class="card-body px-0 py-0">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder">Region Name</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder ps-2">Created At</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder ps-2">Updated At</th>
                                <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regions as $region)
                            <tr>
                                <td>
                                    <div class="d-flex px-2">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $region->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-normal mb-0">
                                        {{ $region->created_at ? $region->created_at->format('d M Y') : 'N/A' }}
                                    </p>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-normal mb-0">
                                        {{ $region->updated_at ? $region->updated_at->format('d M Y') : 'N/A' }}
                                    </p>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('admin.regions.edit', $region->id) }}"
                                       class="action-btn text-primary me-2"
                                       data-bs-toggle="tooltip" data-bs-title="Edit region">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.regions.destroy', $region->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="action-btn text-danger border-0 bg-transparent"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="Delete region"
                                                onclick="return confirm('Are you sure you want to delete this region?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-top py-3 px-3 d-flex align-items-center">
                    @if($regions->previousPageUrl())
                        <a href="{{ $regions->previousPageUrl() }}" class="btn btn-sm btn-white d-sm-block d-none mb-0">Previous</a>
                    @else
                        <button class="btn btn-sm btn-white d-sm-block d-none mb-0" disabled>Previous</button>
                    @endif

                    <nav aria-label="..." class="ms-auto">
                        <ul class="pagination pagination-light mb-0">
                            @foreach ($regions->getUrlRange(1, $regions->lastPage()) as $page => $url)
                                <li class="page-item {{ $regions->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link border-0 font-weight-bold" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                    @if($regions->nextPageUrl())
                        <a href="{{ $regions->nextPageUrl() }}" class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto">Next</a>
                    @else
                        <button class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto" disabled>Next</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
$(document).ready(function() {

    let searchTimer;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            performSearch($('#searchInput').val());
        }, 500);
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            performSearch($(this).val());
        }
    });


    function performSearch(query) {
        $.ajax({
            url: "{{ route('admin.regions.index') }}",
            type: "GET",
            data: { search: query },
            success: function(data) {
                const newTable = $(data).find('table').html();
                const newPagination = $(data).find('.pagination').html();

                $('table tbody').html($(newTable).find('tbody').html());
                $('.pagination').html(newPagination);
            }
        });
    }

    $('#exportExcel').click(function(e) {
        e.preventDefault();
        exportToExcel();
    });


    $('#exportPDF').click(function(e) {
        e.preventDefault();
        exportToPDF();
    });


    function exportToExcel() {

        const data = [];
        const headers = [];

        $('table thead th').each(function() {
            headers.push($(this).text().trim());
        });
        data.push(headers);


        $('table tbody tr').each(function() {
            const rowData = [];
            $(this).find('td').each(function() {
                rowData.push($(this).text().trim());
            });
            data.push(rowData);
        });


        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Regions");


        XLSX.writeFile(wb, "regions_export.xlsx");
    }
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();


        const headers = [];
        $('table thead th').each(function() {
            headers.push($(this).text().trim());
        });

        const data = [];
        $('table tbody tr').each(function() {
            const rowData = [];
            $(this).find('td').each(function() {
                rowData.push($(this).text().trim());
            });
            data.push(rowData);
        });


        doc.autoTable({
            head: [headers],
            body: data,
            styles: { fontSize: 8 },
            margin: { top: 20 }
        });
        doc.save('regions_export.pdf');
    }


    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const searchQuery = $('#searchInput').val();

        $.ajax({
            url: url + (searchQuery ? '&search=' + encodeURIComponent(searchQuery) : ''),
            success: function(data) {
                const newTable = $(data).find('table').html();
                const newPagination = $(data).find('.pagination').html();

                $('table tbody').html($(newTable).find('tbody').html());
                $('.pagination').html(newPagination);
            }
        });
    });
});
</script>

@endsection
