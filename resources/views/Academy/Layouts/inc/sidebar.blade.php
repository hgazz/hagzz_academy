<!--  BEGIN SIDEBAR  -->
<div class="sidebar-wrapper sidebar-theme">

    <nav id="sidebar">

        <div class="navbar-nav theme-brand flex-row  text-center">
            <div class="nav-logo">
                <div class="nav-item theme-logo">
                    <a href="{{ route('academy.index') }}">
                        <img src="{{ asset('assetsAdmin/src/assets/img/logo.svg') }}" class="navbar-logo" alt="logo">
                    </a>
                </div>
                <div class="nav-item theme-text">
                    <a href="{{ route('academy.index') }}" class="nav-link"> {{ trans('admin.bokit') }} </a>
                </div>
            </div>
            <div class="nav-item sidebar-toggle">
                <div class="btn-toggle sidebarCollapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>
                </div>
            </div>
        </div>
        <div class="shadow-bottom"></div>
        <ul class="list-unstyled menu-categories" id="accordionExample">
            <!-- <li class="menu {{ Request::routeIs('academy.index') ? 'active' : '' }}">
                <a href="#dashboard" data-bs-toggle="collapse" aria-expanded="{{ Request::routeIs('academy.index') ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::routeIs('academy.index') ? '' : 'collapsed' }}">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 33 32"
                        fill="#0E1726" fill-opacity=".6">
                        <path class="dash-icon"
                            d="M4.5 16C4.5 16.3536 4.64048 16.6928 4.89052 16.9428C5.14057 17.1929 5.47971 17.3333 5.83333 17.3333H13.8333C14.187 17.3333 14.5261 17.1929 14.7761 16.9428C15.0262 16.6928 15.1667 16.3536 15.1667 16V5.33333C15.1667 4.97971 15.0262 4.64057 14.7761 4.39052C14.5261 4.14048 14.187 4 13.8333 4H5.83333C5.47971 4 5.14057 4.14048 4.89052 4.39052C4.64048 4.64057 4.5 4.97971 4.5 5.33333V16ZM4.5 26.6667C4.5 27.0203 4.64048 27.3594 4.89052 27.6095C5.14057 27.8595 5.47971 28 5.83333 28H13.8333C14.187 28 14.5261 27.8595 14.7761 27.6095C15.0262 27.3594 15.1667 27.0203 15.1667 26.6667V21.3333C15.1667 20.9797 15.0262 20.6406 14.7761 20.3905C14.5261 20.1405 14.187 20 13.8333 20H5.83333C5.47971 20 5.14057 20.1405 4.89052 20.3905C4.64048 20.6406 4.5 20.9797 4.5 21.3333V26.6667ZM17.8333 26.6667C17.8333 27.0203 17.9738 27.3594 18.2239 27.6095C18.4739 27.8595 18.813 28 19.1667 28H27.1667C27.5203 28 27.8594 27.8595 28.1095 27.6095C28.3595 27.3594 28.5 27.0203 28.5 26.6667V16C28.5 15.6464 28.3595 15.3072 28.1095 15.0572C27.8594 14.8071 27.5203 14.6667 27.1667 14.6667H19.1667C18.813 14.6667 18.4739 14.8071 18.2239 15.0572C17.9738 15.3072 17.8333 15.6464 17.8333 16V26.6667ZM19.1667 4C18.813 4 18.4739 4.14048 18.2239 4.39052C17.9738 4.64057 17.8333 4.97971 17.8333 5.33333V10.6667C17.8333 11.0203 17.9738 11.3594 18.2239 11.6095C18.4739 11.8595 18.813 12 19.1667 12H27.1667C27.5203 12 27.8594 11.8595 28.1095 11.6095C28.3595 11.3594 28.5 11.0203 28.5 10.6667V5.33333C28.5 4.97971 28.3595 4.64057 28.1095 4.39052C27.8594 4.14048 27.5203 4 27.1667 4H19.1667Z"
                            fill="#0E1726" fill-opacity="0.6"></path>
                    </svg>                         <span>{{ trans('admin.dashboard') }}</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ Request::routeIs('academy.index') ? 'show' : '' }}" id="dashboard" data-bs-parent="#accordionExample">
                    <li class="active">
                        <a href="{{ route('academy.index') }}"> {{ trans('admin.dashboard') }} </a>
                    </li>
                </ul>
            </li> -->

            <li class="menu {{ Request::routeIs('academy.index') ? 'active' : '' }}">
                <a href="{{ route('academy.index') }}"  aria-expanded="{{ Request::routeIs('academy.index') ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::routeIs('academy.index') ? '' : 'collapsed' }}">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 33 32"
                        fill="#0E1726" fill-opacity=".6">
                        <path class="dash-icon"
                            d="M4.5 16C4.5 16.3536 4.64048 16.6928 4.89052 16.9428C5.14057 17.1929 5.47971 17.3333 5.83333 17.3333H13.8333C14.187 17.3333 14.5261 17.1929 14.7761 16.9428C15.0262 16.6928 15.1667 16.3536 15.1667 16V5.33333C15.1667 4.97971 15.0262 4.64057 14.7761 4.39052C14.5261 4.14048 14.187 4 13.8333 4H5.83333C5.47971 4 5.14057 4.14048 4.89052 4.39052C4.64048 4.64057 4.5 4.97971 4.5 5.33333V16ZM4.5 26.6667C4.5 27.0203 4.64048 27.3594 4.89052 27.6095C5.14057 27.8595 5.47971 28 5.83333 28H13.8333C14.187 28 14.5261 27.8595 14.7761 27.6095C15.0262 27.3594 15.1667 27.0203 15.1667 26.6667V21.3333C15.1667 20.9797 15.0262 20.6406 14.7761 20.3905C14.5261 20.1405 14.187 20 13.8333 20H5.83333C5.47971 20 5.14057 20.1405 4.89052 20.3905C4.64048 20.6406 4.5 20.9797 4.5 21.3333V26.6667ZM17.8333 26.6667C17.8333 27.0203 17.9738 27.3594 18.2239 27.6095C18.4739 27.8595 18.813 28 19.1667 28H27.1667C27.5203 28 27.8594 27.8595 28.1095 27.6095C28.3595 27.3594 28.5 27.0203 28.5 26.6667V16C28.5 15.6464 28.3595 15.3072 28.1095 15.0572C27.8594 14.8071 27.5203 14.6667 27.1667 14.6667H19.1667C18.813 14.6667 18.4739 14.8071 18.2239 15.0572C17.9738 15.3072 17.8333 15.6464 17.8333 16V26.6667ZM19.1667 4C18.813 4 18.4739 4.14048 18.2239 4.39052C17.9738 4.64057 17.8333 4.97971 17.8333 5.33333V10.6667C17.8333 11.0203 17.9738 11.3594 18.2239 11.6095C18.4739 11.8595 18.813 12 19.1667 12H27.1667C27.5203 12 27.8594 11.8595 28.1095 11.6095C28.3595 11.3594 28.5 11.0203 28.5 10.6667V5.33333C28.5 4.97971 28.3595 4.64057 28.1095 4.39052C27.8594 4.14048 27.5203 4 27.1667 4H19.1667Z"
                            fill="#0E1726" fill-opacity="0.6"></path>
                    </svg>                         <span>{{ trans('admin.dashboard') }}</span>
                    </div>
                    <!-- <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div> -->
                </a>
            </li>
            <li class="menu {{ Request::routeIs('academy.address.*') ? 'active' : '' }}">
                <a href="#apps" data-bs-toggle="collapse" aria-expanded="{{ Request::routeIs('academy.address.*') ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::routeIs('academy.address.*') ? '' : 'collapsed' }}">
                    <div class="">

                        <svg fill-opacity=".6" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                        fill="#0E1726">
                        <path
                            d="M7.49935 1.53516L12.561 4.48849L18.3327 2.08349V9.16682H16.666V4.58349L13.3327 5.97266V9.16682H11.666V5.89516L8.33268 3.95099V14.1052L9.88935 15.0135L9.04935 16.4527L7.43768 15.5118L1.66602 17.9168V4.93849L7.49935 1.53516ZM6.66602 14.0277V3.95099L3.33268 5.89516V15.4168L6.66602 14.0277ZM14.9993 11.6668C14.3916 11.6668 13.8087 11.9083 13.3789 12.338C12.9491 12.7678 12.7077 13.3507 12.7077 13.9585C12.7077 15.0018 13.3202 16.0035 14.0535 16.8102C14.3927 17.1835 14.7343 17.4885 14.9993 17.706C15.2643 17.4893 15.606 17.1835 15.9452 16.8102C16.6785 16.0035 17.291 15.0018 17.291 13.9585C17.291 13.3507 17.0496 12.7678 16.6198 12.338C16.19 11.9083 15.6071 11.6668 14.9993 11.6668ZM14.9993 19.751L14.5368 19.4435L14.5352 19.4427L14.5318 19.4402L14.5218 19.4335L14.4885 19.4102C14.3168 19.2891 14.1492 19.1624 13.986 19.0302C13.5705 18.6937 13.1808 18.3266 12.8202 17.9318C11.991 17.0202 11.041 15.626 11.041 13.9585C11.041 12.9087 11.4581 11.9019 12.2004 11.1595C12.9427 10.4172 13.9495 10.0002 14.9993 10.0002C16.0492 10.0002 17.056 10.4172 17.7983 11.1595C18.5406 11.9019 18.9577 12.9087 18.9577 13.9585C18.9577 15.626 18.0077 17.0202 17.1785 17.9318C16.6754 18.4814 16.1163 18.9768 15.5102 19.4102L15.4768 19.4335L15.4668 19.4402L15.4635 19.4427H15.4618L14.9993 19.751ZM13.9577 13.3335H16.041V15.0002H13.9577V13.3335Z"
                            fill="#0E1726"></path>
                    </svg>                         <span>{{trans('admin.address.address')}}</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ Request::routeIs('academy.address.*') ? 'show' : '' }}" id="apps" data-bs-parent="#accordionExample">
                    <li class="menu {{ Request::routeIs('academy.address.*') ? 'active' : '' }}">
                        <a href="{{route('academy.address.index')}}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M57.7 193l9.4 16.4c8.3 14.5 21.9 25.2 38 29.8L163 255.7c17.2 4.9 29 20.6 29 38.5v39.9c0 11 6.2 21 16 25.9s16 14.9 16 25.9v39c0 15.6 14.9 26.9 29.9 22.6c16.1-4.6 28.6-17.5 32.7-33.8l2.8-11.2c4.2-16.9 15.2-31.4 30.3-40l8.1-4.6c15-8.5 24.2-24.5 24.2-41.7v-8.3c0-12.7-5.1-24.9-14.1-33.9l-3.9-3.9c-9-9-21.2-14.1-33.9-14.1H257c-11.1 0-22.1-2.9-31.8-8.4l-34.5-19.7c-4.3-2.5-7.6-6.5-9.2-11.2c-3.2-9.6 1.1-20 10.2-24.5l5.9-3c6.6-3.3 14.3-3.9 21.3-1.5l23.2 7.7c8.2 2.7 17.2-.4 21.9-7.5c4.7-7 4.2-16.3-1.2-22.8l-13.6-16.3c-10-12-9.9-29.5 .3-41.3l15.7-18.3c8.8-10.3 10.2-25 3.5-36.7l-2.4-4.2c-3.5-.2-6.9-.3-10.4-.3C163.1 48 84.4 108.9 57.7 193zM464 256c0-36.8-9.6-71.4-26.4-101.5L412 164.8c-15.7 6.3-23.8 23.8-18.5 39.8l16.9 50.7c3.5 10.4 12 18.3 22.6 20.9l29.1 7.3c1.2-9 1.8-18.2 1.8-27.5zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z"/></svg>
                                <span>{{trans('admin.address.address')}}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu {{ Request::routeIs('academy.training.*') || Request::routeIs('academy.class.*') ? 'active' : '' }}">
                <a href="#services" data-bs-toggle="collapse" aria-expanded="{{ Request::routeIs('academy.training.*') || Request::routeIs('academy.class.*')  ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::routeIs('academy.training.*') || Request::routeIs('academy.class.*')  ? '' : 'collapsed' }}">
                    <div class="">

                        <svg fill-opacity=".6" xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 33 32" fill="#0E1726">
                            <path class="dash-icon" d="M26.4067 17.3066C26.46 16.8799 26.5 16.4532 26.5 15.9999C26.5 15.5466 26.46 15.1199 26.4067 14.6932L29.22 12.4932C29.4734 12.2932 29.54 11.9332 29.38 11.6399L26.7134 7.02657C26.6348 6.889 26.5097 6.78393 26.3607 6.73017C26.2117 6.67642 26.0483 6.67751 25.9 6.73323L22.58 8.06657C21.8867 7.53323 21.14 7.09323 20.3267 6.7599L19.82 3.22657C19.798 3.06969 19.7194 2.92625 19.5992 2.82315C19.4789 2.72005 19.3251 2.66437 19.1667 2.66657H13.8334C13.5 2.66657 13.22 2.90657 13.18 3.22657L12.6734 6.7599C11.86 7.09323 11.1134 7.54657 10.42 8.06657L7.10003 6.73323C7.02272 6.70701 6.94167 6.6935 6.86003 6.69323C6.63336 6.69323 6.4067 6.81323 6.2867 7.02657L3.62003 11.6399C3.4467 11.9332 3.5267 12.2932 3.78003 12.4932L6.59336 14.6932C6.54003 15.1199 6.50003 15.5599 6.50003 15.9999C6.50003 16.4399 6.54003 16.8799 6.59336 17.3066L3.78003 19.5066C3.5267 19.7066 3.46003 20.0666 3.62003 20.3599L6.2867 24.9732C6.3653 25.1108 6.49032 25.2159 6.63936 25.2696C6.7884 25.3234 6.95172 25.3223 7.10003 25.2666L10.42 23.9332C11.1134 24.4666 11.86 24.9066 12.6734 25.2399L13.18 28.7732C13.22 29.0932 13.5 29.3332 13.8334 29.3332H19.1667C19.5 29.3332 19.78 29.0932 19.82 28.7732L20.3267 25.2399C21.14 24.9066 21.8867 24.4532 22.58 23.9332L25.9 25.2666C25.98 25.2932 26.06 25.3066 26.14 25.3066C26.3667 25.3066 26.5934 25.1866 26.7134 24.9732L29.38 20.3599C29.54 20.0666 29.4734 19.7066 29.22 19.5066L26.4067 17.3066ZM23.7667 15.0266C23.82 15.4399 23.8334 15.7199 23.8334 15.9999C23.8334 16.2799 23.8067 16.5732 23.7667 16.9732L23.58 18.4799L24.7667 19.4132L26.2067 20.5332L25.2734 22.1466L23.58 21.4666L22.1934 20.9066L20.9934 21.8132C20.42 22.2399 19.8734 22.5599 19.3267 22.7866L17.9134 23.3599L17.7 24.8666L17.4334 26.6666H15.5667L15.3134 24.8666L15.1 23.3599L13.6867 22.7866C13.1134 22.5466 12.58 22.2399 12.0467 21.8399L10.8334 20.9066L9.42003 21.4799L7.7267 22.1599L6.79336 20.5466L8.23336 19.4266L9.42003 18.4932L9.23336 16.9866C9.19336 16.5732 9.1667 16.2666 9.1667 15.9999C9.1667 15.7332 9.19336 15.4266 9.23336 15.0266L9.42003 13.5199L8.23336 12.5866L6.79336 11.4666L7.7267 9.85323L9.42003 10.5332L10.8067 11.0932L12.0067 10.1866C12.58 9.7599 13.1267 9.4399 13.6734 9.21323L15.0867 8.6399L15.3 7.13323L15.5667 5.33323H17.42L17.6734 7.13323L17.8867 8.6399L19.3 9.21323C19.8734 9.45323 20.4067 9.7599 20.94 10.1599L22.1534 11.0932L23.5667 10.5199L25.26 9.8399L26.1934 11.4532L24.7667 12.5866L23.58 13.5199L23.7667 15.0266ZM16.5 10.6666C13.5534 10.6666 11.1667 13.0532 11.1667 15.9999C11.1667 18.9466 13.5534 21.3332 16.5 21.3332C19.4467 21.3332 21.8334 18.9466 21.8334 15.9999C21.8334 13.0532 19.4467 10.6666 16.5 10.6666ZM16.5 18.6666C15.0334 18.6666 13.8334 17.4666 13.8334 15.9999C13.8334 14.5332 15.0334 13.3332 16.5 13.3332C17.9667 13.3332 19.1667 14.5332 19.1667 15.9999C19.1667 17.4666 17.9667 18.6666 16.5 18.6666Z" fill="#0E1726" fill-opacity="0.75"></path>
                        </svg>                        <span>{{trans('admin.services_management')}}</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ Request::routeIs('academy.training.*') || Request::routeIs('academy.class.*') ? 'show' : '' }}" id="services" data-bs-parent="#accordionExample">
                    <li class="menu {{ Request::routeIs('academy.training.*')  ? 'active' : '' }}">
                        <a href="{{ route('academy.training.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M96 64c0-17.7 14.3-32 32-32h32c17.7 0 32 14.3 32 32V224v64V448c0 17.7-14.3 32-32 32H128c-17.7 0-32-14.3-32-32V384H64c-17.7 0-32-14.3-32-32V288c-17.7 0-32-14.3-32-32s14.3-32 32-32V160c0-17.7 14.3-32 32-32H96V64zm448 0v64h32c17.7 0 32 14.3 32 32v64c17.7 0 32 14.3 32 32s-14.3 32-32 32v64c0 17.7-14.3 32-32 32H544v64c0 17.7-14.3 32-32 32H480c-17.7 0-32-14.3-32-32V288 224 64c0-17.7 14.3-32 32-32h32c17.7 0 32 14.3 32 32zM416 224v64H224V224H416z"/></svg>                        <span>{{ trans('admin.training.training') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.calendar..*')  ? 'active' : '' }}">
                        <a href="{{ route('academy.calendar.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M96 32l0 32L48 64C21.5 64 0 85.5 0 112l0 48 448 0 0-48c0-26.5-21.5-48-48-48l-48 0 0-32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 32L160 64l0-32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192L0 192 0 464c0 26.5 21.5 48 48 48l352 0c26.5 0 48-21.5 48-48l0-272z"/></svg>
                                <span>{{ trans('admin.training.calendar') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.createBooking')  ? 'active' : '' }}">
                        <a href="{{ route('academy.createBooking') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path
                                        d="M0 48V487.7C0 501.1 10.9 512 24.3 512c5 0 9.9-1.5 14-4.4L192 400 345.7 507.6c4.1 2.9 9 4.4 14 4.4c13.4 0 24.3-10.9 24.3-24.3V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48z" />
                                </svg>
                                <span>{{ trans('admin.add_booking') }}</span>
                            </div>
                        </a>
                    </li>

{{--                    <li class="menu {{ Request::routeIs('academy.class.*') ? 'active' : '' }}">--}}
{{--                        <a href="{{ route('academy.class.index') }}" aria-expanded="false" class="dropdown-toggle">--}}
{{--                            <div class="">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M337.8 5.4C327-1.8 313-1.8 302.2 5.4L166.3 96H48C21.5 96 0 117.5 0 144V464c0 26.5 21.5 48 48 48H256V416c0-35.3 28.7-64 64-64s64 28.7 64 64v96H592c26.5 0 48-21.5 48-48V144c0-26.5-21.5-48-48-48H473.7L337.8 5.4zM96 192h32c8.8 0 16 7.2 16 16v64c0 8.8-7.2 16-16 16H96c-8.8 0-16-7.2-16-16V208c0-8.8 7.2-16 16-16zm400 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v64c0 8.8-7.2 16-16 16H512c-8.8 0-16-7.2-16-16V208zM96 320h32c8.8 0 16 7.2 16 16v64c0 8.8-7.2 16-16 16H96c-8.8 0-16-7.2-16-16V336c0-8.8 7.2-16 16-16zm400 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v64c0 8.8-7.2 16-16 16H512c-8.8 0-16-7.2-16-16V336zM232 176a88 88 0 1 1 176 0 88 88 0 1 1 -176 0zm88-48c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H336V144c0-8.8-7.2-16-16-16z"/></svg>--}}
{{--                                <span>{{ trans('admin.clasess.clasess') }}</span>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </li>--}}

{{--                    <li class="menu {{ Request::routeIs('academy.booking.*') ? 'active' : '' }}">--}}
{{--                        <a href="{{ route('academy.booking.index') }}" aria-expanded="false" class="dropdown-toggle">--}}
{{--                            <div class="">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 48V487.7C0 501.1 10.9 512 24.3 512c5 0 9.9-1.5 14-4.4L192 400 345.7 507.6c4.1 2.9 9 4.4 14 4.4c13.4 0 24.3-10.9 24.3-24.3V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48z"/></svg>--}}
{{--                                <span>{{ trans('admin.training.booking') }}</span>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </li>--}}

                </ul>
            </li>


            <li class="menu {{ Request::routeIs('academy.coach') || Request::routeIs('academy.coach.*') ? 'active' : '' }}">
                <a href="{{ route('academy.coach') }}" aria-expanded="{{ Request::routeIs('academy.coach.*')  ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z" fill="#0E1726" fill-opacity=".6"/></svg>                        <span>{{ trans('admin.coaches.coaches') }}</span>
                    </div>
                </a>
            </li>
            <li class="menu {{ Request::routeIs('academy.gallery.*')  ? 'active' : '' }}">
                <a href="{{ route('academy.gallery.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M160 32c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H160zM396 138.7l96 144c4.9 7.4 5.4 16.8 1.2 24.6S480.9 320 472 320H328 280 200c-9.2 0-17.6-5.3-21.6-13.6s-2.9-18.2 2.9-25.4l64-80c4.6-5.7 11.4-9 18.7-9s14.2 3.3 18.7 9l17.3 21.6 56-84C360.5 132 368 128 376 128s15.5 4 20 10.7zM192 128a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zM48 120c0-13.3-10.7-24-24-24S0 106.7 0 120V344c0 75.1 60.9 136 136 136H456c13.3 0 24-10.7 24-24s-10.7-24-24-24H136c-48.6 0-88-39.4-88-88V120z" fill-opacity="0.6"/></svg>                        <span>{{ trans('admin.gallery.gallery') }}</span>
                    </div>
                </a>
            </li>

            <li class="menu {{ Request::routeIs('academy.users.*')  ? 'active' : '' }}">
                <a href="{{ route('academy.users.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <img src="{{ asset('assetsAdmin/people-fill.svg') }}" alt="">
                        <span>{{ trans('admin.profile.user') }}</span>
                    </div>
                </a>
            </li>
            <li class="menu {{ Request::routeIs('academy.notification.*') ? 'active' : '' }}">
                <a href="{{ route('academy.notification.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <img src="{{ asset('assetsAdmin/card-checklist.svg') }}" alt="">
                        <span>{{trans("admin.notifications.notifications")}}</span>
                    </div>
                </a>
            </li>
            <li class="menu {{ Request::routeIs('academy.terms.*')  ? 'active' : '' }}">
                <a href="{{ route('academy.terms.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="#0E1726" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.99961 14.8333C7.45295 14.8333 6.90627 14.66 6.45294 14.32L2.97961 11.72C2.33295 11.2333 1.94629 10.46 1.94629 9.65334V1.17334H14.0529V9.65334C14.0529 10.46 13.6663 11.2333 13.0196 11.72L9.54628 14.32C9.09295 14.66 8.54628 14.8333 7.99961 14.8333ZM2.94629 2.16668V9.64668C2.94629 10.14 3.18628 10.6133 3.57961 10.9133L7.05294 13.5133C7.61294 13.9333 8.39294 13.9333 8.95294 13.5133L12.4263 10.9133C12.8196 10.6133 13.0596 10.14 13.0596 9.64668V2.16668H2.94629Z" fill="#0E1726"/>
                            <path d="M14.6663 2.16675H1.33301C1.05967 2.16675 0.833008 1.94008 0.833008 1.66675C0.833008 1.39341 1.05967 1.16675 1.33301 1.16675H14.6663C14.9397 1.16675 15.1663 1.39341 15.1663 1.66675C15.1663 1.94008 14.9397 2.16675 14.6663 2.16675Z" fill="#0E1726"/>
                            <path d="M10.6663 5.83325H5.33301C5.05967 5.83325 4.83301 5.60659 4.83301 5.33325C4.83301 5.05992 5.05967 4.83325 5.33301 4.83325H10.6663C10.9397 4.83325 11.1663 5.05992 11.1663 5.33325C11.1663 5.60659 10.9397 5.83325 10.6663 5.83325Z" fill="#0E1726"/>
                            <path d="M10.6663 9.16675H5.33301C5.05967 9.16675 4.83301 8.94008 4.83301 8.66675C4.83301 8.39341 5.05967 8.16675 5.33301 8.16675H10.6663C10.9397 8.16675 11.1663 8.39341 11.1663 8.66675C11.1663 8.94008 10.9397 9.16675 10.6663 9.16675Z" fill="#0E1726"/>
                        </svg>
                        <span>{{trans("admin.terms.terms")}}</span>
                    </div>
                </a>
            </li>
            <li class="menu {{ Request::routeIs('academy.report.*') ? 'active' : '' }}">
                <a href="#report" data-bs-toggle="collapse" aria-expanded="{{ Request::routeIs('academy.report.*')  ? 'true' : 'false' }}" class="dropdown-toggle {{ Request::routeIs('academy.report.*')  ? '' : 'collapsed' }}">
                    <div class="">
                        <svg fill-opacity=".6" xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 33 32" fill="#0E1726">
                            <path d="M8.5 9.3335H19.1667V12.0002H8.5V9.3335ZM8.5 14.6668H24.5V17.3335H8.5V14.6668ZM8.5 20.0002H12.4867V22.6668H8.5V20.0002Z" fill="#0E1726" fill-opacity="0.75"></path>
                            <path class="dash-icon" d="M19.1667 4L15.1667 0V2.66667H5.83341C5.12617 2.66667 4.44789 2.94762 3.9478 3.44772C3.4477 3.94781 3.16675 4.62609 3.16675 5.33333V26.6667C3.16675 27.3739 3.4477 28.0522 3.9478 28.5523C4.44789 29.0524 5.12617 29.3333 5.83341 29.3333H11.1667V26.6667H5.83341V5.33333H15.1667V8L19.1667 4ZM13.8334 28L17.8334 32V29.3333H27.1667C27.874 29.3333 28.5523 29.0524 29.0524 28.5523C29.5525 28.0522 29.8334 27.3739 29.8334 26.6667V5.33333C29.8334 4.62609 29.5525 3.94781 29.0524 3.44772C28.5523 2.94762 27.874 2.66667 27.1667 2.66667H21.8334V5.33333H27.1667V26.6667H17.8334V24L13.8334 28Z" fill="#0E1726" fill-opacity="0.75"></path>
                        </svg>                        <span>{{ trans('admin.report') }}</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ Request::routeIs('academy.report.*')  ? 'show' : '' }}" id="report" data-bs-parent="#accordionExample">
                    <li class="menu {{ Request::routeIs('academy.report.settlement.index')  ? 'active' : '' }}">
                        <a href="{{ route('academy.report.settlement.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <img src="{{ asset('assetsAdmin/card-checklist.svg') }}" alt="">
                                <span>{{ trans('admin.settlement.Settlements') }}</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Request::routeIs('academy.report.transaction.index')  ? 'active' : '' }}">
                        <a href="{{ route('academy.report.transaction.index') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M535 41c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l64 64c4.5 4.5 7 10.6 7 17s-2.5 12.5-7 17l-64 64c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l23-23L384 112c-13.3 0-24-10.7-24-24s10.7-24 24-24l174.1 0L535 41zM105 377l-23 23L256 400c13.3 0 24 10.7 24 24s-10.7 24-24 24L81.9 448l23 23c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0L7 441c-4.5-4.5-7-10.6-7-17s2.5-12.5 7-17l64-64c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9zM96 64H337.9c-3.7 7.2-5.9 15.3-5.9 24c0 28.7 23.3 52 52 52l117.4 0c-4 17 .6 35.5 13.8 48.8c20.3 20.3 53.2 20.3 73.5 0L608 169.5V384c0 35.3-28.7 64-64 64H302.1c3.7-7.2 5.9-15.3 5.9-24c0-28.7-23.3-52-52-52l-117.4 0c4-17-.6-35.5-13.8-48.8c-20.3-20.3-53.2-20.3-73.5 0L32 342.5V128c0-35.3 28.7-64 64-64zm64 64H96v64c35.3 0 64-28.7 64-64zM544 320c-35.3 0-64 28.7-64 64h64V320zM320 352a96 96 0 1 0 0-192 96 96 0 1 0 0 192z"/></svg>
                                <span>{{ trans('admin.transaction') }}</span>
                            </div>
                        </a>
                    </li>
                    <li class="menu {{ Request::routeIs('academy.report.joins')  ? 'active' : '' }}">
                        <a href="{{ route('academy.report.joins') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <img src="{{ asset('assetsAdmin/person-arms-up.svg') }}" alt="">
                                <span>{{ trans('admin.bookings.bookings') }}</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Request::routeIs('academy.report.coach')  ? 'active' : '' }}">
                        <a href="{{ route('academy.report.coach') }}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M72 88a56 56 0 1 1 112 0A56 56 0 1 1 72 88zM64 245.7C54 256.9 48 271.8 48 288s6 31.1 16 42.3V245.7zm144.4-49.3C178.7 222.7 160 261.2 160 304c0 34.3 12 65.8 32 90.5V416c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V389.2C26.2 371.2 0 332.7 0 288c0-61.9 50.1-112 112-112h32c24 0 46.2 7.5 64.4 20.3zM448 416V394.5c20-24.7 32-56.2 32-90.5c0-42.8-18.7-81.3-48.4-107.7C449.8 183.5 472 176 496 176h32c61.9 0 112 50.1 112 112c0 44.7-26.2 83.2-64 101.2V416c0 17.7-14.3 32-32 32H480c-17.7 0-32-14.3-32-32zm8-328a56 56 0 1 1 112 0A56 56 0 1 1 456 88zM576 245.7v84.7c10-11.3 16-26.1 16-42.3s-6-31.1-16-42.3zM320 32a64 64 0 1 1 0 128 64 64 0 1 1 0-128zM240 304c0 16.2 6 31 16 42.3V261.7c-10 11.3-16 26.1-16 42.3zm144-42.3v84.7c10-11.3 16-26.1 16-42.3s-6-31.1-16-42.3zM448 304c0 44.7-26.2 83.2-64 101.2V448c0 17.7-14.3 32-32 32H288c-17.7 0-32-14.3-32-32V405.2c-37.8-18-64-56.5-64-101.2c0-61.9 50.1-112 112-112h32c61.9 0 112 50.1 112 112z"/></svg>
                                <span>{{ trans('admin.Coaches') }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>
<!--  END SIDEBAR  -->
