  @extends('layouts.app')
  @section('content')

        <div class="row">
          <div class="col">
           @include('layouts.sidebarmenu') 
  
            <div class="tab-content tab-content-custom" id="myTabContent">
              <div class="tab-pane fade show active" id="cover-pane" role="tabpanel" aria-labelledby="cover"
                tabindex="0">
                <div class="card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col">
                        <h3 class="heading-color mb-0">Employee Details</h3>
                      </div>
                      <div class="col text-end">

                        <button type="button" class="btn btn-light mx-2">Cancel</button>
                        <button type="button" class="btn btn-primary">Submit</button>
                      </div>
                    </div>
                    <hr >
                    <div class="spacer-md">
                    <div class="row">
                      <div class="col-md-auto vertical-divider">
                        <div class="row align-items-center">
                          <div class="col-auto ">

                            @if ($user->profile_pic!='' && $user->profile_pic!= 'noimageMale.jpg' && $user->profile_pic!='noimageFeMale.jpg')
                                <img src="{{ env('HEADS_URL') . $user->profile_pic}}" alt="" class="profile-pic-medium">
                              @else
                                <img src="{{ asset('assets/images/picture-profile.jpg') }}" alt="" class="profile-pic-medium">
                            @endif
                            
                          </div>
                          <div class="col">
                            <h4 class="heading-color mb-0"> {{ $user->name}}</h4>
                            <p class="text-body-secondary">{{ $user->emp_code}}</p>
                            <h5>{{ $user->designation_name }}</h5>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg">
                        <div class="row">
                          <div class="col-auto">
                            <p class="text-body-secondary description-head">Date of Joining</p>
                          </div>
                          <div class="col">
                            <p>{{ $user->date_of_join}}</p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-auto">
                            <p class="text-body-secondary description-head">Appraisal Period</p>
                          </div>
                          <div class="col">
                            <p>{{ $appraisalData->appraisal_period }}</p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col col-auto">
                            <p class="text-body-secondary description-head">Appraising officer</p>
                          </div>
                          <div class="col">
                            <p>{{$appraiserOfficerName}}</p>
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
              </div>


              <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">


                @include('goals_listing_page') 

              </div>

              <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab"
                tabindex="0">tab 3</div>

              <div class="tab-pane fade" id="assign-tab-pane" role="tabpanel" aria-labelledby="assign-tab"
                tabindex="0">
                  
                   @include('assign-admin') 
              </div>

              <div class="tab-pane fade" id="assign-tab-pane" role="tabpanel" aria-labelledby="assign-tab"
                tabindex="0">
                  
                   @include('assign-admin') 
              </div>

            </div>
          </div>
        </div>
  @endsection