@extends('layouts.app')

@section('content')

    <div class="row">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="col-12">
            <div class="card border shadow-xs mb-4">
                <div class="card-header border-bottom pb-0">
                    <div class="d-sm-flex align-items-center mb-3">
                        <div>
                            <h6 class="font-weight-semibold text-lg mb-0">Add Resource</h6>
                            <p class="text-sm mb-sm-0">Add a new Resource</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.resources.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Resource Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="is_global" class="form-label">Resource Type</label>
                                    <select name="is_global" id="is_global" class="form-control"
                                        onchange="toggleRegionField()">
                                        <option value="0" {{ old('is_global') == '0' ? 'selected' : '' }}>Regional
                                            Resource</option>
                                        <option value="1" {{ old('is_global') == '1' ? 'selected' : '' }}>Global
                                            Resource</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Select "Global Resource" if this resource is available for all users
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6" id="regionField">
                                <div class="form-group mb-3">
                                    <label for="region_id" class="form-label">Region</label>
                                    <select name="region_id" id="region_id" class="form-control">
                                        <option value="">Select Region</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}"
                                                {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="user_ids" class="form-label">Assigned Users (Optional)</label>
                                    <select name="user_ids[]" id="user_ids" class="form-control" multiple>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
                                                {{ $user->name }} - {{ $user->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Hold Ctrl/Cmd to select multiple users
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Resource Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="link" class="form-label">Resource Link</label>
                                    <input type="url" class="form-control" id="link" name="link"
                                        value="{{ old('link') }}" required>
                                    <small class="form-text text-muted">
                                        Please enter a valid URL (e.g., https://example.com)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Add Resource</button>
                            <a href="{{ route('admin.resources.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleRegionField() {
            const isGlobal = document.getElementById('is_global').value;
            const regionField = document.getElementById('regionField');
            const userIdsField = document.getElementById('user_ids');

            if (isGlobal == 1) {
                regionField.style.display = 'none';
                document.getElementById('region_id').value = '';

                // تعطيل وإفراغ حقل المستخدمين
                if (userIdsField) {
                    userIdsField.disabled = true;
                    $('#user_ids').val(null).trigger('change');
                }
            } else {
                regionField.style.display = 'block';

                // تمكين حقل المستخدمين
                if (userIdsField) {
                    userIdsField.disabled = false;
                }
            }
        }

        // تشغيل الدالة عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            toggleRegionField();

            // تهيئة select2 للمستخدمين
            $('#user_ids').select2({
                placeholder: "Select users",
                allowClear: true
            });

            // إضافة event listener للتأكد من القواعد
            document.getElementById('is_global').addEventListener('change', function() {
                toggleRegionField();

                // إذا تحول إلى global، إظهار تحذير إذا كان هناك مستخدمين محددين
                if (this.value == 1) {
                    const selectedUsers = $('#user_ids').val();
                    if (selectedUsers && selectedUsers.length > 0) {
                        alert(
                        'Note: All assigned users will be removed when switching to Global resource.');
                    }
                }
            });
        });
    </script>

    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d2d6da;
            border-radius: 0.375rem;
            min-height: 38px;
        }
    </style>

@endsection
