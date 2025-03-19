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
                <a class="nav-link border-bottom gap-2 active" href="/my-appraisal">

                  <i class="bi bi-person-up" ></i>
                  <div>
                    My Appraisal
                  </div>
                  <span></span>
                </a>
              </li>
              <!-- <li class="nav-item">
                <a class="nav-link border-bottom gap-2" href="#">
                  <div>
                    <svg class="review-icon" viewBox="0 0 40 40" fill="none"
                      xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M23.3334 5V11.6667C23.3334 12.1087 23.509 12.5326 23.8215 12.8452C24.1341 13.1577 24.558 13.3333 25 13.3333H31.6667"
                        stroke="#3A3A3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      <path
                        d="M20 35H11.6667C10.7827 35 9.93481 34.6488 9.30968 34.0237C8.68456 33.3986 8.33337 32.5507 8.33337 31.6667V8.33333C8.33337 7.44928 8.68456 6.60143 9.30968 5.97631C9.93481 5.35119 10.7827 5 11.6667 5H23.3334L31.6667 13.3333V20.8333"
                        stroke="#3A3A3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      <path
                        d="M23.3334 29.1667C23.3334 30.2717 23.7724 31.3315 24.5538 32.1129C25.3352 32.8943 26.395 33.3333 27.5 33.3333C28.6051 33.3333 29.6649 32.8943 30.4463 32.1129C31.2277 31.3315 31.6667 30.2717 31.6667 29.1667C31.6667 28.0616 31.2277 27.0018 30.4463 26.2204C29.6649 25.439 28.6051 25 27.5 25C26.395 25 25.3352 25.439 24.5538 26.2204C23.7724 27.0018 23.3334 28.0616 23.3334 29.1667Z"
                        stroke="#3A3A3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M30.8334 32.5L35 36.6667" stroke="#3A3A3A" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </div>
                  Review
                </a>
              </li> -->
              <!-- <li class="nav-item">
                <a class="nav-link border-bottom gap-2" href="#">
                  <i class="bi bi-person-check"></i>
                  Appraiser
                </a>
              </li> -->

              <!-- <li class="nav-item">
                <a class="nav-link border-bottom gap-2" href="{{ route('administration')}}">
                  <i class="bi bi-gear"></i>
                  Administration
                </a>
              </li> -->
              @if(session('logged_user_role') == 1 || session('logged_user_role') == 2)
                <li class="nav-item dropend">
                    <a class="nav-link border-bottom gap-2 dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
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