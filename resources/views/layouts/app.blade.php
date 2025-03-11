<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Appraisal Management System</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-icons/font/bootstrap-icons.css') }}">
  <style>

  </style>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
  <script src="{{ asset('assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> 
  
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>

<body>

  @include('layouts.header')
  <div class="container-fluid wrapper">
    <div class="row h-100">

      @include('layouts.sidebar')
      
        @yield('content')
     
      
    </div>
  </div>
  

</body>

</html>