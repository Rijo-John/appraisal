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
                                                    <!-- <option value="">Select rating</option>    -->                                                
                                                    <option value="4" <?= ($data->rating == 4) ? 'selected' : '' ?>>4</option>
                                                    <option value="3" <?= ($data->rating == 3) ? 'selected' : '' ?>>3</option>
                                                    <option value="2" <?= ($data->rating == 2) ? 'selected' : '' ?>>2</option>
                                                    <option value="1" <?= ($data->rating == 1) ? 'selected' : '' ?>>1</option>
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
                                                <input class="form-control" type="file" name="evidence_{{ $goal->id }}" id="formFile">
                                                
                                            </div>
                                            @if(!empty($data->attachment))
                                            <div class="col" id="evidencediv_{{ $goal->id }}">
                                                    <a href="{{ asset('storage/' . $data->attachment) }}" download >{{ basename($data->attachment) }}</a>
                                                    @if($selfFinalise !=1)
                                                    <i class="bi bi-x ms-1  delete-attachment" data-bs-toggle="modal"
                                                      data-bs-target="#deleteAttachmentModal" 
                                                      data-goal-id="{{$goal->id }}" 
                                                      data-goal-rating-id="{{ $data->id }}"
                                                      title="Delete"
                                                      style="font-size: 17px; cursor: pointer;">
                                                    </i>
                                                    @endif
                                            </div>
                                            @endif
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
                                                    <!-- <option value="" selected>Select rating</option> -->
                                                    
                                                    <option value="4" >4</option>
                                                    <option value="3" >3</option>
                                                    <option value="2" >2</option>
                                                    <option value="1" selected>1</option>
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
            <div class="modal fade" id="deleteAttachmentModal" tabindex="-1" aria-labelledby="deleteAttachmentModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteAttachmentModalLabel">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this evidence?<br>
                            Once deleted, the file will be permanently removed and cannot be recovered.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDelete"><div class="d-flex align-items-center">Delete <div class="loader ms-2 delete-loader" style="display:none;"></div></div></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div style="padding-left:45px">
          <div class="row mt-3 mb-2">
            <div class="col-md-6">
              <p class="">Key contributions/achievements</p>
              <textarea style="height:100px;" name="key_contribution" id="key_contribution" class="form-control"><?= $submittedGeneralData?->key_contributions ?? ''; ?></textarea>
            </div>
            <div class="col-md-6">
              <p class="">Suggestions for improvement for the organization</p>              
              <textarea style="height:100px;" name="appraiser_comment" id="appraiser_comment" class="form-control"><?= $submittedGeneralData?->suggestions_for_improvement ?? ''; ?></textarea>
            </div>
          </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    
    let attachmentToDelete = "";
    let goalId = "";
    let goalRatingId = "";

    $(".delete-attachment").click(function () {
        goalId = $(this).data("goal-id");
        goalRatingId = $(this).data("goal-rating-id");
    });

    $("#confirmDelete").click(function () {
        $("#confirmDelete").prop("disabled", true);
        $(".delete-loader").show();
        $.ajax({
            url: "{{ route('deleteAttachment') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                goal_id: goalId,
                goal_rating_id: goalRatingId
            },
            success: function (response) {
                if (response.success) {                    
                    $("#evidencediv_"+goalId).remove();
                    $("#deleteAttachmentModal").modal("hide");
                    toastr.success(`Evidence deleted successfully`);
                } else {
                  toastr.error(`Error deleting attachment.`);
                }
            },
            error: function () {
              toastr.error(`Something went wrong.`);
            }
        });
    });

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



