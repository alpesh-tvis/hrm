<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HRM</title>

    <link rel="shortcut icon" href="{{ asset('favicon.png')}}">

    <!-- Google Font: Source Sans Pro -->

    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->

    <!-- Font Awesome -->

    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">

    

    <!-- Theme style -->

    <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">

    <link rel="stylesheet" href="{{asset('css/custom.css')}}">

    <link rel="stylesheet" href="{{asset('css/dark.css')}}">

    <!-- overlayScrollbars -->

    <!-- <link rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}"> -->

    

  </head>

  <body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">

    <div class="spinner-wrapper" style="display:none;">

      <div class="spinner"></div>

    </div>

    <style>

    .spinner-wrapper {

    position: fixed;

    left: 0;

    top: 0;

    z-index: 9999;

    background-color: #00000036;

    height: 100vh;

    width: 100%;

    }

    .spinner {

    width: 56px;

    height: 56px;

    border-radius: 50%;

    background: conic-gradient(#0000 10%,#149aa3);

    -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 9px),#000 0);

    animation: spinner-zp9dbg 1s infinite linear;

    left: 50%;

    top: 50%;

    z-index: 999999;

    position: relative;

    }

    @keyframes spinner-zp9dbg {

    to {

    transform: rotate(1turn);

    }

    }
    .notif-dot {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 8px;
        height: 8px;
        background: #ff0000;
        border-radius: 50%;
       
    }
    .nav-item.dropdown:hover .dropdown-menu {
        display: block;
    }
    </style>

    <div class="wrapper">

      <!-- Preloader -->

      <div class="preloader flex-column justify-content-center align-items-center">

        <img class="animation__shake" src="{{asset('img/tvistech_fav.png')}}" alt="tvistech favicon" height="60" width="60">

      </div>

      @include('admin.header')

      @include('admin.sidebar')

      <?php

        $string_with_spaces = str_replace("_", " ", Request::segment(1));

        $userid = auth()->id();

        $employees_details = $userid
        ? Cache::remember('emp_'.$userid, 3600, fn() =>
            \App\Models\Employee::find($userid)
        )
        : null;

      ?>

     

      <div class="content-wrapper">

        <div class="content-header">

          <div class="container-fluid">

            <div class="row">

              <div class="col-sm-6">

                <div class="menuIcon">

                <span></span>

                </div>

                <h1 class="m-0">{{ucwords($string_with_spaces)}}</h1>

              </div>

              <div class="col-sm-6 user">
                @if(auth()->user()->role == 2 || auth()->id() == 2)

                <div class="nav-item dropdown">

                    <a class="nav-link position-relative" href="#" data-toggle="dropdown">

                        <i class="fas fa-bell fa-lg text-warning"></i>

                        @if($hasNotification)
                            <span class="notif-dot text-warning"></span>
                        @endif

                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-center p-0">

                        <div class="dropdown-divider"></div>

                        {{-- Leaves --}}
                        @forelse($pendingLeaves as $leave)
                            <a href="{{ route('leave.index') }}" class="dropdown-item">
                                {{ $leave->employee->full_name }}
                                <small class="text-muted d-block">Leave Request</small>
                            </a>
                            <div class="dropdown-divider"></div>
                        @empty
                        @endforelse

                        {{-- Mail Requests --}}
                        @forelse($pendingMails as $mail)
                            <a href="{{ route('mail-request.index') }}" class="dropdown-item">
                                {{ $mail->employee->full_name }}
                                <small class="text-muted d-block">Mail Request</small>
                            </a>
                            <div class="dropdown-divider"></div>
                        @empty
                        @endforelse

                        @if($pendingLeaves->isEmpty() && $pendingMails->isEmpty())
                            <span class="dropdown-item text-center text-muted">
                                No Notifications
                            </span>
                        @endif

                    </div>
                </div>
              @endif

                <div>

                  <h5>Mode</h5>

                  <input type="checkbox" class="checkbox-mode" id="checkbox-mode">

                  <label for="checkbox-mode" class="checkbox-label-mode">

                    <i class="fas fa-moon"></i>

                    <i class="fas fa-sun"></i>

                    <span class="ball"></span>

                  </label>

                </div>

                <div class="user-info">

                  <h4><a href="{{route('profile')}}"> {{$employees_details->first_name}} {{$employees_details->last_name}}</a></h4>

                  <h5>{{$employees_details->position}}</h5>

                </div>

                @if ($employees_details->profile_image)

                  <span><img src="{{ asset($employees_details->profile_image) }}" style="max-height: 50px;" alt="Profile Image"></span>

                @else

                <span><img src="{{ asset('profile_image/no-profile-picture.png') }}" style="max-height: 50px;"></span>

                  

                @endif

              </div>

            </div>

          </div>

        </div>

        

        @yield('content')

        

       

      </div>

     

      @include('admin.footer')

    </div>

    <!-- ./wrapper -->

    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>

    <script src="{{asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>

    <script src="{{asset('plugins/moment/moment.min.js')}}"></script>

    <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>

    <!-- <script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script> -->

    <script src="{{asset('js/adminlte.js')}}"></script>

    <!-- <script src="{{asset('js/custom.js')}}"></script> -->

    <script>
      jQuery(".menuIcon").click(function(){
       $('body').toggleClass('menucollaspe')
      });
      document.addEventListener('DOMContentLoaded', () => {

        const checkbox = document.getElementById('checkbox-mode');

        const body = document.body;



        const savedTheme = localStorage.getItem('theme');

        if (savedTheme === 'dark') {

          body.classList.add('dark-mode');

          checkbox.checked = true;

        }



        checkbox.addEventListener('change', () => {

          if (checkbox.checked) {

            body.classList.add('dark-mode');

            localStorage.setItem('theme', 'dark');

          } else {

            body.classList.remove('dark-mode');

            localStorage.setItem('theme', 'light');

          }

        });

      });

    </script>



  </body>

</html>