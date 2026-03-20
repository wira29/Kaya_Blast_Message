
<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Favicon icon-->
  <link rel="shortcut icon" type="image/png" href="{{ asset('') }}assets/images/logos/favicon.png" />

  <!-- Core Css -->
  <link rel="stylesheet" href="{{ asset('') }}assets/css/styles.css" />

  <title>Pelanggan - Sanstiket</title>

  @stack('css')

  <style>
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
        background: #5d87ff;
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
    <img src="{{ asset('') }}assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
  </div>

  <!-- ------------------------------------- -->
  <!-- Header Start -->
  <!-- ------------------------------------- -->
  <header class="header-fp p-0 w-100">
    <nav class="navbar navbar-expand-lg bg-primary-subtle py-2 py-lg-10">
      <div class="custom-container d-flex align-items-center justify-content-between">
        <a href="../main/frontend-landingpage.html" class="text-nowrap logo-img">
          <img src="{{ asset('') }}assets/travel-logo.png" width="100" class="dark-logo" alt="Logo-Dark" />
          <img src="{{ asset('') }}assets/travel-logo.png" width="100" class="light-logo" alt="Logo-light" />
        </a>
        <button class="navbar-toggler border-0 p-0 shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
          <i class="ti ti-menu-2 fs-8"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto mb-2 gap-xl-7 gap-8 mb-lg-0">
            <!-- <li class="nav-item">
              <a class="nav-link active fs-4 fw-bold text-dark link-primary" href="../main/frontend-pricingpage.html">Pesan Tiket</a>
            </li> -->
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <!-- ------------------------------------- -->
  <!-- Header End -->
  <!-- ------------------------------------- -->

  <!-- ------------------------------------- -->
  <!-- Responsive Sidebar Start -->
  <!-- ------------------------------------- -->
  <!-- <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
      <a href="../main/frontend-landingpage.html">
        <img src="{{ asset('') }}assets/images/logos/dark-logo.svg" alt="Logo-light" />
      </a>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="list-unstyled ps-0">
        <li class="mb-1">
          <a href="../main/frontend-aboutpage.html" class="px-0 fs-4 d-block text-dark link-primary w-100 py-2">
            About Us
          </a>
        </li>

        <li class="mb-1">
          <a href="../main/frontend-blogpage.html" class="px-0 fs-4 d-block w-100 py-2 text-dark link-primary">
            Blog
          </a>
        </li>

        <li class="mb-1">
          <a href="../main/frontend-portfoliopage.html" class="px-0 fs-4 d-flex align-items-center justify-content-start gap-2 w-100 py-2 text-dark link-primary">
            Portfolio
            <span class="badge text-primary bg-primary-subtle fs-2 fw-bolder hstack">New</span>
          </a>
        </li>

        <li class="mb-1">
          <a href="../main/index.html" class="px-0 fs-4 d-block w-100 py-2 text-dark link-primary">
            Dashboard
          </a>
        </li>

        <li class="mb-1">
          <a href="../main/frontend-pricingpage.html" class="px-0 fs-4 d-block w-100 py-2 text-dark link-primary">
            Pricing
          </a>
        </li>

        <li class="mb-1">
          <a href="../main/frontend-contactpage.html" class="px-0 fs-4 d-block w-100 py-2 text-dark link-primary">
            Contact
          </a>
        </li>
        <li class="mt-3">
          <a href="../main/authentication-login.html" class="btn btn-primary w-100">Log In</a>
        </li>
      </ul>
    </div>
  </div> -->
  <!-- ------------------------------------- -->
  <!-- Responsive Sidebar End -->
  <!-- ------------------------------------- -->

  @yield('content')

  <!-- ------------------------------------- -->
  <!-- Footer Start -->
  <!-- ------------------------------------- -->
  <footer>
    <div class="container-fluid">
      <div class="d-flex justify-content-between py-7 flex-md-nowrap flex-wrap gap-sm-0 gap-3">
        <div class="d-flex gap-3 align-items-center">
          <p class="fs-4 mb-0">All rights reserved by Sanstiket. </p>
        </div>
        <div>
          <p class="mb-0">Dibuat oleh <a target="_blank" href="https://adminmart.com/" class="text-primary link-primary">sanstiket.com</a>.</p>
        </div>
      </div>
    </div>
  </footer>
  <!-- ------------------------------------- -->
  <!-- Footer End -->
  <!-- ------------------------------------- -->

  <!-- Scroll Top -->
  <a href="javascript:void(0)" class="top-btn btn btn-primary d-flex align-items-center justify-content-center round-54 p-0 rounded-circle">
    <i class="ti ti-arrow-up fs-7"></i>
  </a>

  <script src="{{ asset('') }}assets/js/vendor.min.js"></script>
  <!-- Import Js Files -->
  <script src="{{ asset('') }}assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('') }}assets/libs/simplebar/dist/simplebar.min.js"></script>
  <script src="{{ asset('') }}assets/js/theme/app.init.js"></script>
  <script src="{{ asset('') }}assets/js/theme/theme.js"></script>
  <script src="{{ asset('') }}assets/js/theme/app.min.js"></script>

  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="{{ asset('') }}assets/libs/owl.carousel/dist/owl.carousel.min.js"></script>
  <script src="{{ asset('') }}assets/js/frontend-landingpage/homepage.js"></script>

  @stack('js')
</body>

</html>