<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'NutriApp')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> {{-- Or wherever your CSS is --}}
</head>
<body class="@yield('body-class')">
    @yield('content')
</body>
</html>
