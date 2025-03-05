<ul class="nav  nav-tabs-custom" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <a class="nav-link active" id="cover" data-bs-toggle="tab" data-bs-target="#cover-pane" type="button"
                  role="tab" aria-controls="cover-pane" aria-selected="true">Cover</a>
              </li>
              
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                  type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">My Goals</a>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="my-goals" data-bs-toggle="tab" data-bs-target="#contact-tab-pane"
                  type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Project
                  Review</button>
              </li>
              @if(Auth::check() && Auth::user()->role == 1)
              <li class="nav-item" role="presentation">
                <a href="{{ route('assign.admin') }}" class="nav-link" id="assignadmin" data-bs-toggle="tab" data-bs-target="#assign-tab-pane" type="button"
                  role="tab" aria-controls="assign-tab-pane" aria-selected="true">Assign Admin</a>
              </li>
              @endif

              @if(Auth::check() && (Auth::user()->role == 1 || Auth::user()->role == 2))
              <li class="nav-item" role="presentation">
                <a href="{{ route('appraisal.view') }}" class="nav-link" id="appraisal" data-bs-toggle="tab" data-bs-target="#appraisal-tab-pane" type="button"
                  role="tab" aria-controls="appraisal-tab-pane" aria-selected="true">Appraisal</a>
              </li>
              @endif
            </ul>