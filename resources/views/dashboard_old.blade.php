<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome to the Dashboard</h2>
        <p>You have successfully logged in with Microsoft Azure!</p>
        @if(Auth::check() && Auth::user()->role == 1)
         <a href="{{ route('assign.admin') }}" class="btn btn-primary">Assign Admin</a>
        @endif
        @if(Auth::check() && (Auth::user()->role == 1 || Auth::user()->role == 2))
         <a href="{{ route('appraisal.view') }}" class="btn btn-primary">Appraisal</a>
        @endif
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>
</body>
</html>
