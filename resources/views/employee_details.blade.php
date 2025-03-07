
<div class="card">
    <div class="card-body">
                    
                    
        <div class="spacer-md">
            <div class="row align-items-center">
            <div class="col">
                <h5 class="heading-color mb-4">Employee Details</h5>
            </div>
            
            </div>
        <div class="row">
            <div class="col-md-auto vertical-divider">
            <div class="row align-items-center">
                <div class="col-auto ">
                @if ($employeeData['profile_pic']!='' && $employeeData['profile_pic']!= 'noimageMale.jpg' && $employeeData['profile_pic']!='noimageFeMale.jpg')
                    <img src="{{ env('HEADS_URL') . $employeeData['profile_pic']}}" alt="" class="profile-pic-medium">
                    @else
                    <img src="{{ asset('assets/images/picture-profile.jpg') }}" alt="" class="profile-pic-medium">
                @endif
                </div>
                <div class="col">
                <p class=" mb-0"> <strong>{{ $employeeData['name'] }}</strong></p>
                <p class="text-body-secondary">{{ $employeeData['emp_code'] }}</p>
                <h5>{{ $employeeData['designation_name'] }}</h5>
                </div>
            </div>
            </div>
            <div class="col-lg">
            <div class="row">
                <div class="col-auto">
                <p class="text-body-secondary description-head">Date of Joining</p>
                </div>
                <div class="col">
                <p>{{ $employeeData['date_of_join'] }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-auto">
                <p class="text-body-secondary description-head">Appraisal Period</p>
                </div>
                <div class="col">
                <p>{{ $employeeData['appraisal_period'] }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col col-auto">
                <p class="text-body-secondary description-head">Appraising officer</p>
                </div>
                <div class="col">
                <p>{{ $employeeData['appraiserOfficerName'] }}</p>
                </div>
            </div>
            </div>
        </div>
        <hr class="hr-space-xl">
        <h4 class="heading-color mb-3">Instructions</h4>
        <ol class="list-number">
            <li>Appraisee needs to enter details specified in Task review sheet & Employee General sheet</li>
            <li>Appraisee shall enter details of task completed during appraisal period and rate the same on a
            scale of 1-10 (10 being the highest).</li>
            <li>Appraisee needs to list major achievements in project.</li>
            <li>If appraisee has participated in more than one project. Please click on the "New Project"
            button provided and add details of all other projects</li>
            <li>It is the Appraisees duty to ensure that all sections pertaining to his/her entry should be
            filled</li>
        </ol>
        </div>
        </div>
    </div>