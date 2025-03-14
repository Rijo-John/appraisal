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
                      $projectId = $project->parats_project_id ?? 0;
                      $goalKey = $goal->id ?? 0;
                      $ratingExists = isset($projectWiseData[$projectId][$goalKey][0]->rating) && !empty($projectWiseData[$projectId][$goalKey][0]->rating);
                      $existingRating = $ratingExists ? $projectWiseData[$projectId][$goalKey][0]->rating : '';
                      $existingComment = $projectWiseData[$projectId][$goalKey][0]->employee_comment ?? '';
                  @endphp
                    <tr>
                        <td width="50" class="text-center">{{ $index+1 }}</td>
                        <td>{{ $goal->goal }}</td>
                        <td width="150">
                            <select name="rating_{{ $project->parats_project_id }}_{{ $goal->id }}" class="form-select">
                                <!-- <option value="" selected>Select rating</option>
                                <option value="10" >Achieved</option>
                                <option value="5" >Partially Achieved</option>
                                <option value="1" >Not Achieved</option>
                                <option value="0" >Not Applicable</option> -->
                                <option value="" {{ !$ratingExists ? 'selected' : '' }}>Select rating</option>
                                <option value="10" {{ $existingRating == 10 ? 'selected' : '' }}>Achieved</option>
                                <option value="5" {{ $existingRating == 5 ? 'selected' : '' }}>Partially Achieved</option>
                                <option value="1" {{ $existingRating == 1 ? 'selected' : '' }}>Not Achieved</option>
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
              <textarea   name="taskdetails{{ $project->parats_project_id }}" class="form-control" placeholder="Task Details" style="height: 83px;">
              @if (isset($project_extra[$projectId][0]->task_details))
                  {{ $project_extra[$projectId][0]->task_details }}
              @endif
            </textarea>
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
              <tr>
                  <td width="50" class="text-center">{{ $index+1 }}</td>
                  <td>{{ $goal->goal }}</td>
                  <td width="150">
                      <select name="general_rating_{{ $goal->id }}" class="form-select">
                          <option value="" selected>Select rating</option>
                          <option value="10" >Achieved</option>
                          <option value="5" >Partially Achieved</option>
                          <option value="1" >Not Achieved</option>
                          <option value="0" >Not Applicable</option>
                      </select>
                  </td>
                  <td width="350">
                      <textarea name="general_remarks_{{ $goal->id }}" id="" class="form-control" placeholder="comments" style="height: 83px;"></textarea>
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
          <textarea   name="general_taskdetails" class="form-control" placeholder="Enter Task Details" style="height: 83px;"></textarea>
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
        <textarea name="key_contributions"  style="height: 83px;" class="form-control"></textarea>
      </div>
      <div class="col">
        <p class="">Suggestions for Organization’s Improvement</p>
        <textarea name="suggestions_for_improvement"  class="form-control" style="height: 83px;"></textarea>
      </div>
    </div>
</div>
</div>
