
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Laravel AdminR')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset(getSetting('site_favicon')) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.9.55/css/materialdesignicons.min.css"/>
    <link rel="stylesheet" href="{{ asset('vendor/liquid-lite/coreui/css/coreui.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/liquid-lite/css/liquid.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    @stack('scopedCss')
</head>
<body class="c-app flex-row align-items-center">

    @yield('content')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.2/perfect-scrollbar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro@3.4.0/dist/js/coreui.min.js"></script>
    <script src="{{ asset('vendor/liquid/js/adminr-core.js') }}"></script>

    @stack('scopedJs')
</body>
</html>
