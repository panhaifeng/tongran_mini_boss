<!DOCTYPE html>
<html>
<head>
<meta name="renderer" content="webkit"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><{webcontrol type='GetAppInf' varName='systemV'}></title>
<link rel="stylesheet" type="text/css" href="Resource/Css/loginNew.css">
<link type="favicon" rel="shortcut icon" href="favicon.ico" />
<{if $login.CssFile}><link rel="stylesheet" type="text/css" href="<{$login.CssFile}>"><{/if}>
<script type="text/javascript">
	//防止嵌套，并当权限退出时候使得最top级别可以退出
	if(top.location != location){
        top.location.href = top.location.href;
    }
</script>
</head>
<body>
	<div class="container">
		<div class="header">
			<a href='javascript:;'><div class="logo" style="background-image:url(Resource/Image/LoginNew/logo.png);"></div></a>
			<div class="link">
				<span><{webcontrol type='GetAppInf' varName='systemV'}></span>
				&nbsp;|&nbsp;
				<span>免费服务热线: <{$login.servTel}></span>
			</div>
		</div>

		<div class="content" style="background-image:url(<{$login.bg64|default:'Resource/Image/LoginNew/draw.jpg'}>);backgroud-size:100% 100%">
			<div class="mainInner">
				<div class="rightBox">
					<div class="header_login">
						<div id="common_login" to="kuaijie_form" class="login_btn col-6 text-center active">账号密码登陆</div>
						<!-- <div id="kuaijie_login" to="qrcode_form" class="login_btn col-6 text-center">快速登陆</div> -->
						<div class="active_bottom">&nbsp;</div>
					</div>

					<!-- 密码登陆 -->
			<div id="divError"></div>
	        <div class="input input_block" id="kuaijie_form">
	        	<form action="<{url controller=$smarty.get.controller action='login'}>" method="post" autocomplete='off' id="form_login">
	        		<div class="uinArea" id="uinArea">
	        			<label class="input_tips" id="uin_tips" for="username">用户名</label>
		        			<input type="text" class="inputstyle" id="username" name="username" tabindex="1">
	        		</div>
	        		<div class="pwdArea" id="pwdArea">
	        			<label class="input_tips" id="pwd_tips" for="password">密码</label>
	        				<input type="password" class="inputstyle password" id="password" name="password" tabindex="2">
	        		</div>
	        		<!-- <div class="verifyArea" id="verifyArea">
	        			<label class="input_tips" id="verify_tips" for="verify">验证码</label>
	        				<input type="text" class="inputstyle verify" id="verify" name="verify" tabindex="3" autocomplete="off">
                            <img id="imgCode" class="verify-img" src="<{url controller=$smarty.get.controller action='ImgCode' code=$code}>" onclick="freshImg()">
	        		</div> -->
	            <button type="submit" id="submit" tabindex="4" style="<{if $login.btnColor}>background-color:<{$login.btnColor}> <{/if}>">登 录</button>
	            <input type="hidden" name="_t" value="<{$token}>">
	           </form>
	        </div>
					<!-- 二维码登陆 -->
					<!-- <div class="input input_hide" id="qrcode_form">
						<div class="login_text">请使用<span style='color:#3481D8'>微信扫一扫</span>登陆</div>
						<div class="br_20">&nbsp;</div>
						<div id="qrcode"><img src="Resource/Image/LoginNew/yiqi.png" /></div>
					</div> -->
				</div>
			</div>
		</div>
		<div class="footer">
			<a href="http://www.eqinfo.com.cn" target="_blank">关于易奇</a>
			&nbsp;|&nbsp;
			<span class="gray">© 2007-<{$smarty.now|default:'2015'|date_format:'%Y'}> Inc. All Rights Reserved.</span>
		</div>
	</div>
	<iframe width="0" height="0" border=0 src="http://sev1.eqinfo.com.cn/eqinfo_chrome"></iframe>
</body>
<script language="javascript" type="text/javascript" src="Resource/Script/jquery.js"></script>
<script language="javascript" type="text/javascript" src="Resource/Script/jquery.form.js"></script>
<script language="javascript" type="text/javascript" src="Resource/Script/moo.min.js"></script>
<{if $login.JsFile}>
<script language="javascript" type="text/javascript" src="<{$login.JsFile}>"></script>
<{/if}>
<script type="text/javascript">
	var urlImage = '<{url controller=$smarty.get.controller action='ImgCode'}>';
	$(function(){
		//加载界面就判断
		$('.inputstyle').each(function(){
			if(this.value==''){
				$(this).parent().find('label').css({'display':'block'});
			}else{
				$(this).parent().find('label').css({'display':'none'});
			}
		});
		//placeholder效果模拟
		$('.inputstyle').keydown(function(event){
			$(this).parent().find('label').css({'display':'none'});
		});
		$('.inputstyle').keyup(function(event){
			if(this.value==''){
				$(this).parent().find('label').css({'display':'block'});
			}else{
				$(this).parent().find('label').css({'display':'none'});
			}
		});
		$('.inputstyle').blur(function(){
			//边框
			$(this).removeClass('inputstyle_focus');

			//判断是否要显示label placeholder
			if(this.value==''){
				$(this).parent().find('label').css({'display':'block'});
			}else{
				$(this).parent().find('label').css({'display':'none'});
			}
		});

		//边框聚焦问题
		$('.inputstyle').focus(function(){
			$(this).addClass('inputstyle_focus');
		});

		//切换登陆方式
		$('.login_btn').click(function(){
			var that = this;
			//按钮颜色改变
			$('.login_btn').removeClass('active');
			$(that).addClass('active');

			$('.active_bottom').css({'left':(that.offsetLeft+35)+'px'});

			//显示的登陆框改变
			$('.input').removeClass('input_block').addClass('input_hide');
			$('#'+$(that).attr('to')).removeClass('input_hide').addClass('input_block');
		});

		//聚焦用户名输入
		$('#username').focus();

		//确定按钮点击后效果
		$('#form_login').submit(function(){
			$('#submit').attr('disabled',true);
			$('#submit').text('登录中…');
			$(this).ajaxSubmit({
				'data':{'is_ajax':true},
				success:function(t,b,f){
					var json = eval("("+t+")");
					if(json.success==true){
						showSucc('登陆成功');
						setTimeout(function(){window.location.href=json.href;}, 500);
					}else{
						showError(json.msg);
						setTimeout(function(){
							$('#submit').attr('disabled',false);
							$('#submit').text('登 录');
						}, 500);
					}
				}
			});

			return false;
		});
	});
	function showMsg(text){
		$('#divMsg').text(text).fadeIn('slow');
		setTimeout(function(){$('#divMsg').fadeOut('normal');}, 3500);
	}

	function showError(text) {
        $('#divError').removeClass('succMsg');
        $('#divError').text(text).fadeIn('slow');
        setTimeout(function(){$('#divError').fadeOut('normal');}, 3500);
    }

    function showSucc(text) {
        $('#divError').addClass('succMsg');
        $('#divError').text(text).fadeIn('slow');
        setTimeout(function(){$('#divError').fadeOut('normal');}, 3500);
    }

	$('.action-get-verifycode').click(function(e){
		e.preventDefault();
        var el = document.getElementById('username') ;
        if(this.hasClass('disabled')) return false;
        sendVerify(this,'username' + '=' + el.value);
	});

	var Query = {
	    send: function(url, element, data, fn){
	        new Request({
	            url: url,
	            link: 'cancel',
	            onSuccess: function(rs) {
		            if(rs) {
		                try{
		                    rs = JSON.decode(rs);
		                } catch (e) {}
		                if(!rs.success) {
		                    showError(rs.msg);
		                }
		                fn&&fn.call(this, rs);
		            }
		        }
		    }).post(data);
		}
	};

	function sendVerify(el,data) {
	    var url = el.href;
	    var textCont = el.getElement('span span');
		el.addClass('disabled');
	 	// $(el).find('span').attr('disabled',true);
	 	// $(el).attr('disabled',true);
	    textCont.innerHTML = el.innerText + '(<i>0</i> )';
	    var ttt = textCont.getElement('i');
	    var cd = new countdown(textCont.getElement('i'), {
	        start: 60,
	        secondOnly: true,
	        callback: function(e) {
	            el.removeClass('disabled');
	            textCont.innerHTML = '重发验证码';
	        }
	    });
	    Query.send(url, el, data, function(rs) {
	        if(rs.success) {
	            cd.stop();
	            el.removeClass('disabled');
	            textCont.innerHTML = '重发验证码';
	        }else{
	            cd.stop();
	        	el.removeClass('disabled');
	            textCont.innerHTML = '获取验证码';
	        }
	    });
	}

	function freshImg(){
        var code = Date.parse(new Date());
        document.getElementById('imgCode').src = urlImage+'&code='+code;
    }

</script>
</html>
