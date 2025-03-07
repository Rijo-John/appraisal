<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bootstrap demo</title>

  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-icons/font/bootstrap-icons.css') }}">
  <style>

  </style>
</head>

<body>

  @include('layouts.header')
  <div class="container-fluid wrapper">
    <div class="row h-100">

      @include('layouts.sidebar')
      
        @yield('content')
     
      
    </div>
  </div>
  <script src="{{ asset('assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>