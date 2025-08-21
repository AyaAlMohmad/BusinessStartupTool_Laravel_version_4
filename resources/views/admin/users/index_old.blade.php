@extends('layouts.app')
@section('content')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .navbar {
            display: flex;
            justify-content: space-around;
            background-color: #444;
            padding: 10px 0;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
        }

        .navbar a:hover {
            background-color: #555;
        }

        .container {
            /* margin: 20px; */
            /* display: flex; */
            padding: 20px;
            justify-content: flex-end;
            align-items: center;

        }



        .search-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .search-bar input {
            padding: 10px;
            width: 80%;
            border: 1px solid #ddd;
        }

        .search-bar button {
            padding: 10px;
            background-color: #ac0c0c;
            color: white;
            border: none;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #a71212;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            font-family: Arial, sans-serif;
            text-align: left;
        }

        thead tr {
            background-color: #980000;
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }

        th,
        td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        a {
            text-decoration: none;
            color: #009879;
            font-weight: bold;
            margin-right: 10px;
        }

        a:hover {
            text-decoration: underline;
            color: #005f56;
        }

        button {
            background-color: #009879;
            color: #ffffff;
            border: none;
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #007961;
        }

        form {
            display: inline-block;
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .modal .form-control {
            border-radius: 5px;
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .modal .btn {
            border-radius: 5px;
        }
    </style>
    <div class="container">

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search users..." onkeyup="searchTable()" />
            <button onclick="exportTableToCSV('users.csv')">Export Data</button>
            <script>
                function searchTable() {
                    var input = document.getElementById("searchInput");
                    var filter = input.value.toLowerCase();
                    var table = document.querySelector("table");
                    var tr = table.getElementsByTagName("tr");

                    for (var i = 1; i < tr.length; i++) {
                        var td = tr[i].getElementsByTagName("td");
                        var found = false;
                        for (var j = 0; j < td.length; j++) {
                            if (td[j]) {
                                var txtValue = td[j].textContent || td[j].innerText;
                                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                    found = true;
                                    break;
                                }
                            }
                        }
                        tr[i].style.display = found ? "" : "none";
                    }
                }

                function exportTableToCSV(filename) {
                    var csv = [];
                    var table = document.querySelector("table");
                    var rows = table.querySelectorAll("tr");

                    for (var i = 0; i < rows.length; i++) {
                        var row = [], cols = rows[i].querySelectorAll("td, th");
                        for (var j = 0; j < cols.length; j++) {
                            row.push(cols[j].innerText);
                        }
                        csv.push(row.join(","));
                    }

                    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
                    var downloadLink = document.createElement("a");
                    downloadLink.download = filename;
                    downloadLink.href = window.URL.createObjectURL(csvFile);
                    downloadLink.style.display = "none";
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                }
            </script>
        </div>

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Registration Date</th>
                    <th>Last Login</th>
                    <th>Progress</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span style="display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 14px; color: white;
                                     background-color: {{ $user->status === 'active' ? '#28a745' : '#dc3545' }};">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>

                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>{{ $user->last_login ? $user->last_login->format('d/m/Y') : 'Never' }}</td>
                    <td>Welcome
                        <span style="display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 14px; color: white;
                                     background-color:
                                     #771313;">
                            {{ $user->progress ?? '0' }}%
                        </span>
                    </td>

                    <td>
                        <!-- View Icon -->
                        <a href="{{ route('admin.users.show', $user->id) }}" title="View">
                            <i class="fas fa-eye" style="color: #009879; font-size: 18px;"></i>
                        </a>

                        <!-- Edit Icon -->
                        <a href="#" class="btn btn-link" data-bs-toggle="modal"
                            data-bs-target="#editUserModal-{{ $user->id }}">
                            <i class="fas fa-pen"></i>
                        </a>


                        <!-- Change Status Icon -->
                        <form action="{{ route('admin.changeStatus', $user->id) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" title="Change Status"
                                style="background: none; border: none; cursor: pointer;"
                                onclick="return confirm('Are you sure you want to change the status of this user?')">
                                <i class="fas fa-ban" style="color: #ff9800; font-size: 18px; margin-left: 8px;"></i>
                            </button>
                        </form>

                        <!-- Delete Icon -->
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete"
                                style="background: none; border: none; cursor: pointer;"
                                onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="fas fa-trash-alt"
                                    style="color: #ff4d4d; font-size: 18px; margin-left: 8px;"></i>
                            </button>
                        </form>
                    </td>

                </tr>
                <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1"
                    aria-labelledby="editUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <!-- Name -->
                                    <div class="mb-3">
                                        <label for="name-{{ $user->id }}" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name-{{ $user->id }}" name="name"
                                            value="{{ $user->name }}" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email-{{ $user->id }}" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email-{{ $user->id }}" name="email"
                                            value="{{ $user->email }}" required>
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label for="status-{{ $user->id }}" class="form-label">Status</label>
                                        <select class="form-select" id="status-{{ $user->id }}" name="status" required>
                                            <option value="active" {{ $user->status == 'active' ? 'selected' :
                                                '' }}>Active</option>
                                            <option value="inactive" {{ $user->status == 'inactive' ? 'selected' :
                                                '' }}>InActive</option>
                                            <option value="blocked" {{ $user->status == 'blocked' ? 'selected' :
                                                '' }}>Blocked</option>
                                        </select>
                                    </div>
                                    <!-- Admin Status -->
                                    <div class="mb-3">
                                        <label for="is_admin-{{ $user->id }}" class="form-label">User Role</label>
                                        <select class="form-select" id="is_admin-{{ $user->id }}" name="is_admin"
                                            required>
                                            <option value="0" {{ $user->is_admin == 0 ? 'selected' : '' }}>User</option>
                                            <option value="1" {{ $user->is_admin == 1 ? 'selected' : '' }}>Admin
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any additional JS functionality here if needed
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection
