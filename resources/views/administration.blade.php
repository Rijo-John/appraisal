  @extends('layouts.app')
  @section('content')

        <div class="row">
          <div class="col">
           
  
            <div class="tab-content tab-content-custom" id="myTabContent">
              

              <div class="tab-pane fade" id="assign-tab-pane" role="tabpanel" aria-labelledby="assign-tab"
                tabindex="0">

                   @include('assign-admin') 

              </div>

              <div class="tab-pane fade" id="appraisal-tab-pane" role="tabpanel" aria-labelledby="appraisal-tab"
                tabindex="0">
                   @include('appraisal_master') 
                   
              </div>

            </div>
          </div>
        </div>
  @endsection