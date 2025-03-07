@extends('layouts.app')
  @section('content')

    <!-- <div class="row">
      <div class="col">
        include('layouts.sidebarmenu') 
        <div class="tab-content tab-content-custom" id="myTabContent">
          <div class="tab-pane fade show active" id="cover-pane" role="tabpanel" aria-labelledby="cover" tabindex="0">
            
          </div>
          <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
          
          </div>
          <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
              tab 3
          </div>
          <div class="tab-pane fade" id="assign-tab-pane" role="tabpanel" aria-labelledby="assign-tab" tabindex="0">
                tab 4
          </div>
          <div class="tab-pane fade" id="assign-tab-pane" role="tabpanel" aria-labelledby="assign-tab" tabindex="0">
                tab 5
          </div>
        </div>
      </div>
    </div> -->



    <main class="col  ms-sm-auto  content-wrapper">
    <div class="row">
          <div class="col">
            @include('layouts.sidebarmenu') 
            <form action="{{ route('employeeGoalSubmit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row align-items-center mb-3">
              <div class="col">
                <h3 class="heading-color mb-0">My Appraisal</h3>
              </div>
              <div class="col-auto">
                <button type="submit" class="btn btn-success mx-2">Save As Draft</button>
                <button type="button" class="btn btn-primary">Submit</button>
              </div>
            </div>
           
            <div class="tab-content tab-content-custom" id="myTabContent">
              <div class="tab-pane fade show active" id="cover-pane" role="tabpanel" aria-labelledby="cover"
                tabindex="0">
                @include('employee_details') 

              </div>
              <!-- my Goals-->
              <div class="tab-pane fade" id="employee-goals-pane" role="tabpanel" aria-labelledby="employee-goals-tab" tabindex="0">
                    @include('goals_rating')
              </div>
             
              <div class="tab-pane fade" id="employee-tasks-pane" role="tabpanel" aria-labelledby="employee-tasks-tab" tabindex="0">
                The employee's tasks will be listed here.
              </div>

              <div class="tab-pane fade" id="attribute-review-pane" role="tabpanel" aria-labelledby="attribute-review-tab" tabindex="0">
                Designation wise attribute review questions will be listed here.
              </div>

              <div class="tab-pane fade" id="value-creation-pane" role="tabpanel" aria-labelledby="value-creation-tab" tabindex="0">
                Designation wise value creation questions will be listed here.
              </div>

            </div>
            </form>

          </div>
        </div>
        </main>
  @endsection