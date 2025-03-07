<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goals & Projects</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .goal-container {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .project {
            margin-left: 20px;
        }
        textarea {
            width: 30%;
            height: 60px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h2>Goals</h2>
    @foreach($user_goals as $index => $goal)
    <div class="goal-container">
        <h3>Goal {{ $index + 1 }}: {{ $goal->goal }}</h3>

        <div class="project-wrapper" id="goal-{{ $goal->id }}-projects">
            <?php
                if (!empty($goalWiseData) && isset($goalWiseData[$goal->id]) && count($goalWiseData[$goal->id]) > 0) 
                {
                    $flag = 0;
                    foreach($goalWiseData[$goal->id] as $data)
                    {
            ?>   
                <div class="project">
                        <select name="project_{{ $goal->id }}_{{ $flag+1 }}">
                            <option value="0" <?= ($data->parats_project_id == 0) ? 'selected' : '' ?>>Not Applicable</option>
                            @foreach($user_projects as $projectIndex => $project)
                                <option value="<?=$project->parats_project_id?>" <?= ($data->parats_project_id == $project->parats_project_id) ? 'selected' : '' ?>><?=$project->project_name?></option>
                            @endforeach 
                        </select>
                    
                        <!-- Project Selection Dropdown -->
                        <select name="rating_{{ $goal->id }}_1">
                            <option value="0" <?= ($data->rating == 0) ? 'selected' : '' ?>>Not Applicable</option>
                            <option value="10" <?= ($data->rating == 10) ? 'selected' : '' ?>>Achieved</option>
                            <option value="5" <?= ($data->rating == 5) ? 'selected' : '' ?>>Partially Achieved</option>
                            <option value="1" <?= ($data->rating == 1) ? 'selected' : '' ?>>Not Achieved</option>
                        </select>

                        <textarea name="remarks_{{ $goal->id }}_1" placeholder="Add notes about this project..."><?=$data->employee_comment?></textarea>

                    
                        <input type="file" name="evidence_{{ $goal->id }}_1" multiple>

                        <?php if($flag == 0) { ?>
                        <button type="button" class="add-project" data-goal-id="{{ $goal->id }}">+ Add Project</button>
                        <input type="hidden"  name="hiddenCount{{ $goal->id }}" id="hiddenCount{{ $goal->id }}" value="<?=count($goalWiseData[$goal->id])?>" />
                        <?php }else{ ?>
                            <button type="button" class="remove-project">Remove</button>
                        <?php } ?>
                </div>
                    
            <?php  
            $flag++;
                    } // project div  
                }
                else
                {
            ?>
                <div class="project">
                    <input type="hidden" value="1" name="hiddenCount{{ $goal->id }}" id="hiddenCount{{ $goal->id }}" />
                        <select name="project_{{ $goal->id }}_1">
                            <option value="0">Not Applicable</option>
                            @foreach($user_projects as $projectIndex => $project)
                                <option value="10"><?=$project->project_name?></option>
                            @endforeach 
                        </select>
                    
                    <!-- Project Selection Dropdown -->
                    <select name="rating_{{ $goal->id }}_1">
                        <option value="0">Not Applicable</option>
                        <option value="10">Achieved</option>
                        <option value="5">Partially Achieved</option>
                        <option value="1" selected>Not Achieved</option>
                    </select>

                    <textarea name="remarks_{{ $goal->id }}_1" 
                              placeholder="Add notes about this project..."></textarea>

                    
                    <input type="file" name="evidence_{{ $goal->id }}_1" multiple>
                    
                    <button type="button" class="add-project" data-goal-id="{{ $goal->id }}">+ Add Project</button>
                </div>
            <?php 
                }
            ?>
        </div>
        
        
    </div>
    @endforeach
    <div class="goal-container">
        <h3>General Contributions</h3>
        <div class="project-wrapper">
            <table border="1" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 50%;">Learning Goal</th>
                        <th style="width: 25%;align:center">Overall Weightage(W)</th>
                        <th style="width: 25%;">Goal Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Learning & Development
                            <br>Completing a mandatory 50 hours of learning annually, with at least 25% of training through Vigyan</td>
                        <td style="text-align:center">5</td>
                        <td style="text-align:center">90%</td>
                        
                    </tr>
                   
                </tbody>
            </table>

        </div>

        <div class="project-wrapper" style="margin-top: 25px;">
            <label>Key Contributions (To be filled by Appraisee)</label><br>
            <textarea name="remarks_" placeholder=""></textarea>
        </div>

        <div class="project-wrapper" style="margin-top: 25px;">
            <div class="project-wrapper">
                <div>
                    <label>Suggestions for Improvements of the Organization (Appraisee)</label>
                </div>
                <textarea name="remarks_" placeholder=""></textarea>
            </div>

        </div>
        <div class="project-wrapper" style="margin-top: 25px;">
            <div class="project-wrapper">
                <div>
                    <label>Appraiser Comments</label>
                </div>
                <textarea name="remarks_" placeholder=""></textarea>
            </div>

        </div>
    </div>


    <button type="submit">Submit</button>
</form>
</body>

<script>
$(document).ready(function () {
    debugger;
    
    let projectOptions = `
        <option value="">Select Project</option>
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
            <div class="project">
                <select name="project_${goalId}_${incrementValue}">
                    <option value="0">Not Applicable</option>
                    ${projectOptions}
                </select>

                <select name="rating_${goalId}_${incrementValue}">
                    <option value="0">Not Applicable</option>
                    <option value="10">Achieved</option>
                    <option value="5">Partially Achieved</option>
                    <option value="1" selected>Not Achieved</option>
                </select>

                <textarea name="remarks_${goalId}_${incrementValue}" placeholder="Add notes about this project..."></textarea>

                <input type="file" name="evidence_${goalId}_${incrementValue}" multiple>

                <button type="button" class="remove-project">Remove</button>
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
</html>
