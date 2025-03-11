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
    <th>#</th>
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
    <div class="col-xxl-12">
    <table class="table table-bordered">
    <thead>
    <tr>
    <th>#</th>
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

                    <div class="row">
                      <div class="col mt-4">
                        <h5 class="mb-2">Learning Goal</h5>
                        <table class="table table-bordered">
                          <thead>
                          <tr>
                              <th>#</th>
                              <th>Leaning Goals</th>
                              <th>Overall Weightage</th>
                              <th>Goal Status</th> 
                          </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>
                            <h6>Learning & Development</h6>
                            <p>Completing a mandatory 50 hours learning annually with at least 25% of training through Vigyan</p>
                          </td>
                          <td>50</td>
                          <td>Acheived</td>
                        </tr>
                      </tbody>
                      </table>
                      
                      <div class="row mt-3 mb-2">
                        <div class="col">
                          <p class="">Key Contributions</p>
                          <textarea name="" id="" class="form-control"></textarea>
                        </div>
                        <div class="col">
                          <p class="">Suggestions for improvement</p>
                          <textarea name="" id="" class="form-control"></textarea>
                        </div>
                      </div>
                      </div>
                    </div>



</div>