@extends('layouts.app')
@section('content')
<style>
    /* تنسيق العام للـ Modal */
.modal-content {
    border-radius: 10px;
    border: 1px solid #e1e1e1;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9;
}

/* رأس الـ Modal */
.modal-header {
    background-color: #007bff;
    color: #fff;
    border-bottom: 1px solid #ddd;
    padding: 1.25rem;
    border-radius: 10px 10px 0 0;
}

.modal-header h5 {
    font-size: 1.25rem;
    font-weight: bold;
}

.modal-header .btn-close {
    background: none;
    border: none;
    color: #fff;
    font-size: 1.5rem;
    opacity: 0.7;
}

/* الجسم الخاص بالـ Modal */
.modal-body {
    padding: 1.5rem;
}

/* تنسيق الحقول داخل الـ Modal */
.modal-body .form-label {
    font-weight: bold;
    color: #333;
}

.modal-body input, .modal-body select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 0.75rem;
    width: 100%;
    margin-bottom: 1rem;
    font-size: 1rem;
}

.modal-body input:focus, .modal-body select:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* أزرار الـ Modal */
.modal-footer {
    background-color: #f1f1f1;
    border-top: 1px solid #ddd;
    padding: 1rem;
    text-align: right;
}

.modal-footer .btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.modal-footer .btn-secondary {
    background-color: #6c757d;
    color: #fff;
    border-color: #6c757d;
}

.modal-footer .btn-secondary:hover {
    background-color: #5a6268;
}

.modal-footer .btn-success {
    background-color: #28a745;
    color: #fff;
    border-color: #28a745;
}

.modal-footer .btn-success:hover {
    background-color: #218838;
}

/* التنسيق عند الفتح */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

/* تأثير عند التمرير على الأزرار */
.btn-icon-only {
    width: 2rem;
    height: 2rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: background-color 0.3s ease;
}

.btn-icon-only:hover {
    background-color: rgba(0, 123, 255, 0.1);
}

/* تنسيق الـ Icons */
.fas {
    font-size: 1.2rem;
}

</style>
<div class="row">
    <div class="col-12">
        <div class="card border shadow-xs mb-4">
            <div class="card-header border-bottom pb-0">
                <div class="d-sm-flex align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-semibold text-lg mb-0">Users List</h6>
                        <p class="text-sm mb-sm-0">Manage system users</p>
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
                        {{-- <div class="btn-group ms-2">
                            <a href="{{ route('admin.users.create') }}"
                            class="btn btn-sm btn-dark">
                                <span class="btn-inner--icon">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="me-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </span>
                                <span class="btn-inner--text">Add User</span>
                            </a>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="card-body px-0 py-0">
                <div class="table-responsive p-0">
                    <table class="table align-items-center justify-content-center mb-0">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7">User</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Email</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Status</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Role</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Responsibility</th>
                                <th class="text-secondary text-xs font-weight-semibold opacity-7 ps-2">Registered</th>
                                <th class="text-center text-secondary text-xs font-weight-semibold opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2">
                                            <div class="avatar avatar-sm me-2">
                                                <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}"
                                                     class="rounded-circle" alt="avatar">
                                            </div>
                                            <div class="my-auto">
                                                <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-normal mb-0">{{ $user->email }}</p>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'warning' : 'danger') }}" style="color:black">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_admin ? 'primary' : 'secondary' }}" style="color:black">
                                            {{ $user->is_admin ? 'Admin' : 'User' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info" style="color:black">
                                            {{ $user->roles->pluck('name')->join(', ') ?? 'No Role' }}
                                        </span>
                                    </td>

                                    <td>
                                        <p class="text-sm font-weight-normal mb-0">{{ $user->created_at->format('d/m/Y') }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex justify-content-center">
                                            <!-- View Button -->
                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                               class="btn btn-sm btn-icon-only btn-rounded btn-outline-success me-2"
                                               data-bs-toggle="tooltip" data-bs-title="View User">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Edit Button -->
                                            <a href="#"
                                               class="btn btn-sm btn-icon-only btn-rounded btn-outline-primary me-2"
                                               data-bs-toggle="modal"
                                               data-bs-target="#editUserModal-{{ $user->id }}"
                                               data-bs-toggle="tooltip" data-bs-title="Edit User">
                                                <i class="fas fa-pen"></i>
                                            </a>

                                            <!-- Status Button -->
                                            <form action="{{ route('admin.changeStatus', $user->id) }}" method="POST" class="me-2">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="btn btn-sm btn-icon-only btn-rounded btn-outline-warning"
                                                        data-bs-toggle="tooltip" data-bs-title="Change Status"
                                                        onclick="return confirm('Are you sure you want to change the status of this user?')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>

                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-icon-only btn-rounded btn-outline-danger"
                                                        data-bs-toggle="tooltip" data-bs-title="Delete User"
                                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit User Modal -->
                            <!-- Edit User Modal -->
<div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title text-dark" id="editUserModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Edit User: {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column - Basic Info -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Name -->
                                    <div class="mb-3">
                                        <label for="name-{{ $user->id }}" class="form-label fw-bold">Full Name</label>
                                        <input type="text" class="form-control" id="name-{{ $user->id }}" name="name" value="{{ $user->name }}" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email-{{ $user->id }}" class="form-label fw-bold">Email Address</label>
                                        <input type="email" class="form-control" id="email-{{ $user->id }}" name="email" value="{{ $user->email }}" required>
                                        <small class="text-muted">User's primary email address</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Account Settings -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Account Settings</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label for="status-{{ $user->id }}" class="form-label fw-bold">Account Status</label>
                                        <select class="form-select" id="status-{{ $user->id }}" name="status" required>
                                            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="blocked" {{ $user->status == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                        </select>
                                    </div>

                                    <!-- Admin Status -->
                                    <div class="mb-3">
                                        <label for="is_admin-{{ $user->id }}" class="form-label fw-bold">Admin Privileges</label>
                                        <select class="form-select" id="is_admin-{{ $user->id }}" name="is_admin" required>
                                            <option value="0" {{ $user->is_admin == 0 ? 'selected' : '' }}>Regular User</option>
                                            <option value="1" {{ $user->is_admin == 1 ? 'selected' : '' }}>Administrator</option>
                                        </select>
                                    </div>

                                    <!-- Role -->
                                    <div class="mb-3">
                                        <label for="role_id-{{ $user->id }}" class="form-label fw-bold">User Responsibility</label>
                                        <select class="form-select" id="role_ids-{{ $user->id }}" name="role_ids[]" multiple required>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ $user->roles->pluck('id')->contains($role->id) ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
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
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                <div class="border-top py-3 px-3 d-flex align-items-center">
                    @if ($users->previousPageUrl())
                        <a href="{{ $users->previousPageUrl() }}"
                            class="btn btn-sm btn-white d-sm-block d-none mb-0">Previous</a>
                    @else
                        <button class="btn btn-sm btn-white d-sm-block d-none mb-0" disabled>Previous</button>
                    @endif

                    <nav aria-label="..." class="ms-auto">
                        <ul class="pagination pagination-light mb-0">
                            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                <li class="page-item {{ $users->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link border-0 font-weight-bold"
                                        href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>

                    @if ($users->nextPageUrl())
                        <a href="{{ $users->nextPageUrl() }}"
                            class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto">Next</a>
                    @else
                        <button class="btn btn-sm btn-white d-sm-block d-none mb-0 ms-auto" disabled>Next</button>
                    @endif
                </div>
                @endif
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
            XLSX.utils.book_append_sheet(wb, ws, "Users");

            XLSX.writeFile(wb, "users_export.xlsx");
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
            doc.save('users_export.pdf');
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<style>
    .btn-icon-only {
        width: 2rem;
        height: 2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-rounded {
        border-radius: 50% !important;
    }
    .btn-outline-success {
        color: #28a745;
        border-color: #28a745;
    }
    .btn-outline-success:hover {
        background-color: #28a745;
        color: white;
    }
    .btn-outline-primary {
        color: #007bff;
        border-color: #007bff;
    }
    .btn-outline-primary:hover {
        background-color: #007bff;
        color: white;
    }
    .btn-outline-warning {
        color: #ffc107;
        border-color: #ffc107;
    }
    .btn-outline-warning:hover {
        background-color: #ffc107;
        color: white;
    }
    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }
    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
    }
</style>
@endsection
