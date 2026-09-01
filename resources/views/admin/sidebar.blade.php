<!-- Main Sidebar Container -->

<aside class="main-sidebar">

  <div class="brand_row">
    <a href="" class="logo">
      <img src="{{asset('img/tvistech.png')}}" alt="tviStech Logo"  class="logo_full">
      <img src="{{asset('img/tvistech_fav.png')}}" alt="tviStech Logo" class="fav_logo">
    </a>
  </div>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    @php 
       use Illuminate\Support\Facades\Cache;

    $user = Auth::user();

    $check_department = $user
        ? Cache::remember('emp_'.$user->id, 3600, fn() =>
            \App\Models\Employee::where('company_email', $user->email)->first()
        )
        : null;

    $project1 = $user
        ? Cache::remember('proj_'.$user->id, 3600, fn() =>
            \App\Models\Project::whereRaw("find_in_set(?, employee_id)", [$user->id])->exists()
        )
        : false;
    @endphp
    
    <nav class="mt-2">
      <ul class="nav" data-widget="treeview" role="menu" data-accordion="false">
             

        <li class="nav-item">
          <a href="{{route('dashboard.index')}}" class="nav-link {{ (request()->segment(1) == 'dashboard') ? 'active' : '' }}">
            <img src="{{asset('img/dashboard-icon.png')}}" alt="dashboard-icon" class="light">
            <img src="{{asset('img/dashboard-icon-aqua.png')}}" alt="dashboard-icon" class="dark">
            <p> Dashboard </p>
          </a>
        </li>
        
        @if($user->role=='2' || $user->role='')
          <li class="nav-item <?= (request()->segment(1) == 'admin') || (request()->segment(1) == 'admin_upchange') ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link <?= (request()->segment(1) == 'admin') || (request()->segment(1) == 'admin_upchange') ? 'active' : '' ?>">
             <img src="{{asset('img/employee-detail.png')}}" alt="dashboard-icon" class="light">
            <img src="{{asset('img/employee-detail-aqua.png')}}" alt="dashboard-icon" class="dark">
              <p>Employee Details 
                <i class="fa fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: <?= (request()->segment(1) == 'admin') || (request()->segment(1) == 'admin_upchange') || request()->segment(1) == 'shift-settings' ? 'block' : 'none' ?>">
              <li class="nav-item">
                <a href="{{route('admin.index')}}" class="nav-link {{ (request()->segment(1) == 'admin') ? 'active' : '' }}">
                   <img src="{{asset('img/employees.png')}}" alt="dashboard-icon" class="light">
                   <img src="{{asset('img/employees-aqua.png')}}" alt="dashboard-icon" class="dark">
                  <p>Employees</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('admin_upchange')}}" class="nav-link {{ (request()->segment(1) == 'admin_upchange') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-file-import"></i>
                  <p>Users CP</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('shift-settings.index')}}" class="nav-link {{ (request()->segment(1) == 'shift-settings') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-cog"></i>
                  <p>Shift Settings</p>
                </a>
              </li>
            </ul>
          </li>    
          
          <li class="nav-item <?= (request()->segment(1) == 'transaction-type') || (request()->segment(1) == 'importStatement') || (request()->segment(1) == 'rates') || (request()->segment(1) == 'accountname') ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link <?= (request()->segment(1) == 'transaction-type') || (request()->segment(1) == 'importStatement') || (request()->segment(1) == 'rates') || (request()->segment(1) == 'accountname') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-file-invoice-dollar"></i>
              <p>Billing System 
                <i class="fa fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: <?= (request()->segment(1) == 'transaction-type') || (request()->segment(1) == 'importStatement') || (request()->segment(1) == 'rates') || (request()->segment(1) == 'accountname') || (request()->segment(1) == 'currency')? 'block' : 'none' ?>">
              <li class="nav-item">
                <a href="{{route('importStatement.index')}}" class="nav-link {{(request()->segment(1) == 'importStatement' ? 'active' : '')}}">
                  <i class="nav-icon fas fa-file-import"></i>
                  <p>Statements</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('transaction-type.index')}}" class="nav-link {{request()->segment(1) == 'transaction-type' ? 'active' : ''}}">
                  <i class="nav-icon fas fa-user"></i>
                  <p>Transaction Type</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('rates.index')}}" class="nav-link {{ (request()->segment(1) == 'rates') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-file-import"></i>
                  <p>Rates</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('accountname.index')}}" class="nav-link {{ (request()->segment(1) == 'accountname') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-file-import"></i>
                  <p>Accounts</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('currency.index')}}" class="nav-link {{ (request()->segment(1) == 'currency') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-file-import"></i>
                  <p>Currency</p>
                </a>
              </li>
            </ul>
          </li>
        @endif

        @if(auth()->user()->role == 2 || auth()->id() == 8)
            <li class="nav-item">
                <a href="{{ route('client.index') }}"
                   class="nav-link {{ request()->segment(1) == 'client' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user"></i>
                    <p>Client</p>
                </a>
            </li>
        @endif

          @if($check_department->department == 1)
            <li class="nav-item">
              <a href="{{route('bids.index')}}" class="nav-link {{ (request()->segment(1) == 'bids') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user"></i>
                <p>Bids</p>
              </a>
            </li>
          @endif
          <li class="nav-item">
            <a href="{{route('daily_work_report.index')}}" class="nav-link {{ (request()->segment(1) == 'daily_work_report') ? 'active' : '' }}">
               <img src="{{asset('img/daily-work.png')}}" alt="daily-work-icon" class="light">
               <img src="{{asset('img/daily-work-aqua.png')}}" alt="daily-work-icon" class="dark">
              <p>Daily Work</p>
            </a>
          </li> 

          @if($user->role=='2' || $project1 > 0 || $check_department->reporting_person == '1' || $check_department->department == '1')
            <li class="nav-item <?= (request()->segment(1) == 'project') || (request()->segment(1) == 'complete_project') ? 'menu-open' : '' ?>">
              <a href="{{route('project.index')}}" class="nav-link <?= (request()->segment(1) == 'project') || (request()->segment(1) == 'complete_project') ? 'active' : '' ?>">
               <img src="{{asset('img/project-icon.png')}}" alt="project-icon" class="light">
               <img src="{{asset('img/project-icon-aqua.png')}}" alt="project-icon" class="dark">
                <p>Project </p>
             <i class="fa fa-angle-left right"></i>
              </a>
              @if($user->role=='2' || $check_department->reporting_person == '1'|| $check_department->department == '1')
                <ul class="nav nav-treeview" style="display: <?= (request()->segment(1) == 'project') || (request()->segment(1) == 'complete_project') ? 'block' : 'none' ?>">
                  <li class="nav-item">
                    <a href="{{route('project.index')}}" class="nav-link {{ (request()->segment(1) == 'project') ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Active</p>
                    </a>
                  </li>
                  
                  <li class="nav-item">
                    <a href="{{route('project_complete')}}" class="nav-link {{ (request()->segment(1) == 'complete_project') ? 'active' : '' }}">
                     <i class="far fa-circle nav-icon"></i>
                     <p>Complete</p>
                    </a>
                  </li>
                </ul>
              @endif
            </li>
          @endif

          <li class="nav-item">
            <a href="{{route('work_reports.index')}}" class="nav-link {{ (request()->segment(1) == 'work_reports') ? 'active' : '' }}">
             <img src="{{asset('img/work-report-icon.png')}}" alt="work-report-icon" class="light">
              <img src="{{asset('img/work-report-icon-aqua.png')}}" alt="work-report-icon" class="dark">
              <p>Work Report</p>
            </a>
          </li>

          @if($check_department->reporting_person == '1' || $user->role=='2')  
            <li class="nav-item has-treeview <?= (request()->segment(1) == 'leave') || (request()->segment(1) == 'leave_setting') ? 'menu-open' : '' ?>">
              <a href="{{route('leave.index')}}" class="nav-link <?= (request()->segment(1) == 'leave') || (request()->segment(1) == 'leave_setting') ? 'active' : '' ?>">
               <img src="{{asset('img/Leave-icon.png')}}" alt="leave-icon" class="light">
               <img src="{{asset('img/Leave-icon-aqua.png')}}" alt="leave-icon" class="dark">
                <p>Leaves Management</p>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              
              <ul class="nav nav-treeview" style="display: <?= (request()->segment(1) == 'leave') || (request()->segment(1) == 'leave_setting') ? 'block' : 'none' ?>">
                @if($check_department->reporting_person == '1' || $user->role=='2')
                <li class="nav-item">
                  <a href="{{route('leave.index')}}" class="nav-link {{ (request()->segment(1) == 'leave') ? 'active' : '' }}">
                     <img src="{{asset('img/Leave-icon.png')}}" alt="leave-icon" class="light">
                     <img src="{{asset('img/Leave-icon-aqua.png')}}" alt="leave-icon" class="dark">
                    <p>Leaves </p>
                  </a>
                </li>
                @endif
              
                @if($check_department->reporting_person == '1' || $user->role=='2')
                  <li class="nav-item">
                    <a href="{{route('leave_details')}}" class="nav-link {{ (request()->segment(1) == 'leave-details') ? 'active' : '' }}">
                  <img src="{{asset('img/leave-detail-icon.png')}}" alt="leave-detail-icon" class="light">
                  <img src="{{asset('img/leave-detail-icon-aqua.png')}}" alt="leave-detail-icon" class="dark">
                      
                      <p>Leave Details </p>
                    </a>
                  </li>
                @endif

                @if($user->role=='2')
                  <li class="nav-item">
                    <a href="{{route('leave_setting')}}" class="nav-link {{ (request()->segment(1) == 'leave_setting') ? 'active' : '' }}">
                      <i class="nav-icon fas fa-cog"></i>
                      <p>Setting</p>
                    </a>
                  </li>
                @endif
              </ul>
            </li>
          @else
          <li class="nav-item">
            <a href="{{route('leave.index')}}" class="nav-link {{ (request()->segment(1) == 'leave') ? 'active' : '' }}">
             <img src="{{asset('img/Leave-icon.png')}}" alt="leave-icon" class="light">
             <img src="{{asset('img/Leave-icon-aqua.png')}}" alt="leave-icon" class="dark">
              <p>Leaves</p>
            </a>
          </li>
          <li class="nav-item">
             <a href="{{route('leave_details')}}" class="nav-link {{ (request()->segment(1) == 'leave-details') ? 'active' : '' }}">
                 <img src="{{asset('img/leave-detail-icon.png')}}" alt="leave-detail-icon" class="light">
                  <img src="{{asset('img/leave-detail-icon-aqua.png')}}" alt="leave-detail-icon" class="dark">
                <p>Leave Details </p>
              </a>
            </li>
          @endif

          <li class="nav-item has-treeview <?= (request()->segment(1) == 'salary-deduction' || request()->segment(1) == 'salary-deducted' || request()->segment(1) == 'leave-adjust') ? 'menu-open' : '' ?>">
              <a href="{{ route('salary-deduction.index') }}" 
                 class="nav-link <?= (request()->segment(1) == 'salary-deduction' || request()->segment(1) == 'salary-deducted' || request()->segment(1) == 'leave-adjust') ? 'active' : '' ?>">
                
                <img src="{{asset('img/salary-icon.png')}}" alt="leave-detail-icon" class="light">
                  <img src="{{asset('img/salary-icon-aqua.png')}}" alt="leave-detail-icon" class="dark">
                <p>Salary Management</p>
                <span class="pull-right-container">
                  <i class="fas fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="nav nav-treeview" style="display: <?= (request()->segment(1) == 'salary-deduction' || request()->segment(1) == 'salary-deducted' || request()->segment(1) == 'leave-adjust') ? 'block' : 'none' ?>">
                <li class="nav-item">
                  <a href="{{route('salary-deduction.index')}}" class="nav-link {{ (request()->segment(1) == 'salary-deduction') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-wallet"></i>
                    <p>Deduction</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{route('salary-deducted')}}" class="nav-link {{ (request()->segment(1) == 'salary-deducted') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-coins"></i>
                    <p>Deducted</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{route('leave-adjust')}}" class="nav-link {{ (request()->segment(1) == 'leave-adjust') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-money-bill"></i>
                    <p>Adjust</p>
                  </a>
                </li>
              </ul> 
            </li>
          @if($check_department->department == '2')
          <li class="nav-item">
                <a href="{{route('late_coming')}}" class="nav-link {{ (request()->segment(1) == 'late-coming') ? 'active' : '' }}">
                   <img src="{{asset('img/late-icon.png')}}" alt="leave-detail-icon" class="light">
                <img src="{{asset('img/late-aqua.png')}}" alt="leave-detail-icon" class="dark">
                  <p>Late Coming</p>
                </a>
          </li>
          @endif

          @if($check_department->department == '1')
            <li class="nav-item <?= (request()->segment(1) == 'late-coming') || (request()->segment(1) == 'first-shift') || (request()->segment(1) == 'second-shift' ) ? 'menu-open' : '' ?>" > 
              <a href="#" class="nav-link <?= (request()->segment(1) == 'late-coming') || (request()->segment(1) == 'first-shift') || (request()->segment(1) == 'second-shift' ) ? 'active' : '' ?>">
                <img src="{{asset('img/late-icon.png')}}" alt="leave-detail-icon" class="light">
                <img src="{{asset('img/late-aqua.png')}}" alt="leave-detail-icon" class="dark">
                <p>Late Coming </p>
              </a>
              <ul class="nav nav-treeview" style="display: <?= (request()->segment(1) == 'late-coming') || (request()->segment(1) == 'first-shift') || (request()->segment(1) == 'second-shift' ) ? 'block' : 'none' ?>">
                <li class="nav-item">
                  <a href="{{route('late_coming')}}" class="nav-link <?= (request()->segment(1) == 'late-coming')  == 'second-shift'  ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-user-alt"></i>
                    <p>Regular shift</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('first_shift')}}" class="nav-link <?= (request()->segment(1) == 'first-shift') ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-file-import"></i>
                    <p>First shift</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('second_shift')}}" class="nav-link <?= (request()->segment(1) == 'second-shift' ) ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>Second shift</p>
                  </a>
                </li>
              </ul>
            </li>
          @endif

          <li class="nav-item">
                <a href="{{route('early_going')}}" class="nav-link {{ (request()->segment(1) == 'early-going') ? 'active' : '' }}">
                  <img src="{{asset('img/early.png')}}" alt="ealy-icon" class="light">
                <img src="{{asset('img/early-aqua.png')}}" alt="ealy-icon" class="dark">
                  <p>Early Going</p>
                </a>
          </li>
          
          <li class="nav-item">
                <a href="{{route('mail-request.index')}}" class="nav-link {{ (request()->segment(1) == 'mail-request') ? 'active' : '' }}">
                  <img src="{{asset('img/leave-detail-icon.png')}}" alt="leave-detail-icon" class="light">
                  <img src="{{asset('img/leave-detail-icon-aqua.png')}}" alt="leave-detail-icon" class="dark">
                  <p>Mail Request</p>
                </a>
          </li>

          <li class="nav-item">
               <a href="{{route('extra-days.index')}}" class="nav-link {{ (request()->segment(1) == 'extra-days') ? 'active' : '' }}">
                <img src="{{asset('img/extra-day-icon.png')}}" alt="ealy-icon" class="light">
                <img src="{{asset('img/extra-day-icon-aqua.png')}}" alt="ealy-icon" class="dark">
                 <p>Extra Days</p>
               </a>
          </li>

          <li class="nav-item">
               <a href="{{route('holiday.index')}}" class="nav-link {{ (request()->segment(1) == 'holiday') ? 'active' : '' }}">
                 <img src="{{asset('img/holiday-icon.png')}}" alt="ealy-icon" class="light">
                <img src="{{asset('img/holiday-icon-aqua.png')}}" alt="ealy-icon" class="dark">
                 <p>Holidays</p>
               </a>
          </li>
         
          <li class="nav-item">

            <a href="{{route('profile')}}" class="nav-link {{ (request()->segment(1) == 'profile') ? 'active' : '' }}">

               <img src="{{asset('img/Profile-icon.png')}}" alt="ealy-icon" class="light">
                <img src="{{asset('img/Profile-icon-aqua.png')}}" alt="ealy-icon" class="dark">

              <p>Profile</p>

            </a>

          </li>
          @if($user->role=='2')
            <li class="nav-item">

              <a href="{{route('setting.index')}}" class="nav-link {{ (request()->segment(1) == 'setting') ? 'active' : '' }}">

                 <img src="{{asset('img/Profile-icon.png')}}" alt="ealy-icon" class="light">
                  <img src="{{asset('img/Profile-icon-aqua.png')}}" alt="ealy-icon" class="dark">

                <p>Settings</p>

              </a>

            </li>
          @endif  
          <li class="nav-item">

            <a href="{{route('changePasswordGet')}}" class="nav-link {{ (request()->segment(1) == 'changePassword') ? 'active' : '' }}">

           <img src="{{asset('img/password-icon.png')}}" alt="ealy-icon" class="light">
                <img src="{{asset('img/password-icon-aqua.png')}}" alt="ealy-icon" class="dark">

              <p>Change Password</p>

            </a>

          </li>
          
        <li class="nav-item">

          <a href="{{route('logout')}}" class="nav-link">

             <img src="{{asset('img/logout-icon.png')}}" alt="logout-icon" class="light">
                <img src="{{asset('img/logout-icon-aqua.png')}}" alt="logout-icon" class="dark">

            <p>Logout</p>

          </a>

        </li>
        

        

      </ul>

    </nav>

    <!-- /.sidebar-menu -->

  </div>

  <!-- /.sidebar -->

</aside>
