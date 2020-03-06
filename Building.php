<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="javascript" src="Resource/Script/jquery.js"></script>
<script language="javascript" src="Resource/Script/thickbox/thickbox.js"></script>
<link href="Resource/Script/thickbox/thickbox.css" rel="stylesheet" type="text/css" />
<title>Building</title>
<style type="text/css">
#divError{ display: none;}
.msgError{ font-size:14px; font-weight:500;}
h1 {
    font-size: 24px;
    font-weight: bold;
    color: #6699cc;
}
 
h2 {
    font-size: 14px;
    font-weight: bold;
    margin: 0px;
    padding: 0px;
    color: #6699cc;
    margin-bottom: 8px;
};
.word {
	FONT-SIZE: 9pt; WORD-SPACING: 2pt; COLOR: #333333; LINE-HEIGHT: 17pt; FONT-FAMILY: "宋体"; LETTER-SPACING: 0.5pt
}
A:link {
	COLOR: #000000; FONT-STYLE: normal; TEXT-DECORATION: none
}
A:visited {
	COLOR: #000000; FONT-STYLE: normal; TEXT-DECORATION: none
}
A:hover {
	COLOR: #0066cc; FONT-STYLE: normal; TEXT-DECORATION: none
}
A:active {
	COLOR: #0066cc; FONT-STYLE: normal; TEXT-DECORATION: none
}
.en {
	FONT-SIZE: 12px; FONT-FAMILY: "Arial", "Helvetica", "sans-serif"
}
</style>
<script language="javascript">
$(function(){
	$('.thick').click(function(){
		var html=$('#divError').html();
		if(html!=''){
			html="<div class='msgError'>"+html+"</div>";
			$('#divError').html(html);
			tb_show('错误提示',"#TB_inline?height=320&width=650&inlineId=divError");
		}
	});
	
});
</script>
</head>

<body bgColor=#8d878d leftMargin=0 topMargin=0 marginwidth="0" marginheight="0">
<TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TBODY>
  <TR>
    <TD align=middle>
      <TABLE 
      style="BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid" 
      cellSpacing=0 cellPadding=0 width=689 align=center bgColor=#ffffff 
      border=0>
        <TBODY>
        <TR bgColor=#666666>
          <TD colSpan=2 height=8></TD></TR>
        <TR>
          <TD vAlign=center align=middle width=250 height=333>
            <DIV align=center><IMG height=333 alt=快乐指南 
            src="Resource/Image/left.gif" width=202 border=0></DIV></TD>
          <TD vAlign=center width=437>            <TABLE height=155 cellSpacing=0 cellPadding=0 width=435 border=0>
              <TBODY> 
              <TR> 
                <TD class=word height=10> 
                  <DIV align=center><!--<IMG height=49 src="images/top.gif" 
                  width=307 align=absMiddle>--></DIV>
                </TD>
              </TR>
              <TR> 
                <TD class=word height=35 style="font-size:17px"> 
                  <DIV align=center><b><font face="Times New Roman, Times, serif"><a href='#' class='thick'><?php echo $_GET['msg']==''?'系统维护中…':$_GET['msg']; ?></a></font></b></DIV>                  </TD>
              </TR>
              <TR> 
                <TD class=word align=right height=25> 
                  <DIV align=center><FONT color=#ff6600></FONT></DIV>
                </TD>
              </TR>
              </TBODY> 
            </TABLE>
          </TD></TR>
        <TR bgColor=#33ccff>
          <TD colSpan=2 height=5></TD></TR>
        <TR align=right background="Resource/Image/bg.gif">
          <TD colSpan=2 height=29><SPAN class=word></SPAN>　<IMG height=16 hspace=3 
            src="Resource/Image/arrow.gif" 
  width=22></TD>
        </TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
<div id='divError' name='divError'><?php if($_GET['view']!=''){
		echo '<h2>',stripslashes($_GET['view']),'</h2>';
}?></div>
</body>
</html>
