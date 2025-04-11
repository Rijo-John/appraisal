<?php 
//echo '<pre>'; print_r($vigyanCourseDetails); die();
?>

<div class="card">
    <div class="card-body card-body-scrollable-content">
        <div class="row">
            <div class="col mt-4">
                <h5 class="mb-2">Training & certifications</h5> 
                <hr>
                <!-- <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Learning Goals</th>
                        
                        <th class="text-center">Goal Status</th> 
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                    <h6>Learning & Development</h6>
                    <p>Completing a mandatory 50 hours learning annually with at least 25% of training through Vigyan</p>
                    </td>
                    
                    <td class="text-center">{{ $vigyanCourseDetails['calculatePercentage']}}%</td>
                </tr>
                </tbody>
                </table> -->
            </div>
        </div> 
        <div class="row align-items-center">
            <div class="col">
                <h5></h5>
                <h6 class="mt-5 mb-3">Vigyan Trainings and Other Trainings</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr class="align-top">
                            <th>Category</th>
                            <th>Program</th>
                            <th>Hours Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Vigyan</td>
                            <td>
                                <ol>
                                    @if(count($vigyanCourseDetails['courses']) > 0)
                                        @foreach($vigyanCourseDetails['courses']  as $vigyan) 
                                            <li> 
                                            {{ $vigyan }}
                                            </li>
                                        @endforeach 
                                    @else
                                        <li>-</li>
                                    @endif
                                </ol>
                            </td>
                            <td>
                            {{ $vigyanCourseDetails['timeSpent']}}
                           
                           
                            </td>
                        </tr>
                        <tr>
                            <td>Training</td>
                            <td>
                                <ol>
                                    @if(count($vigyanCourseDetails['training_name']) > 0)
                                    @foreach($vigyanCourseDetails['training_name']  as $training) 
                                            <li> 
                                            {{ $training }}
                                            </li>
                                        @endforeach 
                                    @else
                                        -
                                    @endif
                                </ol>
                            </td>
                            <td>{{ $vigyanCourseDetails['trainingTimeSpent']}}</td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td></td>
                            <td>{{ $vigyanCourseDetails['totalVigyanTimeSpent']}} </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" id="number_of_clicks" value="0" >
                <h6 class="mt-5 mb-3"></h6>
                <table class="table table-bordered">
                    <thead>
                        <tr class="align-top">
                            <th>Category</th>
                            <th>
                                <div class="row g-0">
                                    <div class="col">
                                    Details
                                    </div>
                                    <div class="col-auto "><span class="text-right heading-color pointer" style="font-weight: normal;" id="refresh_certification"><i class="bi bi-arrow-repeat"></i> Refresh</span>
                                </div></div>
                            </th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="40%">Relevant education qualification / certifications Achieved during the appraisal period</td>
                            <td>
                                <ul id="certification-list">
                                    @if(count($certificationsfromHeads->AppraisalCertListDataResponse) > 0)
                                    @foreach($certificationsfromHeads->AppraisalCertListDataResponse  as $certificate) 
                                            <li> 
                                            <span class="heading-color">{{ $certificate->Certification }}</span><small class="text-body-secondary ms-2">Issued on {{ $certificate->IssuedDate }}</small>
                                            <p class="font-small">{{ $certificate->Description }}</p>
                                            </li>
                                        @endforeach 
                                    @else
                                        <li>-</li>
                                    @endif


                                </ul>
                            <i style="font-size:10px;">If your certifications and educational qualification are  not listed here please update the same in HEADS  and click on refresh bottom</i>
                            </td>
                        </tr>
                        <tr>
                            <td>Enter the workshops/seminars attended</td>
                            <td>
                            @php
                                $workshops_attended = isset($general_data) ? ($general_data->workshops_attended ?? '') : '';
                                $trainings_conducted = isset($general_data) ? ($general_data->trainings_conducted ?? '') : '';
                            @endphp
                                <textarea name="employee_workshops"  class="form-control" placeholder="Enter your Workshops / Seminars attended"><?=$workshops_attended?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Trainings / Mentoring conducted during the appraisal period</td>
                            <td>
                                <textarea name="employee_training_conducted" class="form-control" placeholder="Enter your Trainings / Mentoring conducted"><?=$trainings_conducted?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>