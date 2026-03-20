<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Red_Theme" data-layout="vertical">

<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Favicon icon-->
  <link rel="shortcut icon" type="image/png" href="{{ asset('') }}assets/images/logos/favicon.png" />

  <!-- Core Css -->
  <link rel="stylesheet" href="{{ asset('') }}assets/css/styles.css" />

  <title>
    {{ config('app.name', 'Laravel') }}
  </title>
  <!-- Owl Carousel  -->
  <link rel="stylesheet" href="{{ asset('') }}assets/libs/owl.carousel/dist/assets/owl.carousel.min.css" />

  @stack('css')
  <style>
    /* Primary Color Override - Red Theme */
    [data-bs-theme=light][data-color-theme=Red_Theme]:root,
    [data-bs-theme=dark][data-color-theme=Red_Theme]:root {
      --bs-primary: #f83f3a;
      --bs-primary-rgb: 248, 63, 58;
      --bs-light-primary: rgba(248, 63, 58, 0.1);
      --bs-primary-bg-subtle: rgba(248, 63, 58, 0.1);
    }

    [data-bs-theme=light][data-color-theme=Red_Theme]:root .btn-primary,
    [data-bs-theme=dark][data-color-theme=Red_Theme]:root .btn-primary {
      --bs-btn-bg: #f83f3a;
      --bs-btn-border-color: #f83f3a;
      --bs-btn-hover-bg: #e02d28;
      --bs-btn-hover-border-color: #e02d28;
    }

    /* Global button primary override */
    .btn-primary {
      background-color: #f83f3a !important;
      border-color: #f83f3a !important;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
      background-color: #e02d28 !important;
      border-color: #e02d28 !important;
    }

    /* Tab navigation active state */
    .nav-tabs .nav-link.active {
      color: #ffffff !important;
      background-color: #f83f3a !important;
      border-color: transparent transparent #f83f3a !important;
    }

    .nav-tabs .nav-link {
      color: #7c8fac;
    }

    .nav-tabs .nav-link:focus {
      color: #ffffff !important;
      background-color: #f83f3a !important;
    }

    /* Badge primary */
    .badge.bg-primary {
      background-color: #f83f3a !important;
    }

    /* Link primary */
    .link-primary {
      color: #f83f3a !important;
    }

    .seat-wrapper {
    display: flex;
    flex-direction: column;
    gap: 12px;
    }

    .seat-row {
        display: grid;
        grid-template-columns: 45px 45px 30px 45px 45px;
        gap: 10px;
        justify-content: center;
    }

    .seat-row.center {
        grid-template-columns: repeat(5, 45px);
    }

    .seat {
        height: 45px;
        border-radius: 6px;
        border: none;
        background: #e0e0e0;
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s;
    }

    .seat:hover {
        background: #d6d6d6;
    }

    .seat.active {
        background: #f83f3a;
        color: #fff;
    }

    .seat-booked {
        background-color: #dc3545 !important; /* merah */
        color: #fff;
        cursor: not-allowed;
        opacity: 0.8;
    }

    .aisle {
        width: 30px;
    }

    .empty {
        visibility: hidden;
    }



    </style>
</head>

<body>
  <!-- Preloader -->
  <div class="preloader">
      <div class="lds-ellipsis">
          <span></span>
          <span></span>
          <span></span>
      </div>
  </div>
  <div id="main-wrapper">
    <!-- Sidebar Start -->
    @include('layouts.sidebar')
    <!--  Sidebar End -->
    <div class="page-wrapper">
      <!--  Header Start -->
      @include('layouts.header')
      <!--  Header End -->

      <div class="body-wrapper">
        <div class="container-fluid">
            @yield('content')
        </div>
      </div>

  <div class="dark-transparent sidebartoggler"></div>
  <script src="{{ asset('') }}assets/js/vendor.min.js"></script>
  <!-- Import Js Files -->
  <script src="{{ asset('') }}assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('') }}assets/libs/simplebar/dist/simplebar.min.js"></script>
  <script src="{{ asset('') }}assets/js/theme/app.init.js"></script>
  <script src="{{ asset('') }}assets/js/theme/theme.js"></script>
  <script src="{{ asset('') }}assets/js/theme/app.min.js"></script>
  <!-- <script src="{{ asset('') }}assets/js/theme/sidebarmenu.js"></script> -->

  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  @stack('js')
</body>

</html>
