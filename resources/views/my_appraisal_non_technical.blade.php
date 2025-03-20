<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<div class="card">
    <div class="card-body card-body-scrollable-content">

        <div class="row align-items-center">
            <div class="col">
                <h5 class="heading-color mb-4">My Goals</h5>
            </div>
        </div>
        @foreach($user_goals as $index => $goal)
        <div class="row">
            <div class="col-auto">
                <span class="count">{{ $index + 1 }}</span>
            </div>
            
            <div class="col" id="goal-{{ $goal->id }}-projects">
                <p>{{ $goal->goal }}</p>
                <p class="text-body-secondary">Weightage - {{ $goal->weightage }}</p>

            <?php
                if (!empty($goalWiseData) && isset($goalWiseData[$goal->id]) && count($goalWiseData[$goal->id]) > 0) 
                {
                   // print_r($goalWiseData); die();
                    $flag = 0;
                    foreach($goalWiseData[$goal->id] as $data)
                    {
            ?> 
                <div class="row goal-rating-div-{{ $goal->id }}{{ $flag+1 }}">
                    <div class="col-xxl-9">
                        <div class="card mb-4">
                            <div class="card-body card-body-goal">

                                <div class="row">
                                    <div class="col">
                                        <h5>
                                            <span class="heading-color">Goal Rating</span> 
                                            <?php if($flag == 0) { ?>
                                            <span class="bi bi-plus-circle-fill pointer text-primary add-project" data-goal-id="{{ $goal->id }}"></span>
                                            <input type="hidden"  name="hiddenCount{{ $goal->id }}" id="hiddenCount{{ $goal->id }}" value="<?=count($goalWiseData[$goal->id])?>" />
                                            <?php }else{ ?>
                                                <span class="bi bi-dash-circle-fill pointer text-primary ms-2 remove-project" data-goal-id="{{ $goal->id }}" 
                                                data-project-count="{{ $flag+1 }}"></span>
                                            <?php } ?>
                                        </h5>
                                    </div>
                                </div>

                                <div class="row project">
                                    <div class="col">
                                        <div class="row mb-3">
                                            <label  class="col-md-2 col-form-label">Rating <span class="text-danger">*</span></label>
                                            <div class="col-md-3">
                                                <select name="rating_{{ $goal->id }}" class="form-select">
                                                    <option value="">Select rating</option>                                                   
                                                    <option value="10" <?= ($data->rating == 10) ? 'selected' : '' ?>>Achieved</option>
                                                    <option value="5" <?= ($data->rating == 5) ? 'selected' : '' ?>>Partially Achieved</option>
                                                    <option value="1" <?= ($data->rating == 1) ? 'selected' : '' ?>>Not Achieved</option>
                                                    <option value="0" <?= ($data->rating == 0) ? 'selected' : '' ?>>Not Applicable</option>
                                                </select>
                                            </div>
                                            <!-- <label  class="col-md-2 col-form-label col-auto-xxl">Project Name</label>
                                            <div class="col-md-5">
                                                <select name="project_{{ $goal->id }}_{{ $flag+1 }}" class="form-select">
                                                    <option value="0" <?= ($data->parats_project_id == 0) ? 'selected' : '' ?>>Not Applicable</option>
                                                    @foreach($user_projects as $projectIndex => $project)
                                                        <option value="<?=$project->parats_project_id?>" <?= ($data->parats_project_id == $project->parats_project_id) ? 'selected' : '' ?>><?=$project->project_name?></option>
                                                    @endforeach 
                                                </select>
                                                
                                            </div> -->
                                        </div>

                                        <div class="row mb-3">
                                            <label  class="col-sm-2 col-form-label">Task Details</label>
                                            <div class="col-sm-10">
                                                <textarea  name="remarks_{{ $goal->id }}" class="form-control" placeholder="Enter Task details.."><?=$data->employee_comment?></textarea>
                                            </div>
                                        </div>

                                        <div class="row mb-3 align-items-center">
                                            <label  class="col-sm-2 col-form-label">Evidence</label>
                                            <div class="col-sm-5">
                                                <input class="form-control" type="file" name="evidence_{{ $goal->id }}_{{ $flag+1 }}" id="formFile">
                                                
                                            </div>
                                            <div class="col">
                                                @if(!empty($data->attachment))
                                                    <a href="{{ asset('storage/' . $data->attachment) }}" download >{{ basename($data->attachment) }}</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col offset-sm-2 text-danger">
                                                (Max file size 2MB, Allowed file types are  pdf,png,jpg)
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                

                            </div>
                          
                          
                        </div>
                    </div>
                </div>
                <?php  
            $flag++;
                    } // project div  
                }
                else
                {
            ?>

                <div class="row goal-rating-div-{{ $goal->id }}1">
                    <div class="col-xxl-9">
                        <div class="card mb-4">
                            <div class="card-body card-body-goal">

                                <div class="row">
                                    <div class="col">
                                        <h5>
                                            <span class="heading-color">Goal Rating</span> 
                                            
                                           
                                        </h5>
                                    </div>
                                </div>

                                <div class="row project">
                                    <div class="col">
                                        <div class="row mb-3">
                                            <label  class="col-md-2 col-form-label">Rating <span class="text-danger">*</span></label>
                                            <div class="col-md-3">
                                                <select name="rating_{{ $goal->id }}" class="form-select">
                                                    <option value="" selected>Select rating</option>
                                                    
                                                    <option value="10" >Achieved</option>
                                                    <option value="5" >Partially Achieved</option>
                                                    <option value="1" >Not Achieved</option>
                                                    <option value="0" >Not Applicable</option>
                                                </select>
                                            </div>
                                            <!-- <label  class="col-md-2 col-form-label col-auto-xxl">Project Name</label>
                                            <div class="col-md-5">
                                                <select name="project_{{ $goal->id }}_1" class="form-select">
                                                    <option value="0" >Not Applicable</option>
                                                    @foreach($user_projects as $projectIndex => $project)
                                                        <option value="<?=$project->parats_project_id?>"><?=$project->project_name?></option>
                                                    @endforeach 
                                                </select>
                                                
                                            </div> -->
                                        </div>

                                        <div class="row mb-3">
                                            <label  class="col-sm-2 col-form-label">Task Details</label>
                                            <div class="col-sm-10">
                                                <textarea  name="remarks_{{ $goal->id }}" class="form-control" placeholder="Enter Task details.."></textarea>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label  class="col-sm-2 col-form-label">Evidence</label>
                                            <div class="col-sm-5">
                                                <input class="form-control" type="file" name="evidence_{{ $goal->id }}" id="formFile">
                                            </div>
                                            <div class="row">
                                                <div class="col offset-sm-2 text-danger">
                                                    (Max file size 2MB, Allowed file types are  pdf,png,jpg)
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                </div>
            <?php 
                }
            ?>
            </div>
        </div>
        @endforeach

        <div style="padding-left:45px">
          <div class="row mt-3 mb-2">
            <div class="col-md-6">
              <p class="">Key Contributions</p>
              <textarea style="height:100px;" name="key_contribution" id="key_contribution" class="form-control"><?= $submittedGeneralData?->key_contributions ?? ''; ?></textarea>
            </div>
            <div class="col-md-6">
              <p class="">Appraiser Comments</p>              
              <textarea style="height:100px;" name="appraiser_comment" id="appraiser_comment" class="form-control"><?= $submittedGeneralData?->suggestions_for_improvement ?? ''; ?></textarea>
            </div>
          </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#finaliseButton').click(function(event) {
        event.preventDefault(); // Prevent form submission

        let missingRatings = [];
        $('select[name^="rating_"]').each(function(index) {
            if ($(this).val() === '') {
                missingRatings.push(index + 1);
            }
        });

        if (missingRatings.length > 0) {
            toastr.error("Please select ratings for goals  " + missingRatings.join(', '));
        } else {
            $('#appraisalForm').submit(); // Submit form if no validation errors
        }
    });
});
</script>



