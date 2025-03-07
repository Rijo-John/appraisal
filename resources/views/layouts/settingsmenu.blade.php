<ul class="nav  nav-tabs-custom" id="myTab" role="tablist">
            
             
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