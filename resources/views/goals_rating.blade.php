
<div class="card">
    <div class="card-body">

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
                <div class="row">
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
                                                <span class="bi bi-dash-circle-fill pointer text-primary ms-2 remove-project"></span>
                                            <?php } ?>
                                        </h5>
                                    </div>
                                </div>

                                <div class="row project">
                                    <div class="col">
                                        <div class="row mb-3">
                                            <label  class="col-md-2 col-form-label">Rating</label>
                                            <div class="col-md-3">
                                                <select name="rating_{{ $goal->id }}_{{ $flag+1 }}" class="form-select">
                                                    <option value="">Select rating</option>
                                                    <option value="0" <?= ($data->rating == 0) ? 'selected' : '' ?>>Not Applicable</option>
                                                    <option value="10" <?= ($data->rating == 10) ? 'selected' : '' ?>>Achieved</option>
                                                    <option value="5" <?= ($data->rating == 5) ? 'selected' : '' ?>>Partially Achieved</option>
                                                    <option value="1" <?= ($data->rating == 1) ? 'selected' : '' ?>>Not Achieved</option>
                                                </select>
                                            </div>
                                            <label  class="col-md-2 col-form-label col-auto-xxl">Project Name</label>
                                            <div class="col-md-5">
                                                <select name="project_{{ $goal->id }}_{{ $flag+1 }}" class="form-select">
                                                    <option value="0" <?= ($data->parats_project_id == 0) ? 'selected' : '' ?>>Not Applicable</option>
                                                    @foreach($user_projects as $projectIndex => $project)
                                                        <option value="<?=$project->parats_project_id?>" <?= ($data->parats_project_id == $project->parats_project_id) ? 'selected' : '' ?>><?=$project->project_name?></option>
                                                    @endforeach 
                                                </select>
                                                
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label  class="col-sm-2 col-form-label">Task Details</label>
                                            <div class="col-sm-10">
                                                <textarea  name="remarks_{{ $goal->id }}_{{ $flag+1 }}" class="form-control"><?=$data->employee_comment?></textarea>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label  class="col-sm-2 col-form-label">Attachments</label>
                                            <div class="col-sm-5">
                                                <input class="form-control" type="file" name="evidence_{{ $goal->id }}_{{ $flag+1 }}" id="formFile">
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

                <div class="row">
                    <div class="col-xxl-9">
                        <div class="card mb-4">
                            <div class="card-body card-body-goal">

                                <div class="row">
                                    <div class="col">
                                        <h5>
                                            <span class="heading-color">Goal Rating</span> 
                                            <span class="bi bi-plus-circle-fill pointer text-primary add-project" data-goal-id="{{ $goal->id }}"></span>
                                            <input type="hidden"  name="hiddenCount{{ $goal->id }}" id="hiddenCount{{ $goal->id }}" value="1" />
                                        </h5>
                                    </div>
                                </div>

                                <div class="row project">
                                    <div class="col">
                                        <div class="row mb-3">
                                            <label  class="col-md-2 col-form-label">Rating</label>
                                            <div class="col-md-3">
                                                <select name="rating_{{ $goal->id }}_1" class="form-select">
                                                    <option value="" selected>Select rating</option>
                                                    <option value="0" >Not Applicable</option>
                                                    <option value="10" >Achieved</option>
                                                    <option value="5" >Partially Achieved</option>
                                                    <option value="1" >Not Achieved</option>
                                                </select>
                                            </div>
                                            <label  class="col-md-2 col-form-label col-auto-xxl">Project Name</label>
                                            <div class="col-md-5">
                                                <select name="project_{{ $goal->id }}_1" class="form-select">
                                                    <option value="0" >Not Applicable</option>
                                                    @foreach($user_projects as $projectIndex => $project)
                                                        <option value="<?=$project->parats_project_id?>"><?=$project->project_name?></option>
                                                    @endforeach 
                                                </select>
                                                
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label  class="col-sm-2 col-form-label">Task Details</label>
                                            <div class="col-sm-10">
                                                <textarea  name="remarks_{{ $goal->id }}_1" class="form-control"></textarea>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label  class="col-sm-2 col-form-label">Attachments</label>
                                            <div class="col-sm-5">
                                                <input class="form-control" type="file" name="evidence_{{ $goal->id }}_1" id="formFile">
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


    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    debugger;
    
    let projectOptions = `
        <option value="0">Not Applicable</option>
        @foreach($user_projects as $project)
            <option value="{{ $project->parats_project_id }}">{{ $project->project_name }}</option>
        @endforeach
    `;

    $(".add-project").on("click", function () {
        let goalId = $(this).data("goal-id");
        let projectWrapper = $(`#goal-${goalId}-projects`);
        let hiddenCount = $(`#hiddenCount${goalId}`);

        let incrementValue = parseInt(hiddenCount.val()) + 1;
        hiddenCount.val(incrementValue);

        let newProject = $(`
        <div class="row">
                    <div class="col-xxl-9">
                        <div class="card mb-4">
                            <div class="card-body card-body-goal">

                                <div class="row">
                                    <div class="col">
                                        <h5>
                                            <span class="heading-color">Goal Rating</span> 
                                            <span class="bi bi-dash-circle-fill pointer text-primary ms-2 remove-project"></span>
                                            
                                        </h5>
                                    </div>
                                </div>
        <div class="row project">
            <div class="col">
                <div class="row mb-3">
                    <label  class="col-md-2 col-form-label">Rating</label>
                    <div class="col-md-3">
                    <select name="rating_${goalId}_${incrementValue}" class="form-select">
                        <option value="">Select rating</option>
                        <option value="0">Not Applicable</option>
                        <option value="10">Achieved</option>
                        <option value="5">Partially Achieved</option>
                        <option value="1">Not Achieved</option>
                    </select>
                    </div>
                    <label  class="col-md-2 col-form-label col-auto-xxl">Project Name</label>
                <div class="col-md-5">
                    
                    <select name="project_${goalId}_${incrementValue}" class="form-select">
                             ${projectOptions}
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label  class="col-sm-2 col-form-label">Task Details</label>
                    <div class="col-sm-10">
                        <textarea  name="remarks_${goalId}_${incrementValue}" class="form-control"></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <label  class="col-sm-2 col-form-label">Attachments</label>
                    <div class="col-sm-5">
                        <input class="form-control" type="file" name="evidence_${goalId}_${incrementValue}" id="formFile_${goalId}_${incrementValue}">
                    </div>
                </div>
                
            </div>
            </div>
                          
                          
                        </div>
                    </div>
                </div>
        `);

        projectWrapper.append(newProject);
    });

    // Remove dynamically added project
    // $(document).on("click", ".remove-project", function () {
    //     $(this).parent().remove();
    // });
    $(document).on("click", ".remove-project", function (event) {
        event.preventDefault(); // Prevents the page from jumping or reloading
        $(this).parent().remove();
    });

});


</script>