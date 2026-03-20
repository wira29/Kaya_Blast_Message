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

  <title>Login | Web Kaya</title>

  <style>
    /* Primary Color Override - Red Theme */
    [data-bs-theme=light][data-color-theme=Red_Theme]:root,
    [data-bs-theme=dark][data-color-theme=Red_Theme]:root {
      --bs-primary: #f83f3a;
      --bs-primary-rgb: 248, 63, 58;
      --bs-light-primary: rgba(248, 63, 58, 0.1);
      --bs-primary-bg-subtle: rgba(248, 63, 58, 0.1);
    }

    /* Global button primary override */
    .btn-primary {
      background-color: #f83f3a !important;
      border-color: #f83f3a !important;
      color: #ffffff !important;
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
      background-color: #e02d28 !important;
      border-color: #e02d28 !important;
    }

    .form-check-input:checked {
      background-color: #f83f3a;
      border-color: #f83f3a;
    }

    .form-check-input:focus {
      border-color: #f83f3a;
      box-shadow: 0 0 0 0.25rem rgba(248, 63, 58, 0.25);
    }
  </style>
</head>

<body>
  <!-- Preloader -->
  <div class="preloader">
    <img src="../assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
  </div>
  <div id="main-wrapper" class="auth-customizer-none">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 w-100">
      <div class="position-relative z-index-5">
        <div class="row">
          <div class="col-xl-7 col-xxl-8">
            <div class="d-none d-xl-flex align-items-center justify-content-center h-n80">
              <img src="{{ asset('') }}assets/images/backgrounds/login-security.svg" alt="modernize-img" class="img-fluid" width="500">
            </div>
          </div>
          <div class="col-xl-5 col-xxl-4">
            <div class="authentication-login min-vh-100 bg-body row justify-content-center align-items-center p-4">
              <div class="auth-max-width col-sm-8 col-md-6 col-xl-7 px-4">
                <h2 class="mb-1 fs-7 fw-bolder">Selamat Datang</h2>
                <p class="mb-7">masukan email & password untuk menuju beranda.</p>

                <form method="POST" action="{{ route('login') }}">
                   @csrf

                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="admin@example.com" id="exampleInputEmail1" aria-describedby="emailHelp">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="mb-4">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" placeholder="password" name="password" class="form-control @error('password') is-invalid @enderror" id="exampleInputPassword1">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                      <label class="form-check-label text-dark fs-3" for="flexCheckChecked" >
                        Ingat Saya
                      </label>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Masuk</button>
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-medium">copyright © {{ now()->year }}</p>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <!-- Import Js Files -->
  <script src="{{ asset('') }}assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('') }}assets/libs/simplebar/dist/simplebar.min.js"></script>
  <script src="{{ asset('') }}assets/js/theme/app.init.js"></script>
  <script src="{{ asset('') }}assets/js/theme/theme.js"></script>
  <!-- <script src="{{ asset('') }}assets/js/theme/app.min.js"></script> -->

  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>
