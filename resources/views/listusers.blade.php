@extends('layouts.app')
@section('content')
@php use Illuminate\Support\Facades\Crypt; @endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<main class="col  ms-sm-auto  content-wrapper-no-left-tab">
    <div class="row align-items-center mb-3">
        <div class="col">
            <h3 class="heading-color mb-0">Appraisal Users</h3>
        </div>



    </div>
    
<div class="card">
    <div class="card-body">
        <div class="row mb-2">
        </div>
        <div class="row">
            <div class="col">
                
    
                    
                    <div id="success-message" class="alert alert-success d-none"></div>
                    
                    
                    <table id="appraisalTable" class="table table-bordered">
                        <thead >
                            <tr>
                                <th>Employee Name</th>
                                <th>Employee Code</th>
                                <th>Designation</th>
                                <th>Finalised</th>
                                <th>Goal</th>
                                <th>Attribute Value</th>
                                <th>Value Creation</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($ratings as $rating)
                                <tr>
                                    <td>{{ $rating->full_name }}</td>
                                    <td>{{ $rating->emp_code }}</td>
                                    <td>{{ $rating->designation_name  }}</td>
                                    <td>{{ $rating->finalise_status }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        @if($rating->appraisal_category == 2 && $rating->finalise_status == 'Finalised')
                                            <a href="{{ route('appraiserevaluateindex', ['id' => Crypt::encrypt($rating->id)]) }}">
                                                <i class="bi bi-eye mx-1"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                
            </div>
        </div>

    </div>
</div>
</main>
@endsection