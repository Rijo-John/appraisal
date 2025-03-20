
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

 
<div class="card">
  <div class="card-body card-body-scrollable-content">
    <div class="row align-items-center">
      <div class="col">
          <h5>My Goals</h5>
          <hr>
          <div class="alert alert-info" role="alert" style="font-size: 11px;">
          Please rate your goals against the projects you were assigned during the appraisal period. If any of your assigned goals are not applicable to a project, please mark them as 'Not applicable.' If the goals are not applicable to any of your allocated projects, please rate them in the 'General' section at the end.
          </div>
      </div>
    </div>
   
    @if (!empty($user_projects) && count($user_projects) > 0)
    @foreach($user_projects as $projectIndex => $project)
   
      <!------ project div starts here   ----------->
      <input type="hidden" id="project_name_{{ $project->parats_project_id }}" value="<?=$project->project_name?>" />
      <input type="hidden" name="self_finalise" id="self_finalise" value="<?=$selfFinalise?>" />
      <div class="row">
        <div class="col">
          <div class="row align-items-center mb-3">
              <div class="col-auto">
                  <span class="count">{{ $projectIndex + 1 }}</span>
              </div>
              <div class="col ps-0">
                  <h6 class="mb-0"><span><?=$project->project_name?> </span></h6>
              </div>
          </div>

          <div class="row">
            <div class="col-xxl-12">

              <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Goals</th>
                        <th>Rating</th>
                        <th>Comments &amp; Evidence</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach($user_goals as $index => $goal)   
                  @php
                      //echo '<pre>'; print_r($projectWiseData); die();
                      $projectId = $project->parats_project_id ?? 0;
                      $goalKey = $goal->id ?? 0;
                      $ratingExists = isset($projectWiseData[$projectId][$goalKey][0]->rating) && ($projectWiseData[$projectId][$goalKey][0]->rating!='');
                      $existingRating = $ratingExists ? $projectWiseData[$projectId][$goalKey][0]->rating : '';
                      $existingComment = $projectWiseData[$projectId][$goalKey][0]->employee_comment ?? '';
                  @endphp
                    <tr>
                        <td width="50" class="text-center">{{ $index+1 }}</td>
                        <td>{{ $goal->goal }}</td>
                        <td width="150">
                          
                            <select name="rating_{{ $project->parats_project_id }}_{{ $goal->id }}" class="form-select project_goal_rating" data-projectId="{{ $project->parats_project_id }}">
                                <option value="" {{ !$ratingExists ? 'selected' : '' }}>Select rating</option>
                                <option value="1" {{ $existingRating == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $existingRating == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $existingRating == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $existingRating == 4 ? 'selected' : '' }}>4</option>
                                <option value="0" {{ $existingRating == 0 ? 'selected' : '' }}>Not Applicable</option>
                            </select>
                        </td>
                        <td width="350">
                            <textarea name="remarks_{{ $project->parats_project_id }}_{{ $goal->id }}" id="" class="form-control" placeholder="comments" style="height: 83px;"><?=$existingComment?></textarea>
                            <input class="form-control" type="file" name="evidence_{{ $project->parats_project_id }}_{{ $goal->id }}" style=" margin-top: 15px;">
                        </td>
                    </tr>
                  @endforeach
                  
                </tbody>
              </table>

            </div>
          </div>

          <div class="row mb-4">
            <div class="col">
              <p class="mb-1">Task Details</p>
              <textarea   name="taskdetails{{ $project->parats_project_id }}" class="form-control" placeholder="Task Details" style="height: 83px;">@if(isset($project_extra[$projectId][0]->task_details)){{ $project_extra[$projectId][0]->task_details }}@endif</textarea>
            </div>
          </div>
  
        </div>
      </div>
      <!---- ends here --->
    @endforeach
    @endif

    <!------ General div starts here   ----------->
    <div class="row">
      <div class="col">
        <div class="row align-items-center mb-3">
          <div class="col-auto">
            <span class="count"><?=count($user_projects)+1?></span>
          </div>
        <div class="col ps-0">
          <h6 class="mb-0"><span>General </span></h6>
        </div>
        <input type="hidden" id="project_name_general" value="General" />
      </div>
      <div class="row">
        <div class="col">
        <div class="alert alert-info" role="alert" style="font-size: 11px;">
          Please rate your goals under this section if the assigned goals are not related to any of your allocated projects, or if you don’t have any project allocation. If any of the goals listed below are related to projects, please rate them as 'Not applicable.'
        </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xxl-12">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Goals</th>
                <th>Rating</th>
                <th>Comments &amp; Evidence</th>
              </tr>
            </thead>
            <tbody>
             
              @foreach($user_goals as $index => $goal)  
              @php
                  $goaalId = $goal->id ?? 0;
                  $ratingExists = isset($projectWiseData[-1][$goaalId][0]->rating) && !empty($projectWiseData[-1][$goaalId][0]->rating);
                  $existingRating = $ratingExists ? $projectWiseData[-1][$goaalId][0]->rating : '';
                  $existingComment = $projectWiseData[-1][$goaalId][0]->employee_comment ?? '';
              @endphp 
              <tr>
                  <td width="50" class="text-center">{{ $index+1 }}</td>
                  <td>{{ $goal->goal }}</td>
                  <td width="150">
                      <select name="general_rating_{{ $goal->id }}" class="form-select project_goal_rating" data-projectId="general">
                          <option value="" {{ !$ratingExists ? 'selected' : '' }}>Select rating</option>
                          <option value="1" {{ $existingRating == 1 ? 'selected' : '' }}>1</option>
                          <option value="2" {{ $existingRating == 2 ? 'selected' : '' }}>2</option>
                          <option value="3" {{ $existingRating == 3 ? 'selected' : '' }}>3</option>
                          <option value="4" {{ $existingRating == 4 ? 'selected' : '' }}>4</option>
                          <option value="0" {{ $existingRating == 0 ? 'selected' : '' }}>Not Applicable</option>
                      </select>
                  </td>
                  <td width="350">
                      <textarea name="general_remarks_{{ $goal->id }}" id="" class="form-control" placeholder="comments" style="height: 83px;"><?=$existingComment?></textarea>
                      <input class="form-control" type="file" name="general_evidence_{{ $goal->id }}" style=" margin-top: 15px;">
                  </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>


      <div class="row">
        
        <div class="col">
          <p class="mb-1">Task Details</p>
          @php
            $taskDetailsGeneral = isset($project_extra[-1]) ? ($project_extra[-1][0]->task_details ?? '') : '';
          @endphp

            <textarea name="general_taskdetails" class="form-control" placeholder="Enter Task Details" style="height: 83px;">{{ $taskDetailsGeneral }}</textarea>

        </div>
      </div>
    </div>
  </div>
  <div class="row align-items-center">
      <div class="col">
          
          <hr>
      </div>
    </div>
    <div class="row mt-3 mb-12">
      <div class="col">
        <p class="">Key Contributions</p>
          @php
            $keyContributions = isset($general_data[0]) ? ($general_data[0]->key_contributions ?? '') : '';
            $suggestionsimprovemnts = isset($general_data[0]) ? ($general_data[0]->suggestions_for_improvement ?? '') : '';
          @endphp
        <textarea name="key_contributions"  style="height: 83px;" class="form-control"><?=$keyContributions?></textarea>
      </div>
      <div class="col">
        <p class="">Suggestions for Organization’s Improvement</p>
        <textarea name="suggestions_for_improvement"  class="form-control" style="height: 83px;"><?=$suggestionsimprovemnts?></textarea>
      </div>
    </div>
</div>
</div>


<script>
//  $(document).ready(function() {
//     $("#finaliseButton").click(function(e) {
//       var self_finalise = $("#self_finalise").val();
//       debugger;
//       if(self_finalise == 0) { 
//         let isValid = true;
//         let firstErrorElement = null;
//         let missingProjects = [];

//         // Remove previous error messages
//         $(".rating-error").remove();
//         $(".project_goal_rating").removeClass("is-invalid");

//         // Loop through all rating select elements
//         $(".project_goal_rating").each(function() {
//             let projectId = $(this).data("projectid"); // Get project ID from data attribute
            

//             if ($(this).val() === "") {
//                 isValid = false;
//                 $(this).addClass("is-invalid"); // Highlight error
//                 $(this).after('<div class="text-danger rating-error">Please select a rating</div>');

//                 var projectName = $('#project_name_'+projectId).val();

//                 toastr.error(`Please provide a rating for all the goals in the ${projectName} Project.`);
//                 return false;
              
//             }
//         });

      

//         // Prevent form submission if validation fails
//         if (!isValid) {
//             e.preventDefault();
//         }
//       } else {
//           toastr.error(`Sorry, You have already finalised the self rating`);
//           return false;
//       }
//     });

//     // Remove error message when selecting a rating
//     $(document).on("change", ".project_goal_rating", function() {
//         $(this).removeClass("is-invalid");
//         $(this).next(".rating-error").remove();
//     });
// });


  </script>