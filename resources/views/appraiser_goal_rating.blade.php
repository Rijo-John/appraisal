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
   
    
        <!------ project div starts here   ----------->
        @if (!empty($employeeratingdata ) && count($employeeratingdata ) > 0)
        @php $icount = 1; @endphp
        @foreach($employeeratingdata as $projectId => $goals)
            @php
                if($projectId == -1) {
                    $projectName = "General";
                } else {
                    $projectName = '';
                    $projectName = explode('-', $goals[0]->project_name)[0];
                    $projectName = trim($projectName); 
                }
            @endphp
        <input type="hidden" name="appraisee_heads_id" value="{{ $employeeData['appraisee_heads_id'] }}" >
        <div class="row">
            <div class="col">
                <div class="row align-items-center mb-3">
                    <div class="col-auto">
                        <span class="count">{{ $icount }}</span>
                    </div>
                    <div class="col ps-0">
                        <h6 class="mb-0"><span>{{ $projectName }} </span></h6>
                    </div>
                </div>
                <!--------- goal starts here ------------>
                
                <div style="padding-left:34px">
                    <div class="card mb-3">
                    @foreach($goals  as $index => $goal)  
                        <input type="hidden" name="employee_goal_rating_ids[]" value="{{ $goal->id }}">
                        <div class="card-body card-body-goal">
                            <div class="row">
                                <div class="col" style="max-width:150px"><p class="mb-2"><strong>Goal {{ $index+1 }}</strong></p></div>
                                <div class="col"><p class="mb-2">{{ $goal->goal }}</p></div>
                            </div>

                            <div class="row">
                                <div class="col" style="max-width:150px"><p class="mb-2">Appraisee Rating</p></div>
                                <div class="col"><p class="mb-2"><strong>{{ $goal->rating ?? 'N/A' }}</strong></p></div>
                            </div>
                            <div class="row">
                                <div class="col" style="max-width:150px"><p  class="mb-2">Task Details</p></div>
                                <div class="col"><p class="mb-2">{{ $goal->employee_comment ?? '' }}</p></div>
                            </div>
                            <div class="row">
                                <div class="col" style="max-width:150px"><p >Evidence uploaded</p></div>
                                <div class="col"><p class="heading-color ">filenaem.jpg</p></div>
                            </div>


                            <div class="row">
                                <div class="col-md-4">
                                    <select name="appraiser_rating_{{ $goal->id }}" class="form-select mb-2 project_goal_rating" >
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="0">Not Applicable</option>
                                    </select>
                                    <input class="form-control" type="file" name="evidence_{{ $goal->id }}" >
                                </div>
                                <div class="col-md-8">
                                    <textarea name="appraiser_remarks_{{ $goal->id }}" id="" class="form-control" placeholder="comments" style="height: 85px;"></textarea>
                                </div>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    <div class="row mb-4">
                        <div class="col">
                        <p class="mb-1">Task Details</p>
                        <textarea   name="taskdetails_{{ $projectId }}" class="form-control" placeholder="Task Details" style="height: 83px;"></textarea>
                        </div>
                    </div>
                </div>
                <!-- goals ends here -->

            </div>
        </div>
        @php $icount++; @endphp
        @endforeach
        @endif
        <!---- ends here --->
    
                
    
        <div class="row mt-3 mb-12">
            <div class="col">
                <p class="">Key Contributions</p>
                <textarea name="key_contributions"  style="height: 83px;" class="form-control">fgdg</textarea>
            </div>
            <div class="col">
                <p class="">Suggestions for Organization’s Improvement</p>
                <textarea name="suggestions_for_improvement"  class="form-control" style="height: 83px;">dfsgdgdh</textarea>
            </div>
        </div>


                <!-- Delete Confirmation Modal -->
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
                <!-- Delete Confirmation Modal -->

    </div>
</div>

