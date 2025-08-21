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
                    <table class="table align-items-center justify-content-center mb-0">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7">Region Name</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Created At</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Updated At</th>
                                <th class="text-center text-secondary text-xs font-weight-semibold opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regions as $region)
                            <tr>
                                <td>
                                    <div class="d-flex px-2">
                                        <div class="my-auto">
                                            <h6 class="mb-0 text-sm">{{ $region->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-normal mb-0">{{ $region->created_at->format('d M Y')??'N/A' }}</p>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-normal mb-0">{{ $region->updated_at->format('d M Y')??'N/A' }}</p>
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('admin.regions.edit', $region->id) }}" class="text-secondary font-weight-bold text-xs me-2" data-bs-toggle="tooltip" data-bs-title="Edit region">
                                        <svg width="14" height="14" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.2201 2.02495C10.8292 1.63482 10.196 1.63545 9.80585 2.02636C9.41572 2.41727 9.41635 3.05044 9.80726 3.44057L11.2201 2.02495ZM12.5572 6.18502C12.9481 6.57516 13.5813 6.57453 13.9714 6.18362C14.3615 5.79271 14.3609 5.15954 13.97 4.7694L12.5572 6.18502ZM11.6803 1.56839L12.3867 2.2762L12.3867 2.27619L11.6803 1.56839ZM14.4302 4.31284L15.1367 5.02065L15.1367 5.02064L14.4302 4.31284ZM3.72198 15V16C3.98686 16 4.24091 15.8949 4.42839 15.7078L3.72198 15ZM0.999756 15H-0.000244141C-0.000244141 15.5523 0.447471 16 0.999756 16L0.999756 15ZM0.999756 12.2279L0.293346 11.5201C0.105383 11.7077 -0.000244141 11.9624 -0.000244141 12.2279H0.999756ZM9.80726 3.44057L12.5572 6.18502L13.97 4.7694L11.2201 2.02495L9.80726 3.44057ZM12.3867 2.27619C12.7557 1.90794 13.3549 1.90794 13.7238 2.27619L15.1367 0.860593C13.9869 -0.286864 12.1236 -0.286864 10.9739 0.860593L12.3867 2.27619ZM13.7238 2.27619C14.0917 2.64337 14.0917 3.23787 13.7238 3.60504L15.1367 5.02064C16.2875 3.8721 16.2875 2.00913 15.1367 0.860593L13.7238 2.27619ZM13.7238 3.60504L3.01557 14.2922L4.42839 15.7078L15.1367 5.02065L13.7238 3.60504ZM3.72198 14H0.999756V16H3.72198V14ZM1.99976 15V12.2279H-0.000244141V15H1.99976ZM1.70617 12.9357L12.3867 2.2762L10.9739 0.86059L0.293346 11.5201L1.70617 12.9357Z" fill="#64748B" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.regions.destroy', $region->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger font-weight-bold text-xs border-0 bg-transparent" data-bs-toggle="tooltip" data-bs-title="Delete region" onclick="return confirm('Are you sure you want to delete this region?')">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.33398 3.99992V2.66659C5.33398 2.31296 5.47446 1.97382 5.72451 1.72378C5.97455 1.47373 6.31369 1.33325 6.66732 1.33325H9.33398C9.68761 1.33325 10.0268 1.47373 10.2768 1.72378C10.5268 1.97382 10.6673 2.31296 10.6673 2.66659V3.99992M12.6673 3.99992H3.33398L4.00065 13.3333C4.00065 13.8637 4.21136 14.3724 4.58644 14.7475C4.96151 15.1225 5.47022 15.3333 6.00065 15.3333H10.0006C10.5311 15.3333 11.0398 15.1225 11.4149 14.7475C11.7899 14.3724 12.0006 13.8637 12.0006 13.3333L12.6673 3.99992Z" stroke="#EF4444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
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
