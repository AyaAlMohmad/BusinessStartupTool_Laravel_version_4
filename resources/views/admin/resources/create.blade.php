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
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="region_id" class="form-label">Regoins</label>
                                <select name="region_id" id="region_id" class="form-control">
                                    <option value="">Select Region</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Resource Description</label>
                                <textarea class="form-control" id="description" name="description" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="link" class="form-label">Resource Link</label>
                                <input type="text" class="form-control" id="link" name="link" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Resource</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

