<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Performance</title>
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-icons/font/bootstrap-icons.css') }}">
  <style>

  </style>
</head>

<body>

    <div class="login-bg-wrapper">
        <div class="logo-sidebar">
        <div class="row">
          <div class="col text-center">
            <img src="{{ asset('assets/images/appraisal-management-logo.png') }}">
          </div>
        </div>
        <div class="login-bottom-content">
          
         
          <hr class="mt-5 mb-5">
          <p class="text-body-secondary">Log in using your account on:</p>
          <div class="d-grid gap-2 col-12 mx-auto">
            <a href="{{ route('azure.login')}}" class="btn btn-outline-primary btn-lg ">
                <img src="{{ asset('assets/images/microsoft-icon.png') }}" class="me-2"> Microsoft</a>
          </div>
        </div>
      </div>
       </div>
  <script src="{{ asset('assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>