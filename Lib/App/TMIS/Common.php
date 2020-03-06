
<?php
class TMIS_Common {
    /**
    * 将数字金额转换成中文大写数字
    * 例子: 231123.402 => 贰拾叁万壹仟壹佰贰拾叁元肆角整
    *
    * @author Sandy Lee(leeqintao@gmail.com)
    * @param float $num 表示金额的浮点数
    * @return string 返回中文大写的字符串
    */
    function trans2rmb($num) {
        $rtn = '';
        $num = round($num, 2);
        $s = array(); // 存储数字的分解部分
        //==> 转化为字符串,$s[0]整数部分,$s[1]小数部分
        $s = explode('.', strval($num));
        // 超过12位(大于千亿)则不予处理
        if (strlen($s[0]) > 12) {
            return '*'.$num;
        }

        // 中文大写数字数组
        $c_num = array('零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');

        // 保存处理过程数据的数组
        $r = array();

        //==> 处理 分/角 部分
        if (!empty($s[1])){
            $jiao = substr($s[1], 0,1);
            if (!empty($jiao)){
                $r[0] .= $c_num[$jiao].'角';
            } else{
                $r[0] .= '零';
            }

            $cent = substr($s[1], 1,1);
            if (!empty($cent)){
                $r[0] .=  $c_num[$cent].'分';
            }
        }

        //==> 数字分为三截,四位一组,从右到左:元/万/亿,大于9位的数字最高位都归为"亿"
        $f1 = 1;
        for ($i = strlen($s[0])-1; $i >= 0; $i--, $f1 ++){
            $f2 = floor(($f1-1)/4)+1; // 第几截
            if ($f2 > 3){
                $f2 = 3;
            }

            // 当前数字
            $curr = substr($s[0], $i, 1);

            switch ($f1%4){
                case 1:
                    $r[$f2] = (empty($curr) ? '零' : $c_num[$curr]).$r[$f2];
                    break;
                case 2:
                    $r[$f2] = (empty($curr) ? '零' : $c_num[$curr].'拾').$r[$f2];
                    break;
                case 3:
                    $r[$f2] = (empty($curr) ? '零' : $c_num[$curr].'佰').$r[$f2];
                    break;
                case 0:
                    $r[$f2] = (empty($curr) ? '零' : $c_num[$curr].'仟').$r[$f2];
                    break;
            }
        }

        $rtn .= empty($r[3]) ? '' : $r[3].'亿';
        $rtn .= empty($r[2]) ? '' : $r[2].'万';
        $rtn .= empty($r[1]) ? '' : $r[1].'元';
        $rtn .= $r[0].'整';

        //==> 规则:如果位数为零,在"元"之前不出现"零",在空位处且不在"元"之间的,则填充一个"零"(num为0的情况除外)
        if ($num != 0){
            while(1){
                if (substr_count($rtn, "零零") == 0 && substr_count($rtn, "零元") == 0
                    && substr_count($rtn, "零万") == 0 && substr_count($rtn, "零亿") == 0){
                    break;
                }
                $rtn = str_replace("零零", "零", $rtn);
                $rtn = str_replace("零元", "元", $rtn);
                $rtn = str_replace("零万", "万", $rtn);
                $rtn = str_replace("零亿", "亿", $rtn);
            }
        }
        return $rtn;
    }

    /*
    *导出功能通用方法，适合用于导出查询列表，需要传进来的几个参数
    *支持导出图片
    *auther:li
    *export excel file
    *注意：data参数为数组类型，其中关键的为（title,arr_field_info,arr_field_value,）
    *其他的参数还有：fileName:文件导出的名字；sheetName:sheet显示的标题
    *支持：支持设置列宽，和查询模板中设置列宽相同，
    *显示图片的时候应该注意：在arr_field_info中，需要显示图片的列要有image=true:eg(array('text'=>'图片','image'=>true))
    */
    function _Export($data)
    {
        /*
        *处理表头信息，如果表头信息为空，则不允许导出
        */
        $col_cnt=count($data['arr_field_info'])+64;
        if($col_cnt<65)exit;
        $info_asc=TMIS_Common::_chr($col_cnt);
        //end
        /*
        *开始导出excel
        */
        set_time_limit(900);
        require_once('Lib/PHPExcel/PHPExcel.php') ;
        $obj_phpexcel = new PHPExcel();
        //sheet标签
        $obj_phpexcel->setActiveSheetIndex(0);
        //获取当前sheet
        $objActSheet = $obj_phpexcel->getActiveSheet();
        //sheet的名字
        $objActSheet->setTitle($data['sheetName']==''?'sheet1':$data['sheetName']);
        // $obj_phpexcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(2, 3);

        /*
        *处理表头等信息；标题栏合并单元格，并设置居中
        */
        $title=$data['title']==''?FLEA::getAppInf('compName'):$data['title'];
        //合并单元格
        $objActSheet->mergeCells("A1:{$info_asc}1");
        //填充标题内容
        $objActSheet->setCellValue('A1',$title);
        //设置格式
        $objActSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objActSheet->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        // $objActSheet->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
        $objActSheet->getStyle('A1')->getFont()->setBold(true);
        $objActSheet->getStyle('A1')->getFont()->setSize(17);
        //end

        //导出表头与主信息
        if($data['arr_field_info']){
            $row=2;//标题行在第2行开始导出
            //从A列开始导出
            $start_asc_cnt=65;
            //表头的开始列
            $start_info_asc=$start_asc_cnt;
            foreach ($data['arr_field_info'] as $key => & $info) {
                //处理列
                $asc=TMIS_Common::_chr($start_info_asc);
                //获取坐标
                $x_y=$asc.$row;

                //设置默认列宽
                $default_jian=20;
                $widht_default=(100-$default_jian)/6;
                $objActSheet->getColumnDimension($asc)->setWidth($widht_default);

                //处理标题，标题可能是数组，包括行宽等信息
                if(is_array($info)){
                    $objActSheet->setCellValue($x_y , $info['text']);
                    if($info['width']>$default_jian)$objActSheet->getColumnDimension($asc)->setWidth(($info['width']-$default_jian)/6);
                }else{
                    $objActSheet->setCellValue($x_y , $info);
                }

                $objActSheet->getStyle($x_y)->getFont()->setBold(true);

                $start_info_asc++;
            }

            //处理主数据信息
            //主信息的开始列
            $_row=$row+1;
            foreach($data['arr_field_value'] as $key => & $v){
                //每行的开始都是从A开始
                $start_val_asc=$start_asc_cnt;
                foreach ($data['arr_field_info'] as $k => & $info) {
                    //处理列
                    $asc=TMIS_Common::_chr($start_val_asc);
                    //获取坐标
                    $x_y=$asc.$_row;

                    $start_val_asc++;

                    //如果输出的信息为图片
                    if(is_array($info) && $info['image']==true && file_exists($v[$k])){
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName('img');
                        $objDrawing->setPath($v[$k]);
                        if($info['width']>10)$objDrawing->setWidth($info['width']-10);
                        $objDrawing->setCoordinates($x_y);
                        $objDrawing->setOffsetX(2);
                        $objDrawing->setOffsetY(2);
                        $objDrawing->getShadow()->setVisible(true);
                        $objDrawing->setWorksheet($objActSheet);
                    }else{
                        $objActSheet->setCellValue($x_y , $v[$k]);
                    }

                    $ex_cell=$x_y;
                }
                $_row++;
            }
        }

        //单元格边框及颜色
        $objActSheet->getStyle('A2:'.$ex_cell)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //文件的名字
        $filename = $data['fileName']==''? "文件_".date('Ymd'):$data['fileName'];

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header("Content-Disposition: attachment;filename=".iconv('utf-8',"gb2312", $filename).".xls");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($obj_phpexcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    //处理导出的ASCII码
    //A-Z[65-90]
    function _chr($int){
        $asc='';
        //大于90后，应用两位表示如AC
        if($int>90){
            //获取第一位的ascii
            $fir=floor(($int-65)/26)+64;
            $fir_asc=chr($fir);

            //获取第二位的ascii
            $ser=($int-65)%26+65;
            $ser_asc=chr($ser);

            $asc=$fir_asc.$ser_asc;

        }else $asc=chr($int);
        return $asc;
    }

    #用html格式导出excel表格，office2000没有xml表格格式，只有用html代码进行导出。
    function export2Excell(&$rowset,$arrField=null,$fileName='export') {
        /*foreach($rowset as & $v) {
            if($arrField) {
                foreach($arrField as $key =>& $vv) {
                    $temp = "[".join('][',explode('.',$key))."]";
                    eval("echo \"<br>\".\$v".$temp.";");
                }
            }
        }
        exit;*/
        if(!$rowset) return false;
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$fileName}.xls");

        echo '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<table  style="BORDER-COLLAPSE: collapse" borderColor=#000000 border="1" align="center" cellpadding="2" cellspacing="0">
';
        //输出标题
        if($arrField) {
            echo '<tr>
            <td>'.join('</td>
            <td>',$arrField).'</td>
            </tr>';
        }

        //输出数据
        foreach($rowset as & $v) {
            echo '
            <tr>';
            if($arrField) {
                foreach($arrField as $key =>& $vv) {
                    //echo mb_convert_encoding('gb2312','UTF-8',$v[$vv])."\t";
                    $temp = "[".join('][',explode('.',$key))."]";
                    //eval("echo \"<td>\".\$v".$temp."</td>;");
                    eval("echo \"<td>\".\$v".$temp.".\"</td>\";");
                }
            } else {
                foreach($v as & $vv) {
                    //echo mb_convert_encoding('gb2312','UTF-8',$vv)."\t";
                    echo "<td>".$vv."</td>";
                }
            }
            echo "</tr>
            ";
        }

        echo '</table>
</body>
</html>';exit;
    }

    //以进度条形式显示百分比
    function percentPic($total,$out,$opt){
        $per = round($out/$total*100);
        $per1 = 100-$per;
        $width = $opt['width'] ? $opt['width'] : 60 ;
        $height = $opt['height'] ? $opt['height'] : 100 ;
        $bgColor=empty($opt['bgColor'])?'#00FF00':$opt['bgColor'];
        if($opt['direction']==0) {
            $str = '<table border="0" cellspacing="1" cellpadding="0" bgcolor="#cccccc" style="width:'.$width.'px;height:10px"><tr><td bgcolor="'.$bgColor.'" title="已完成'.$out.','.$per.'%" width="'.$per.'%"></td><td title="共'.$total.'" width="'.$per1.'%"></td></tr></table>';
        } else {
            $str='<table border="0" cellspacing="1" cellpadding="0" bgcolor="#cccccc" style="width:10px;height:'.$height.'px">';
            $str.='<tr><td title="共'.$total.'" height="'.$per1.'%"></td></tr>';
            if($per>0) {
            $str.='<tr><td bgcolor="'.$bgColor.'" title="已完成'.$out.','.$per.'%" height="'.$per.'%"></td></tr>';
            }
            $str.="</table>";
        }

        return $str;
    }

    //以进度条形式显示百分比
    function percentPer($total,$out,$opt){
        $per = round($out/$total*100);
        $per1 = 100-$per;
        $width = $opt['width'] ? $opt['width'] : 60 ;
        $height = $opt['height'] ? $opt['height'] : 100 ;
        $bgColor = $opt['bgColor'] ? $opt['bgColor'] : '#00FF00' ;
        if($opt['direction']==0) {
            $str = '<table border="0" cellspacing="0" cellpadding="0" bgcolor="#999999" style="width:'.$width.'px;height:13px"><tr><td bgcolor="'.$bgColor.'" style="padding:0px;border:0px;width:'.$per.'%"></td><td  style="padding:0px;border:0px;width:'.$per1.'%"></td></tr></table>';
        } else {
            $str='<table border="0" cellspacing="0" cellpadding="0" bgcolor="#999999" style="width:10px;height:'.$height.'px">';
            $str.='<tr><td height="'.$per1.'%" style="padding:0px;border:0px;"></td></tr>';
            if($per>0) {
            $str.='<tr><td bgcolor="'.$bgColor.'" height="'.$per.'%" style="padding:0px;border:0px;"></td></tr>';
            }
            $str.="</table>";
        }
        if($opt['url']=='')return "<span title='".$opt['title']."'>".$str."</span>";
        return "<a href='".$opt['url']."' title='".$opt['title']."' class='thickbox'>".$str."</a>";
    }

    //显示进度条,必须使用ext-all.css文件
    //rate为<1的小数
    function progressBar($rate,$opt=array()) {
        if((float)$rate>1) {
            //return '进度条的百分比>1';
            $rate=1;
        }
        if((float)$rate<0) {
            return '进度条的百分<0';
        }
        $percent = $rate*100;
        $width = $opt['width']?($opt['width']-1):85;
        $height = $opt['height']?($opt['height']-1):10;
        $tip = $opt['tip']?$opt['tip']:($opt['title']?$opt['title']:"完成度{$percent}%");
        $html = "<div class='x-progress-wrap' style='width:{$width}px;height:{$height}px; position:static !important;' ext:qtip=\"{$tip}\">";
        $html .= "<div class='x-progress-inner' style='height:{$height}px;overflow:hidden; position:static !important;'>";
        if($percent>0) {
            if($opt['bgColor']!='')$style="background-color:{$opt['bgColor']};background-image:url();";
            $html .= "<div class='x-progress-bar' style='width:{$percent}%;{$style}'>";
            $html.="</div>";
        }
        $html .= '</div>';
        $html .= '</div>';
        if($opt['url']) {
            $opt['class'] = $opt['class'] ? $opt['class'] :'thickbox';
            return "<a href='".$opt['url']."' class='{$opt['class']}' width='{$opt['dialogWidth']}'>".$html."</a>";
        }
        return $html;
    }



    //以utf8模式去对中文字串强制换行
    function wordwrap_utf8($str, $length = 75, $break = '<br />') {
        $len = mb_strlen($str,'utf-8');
        if($len <= $length) return $str;
        $temp = array();
        $num = ceil($len/$length);
        for($i = 0; $i < $num; $i++) {
            array_push($temp,
                mb_substr($str, $length*$i, $length, 'utf-8')
            );
        }
        return implode($break, $temp);
    }

    //从sys_set表中取得所有的配置信息
    function getSysSet($item=null) {
        $m= & FLEA::getSingleton('Model_Jichu_Client');
        $sql = "select * from sys_set";
        if($item) $sql.= " where item='{$item}'";
        $query = mysql_query($sql) or die(mysql_error());
        while ($r = mysql_fetch_assoc($query)) {
            $gz[$r['item']] = $r['value'];
        }
        return $gz;
    }

    #用html格式导出excel表格，office2000没有xml表格格式，只有用html代码进行导出。
    function exportByHtml(&$rowset,$arrField=null,$t='test') {
        if(!$rowset) return false;
        //去掉_edit栏
        $f = array();
        foreach($arrField as $key=>&$v) {
            if($key!='_edit') $f[$key] = $v;
        }
        $arrField = $f;

        //处理title,可接收数组，形成复杂的格式
        if(is_array($t)) {
            $title = $t['big'];
            $reportName = $t['f'];
            $t1 = $t['t1'];
            $t2 = $t['t2'];
            $t3 = $t['t3'];
            $b1 = $t['b1'];
            $b2 = $t['b2'];
            $b3 = $t['b3'];
        }
        //dump($t);exit;

        header("Content-type: application/vnd.ms-excel");
        $fileName = iconv('UTF-8', 'gb2312', $title);
        header("Content-Disposition: attachment; filename={$fileName}.xls");
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--[if gte mso 9]><xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>外加工应付款报表</x:Name>
    <x:WorksheetOptions>
     <x:DefaultRowHeight>285</x:DefaultRowHeight>
     <x:Selected/>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml><![endif]-->
</head>

<body>
<div style="font-size:20px;font-weight:bold;width:620px;height:30pt;" align="center">'.$title.'</div>
<div style="width:620px;">
    '.$t1.'
</div>
<table  style="BORDER-COLLAPSE: collapse;width:620px;" border=1 align="center" cellpadding="2" cellspacing="0">
';
        //输出标题
        if($arrField) {
            echo '<tr>
            <td>'.join('</td>
            <td>',$arrField).'</td>
            </tr>';
        }

        //输出数据
        foreach($rowset as & $v) {
            echo '
            <tr>';
            if($arrField) {
                foreach($arrField as $key =>& $vv) {
                    //echo mb_convert_encoding('gb2312','UTF-8',$v[$vv])."\t";
                    $temp = "[".join('][',explode('.',$key))."]";
                    //eval("echo \"<td>\".\$v".$temp."</td>;");
                    eval("echo \"<td>\".strip_tags(\$v".$temp.").\"</td>\";");
                }
            } else {
                foreach($v as & $vv) {
                    //echo mb_convert_encoding('gb2312','UTF-8',$vv)."\t";
                    echo "<td>".strip_tags($vv)."</td>";
                }
            }
            echo "</tr>
            ";
        }

        echo '</table>
</body>
</html>';exit;
    }

    //获取中文字的首字母
    function getPinyin($zh){
        $ret = "";
        $s1 = iconv("UTF-8","gb2312", $zh);
        $s2 = iconv("gb2312","UTF-8", $s1);
        if($s2 == $zh){$zh = $s1;}
            for($i = 0; $i < strlen($zh); $i++){
                    $s1 = substr($zh,$i,1);
                    $p = ord($s1);
                    if($p > 160){
                            $s2 = substr($zh,$i++,2);
                            $ret .= self::getfirstchar($s2);
                    }else{
                            $ret .= $s1;
                    }
        }
        return $ret;
    }
    function getfirstchar($s0){
        $fchar = ord($s0{0});
        if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
        $s1 = iconv("UTF-8","gb2312", $s0);
        $s2 = iconv("gb2312","UTF-8", $s1);
        if($s2 == $s0){$s = $s1;}else{$s = $s0;}
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if($asc >= -20319 and $asc <= -20284) return "A";
        if($asc >= -20283 and $asc <= -19776) return "B";
        if($asc >= -19775 and $asc <= -19219) return "C";
        if($asc >= -19218 and $asc <= -18711) return "D";
        if($asc >= -18710 and $asc <= -18527) return "E";
        if($asc >= -18526 and $asc <= -18240) return "F";
        if($asc >= -18239 and $asc <= -17923) return "G";
        if($asc >= -17922 and $asc <= -17418) return "H";
        if($asc >= -17417 and $asc <= -16475) return "J";
        if($asc >= -16474 and $asc <= -16213) return "K";
        if($asc >= -16212 and $asc <= -15641) return "L";
        if($asc >= -15640 and $asc <= -15166) return "M";
        if($asc >= -15165 and $asc <= -14923) return "N";
        if($asc >= -14922 and $asc <= -14915) return "O";
        if($asc >= -14914 and $asc <= -14631) return "P";
        if($asc >= -14630 and $asc <= -14150) return "Q";
        if($asc >= -14149 and $asc <= -14091) return "R";
        if($asc >= -14090 and $asc <= -13319) return "S";
        if($asc >= -13318 and $asc <= -12839) return "T";
        if($asc >= -12838 and $asc <= -12557) return "W";
        if($asc >= -12556 and $asc <= -11848) return "X";
        if($asc >= -11847 and $asc <= -11056) return "Y";
        if($asc >= -11055 and $asc <= -10247) return "Z";
        return null;
    }

    /**
     * 判断浏览器类型
     * Time：2015/08/18 16:03:04
     * @author li
     * @return array('version') version:string浏览器版本
     * @return array('browser') browser:string浏览器类型
    */
    function getBrowser(){
        $agent=$_SERVER["HTTP_USER_AGENT"];
        $Browser = '';
        if(strpos($agent,'MSIE')!==false || strpos($agent,'rv:11.0')) //ie11判断
        $Browser = "ie";
        else if(strpos($agent,'Firefox')!==false)
        $Browser = "firefox";
        else if(strpos($agent,'Chrome')!==false)
        $Browser = "chrome";
        else if(strpos($agent,'Opera')!==false)
        $Browser = 'opera';
        else if((strpos($agent,'Chrome')==false)&&strpos($agent,'Safari')!==false)
        $Browser = 'safari';
        else
        $Browser = 'unknown';


        //版本号
        $version = '';
        if (empty($_SERVER['HTTP_USER_AGENT'])){    //当浏览器没有发送访问者的信息的时候
            $version = 'unknow';
        }
        $agent= $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs))
            $version = $regs[1];
        elseif (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs))
            $version = $regs[1];
        elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs))
            $version = $regs[1];
        elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs))
            $version = $regs[1];
        elseif ((strpos($agent,'Chrome')==false)&&preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs))
            $version = $regs[1];
        else
            $version = 'unknow';

        return array(
                    'version'=>$version,
                    'browser'=>$Browser,
                );
    }

    //设置IE10一下版本  不允许登录
    function doBrowser(){
        /*$browser = self::getBrowser();
        // dump($browser);exit;
        if($browser['browser'] == 'ie' && (int)$browser['version']<9){
            echo "<pre>";
            echo "您的浏览器版本太低了<br>建议使用IE11，谷歌浏览器，或360等浏览器的急速模式(高级模式)";
            echo "<br/> <br>下载谷歌浏览器<br/><a href='https://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=0&rsv_idx=1&tn=baidu&wd=谷歌浏览器&rsv_pq=bbb32437000202f4&rsv_t=e7391qEs%2FmnjsZD62BQ%2F6IgC6h11LO3MAV4u%2FwNHcJNnS9P2PRGpp6PM4TY&rsv_enter=1&rsv_sug3=5&rsv_sug1=2'><image src='Resource/Image/chrome.jpg' title='下载谷歌浏览器'/></a>";
            echo "</pre>";
            exit;
        }*/
    }

    /**
     * 获取外网ip
     * Time：2015/10/27 19:20:42
     * @author li
     * @return string
    */
    function get_onlineip() {
        $ch = curl_init('http://city.ip138.com/ip2city.asp');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,3);
        $a = curl_exec($ch);
        preg_match('/\[(.*)\]/', $a, $ip);
        return $ip[1];
    }

    //设置网络请求配置
    function _request($curl,$https=false,$method='GET',$data=null ,$timeout = 30){
        //1.创建一个新cURL资源
        $ch = curl_init();

        //2.设置URL和相应的选项
        //要访问的网站
        curl_setopt($ch, CURLOPT_URL, $curl);
        //启用时会将头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_HEADER, false);
        //将curl_exec()获取的信息以字符串返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if($https){
            //FALSE 禁止 cURL 验证对等证书（peer's certificate）。
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  //验证主机
        }
        if($method == 'POST'){
            //发送 POST 请求
            curl_setopt($ch, CURLOPT_POST, true);
            //全部数据使用HTTP协议中的 "POST" 操作来发送。
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        //3.抓取URL并把它传递给浏览器
        $content = curl_exec($ch);

        if ($content  === false) {
            return "网络请求出错: " . curl_error($ch);
            // exit();
        }

        //4.关闭cURL资源，并且释放系统资源
        curl_close($ch);
        return $content;
    }


    /**
     * 图片处理
     * Time：2018/12/26 12:44:33
     * @author li
    */
    function _imageHtml($src = '',$cssText='' ,$class='' ,$async = false ,$asyncName='data-src'){
        $src = self::_imageSrc($src);

        //是否异步加载，异步加载需要自己写js处理或者使用自己的js
        if($async){
            $_src = $asyncName."='{$src}'";
        }else{
            $_src = " src='{$src}'";
        }

        return "<img {$_src} style='{$cssText}' class='{$class}' />";
    }

    /**
     * 图片处理
     * Time：2018/12/26 12:44:33
     * @author li
    */
    function _imageSrc($src = ''){
        $urlMode = FLEA::getAppInf('urlMode');
        if($urlMode == 'URL_PATHINFO' && defined('__ROOT__') && __ROOT__){
            $src = __ROOT__ . '/' .ltrim($src,'/');
        }

        return $src;
    }

    function getYear(){
        $year = date('Y');
        for($y = 0 ;$y <= 4 ;$y++){
            $value = $year - $y;
            $arr[] = array('text'=> $value,'value'=>$value);
        }

        return $arr;
    }

    //获取公司编号
    function getCompCode(){
        $certConf = 'Config/config.point.php';
        if(file_exists($certConf)){
            require($certConf);
        }else{
            $code = md5(FLEA::getAppInf('compName').time());
            $data = <<<controller
<?php
    \$compCode = '{$code}';
controller;
            if (safe_file_put_contents($certConf, $data)) {
                require($certConf);
            } else {
               return false;
            }
        }

        return $compCode;
    }
}
?>