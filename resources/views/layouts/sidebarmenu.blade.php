<ul class="nav  nav-tabs-custom" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <a class="nav-link active" id="cover" data-bs-toggle="tab" data-bs-target="#cover-pane" type="button"
                  role="tab" aria-controls="cover-pane" aria-selected="true">Cover</a>
              </li>
              
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="employee-goals" data-bs-toggle="tab" data-bs-target="#employee-goals-pane"
                  type="button" role="tab" aria-controls="employee-goals-pane" aria-selected="false">My Goals</a>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="employee-tasks" data-bs-toggle="tab" data-bs-target="#employee-tasks-pane"
                  type="button" role="tab" aria-controls="employee-tasks-pane" aria-selected="false">Tasks</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="attribute-review" data-bs-toggle="tab" data-bs-target="#attribute-review-pane"
                  type="button" role="tab" aria-controls="attribute-review-pane" aria-selected="false">Attribute Review</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="value-creation" data-bs-toggle="tab" data-bs-target="#value-creation-pane"
                  type="button" role="tab" aria-controls="value-creation-pane" aria-selected="false">Value Creation</button>
              </li>
              @if(Auth::check() && Auth::user()->role == 1)
              <!-- <li class="nav-item" role="presentation">
                <a href="{{ route('assign.admin') }}" class="nav-link" id="assignadmin" data-bs-toggle="tab" data-bs-target="#assign-tab-pane" type="button"
                  role="tab" aria-controls="assign-tab-pane" aria-selected="true">Assign Admin</a>
              </li> -->
              @endif

              @if(Auth::check() && (Auth::user()->role == 1 || Auth::user()->role == 2))
              <!-- <li class="nav-item" role="presentation">
                <a href="{{ route('appraisal.view') }}" class="nav-link" id="appraisal" data-bs-toggle="tab" data-bs-target="#appraisal-tab-pane" type="button"
                  role="tab" aria-controls="appraisal-tab-pane" aria-selected="true">Appraisal</a>
              </li> -->
              @endif
            </ul>