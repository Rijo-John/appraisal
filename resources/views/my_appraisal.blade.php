@extends('layouts.app')
  @section('content')
  <main class="col  ms-sm-auto  content-wrapper">
    <div class="row">
          <div class="col">
            @include('layouts.sidebarmenu') 
            
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('technical') == 0)
            <form action="{{ route('employeeGoalSubmitNonTechnical') }}" method="POST" enctype="multipart/form-data">
            @else
            <form action="{{ route('employeeGoalSubmit') }}" method="POST" enctype="multipart/form-data">
            @endif
            @csrf
            <div class="row align-items-center mb-3">
              <div class="col">
                <h3 class="heading-color mb-0">My Appraisal</h3>
              </div>
              <div class="col-auto">
                <button type="submit" name="action" value="draft"class="btn btn-success mx-2">Save As Draft</button>
                <button type="submit" name="action" value="finalise" class="btn btn-primary">Finalise</button>
              </div>
            </div>
           
            <div class="tab-content tab-content-custom" id="myTabContent">
              <div class="tab-pane fade show active" id="cover-pane" role="tabpanel" aria-labelledby="cover"
                tabindex="0">
                @include('employee_details') 

              </div>
              <!-- my Goals-->
              
              <div class="tab-pane fade" id="employee-goals-pane" role="tabpanel" aria-labelledby="employee-goals-tab" tabindex="0">
                @if(session('technical') == 0)
                    @include('my_appraisal_non_technical')
                @else
                    @include('goals_rating') 
                @endif
              </div>
             
              <div class="tab-pane fade" id="employee-tasks-pane" role="tabpanel" aria-labelledby="employee-tasks-tab" tabindex="0">
                @include('appraisal_training')
              </div>

              <div class="tab-pane fade" id="attribute-review-pane" role="tabpanel" aria-labelledby="attribute-review-tab" tabindex="0">
                @include('appraisal_attribute_review')
              </div>

              <div class="tab-pane fade" id="value-creation-pane" role="tabpanel" aria-labelledby="value-creation-tab" tabindex="0">
                Designation wise value creation questions will be listed here.
              </div>

            </div>
            </form>

          </div>
        </div>
        </main>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(function () {
                    let successAlert = document.querySelector('.alert.alert-success'); // Select only success alerts
                    if (successAlert) {
                        let bsAlert = new bootstrap.Alert(successAlert);
                        bsAlert.close();
                    }
                }, 3000); // 3 seconds timeout for success message
            });
        </script>

  @endsection