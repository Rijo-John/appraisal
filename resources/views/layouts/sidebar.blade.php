<div class="sidebar border border-right col-auto ">
        <div class="offcanvas-md offcanvas-end " tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">
              <img src="{{ asset('assets/images/appraisal-management-logo.png') }}">
            </h5>

            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu"
              aria-label="Close"></button>
          </div>
          <div class="offcanvas-body d-md-flex flex-column pt-lg-3">
            <ul class="nav flex-column">
              <li class="nav-item">
              @if(session()->has('appraisal_category'))
                  @if(session('appraisal_category') == 1 || session('appraisal_category') == 2)
                    <a class="nav-link border-bottom gap-2 {{ Request::is('my-appraisal') ? 'active' : '' }}" href="/my-appraisal">
                  @elseif(session('appraisal_category') == 3)
                    <a class="nav-link border-bottom gap-2 {{ Request::is('my-appraisal') ? 'active' : '' }}" href="/myapp">
                  @else
                    <a class="nav-link border-bottom gap-2 {{ Request::is('my-appraisal') ? 'active' : '' }}" href="/nopermission">
                  @endif
                @else
                <a class="nav-link border-bottom gap-2 {{ Request::is('my-appraisal') ? 'active' : '' }}" href="/nopermission">
              @endif
                  <i class="bi bi-person-up" ></i>
                  <div>
                    My Appraisal
                  </div>
                  <span></span>
                </a>
              </li>
              

              @if(session('logged_user_role') == 1 || session('logged_user_role') == 2)
                <li class="nav-item dropend">
                    <a class="nav-link border-bottom gap-2 dropdown-toggle {{ Request::is('assign-admin') || Request::is('getappraisaldata') ? 
                    'active' : '' }}" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i>
                        Administration
                        <span></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-start-sm">
                        @if(session('logged_user_role') == 1)
                            <li><a class="dropdown-item" href="{{ route('assign.admin') }}">Assign Admin</a></li>
                        @endif
                        @if(session('logged_user_role') == 2 || session('logged_user_role') == 1)
                            <li><a class="dropdown-item" href="{{ route('appraisaldata') }}">Appraisal</a></li>
                        @endif
                    </ul>
                </li>
              @endif

              <li class="nav-item">
                <a class="nav-link border-bottom gap-2 {{ Request::is('list*') ? 'active' : '' }}" href="{{ route('list') }}">
                  
                    <i class="bi bi-person-check"></i>

                    Appraiser
                    <span></span>
                </a>
              </li>

            </ul>

           


            <ul class="nav flex-column logout-btn">

              <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button class="nav-link border-top gap-2" >
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                  </button>
              </form>
              </li>

             
            </ul>
          </div>
        </div>
      </div>