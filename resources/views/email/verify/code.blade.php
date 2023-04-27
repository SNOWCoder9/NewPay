<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0" style="font-size:14px;font-family:Microsoft Yahei,Arial,Helvetica,sans-serif;padding:0;margin:0;color:#333;background-image:url(https://ci5.googleusercontent.com/proxy/jjm05xLyfHgTlorIlQeq1oSnb-nDnywhev1faP--AYobL2jLHbk4XHeVLjShoin7_Ba_A7fL06PtNYflAAWHKY5uSUVkJTdaIcnn8KBG-g=s0-d-e1-ft#https://ftp.binance.com/img/20180206/image_1509938087118.jpg);background-color:#f7f7f7;background-repeat:repeat-x;background-position:bottom left">
    <tbody>
    <tr>
        <td style="padding: 30px;">
            <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                        <td align="center" valign="middle" style="padding:33px 0;">
                            <h1 style="text-align: center;color: #1890ff;font-size: 28px;">{{ config('app.name') }}</h1>
                        </td>
                    </tr>
                  <tr>
                    <td>
                        <div style="padding:0 30px;background:#fff">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tbody><tr>
                                    <td style="border-bottom:1px solid #e6e6e6;font-size:18px;padding:20px 0">
                                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                            <tbody><tr>
                                                <td>登录<span class="il">验证</span></td>
                                                <td>

                                                </td>
                                            </tr>
                                            </tbody></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px;line-height:30px;padding:20px 0 0;color:#666">
                                        您好，<br>
                                    </td></tr><tr>
                                    <td style="font-size:14px;line-height:30px;padding:0 0 20px;color:#666">您的<span class="il">验证</span><span class="il">码</span>为：
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding:5px 0;font-size:20px;font-weight:bolder;color:#1890ff">{{ $code }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:20px 0 10px 0;line-height:26px;color:#666">
                                        该<span class="il">验证</span><span class="il">码</span>有效期为10分钟，请勿向他人透露！<br>若非您本人操作，可以点击下方链接，<wbr>联系官方客服：<br><a style="color:#1890ff" href="{{ config('app.url') }}" target="_blank">{{ config('app.url') }}</a>
                                    </td>
                                </tr>



                                <tr>
                                    <td style="padding:30px 0 15px 0;font-size:12px;color:#999;line-height:20px">
                                        {{ config('app.name') }}团队<br>系统邮件，请勿回复
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
  </tbody>
</table>
