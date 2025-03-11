<div class="card-body card-body-scrollable-content">
    <div class="row align-items-center">
        <div class="col">
            <h5>My Goals</h5>
            <hr>
        </div>
    </div>



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
    <th>SL NO.</th>
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
            <select name="rating_{{ $project->parats_project_id }}_{{ $goal->id }}" class="form-select">
                <option value="" selected>Select rating</option>
                <option value="0" >Not Applicable</option>
                <option value="10" >Achieved</option>
                <option value="5" >Partially Achieved</option>
                <option value="1" >Not Achieved</option>
            </select>
        </td>
        <td width="350">
            <textarea name="remarks_{{ $project->parats_project_id }}_{{ $goal->id }}" id="" class="form-control" placeholder="comments" style="height: 83px;"></textarea>
            <input class="form-control" type="file" name="evidence_{{ $project->parats_project_id }}_{{ $goal->id }}" style=" margin-top: 15px;">
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
    </div>
    </div>
    <div class="row">
    <div class="col">
    <textarea   name="taskdetails{{ $project->parats_project_id }}" class="form-control" placeholder="Task Details" style="height: 83px;"></textarea>
    </div>
    </div>
    </div>
    </div>
    <!---- ends here --->
@endforeach







</div>