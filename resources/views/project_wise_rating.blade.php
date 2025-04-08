
<div class="card">
  <div class="card-body card-body-scrollable-content">
    <div class="row align-items-center">
      <div class="col">
          <h5>Project Review</h5>
          <hr>
          <div class="alert alert-info" role="alert" style="font-size: 11px;">
            Assessment to be done on a scale of 1 to 4 ( 4 being the highest )Details given in "Rating" sheet
          </div>
      </div>
    </div>
   
    @if (!empty($user_projects) && count($user_projects) > 0)
    @foreach($user_projects as $projectIndex => $project)
   
      <!------ project div starts here   ----------->
      <input type="hidden" id="project_name_{{ $project->parats_project_id }}" value="<?=$project->project_name?>" />
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
                        <th>Attributes</th>
                        <th>Rating</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach($user_attributes as $index => $attribute)   
                  @php
                      $projectId = $project->parats_project_id ?? 0;
                      $attributeKey = $attribute->id ?? 0;
                      $ratingExists = isset($projectWiseData[$projectId][$attributeKey][0]->rating) && !empty($projectWiseData[$projectId][$attributeKey][0]->rating);
                      $existingRating = $ratingExists ? $projectWiseData[$projectId][$attributeKey][0]->rating : '';
                      $existingComment = $projectWiseData[$projectId][$attributeKey][0]->employee_comment ?? '';
                  @endphp
                    <tr>
                        <td width="50" class="text-center">{{ $index+1 }}</td>
                        <td>{{ $attribute->attribute }}</td>
                        <td width="150">
                          
                            <select name="rating_{{ $project->parats_project_id }}_{{ $attribute->id }}" class="form-select project_attribute_rating" data-projectId="{{ $project->parats_project_id }}">
                                <option value="1" {{ $existingRating == 1 ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $existingRating == 2 ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $existingRating == 3 ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $existingRating == 4 ? 'selected' : '' }}>4</option>
                            </select>
                        </td>
                        <td width="350">
                            <textarea name="remarks_{{ $project->parats_project_id }}_{{ $attribute->id }}" id="" class="form-control" placeholder="comments" style="height: 50px;"><?=$existingComment?></textarea>
                            <!-- <input class="form-control" type="file" name="evidence_{{ $project->parats_project_id }}_{{ $attribute->id }}" style=" margin-top: 15px;"> -->
                        </td>
                    </tr>
                  @endforeach
                  
                </tbody>
              </table>

            </div>
          </div>

          <div class="row mb-4">
            <div class="col">
              <p class="mb-1"><strong>Details of Tasks undertaken in project</strong></p>
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

      </div>
      <div class="row">
        <div class="col">
        <div class="alert alert-info" role="alert" style="font-size: 11px;">
          Please rate your attributes under this section if the assigned attributes are not related to any of your allocated projects, or if you don’t have any project allocation. If any of the attributes listed below are related to projects, please rate them as 'Not applicable.'
        </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xxl-12">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Attributes</th>
                <th>Rating</th>
                <th>Comments</th>
              </tr>
            </thead>
            <tbody>
             
              @foreach($user_attributes as $index => $attribute)  
              @php
                  $attributeId = $attribute->id ?? 0;
                  $ratingExists = isset($projectWiseData[-1][$attributeId][0]->rating) && !empty($projectWiseData[-1][$attributeId][0]->rating);
                  $existingRating = $ratingExists ? $projectWiseData[-1][$attributeId][0]->rating : '';
                  $existingComment = $projectWiseData[-1][$attributeId][0]->employee_comment ?? '';
              @endphp 
              <tr>
                  <td width="50" class="text-center">{{ $index+1 }}</td>
                  <td>{{ $attribute->attribute }}</td>
                  <td width="150">
                      <select name="general_rating_{{ $attribute->id }}" class="form-select">
                          <option value="1" {{ $existingRating == 1 ? 'selected' : '' }}>1</option>
                          <option value="2" {{ $existingRating == 2 ? 'selected' : '' }}>2</option>
                          <option value="3" {{ $existingRating == 3 ? 'selected' : '' }}>3</option>
                          <option value="4" {{ $existingRating == 4 ? 'selected' : '' }}>4</option>
                        </select>
                  </td>
                  <td width="350">
                      <textarea name="general_remarks_{{ $attribute->id }}" id="" class="form-control" placeholder="comments" style="height: 55px;"><?=$existingComment?></textarea>
                      <!-- <input class="form-control" type="file" name="general_evidence_{{ $attribute->id }}" style=" margin-top: 15px;"> -->
                  </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>


      <div class="row">
        
        <div class="col">
          <p class="mb-1"><strong>Details of Tasks undertaken in project</strong></p>
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
            $keyContributions = isset($general_data) ? ($general_data->key_contributions ?? '') : '';
            $suggestionsimprovemnts = isset($general_data) ? ($general_data->suggestions_for_improvement ?? '') : '';
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


