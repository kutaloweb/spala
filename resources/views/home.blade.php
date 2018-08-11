<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('config.company_name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="icon" href="/images/favicon.ico">
    <meta name="theme-color" content="#ffffff">
    <link href="{{ mix('/css/style.css') }}" rel="stylesheet">
    <link href="{{ mix('/css/colors/'.config('config.color_theme').'.css') }}" id="theme" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="fix-header fix-sidebar card-no-border">
<div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
    </svg>
</div>
<div id="root">
    <router-view></router-view>
</div>
<script src="/js/lang"></script>
<script src="{{ mix('/js/plugin.js') }}"></script>
<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
