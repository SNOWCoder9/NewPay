<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{env('APP_NAME')}}</title>
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/ionicons.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <style>
        .ant-card{
            border-radius: 5px !important;
        }
        @media only screen and (max-width: 1024px) {
            .dashboard-table .ant-table table{
                width: 1200px;
            }
            .dashboard-table .ant-table-body {
                overflow-x: scroll;
            }
        }
        .develop-label .ant-descriptions-item-label {
            width: 20%;
        }
    </style>
</head>
<body style="margin: 0;padding: 0;">
<div id="app"></div>

<script src="/i18n/i18n.js"></script>
<script src="/i18n/zh_CN.js"></script>
<script src="/i18n/en.js"></script>
<script src="/js/vue@2.6.10.min.js"></script>
<script src="/js/vue-router@3.0.1.min.js"></script>
<script src="{{mix('/build/manifest.js')}}"></script>
<script src="{{mix('/build/vendor.js')}}"></script>
<script src="{{mix('/build/app.js') }}"></script>
</body>
</html>
