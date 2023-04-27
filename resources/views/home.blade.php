<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{env('APP_NAME')}}</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link href='https://fonts.loli.net/css?family=Raleway:300,400,500,600,700|Open+Sans:400,600,700' rel='stylesheet'
          type='text/css'>

    <link rel="stylesheet" href="/theme/css/bootstrap.min.css">
    <link rel="stylesheet" href="/theme/css/font-awesome.min.css">
    <link rel="stylesheet" href="/theme/css/preview.css">
    <link rel="stylesheet" href="/theme/css/responsive.css">

    <link rel="shortcut icon" type="image/png" href="/theme/img/favicon.ico">


    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body data-spy="scroll" data-target="#scroll-menu" data-offset="100">

<div class="preloader-wrap">
    <div class="preloader-inside">
        <div class="preloader">
            <div class="preloader-inner box1"></div>
            <div class="preloader-inner box2"></div>
            <div class="preloader-inner box3"></div>
        </div>
    </div>
</div>

<header id="home">
    <div class="full-wrap hero-wrap">
        <div class="hero-inner">
            <div class="hero-wrap-inside">
                <h1>{{env('APP_NAME')}}</h1>
                <span>{{env('APP_NAME')}}</span>
                <div class="purchase-button">

                    <a class="btn btn-default btn-orange smoothscroll" href="{{ url('index') }}">
                        <i class="fa fa-link"></i>
                        点击登录
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script src="/theme/js/jquery-1.12.4.min.js"></script>
<script src="/theme/js/bootstrap.min.js"></script>
<script src="/theme/js/theme.js"></script>

<script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-23581568-13');
</script>
</body>
</html>
