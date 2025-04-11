
@extends('layouts.app')
  @section('content')
  <main class="col  ms-sm-auto  content-wrapper">
    <div class="row">
          <div class="col">
            @include('layouts.sidebarmenu') 
            
            
           
            <form action="{{ route('appraiserSubmitEmpRating') }}" id="myAppForm" method="POST" enctype="multipart/form-data">
           
            @csrf
             
            <div class="row align-items-center mb-3">
              <div class="col">
                <h3 class="heading-color mb-0">Appraisal Evaluation</h3>
              </div>
              <div class="col-auto">
                <button type="submit" class="btn btn-success mx-2">Save As Draft</button>
                <button type="button" class="btn btn-primary"><div class="d-flex align-items-center">Finalise </div></button>
              </div>
            </div>
           
            <div class="tab-content tab-content-custom" id="myTabContent">
              <div class="tab-pane fade show active" id="cover-pane" role="tabpanel" aria-labelledby="cover"
                tabindex="0">
                @include('employee_details') 
              </div>
              <!-- my Goals-->
              
              <div class="tab-pane fade" id="employee-goals-pane" role="tabpanel" aria-labelledby="employee-goals-tab" tabindex="0">
                @include('appraiser_goal_rating') 
              </div>
             
              <div class="tab-pane " id="employee-tasks-pane" role="tabpanel" aria-labelledby="employee-tasks-tab" tabindex="0">
            
              </div>

              <div class="tab-pane" id="attribute-review-pane" role="tabpanel" aria-labelledby="attribute-review-tab" tabindex="0">
                @include('appraisal_attribute_review')
              </div>

              <div class="tab-pane" id="value-creation-pane" role="tabpanel" aria-labelledby="value-creation-tab" tabindex="0">
                Designation wise value creation questions will be listed here.
              </div>

            </div>

              <!-- Modal -->
                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        Are you sure you want to submit your performance self-rating? Once confirmed, this action cannot be undone. 
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          
                          <button type="button" class="btn btn-primary" id="confirmSelfRating"><div class="d-flex align-items-center">Confirm <div class="loader ms-2 confirm-loader" style="display:none;"></div></div></button>
                        </div>
                      </div>
                    </div>
                  </div>
              <!-- Modal -->
            </form>

          </div>
        </div>
        </main>

  @endsection