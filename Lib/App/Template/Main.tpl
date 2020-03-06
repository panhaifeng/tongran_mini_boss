<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title><{webcontrol type='GetAppInf' varName='systemV'}></title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/css/layui.css"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/style/admin.css"}>

  <link type="favicon" rel="shortcut icon" href="favicon.ico" />
</head>
<body class="layui-layout-body">
  <div id="loading-mask" class="layui-anim"></div>
  <div id="loading" class="layui-anim">
    <div class="loading-indicator">
      <img src="Resource/Image/main.png" style="margin-right:8px;" align="absmiddle">
    </div>
  </div>
  <div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
      <div class="layui-header">
        <!-- 头部区域 -->
        <ul class="layui-nav layui-layout-left">
          <li class="layui-nav-item layadmin-flexible" lay-unselect>
            <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
              <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
            </a>
          </li>

          <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;" layadmin-event="refresh" title="重载当前标签页">
              <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
          </li>
        </ul>
        <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

          <!-- <li class="layui-nav-item" lay-unselect>
            <a lay-href="<{url controller=Mail action=Right}>" layadmin-event="message" lay-text="消息中心">
              <i class="layui-icon layui-icon-notice"></i>
              如果有新消息，则显示小圆点
              <{if $notice.cnt > 0}>
              <span class="layui-badge-dot"></span>
              <span class="layui-badge" style="margin-left: 15px;border-radius: 50%;margin-top: -10px;">9</span>
              <{/if}>
            </a>
          </li> -->
          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="theme">
              <i class="layui-icon layui-icon-theme"></i>
            </a>
          </li>
          <!-- <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="note">
              <i class="layui-icon layui-icon-note"></i>
            </a>
          </li> -->
          <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="fullscreen">
              <i class="layui-icon layui-icon-screen-full"></i>
            </a>
          </li>
          <li class="layui-nav-item" lay-unselect  style="margin-right: 15px;">
            <a href="javascript:;">
              <cite><{$smarty.session.REALNAME}></cite>
            </a>
            <dl class="layui-nav-child">
              <dd><a lay-href="<{url controller=Acm_User action=ChangePwd}>">修改密码</a></dd>
              <hr>
              <dd><a href="<{url controller=login action=logout}>">安全退出</a></dd>
            </dl>
          </li>

          <li class="layui-nav-item layui-hide-xs layui-hide-sm" lay-unselect>
            <a href="javascript:;" class='popupRightAuto' layadmin-event="more" data-url=""><i class="layui-icon layui-icon-more-vertical"></i></a>
          </li>
        </ul>
      </div>
      <!-- 侧边菜单 -->
      <div class="layui-side layui-side-menu">
        <div class="layui-side-scroll">
          <!-- <a href="javascript:;" layadmin-event="flexibleLogo" title="侧边伸缩"> -->
            <div class="layui-logo">
              <span><{webcontrol type='GetAppInf' varName='appName'}></span>
            </div>
          <!-- </a> -->
          <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
            <!-- <li data-name="home" class="layui-nav-item layui-this" style="display: none;">
              <a href="javascript:;" lay-tips="主页" lay-direction="2" lay-href="<{url controller=main action=welcome}>">
                <i class="layui-icon layui-icon-home"></i>
                <cite>主页</cite>
              </a>
            </li> -->
            <{foreach from=$Menu item=mm}>
                <{assign var=mLevel value=0}>
                <{include file=menuFile.tpl}>
            <{/foreach}>
          </ul>
        </div>
      </div>

      <!-- 页面标签 -->
      <div class="layadmin-pagetabs layadmin-pagetabs-custom" id="LAY_app_tabs">
        <div class="layui-icon layadmin-tabs-control layui-icon-prev tab-main-left-btn" layadmin-event="leftPage"></div>
        <div class="layui-icon layadmin-tabs-control layui-icon-next tab-main-right-btn" layadmin-event="rightPage"></div>
        <div class="layui-icon layadmin-tabs-control layui-icon-down tab-right-down-custom">
          <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
            <li class="layui-nav-item" lay-unselect>
              <a href="javascript:;"></a>
              <dl class="layui-nav-child layui-anim-fadein">
                <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
              </dl>
            </li>
          </ul>
        </div>
        <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
          <ul class="layui-tab-title" id="LAY_app_tabsheader">
            <li lay-id="<{url controller=main action=welcome}>" lay-attr="<{url controller=main action=welcome}>" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
          </ul>
        </div>
      </div>
      <!-- 主体内容 -->
      <div class="layui-body" id="LAY_app_body">
        <div class="layadmin-tabsbody-item layui-show">
          <iframe src="<{url controller=main action=welcome}>" frameborder="0" class="layadmin-iframe"></iframe>
        </div>
      </div>
      <!-- 辅助元素，一般用于移动设备下遮罩 -->
      <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
  </div>
  <{webcontrol type='LoadJsCss' src="Resource/Script/layui/layui/layui.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/jquery.1.9.1.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/toastr/toastr.min.js"}>
  <{webcontrol type='LoadJsCss' src="Resource/Script/toastr/toastr.min.css"}>

  <script>
    layui.config({
      base: 'Resource/Script/layui/' //静态资源所在路径
    }).extend({
      index: 'lib/index'
    }).use('index');

    //测试showMsg方法
    //success|error|info|warning
    // showMsg('操作成功');
    // showMsg('操作失败','error');
    //测试自动弹出右边窗口
    $(function(){
      // -- 初始化通知
      var _urlMsg = "<{$notice.url}>";
      if(_urlMsg){
        setTimeout(function(){
          jQuery('.popupRightAuto').attr('data-url' ,_urlMsg);
          jQuery('.popupRightAuto').click();
        },600);
      }
      // -- end 通知

      //快捷更新数据方式
      $('body').keydown(function(e){
        var currKey=e.keyCode||e.which||e.charCode;
        // alert(currKey);
        //如果ctrl+alt+shift+A弹出db_change输入框,此功能只开发给开发人员形成db_change文档时用
        if(e.altKey&&e.ctrlKey&&e.shiftKey&&currKey==65) {
          var url = '?controller=Dbchange&action=Add';
          window.open(url);
        }
        //如果ctrl+alt+shift+z弹出执行窗口,此功能只给实施人员用
        if(e.altKey&&e.ctrlKey&&e.shiftKey&&currKey==90) {
          var url = '?controller=Dbchange&action=AutoUpdate';
          window.open(url);
        }

        if(e.altKey&&e.ctrlKey&&e.shiftKey&&currKey==83) {
          var url = '?controller=Acm_SetParamters&action=Right';
          window.open(url);
        }
      });
    });
  </script>
</body>
</html>


