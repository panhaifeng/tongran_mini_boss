<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><{webcontrol type='GetAppInf' varName='systemV'}></title>
<link rel="stylesheet" type="text/css" href="Resource/Css/qrcode.css">
</head>
<body>
    <div class="container" style="overflow: hidden;">
        <div class="header">
            <a href='http://www.eqinfo.com.cn' target="_blank"><div class="logo" style="background-image:url(Resource/Image/LoginNew/logo.png)"></div></a>
        </div>
        <div class="qrcodeBox">
            <!-- 二维码登陆 -->
            <div class="input" id="qrcode_form">
                <div id="qrcode"><img style="width: 270px;height: 270px;" /></div>
                <div id="prompt"><img src="Resource/Image/LoginNew/prompt_success.png" /></div>
            </div>
            <div style="text-align: center;font-size: 20px;margin-bottom: 50px;" id="qrText">
                手机微信扫码验证
            </div>
        </div>
        <div class="foot">
            <a href="http://www.eqinfo.com.cn" target="_blank" style="font-size: 13px;color: #fff;">关于易奇</a>
            &nbsp;|&nbsp;
            <span>©2007 - <{$smarty.now|default:'2015'|date_format:'%Y'}> EQINFO Inc. All Rights Reserved.</span>
        </div>

    </div>
</body>
<script language="javascript" type="text/javascript" src="Resource/Script/jquery.1.9.1.js"></script>
<script language="javascript" type="text/javascript" src="Resource/Script/jquery.form.js"></script>
<script type="text/javascript">
var backurl='Resource/Image/LoginNew/qrCode_bg.jpg';
var qrCodePath='<{$qrCodePath}>';
var token='<{$token}>';
var mainUrl = '<{$mainUrl}>';
var pngSuccess = 'Resource/Image/LoginNew/prompt_success.png';
var pngFail = 'Resource/Image/LoginNew/prompt_error.png';
var pngRefresh = 'Resource/Image/LoginNew/prompt_refresh.png';

    $(function(){
        // 设置二维码图片
        $('img','#qrcode').attr('src',qrCodePath);
        // 设置背景图片
        $('body').css('background-image', 'url('+backurl+')');
        // 开启轮询
        setTimeout('lunxun()',500);
        // 刷新点击
        $('#prompt').on('click','#aRefresh',function(){
            refresh();
        });
    });

// 刷新
function refresh(){
    var url='?controller=Login&action=RefreshQrcode';
    var param={'token':token};
    $.getJSON(url,param,function(json){
        if(!json.success){
            $('#qrText').html('<span style="color:red">身份信息已失效</span>');
            // setTimeout(function(){window.location.href=json.loginUrl;}, 500);
            return false;
        }
        token = json.data.token;
        $('#qrText').html('手机微信扫码验证');
        $('img','#qrcode').attr('src',json.data.qrCodePath);
        $('#prompt').css('display','none');
        setTimeout('lunxun()',500);
    });

}

// 轮询获取二维码验证状态
function lunxun(){
    var url='?controller=Login&action=GetStatusByAjax';
    var param={'token':token};
    $.getJSON(url,param,function(json){
        if(json.success){
            if(json.verifyInfo.status!='CREATED' && json.verifyInfo.status!='SCANED'){
                if(json.verifyInfo.status=='OVERTIME'){
                    // 超时--显示刷新按钮
                    var html = '<a id="aRefresh" style="margin-top:-263px;"><img src="'+pngRefresh+'"></a>';
                    $('#prompt').html(html);
                    // $('img','#prompt').attr('src',pngRefresh);
                    $('#prompt').css('display','block');
                    $('#qrText').html('二维码已失效，请刷新');
                    return false;
                }else if(json.verifyInfo.status=='SUCCESS'){
                    // 验证成功跳转--显示刷新按钮
                    $('img','#prompt').attr('src',pngSuccess);
                    $('#prompt').css('display','block');
                    $('#qrText').html('<span style="color:#25AE88">验证成功</span>');
                    setTimeout(function(){window.location.href=mainUrl;}, 500);
                    return false;
                }else{
                    // 失败--显示错误信息
                    $('img','#prompt').attr('src',pngFail);
                    $('#prompt').css('display','block');
                    $('#qrText').html(json.verifyInfo.message);
                    return false;
                }
            }
            setTimeout('lunxun()',500);
        }

    });
}

</script>
</html>
