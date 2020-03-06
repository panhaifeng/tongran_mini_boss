<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{$title}</title>
    {webcontrol type='LoadJsCss' src="Resource/Script/jquery.js"}
</head>
<body style="padding: 5px 15px;">
  <div class="layui-card-body layadmin-setTheme">
    <div class="layui-card-body layui-text layadmin-about" style="line-height: 200%;">
        <div class="layui-col-xs12 layui-col-sm12 layui-col-md12">
            一、如果还没有打开小程序，请先扫码打开小程序
        </div>
        <div class="layui-col-xs12 layui-col-sm12 layui-col-md12" style="text-align: center;">
            <img src="Resource/Image/zhanhui_qr.jpg?v=1" style="max-height: 200px;margin-left: auto;margin-right: auto;display: inline-block;" class="image-qr">
        </div>
        <!-- <div class="layui-col-xs12 layui-col-sm12 layui-col-md12">
            <img src="Resource/Image/search_sample.jpg" style="max-height: 120px;">
        </div> -->
        <div class="layui-col-xs12 layui-col-sm12 layui-col-md12" style="margin-top: 30px;">
            二、小程序中扫码登录当前账号
        </div>
        <div class="layui-col-xs12 layui-col-sm12 layui-col-md12" style="margin-top: 20px;color: orange;">
            注意： 二维码等同于账号密码，不要泄露！！
            <!-- {$qrcodestr} -->
        </div>
        <div class="layui-col-xs12 layui-col-sm12 layui-col-md12" style="text-align: center;">
            <img src="{$qrcode}" style="max-height: 150px;">
        </div>
        <div class="layui-col-xs12 layui-col-sm12 layui-col-md12" style="margin-top: 20px;color: green;">
            {if $user.openid !=''}
            已绑定微信账号：{$user.nickname}  -  {$user.openid} <br/>
            <input type='button' class="layui-btn layui-btn-danger bingCannel" value='解除绑定' style="background-color: #dedede;color: #333;width: 100px;height: 33px;line-height: 33px;border: 0px;">
            {/if}
        </div>
    </div>

</div>
<script>
    var url = "{url controller=App_Mp action=bindCannel}";
    {literal}
    $('.bingCannel').click(function(){
        if(!confirm('是否要解除绑定微信!')){
            return false;
        }
        $.post(url,{},function(res){
            if(res.success){
                window.top.showMsg("解除绑定完成");
                window.location.href = window.location.href;
            }
        },'json');
    });
    {/literal}
</script>
</body>
</html>