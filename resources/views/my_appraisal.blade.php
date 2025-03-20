
@extends('layouts.app')
  @section('content')
  <main class="col  ms-sm-auto  content-wrapper">
  @if(session('error-user-not-in-appraisal'))
    <div class="alert alert-danger">
        {{ session('error-user-not-in-appraisal') }}
    </div>
  @endif
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

           
            <form action="{{ route('employeeGoalSubmit') }}" id="myAppForm" method="POST" enctype="multipart/form-data">
           
            @csrf
             <input type="hidden" name="self_finalise" id="self_finalise" value="<?=$selfFinalise?>" />
            <div class="row align-items-center mb-3">
              <div class="col">
                <h3 class="heading-color mb-0">My Appraisal</h3>
              </div>
              <div class="col-auto">
              @if ($selfFinalise == 0)
                <button type="submit" name="action" value="draft" id="appraisal_draft" class="btn btn-success mx-2 {{ $selfFinalise == 1 ? 'disabled' : '' }}" >Save As Draft</button>
                <button type="submit" name="action" value="finalise" id="finaliseButton" class="btn btn-primary {{ $selfFinalise == 1 ? 'disabled' : '' }}">Finalise</button>
                @else
                  <p class="text-danger">You are already finalised!!</p>
              @endif
              </div>
            </div>
           
            <div class="tab-content tab-content-custom" id="myTabContent">
              <div class="tab-pane fade show active" id="cover-pane" role="tabpanel" aria-labelledby="cover"
                tabindex="0">
                @include('employee_details') 

              </div>
              <!-- my Goals-->
              
              <div class="tab-pane fade" id="employee-goals-pane" role="tabpanel" aria-labelledby="employee-goals-tab" tabindex="0">
                @if(session('appraisal_category') == 2)
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


      $(document).ready(function() {
        if($("#self_finalise").val() == 1) { 
          $('input, select, textarea').prop('disabled', true);
        }
        $("#confirmSelfRating").click(function(e) {
            $("#finaliseButton").prop("disabled", true).text("Submitting...");
            $("#appraisal_draft").prop("disabled", true);
            $("#confirmSelfRating").prop("disabled", true);
            $(".confirm-loader").show();
            $("input[name='is_finalise']").remove();
            $("<input>").attr({
                type: "hidden",
                name: "is_finalise",
                value: "1"
            }).appendTo("form");
            $("#myAppForm").submit();
        });
        $("#appraisal_draft").click(function(e) {
          $("#appraisal_draft").prop("disabled", true).text("Submitting...");
          $("#finaliseButton").prop("disabled", true);
          $("#myAppForm").submit();
        });

        $("#finaliseButton").click(function(e) {
          debugger;
          e.preventDefault();
          var self_finalise = $("#self_finalise").val();
          if(self_finalise == 0) { 
            let isValid = true;
            let firstErrorElement = null;
            let missingProjects = [];

            // Remove previous error messages
            $(".rating-error").remove();
            $(".project_goal_rating").removeClass("is-invalid");

              // Loop through all rating select elements
              $(".project_goal_rating").each(function() {
                  let projectId = $(this).data("projectid"); // Get project ID from data attribute
                  

                  if ($(this).val() === "") {
                      isValid = false;
                      $(this).addClass("is-invalid"); // Highlight error
                      $(this).after('<div class="text-danger rating-error">Please select a rating</div>');

                      var projectName = $('#project_name_'+projectId).val();

                      toastr.error(`Please provide a rating for all the goals in the ${projectName} Project.`);
                      return false;
                    
                  }
              });

          

              // Prevent form submission if validation fails
              if (!isValid) {
                  e.preventDefault();
              } else {
                $("#confirmationModal").modal('show');
                
              }
          }
        });

        // Remove error message when selecting a rating
        $(document).on("change", ".project_goal_rating", function() {
            $(this).removeClass("is-invalid");
            $(this).next(".rating-error").remove();
        });
    });
      
  </script>

  @endsection