<div class="sidebar-wrapper" data-layout="stroke-svg">
    <div class="logo-wrapper">
        <a href="/dashboard">
            <img class="img-fluid" style="width: 100px;" src="{{ asset('own_assets/logo/logo.png') }}" alt="">
        </a>
        <div class="back-btn"><i class="fa fa-angle-left"> </i></div>
        <div class="toggle-sidebar">
            <i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i>
        </div>
    </div>

    <div class="logo-icon-wrapper">
        <a href="/dashboard">
            <img class="img-fluid" src="{{ asset('own_assets/logo/logo.png') }}" alt="">
        </a>
    </div>
    <nav class="sidebar-main">
        <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
        <div id="sidebar-menu">
            <ul class="sidebar-links" id="simple-bar">
                <li class="back-btn"><a href="/dashboard"><img class="img-fluid"
                            src="{{ asset('own_assets/logo/logo.png') }}" alt=""></a>
                    <div class="mobile-back text-end"> <span>Back </span><i class="fa fa-angle-right ps-2"
                            aria-hidden="true"></i></div>
                </li>
                <li class="pin-title sidebar-main-title">
                    <div>
                        <h6>Pinned</h6>
                    </div>
                </li>
                <li class="sidebar-main-title">
                    <div>
                        <h6 class="lan-1">General</h6>
                    </div>
                </li>

                <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i><a class="sidebar-link sidebar-title link-nav"
                        href="/dashboard">
                        <svg class="stroke-icon">
                            <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                        </svg>
                        <svg class="fill-icon">
                            <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#fill-home') }}"></use>
                        </svg><span class="lan-3">Dashboard </span></a>
                </li>

                <li class="sidebar-main-title">
                    <div>
                        <h6 class="">Master</h6>
                    </div>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/users">
                        <i class="fa fa-users text-white"></i>
                        <span class="">Users</span>
                    </a>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/banner">
                        <i class="fa fa-picture-o text-white" aria-hidden="true"></i>
                        <span class="">Banner</span>
                    </a>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/products">
                        <i class="fa fa-archive text-white" aria-hidden="true"></i>
                        <span class="">Produk</span>
                    </a>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/stores">
                        <i class="fa fa-shopping-bag text-white"></i>
                        <span class="">Toko (Cabang)</span>
                    </a>
                </li>

                <li class="sidebar-main-title">
                    <div>
                        <h6 class="">Marketing</h6>
                    </div>
                </li>

                <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title"
                        href="#">
                        <i class="fa fa-bullhorn text-white"></i>
                        <span>Promo </span>
                        <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                    </a>
                    <ul class="sidebar-submenu" style="display: none;">
                        <li><a href="/promo-reguler">Promo Reguler</a></li>
                        <li><a href="/promo-flash">Promo Flash</a></li>
                        <li><a href="/promo-customer">Promo Customer</a></li>
                        <li><a href="/promo-video">Promo Video</a></li>
                    </ul>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/reward-pont">
                        <i class="fa fa-star text-white"></i>
                        <span class="">Reward Point</span>
                    </a>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/voucher">
                        <i class="fa fa-ticket text-white"></i>
                        <span class="">Vucher</span>
                    </a>
                </li>

                <li class="sidebar-main-title">
                    <div>
                        <h6 class="">Parenting Edu</h6>
                    </div>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/materi-parenting">
                        <i class="fa fa-book text-white"></i>
                        <span class="">Materi Parenting</span>
                    </a>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/kuis">
                        <i class="fa fa-check-square text-white"></i>
                        <span class="">Kuis Parenting</span>
                    </a>
                </li>

                <li class="sidebar-main-title">
                    <div>
                        <h6 class="">Support</h6>
                    </div>
                </li>

                <li class="sidebar-list">
                    <a class="sidebar-link sidebar-title link-nav" href="/survey-layanan">
                        <i class="fa fa-clipboard text-white"></i>
                        <span>Survey Layanan</span>
                    </a>
                </li>

            </ul>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</div>
