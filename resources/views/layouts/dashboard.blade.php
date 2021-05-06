<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content={{csrf_token()}}>

    {{-- Title --}}
    <title>JS3</title>

    {{-- Custom stylesheets --}}
    @include('layouts.admin-partials.styles')
    @yield('css')
    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
</head>

<body class="hold-transition layout-top-nav">
  <div class="wrapper">
    @include('layouts.admin-partials.navbar-rel')
      @yield('content')
    @include('layouts.admin-partials.footer')
  </div>
  @include('layouts.admin-partials.scripts')
  @yield('javascript')
</body>

</html>
