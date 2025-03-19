@extends('layouts.app')

@section('content')
<div class="content-wrapper-no-left-tab col">
    <div class="row align-items-center mb-3">
      <div class="col">
        <h3 class="heading-color mb-0">Edit Appraisal</h3>
      </div>
     
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('update-appraisal', $appraisal->id) }}" method="POST">
            @csrf
            <div class="row mb-3 align-items-center">
                <label for="appraiser_officer_name" class="form-label col-md-3">Appraiser Officer</label>
                <div class="col-5">
                <select class="form-control" id="appraiser_officer_heads_id" name="appraiser_officer_heads_id">
                    <option value="">Select an Appraiser Officer</option>
                    @foreach($internalUsers as $user)
                        <option value="{{ $user->heads_id }}" {{ $appraisal->appraiser_officer_heads_id == $user->heads_id ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            </div>
            <div class="row mb-3 align-items-center">
                <div class="col-5 offset-md-3">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('appraisaldata') }}" class="btn btn-secondary ms-1">Cancel</a>
            </div>
            </div>
            </form>
        </div>
    </div>


    
</div>
@endsection
