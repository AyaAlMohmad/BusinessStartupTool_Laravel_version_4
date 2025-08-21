@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card border shadow-xs mb-4">
            <div class="card-header border-bottom pb-0">
                <div class="d-sm-flex align-items-center mb-3">
                    <div>
                        <h6 class="font-weight-semibold text-lg mb-0">Add Region</h6>
                        <p class="text-sm mb-sm-0">Add a new region</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.regions.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Region Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Region</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

