@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>User Details</h1>
            </div>
            <div class="card-body">
                <p><strong>Username:</strong> {{ $user->username }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Status:</strong> 
                    <span class="status-badge" style="background-color: {{ $user->status == 'active' ? '#28a745' : '#dc3545' }};">
                        {{ ucfirst($user->status) }}
                    </span>
                </p>
                <p><strong>Registration Date:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                <p><strong>Last Login:</strong> {{ $user->last_login ? $user->last_login->format('d/m/Y') : 'Never' }}</p>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Back to User Management</a>
            </div>
        </div>
    </div>

    <style>
        .container {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 60px);
        }

        .card {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        .card-header {
            background-color: #009879;
            color: white;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        .card-body {
            padding: 20px;
            font-size: 16px;
            line-height: 1.6;
        }

        .card-footer {
            text-align: center;
            padding: 15px;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .status-badge {
            padding: 5px 10px;
            color: white;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
@endsection
