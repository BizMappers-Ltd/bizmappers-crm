@php
    use App\Models\SystemNotification;
    if (auth()->user()->role == 'customer') {
        $notifications = SystemNotification::where('notifiable_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    } else {
        $notifications = SystemNotification::latest()->take(5)->get();
    }

@endphp

<!--**********************************
            Nav header start
        ***********************************-->


<div class="nav-header position-fixed">
    <div class="brand-logo bg-white  h-100 d-flex justify-content-center align-items-center">
        <a href="{{ route('dashboard') }}">
            <b class="logo-abbr"><img src="../../template/images/favicon.png" alt=""> </b>
            <span class="logo-compact"><img src="../../template/images/logo-text.png" alt=""></span>
            <span class="brand-title">
                <img src="../../template/images/logo-text.png" height="52" alt="">
            </span>
        </a>
    </div>
</div>


<!--**********************************
                    Nav header end
                ***********************************-->

<!--**********************************
                    Header start
                ***********************************-->
<div class="header">
    <div class="header-content clearfix">

        <div class="nav-control">
            <div class="hamburger">
                <span class="toggle-icon"><i class="icon-menu"></i></span>
            </div>
        </div>

        <div class="header-right">
            <ul class="clearfix">

                <li class="icons dropdown"><a href="javascript:void(0)" data-toggle="dropdown">
                        <i class="mdi mdi-bell-outline"></i>
                    </a>
                    <div class="drop-down animated fadeIn dropdown-menu dropdown-notfication">

                        <div class="dropdown-content-body">
                            <ul>
                                @foreach ($notifications as $notification)
                                    <li>
                                        <span class="mr-3 avatar-icon bg-success-lighten-2"><i class="fa fa-bell"
                                                aria-hidden="true"></i></span>
                                        <div class="notification-content">
                                            <h6 class="notification-heading">{{ $notification->notification }}</h6>
                                            <span
                                                class="notification-text">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                        </div>
                                    </li>
                                @endforeach




                            </ul>

                            @if (auth()->user()->role != 'customer')
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('notification.index') }}">See all</a>
                                </div>
                            @endif

                            @if (auth()->user()->role == 'customer')
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('notification.indexClient', auth()->user()->id) }}">See all</a>
                                </div>
                            @endif

                        </div>
                    </div>
                </li>

                <li class="icons dropdown">
                    <div class="user-img c-pointer position-relative" data-toggle="dropdown">
                        <span class="activity active"></span>
                        <img src="../../template/images/user/101.jpg" height="40" width="40" alt="">
                    </div>
                    <div class="drop-down dropdown-profile animated fadeIn dropdown-menu">
                        <div class="dropdown-content-body">
                            <ul>
                                <li>
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>
                                </li>

                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--**********************************
                    Header end ti-comment-alt
                ***********************************-->
