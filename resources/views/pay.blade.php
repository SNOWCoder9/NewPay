<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>TRON支付</title>
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <link rel="stylesheet" href="/assets/css/layui.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link id="layuicss-layer" rel="stylesheet" href="/assets/css/layer.css" media="all">
</head>
<body>
<div class="background"></div>
<div class="header" style="">
    <div class="layui-row">
        <div class="layui-col-md8 layui-col-md-offset2 layui-col-sm12">
            <div class="header-box">

            </div>
        </div>
    </div>
</div>
<script src="/assets/js/clipboard.min.js"></script>
<script src="/assets/js/qrcode.min.js"></script>
<style>
    .show_code {
        text-align: center;
        border: 3px solid #3C8CE7;
        border-radius: 10px;
        width: 30rem;
        margin: 0 auto;
        padding-top: 10px
    }

    @media (max-width: 768px) {
        .show_code {
            text-align: center;
            border: 3px solid #3C8CE7;
            border-radius: 10px;
            width: 100%;
            margin: 0 auto;
            padding-top: 10px
        }
    }

    #qrcode {
        margin: 20px auto;
        width:240px;
        height:240px;
    }
    .layui-fixbar .layui-icon {
        border-radius: 50px;
    }
    .main {
        min-height: 800px;
    }
</style>
<div class="main">
    <div class="layui-row">
        <div class="layui-col-md8 layui-col-md-offset2 layui-col-sm12">
            <div class="main-box">
                <div class="layui-row pay-title">
                    <div class="layui-col-md8">
                        <svg style="margin-bottom: -6px;" t="1603122535052" class="icon" viewBox="0 0 1024 1024"
                             version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1949" width="27" height="27">
                        <path
                            d="M146.432 336.896h-81.92V106.496l40.96-40.96h231.424v81.92H146.432zM336.896 958.464H105.472l-40.96-40.96V687.104h81.92v189.44h190.464zM956.416 336.896h-81.92V147.456H684.032v-81.92h231.424l40.96 40.96zM915.456 958.464H613.376v-81.92h261.12V659.456h81.92v258.048z"
                            fill="#3C8CE7" p-id="1950" data-spm-anchor-id="a313x.7781069.0.i11" class="selected"></path>
                        <path
                            d="M326.656 334.848h61.44v98.304h-61.44zM415.744 575.488h61.44v133.12h-61.44zM265.216 575.488h61.44v114.688h-61.44zM566.272 575.488h61.44v98.304h-61.44zM706.56 575.488h61.44v154.624h-61.44zM477.184 297.984h61.44v135.168h-61.44zM627.712 329.728h61.44v103.424h-61.44z"
                            fill="#00EAFF" p-id="1951" data-spm-anchor-id="a313x.7781069.0.i9" class=""></path>
                        <path d="M10.24 473.088h1003.52v61.44H10.24z" fill="#3C8CE7" p-id="1952"
                              data-spm-anchor-id="a313x.7781069.0.i12" class="selected"></path>
                    </svg>
                    扫码支付
                    </div>
                    <div class="layui-col-md4" style="text-align: right!important;" id="test2"></div>
                </div>
                <div class="layui-card-body">
                    <div class="product-info">
                        <p style="color: #3C8CE7 ;font-size: 18px;font-weight: 700; text-align: center;margin: 20px 0">
                            支付方式：[ USDT(TRC20) ], 请打开 APP 扫码支付！有效期10分钟
                        </p>
                    </div>

                    <div class="show_code">
                        <p class="product-pay-price" style="font-size: 16px;color: #737373;;margin-bottom: 10px">
                            需要支付USDT
                        </p>
                        <p id="copy_price" data-clipboard-text="{{ $data['price'] }}">
                            <span style="font-size: 24px;font-weight: bold">${{ $data['price'] }}</span>
                        </p>
                        <div id="copy_address" data-clipboard-text="{{ $data['address'] }}"
                             style="cursor:pointer">
                            <div id="qrcode"></div>
                            <p class="product-pay-price" style="font-size: 16px; margin-bottom: 15px;">
                                地址： <span style="color: #999;">{{ $data['address'] }}</span>
                            </p>
                        </div>

                    </div>
                    <div class="layui-row" style="text-align: center; margin-top: 20px">
                        <p class="product-pay-price" style="font-size: 16px;color: red;">
                            支付金额<strong>必须相同</strong>，否则订单将无效
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var clipboard = new ClipboardJS('#copy_address');
    clipboard.on('success', function (e) {
        alert("复制成功");
    });
    var clipboard2 = new ClipboardJS('#copy_price');
    clipboard2.on('success', function (e) {
        alert("复制成功");
    });
</script>
<script src="/assets/js/layui.js"></script>
<script src="/assets/js/jquery-3.4.1.min.js"></script>
<script src="/assets/js/main.js"></script>
<script src="/assets/js/layer.js"></script>
<script type="text/javascript">
    new QRCode("qrcode", {
        text: "{{ $data['address'] }}",
        width: 240,
        height: 240,
        colorDark : "#2c2c2c",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    layui.use(['util'], function() {
        var util = layui.util;
        util.fixbar({
            bar1: '&#xe626;'
            ,css: {right: 10, bottom: 50}
            ,bgcolor: '#3c8ce7'
            ,click: function(type){
                if(type === 'bar1'){
                    window.open("https://t.me/{{ $data['user']['telegram_account'] }}")
                }
            }
        });

        var curTime = new Date();
        //示例
        var endTime = new Date(curTime.setMinutes(curTime.getMinutes() + 10)).getTime() //假设为结束日期
            ,serverTime = new Date().getTime(); //假设为当前服务器时间，这里采用的是本地时间，实际使用一般是取服务端的

        util.countdown(endTime, serverTime, function(date, serverTime, timer){
            var str = date[2] + '分' + date[3] + '秒';
            layui.$('#test2').html('剩余时间：'+ str);
        });
        layer.tips('数字货币交易到账会有延迟，请耐心等待。如果长时间没提示【交易成功】，请点击联系商家咨询！', '.layui-fixbar', {
            tips: [2,'#3c8ce7'],
            time: 0
        });
    });

</script>
<script>
    var getting = {
        url: "/api/v1/check_order_status/{{ $data['key'] }}",
        dataType: 'json',
        success: function (res) {
            if (res.code === -1) {
                window.clearTimeout(timer);
                layer.alert("订单已过期，请重新下单", {
                    icon: 2
                }, function () {
                    window.location.href = "{{ $data['return_url'] }}"
                });
            }
            if (res.code === 1) {
                window.clearTimeout(timer);
                layer.alert("支付成功！", {
                    icon: 1,
                    closeBtn: 0
                }, function () {
                    window.location.href = "{{ $data['return_url'] }}"
                });
            }
        }

    };
    var timer = window.setInterval(function () {
        $.ajax(getting)
    }, 3000);
</script>

</body>
</html>
