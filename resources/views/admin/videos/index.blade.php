@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border shadow-xs mb-4">
            <div class="card-header border-bottom pb-0">
                <div class="d-sm-flex align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-semibold text-lg mb-0">Videos List</h6>
                        <p class="text-sm mb-sm-0">Manage system videos</p>
                    </div>
                    <div class="ms-auto d-flex">
                        <div class="btn-group ms-2">
                            <button type="button" class="btn btn-sm btn-dark dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="me-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="exportExcel">Excel</a></li>
                                <li><a class="dropdown-item" href="#" id="exportPDF">PDF</a></li>
                            </ul>
                        </div>
                        <div class="btn-group ms-2">
                            <a href="{{ route('admin.videos.create') }}"
                            class="btn btn-sm btn-dark">
                                <span class="btn-inner--icon">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="me-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </span>
                                <span class="btn-inner--text">Add Video</span>
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
                                <th class="text-secondary text-xs font-weight-semibold opacity-7">Video</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Title</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Description</th>
                                {{-- <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Status</th> --}}
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Uploaded</th>
                                <th class="text-center text-secondary text-xs font-weight-semibold opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($videos as $video)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2">
                                            <div class="video-thumbnail me-2">
                                                <video class="rounded" width="80" height="60" poster="{{ $video->thumbnail_url ?? '' }}">
                                                    <source src="{{ asset($video->video_path) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="play-overlay">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-normal mb-0">{{ $video->title }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-normal mb-0">{{ Str::limit($video->description, 50) }}</p>
                                    </td>
                                    {{-- <td>
                                        <span class="badge bg-{{ $video->status === 'active' ? 'success' : 'danger' }}" style="color:black">
                                            {{ ucfirst($video->status) }}
                                        </span>
                                    </td> --}}
                                    <td>
                                        <p class="text-sm font-weight-normal mb-0">{{ $video->created_at->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.videos.show', $video->id) }}"
                                            class="text-secondary font-weight-bold text-xs me-2"
                                            data-bs-toggle="tooltip" data-bs-title="View video">
                                            <svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z" fill="#64748B"/>
                                                <path d="M0 10C0 10 4.375 3.125 10 3.125C15.625 3.125 20 10 20 10C20 10 15.625 16.875 10 16.875C4.375 16.875 0 10 0 10ZM10 14.375C11.0355 14.375 12.0312 13.9598 12.773 13.218C13.5148 12.4762 13.93 11.4805 13.93 10.445C13.93 9.40951 13.5148 8.41377 12.773 7.67199C12.0312 6.93021 11.0355 6.515 10 6.515C8.96451 6.515 7.96877 6.93021 7.22699 7.67199C6.48521 8.41377 6.07 9.40951 6.07 10.445C6.07 11.4805 6.48521 12.4762 7.22699 13.218C7.96877 13.9598 8.96451 14.375 10 14.375Z" fill="#64748B"/>
                                            </svg>
                                        </a>
                                        <a href="#editVideoModal-{{ $video->id }}" data-bs-toggle="modal"
                                            class="text-secondary font-weight-bold text-xs me-2"
                                            data-bs-toggle="tooltip" data-bs-title="Edit video">
                                            <svg width="14" height="14" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.2201 2.02495C10.8292 1.63482 10.196 1.63545 9.80585 2.02636C9.41572 2.41727 9.41635 3.05044 9.80726 3.44057L11.2201 2.02495ZM12.5572 6.18502C12.9481 6.57516 13.5813 6.57453 13.9714 6.18362C14.3615 5.79271 14.3609 5.15954 13.97 4.7694L12.5572 6.18502ZM11.6803 1.56839L12.3867 2.2762L12.3867 2.27619L11.6803 1.56839ZM14.4302 4.31284L15.1367 5.02065L15.1367 5.02064L14.4302 4.31284ZM3.72198 15V16C3.98686 16 4.24091 15.8949 4.42839 15.7078L3.72198 15ZM0.999756 15H-0.000244141C-0.000244141 15.5523 0.447471 16 0.999756 16L0.999756 15ZM0.999756 12.2279L0.293346 11.5201C0.105383 11.7077 -0.000244141 11.9624 -0.000244141 12.2279H0.999756ZM9.80726 3.44057L12.5572 6.18502L13.97 4.7694L11.2201 2.02495L9.80726 3.44057ZM12.3867 2.27619C12.7557 1.90794 13.3549 1.90794 13.7238 2.27619L15.1367 0.860593C13.9869 -0.286864 12.1236 -0.286864 10.9739 0.860593L12.3867 2.27619ZM13.7238 2.27619C14.0917 2.64337 14.0917 3.23787 13.7238 3.60504L15.1367 5.02064C16.2875 3.8721 16.2875 2.00913 15.1367 0.860593L13.7238 2.27619ZM13.7238 3.60504L3.01557 14.2922L4.42839 15.7078L15.1367 5.02065L13.7238 3.60504ZM3.72198 14H0.999756V16H3.72198V14ZM1.99976 15V12.2279H-0.000244141V15H1.99976ZM1.70617 12.9357L12.3867 2.2762L10.9739 0.86059L0.293346 11.5201L1.70617 12.9357Z" fill="#64748B"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.videos.destroy', $video->id) }}"
                                            method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-danger font-weight-bold text-xs border-0 bg-transparent"
                                                data-bs-toggle="tooltip" data-bs-title="Delete video"
                                                onclick="return confirm('Are you sure you want to delete this video?')">
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
                    @if ($videos->previousPageUrl())
                        <a href="{{ $videos->previousPageUrl() }}"
                            class="btn btn-sm btn-white d-sm-block d-none mb-0">Previous</a>
                    @else
                        <button class="btn btn-sm btn-white d-sm-block d-none mb-0" disabled>Previous</button>
                    @endif

                    <nav aria-label="..." class="ms-auto">
                        <ul class="pagination pagination-light mb-0">
                            @foreach ($videos->getUrlRange(1, $videos->lastPage()) as $page => $url)
                                <li class="page-item {{ $videos->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link border-0 font-weight-bold"
                                        href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                    @if ($videos->nextPageUrl())
                        <a href="{{ $videos->nextPageUrl() }}"
                            class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto">Next</a>
                    @else
                        <button class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto" disabled>Next</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Video Modal -->
@foreach ($videos as $video)
<div class="modal fade" id="editVideoModal-{{ $video->id }}" tabindex="-1" aria-labelledby="editVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="editVideoModalLabel">
                        <i class="fas fa-video me-2"></i>Edit Video: {{ $video->title }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column - Video Info -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Video Information</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Title -->
                                    <div class="mb-3">
                                        <label for="title-{{ $video->id }}" class="form-label fw-bold">Title</label>
                                        <select name="title" id="title-{{ $video->id }}" class="form-select" required>
                                            <option value="{{ $video->title }}" selected>{{ $video->title }}</option>
                                            <option value="Business_Idea">Business Idea</option>
                                            <option value="Testing_Your_Idea">Testing Your Idea</option>
                                            <option value="Market_Research">Market Research</option>
                                            <option value="Start_Simple">Start Simple</option>
                                            <option value="Marketing">Marketing</option>
                                            <option value="Marketing2">Marketing2</option>
                                            <option value="Sales_Strategy">Sales Strategy</option>
                                            <option value="Sales_Strategy2">Sales Strategy2</option>
                                            <option value="Business_Setup">Business Setup</option>
                                            <option value="Financial_Planning">Financial Planning</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="description-{{ $video->id }}" class="form-label fw-bold">Description</label>
                                        <textarea class="form-control" id="description-{{ $video->id }}"
                                               name="description" rows="3">{{ $video->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Video Settings -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Video Settings</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label for="status-{{ $video->id }}" class="form-label fw-bold">Status</label>
                                        <select class="form-select" id="status-{{ $video->id }}" name="status" required>
                                            <option value="active" {{ $video->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $video->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    <!-- Current Video -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Current Video</label>
                                        <div class="video-thumbnail">
                                            <video controls class="rounded" width="100%">
                                                <source src="{{ asset($video->video_path) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>

                                    <!-- Video Upload -->
                                    <div class="mb-3">
                                        <label for="video-{{ $video->id }}" class="form-label fw-bold">Replace Video</label>
                                        <input type="file" class="form-control" id="video-{{ $video->id }}"
                                               name="video" accept="video/mp4,video/x-m4v,video/*">
                                        <small class="text-muted">Leave empty to keep current video</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
    .video-thumbnail {
        position: relative;
        width: 80px;
        height: 60px;
        border-radius: 6px;
        overflow: hidden;
    }

    .video-thumbnail video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0,0,0,0.3);
        color: white;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .video-thumbnail:hover .play-overlay {
        opacity: 1;
    }

    .play-overlay i {
        font-size: 20px;
    }
</style>

<script>
    $(document).ready(function() {
        // Export functionality
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
            XLSX.utils.book_append_sheet(wb, ws, "Videos");

            XLSX.writeFile(wb, "videos_export.xlsx");
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
            doc.save('videos_export.pdf');
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Video thumbnail hover effect
        $('.video-thumbnail').hover(
            function() {
                $(this).find('video')[0].play();
                $(this).find('.play-overlay').hide();
            },
            function() {
                $(this).find('video')[0].pause();
                $(this).find('.play-overlay').show();
            }
        );
    });
</script>
@endsection
