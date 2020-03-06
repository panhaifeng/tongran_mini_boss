function CheckLodop(){
   var oldVersion=LODOP.Version;
       newVerion="6.1.1.8";	
   if (oldVersion==null){
	document.write("<h6><font color='blue'>打印控件未安装! 点击这里<a href='Resource/Script/Print/install_lodop.exe'>执行安装</a>, 安装后请刷新页面。</font></h6>");
	if (navigator.appName=="Netscape")
	document.write("<h6><font color='blue'>（Firefox浏览器用户需先点击这里<a href='Resource/Script/Print/npActiveX0712SFx31.xpi'>安装运行环境</a>）</font></h6>");
   } else if (oldVersion<newVerion)
	document.write("<h6><font color='blue'>打印控件需要升级!点击这里<a href='Resource/Script/Print/install_lodop.exe'>执行升级</a>,升级后请重新进入。</font></h6>");
}