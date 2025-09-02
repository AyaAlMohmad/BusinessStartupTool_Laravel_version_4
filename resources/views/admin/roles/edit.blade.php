@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border shadow-xs mb-4">
            <div class="card-header border-bottom pb-0">
                <div class="d-sm-flex align-items-center">
                    <div>
                        <h6 class="font-weight-semibold text-lg mb-0">Edit Role</h6>
                        <p class="text-sm mb-sm-0">Update role information and permissions</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Role Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active"   @selected(old('status', $role->status) === 'active')>Active</option>
                                    <option value="inactive" @selected(old('status', $role->status) === 'inactive')>Inactive</option>
                                    <option value="blocked"  @selected(old('status', $role->status) === 'blocked')>Blocked</option>
                                </select>
                                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- NEW: Region select --}}
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="region_id">Region</label>
                                <select class="form-control" id="region_id" name="region_id" required>
                                    <option value="" disabled>Choose region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}"
                                            @selected(old('region_id', $role->region_id) == $region->id)>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3">Permissions</h6>
                            @foreach($permissions as $group => $groupPermissions)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">{{ ucfirst($group) }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($groupPermissions as $permission)
                                                <div class="col-md-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $permission->id }}"
                                                            id="perm_{{ $permission->id }}"
                                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-dark">Update Role</button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
