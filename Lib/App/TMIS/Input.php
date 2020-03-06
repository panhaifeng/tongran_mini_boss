<?php
/**
 * Input Class 过滤特殊字符串，过滤xss攻击
 * Time：2018/02/08 08:42:18
 * @author li
*/
class TMIS_Input {
    /**
     * 构造方法
     * @param object application object
     * @return null
     */
    function __construct(){

    }

    /**
     * check_input 入口函数
     * @param array array
     * @return array
     */
    public function check_input($array = array()){
        if (is_array($array)){
            foreach($array as $key=>$v){
                $array[$key] = self::check_input($array[$key]);
            }
        }
        else{
            $array = strip_tags($array);
            $array = self::filterEmoji($array);//过滤emoji表情
            $array = self::_filter_input($array);//过滤xss攻击
        }
        return $array;
    }

    static function _filter_input($data){
        if(is_array($data)){
            foreach($data as $key=>$v){
                $data[$key] = self::_filter_input($data[$key]);
            }
        }else{
            if(strlen($data)){
                $data = self::_RemoveXSS($data);
            }else{
                $data = $data;
            }
        }
        return $data;
    }


    //过滤用户输入的数据，防范xss攻击
    static function _RemoveXSS($val) {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        // $val = preg_replace('/([\x00-\x08|\x0b-\x0c|\x0e-\x19])/', '', $val);

        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!<>@#$%^&*()';
        $search .= ',~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }

        return $val;
    }

    //过滤CRLF注入攻击
    static function _filter_crlf($url){
        $url = trim($url);
        $url = strip_tags($url,""); //清除HTML如<br />等代码
        $url = str_replace("\n", "", str_replace(" ", "", $url));//去掉空格和换行
        $url = str_replace("\t","",$url); //去掉制表符号
        $url = str_replace("\r\n","",$url); //去掉回车换行符号
        $url = str_replace("\r","",$url); //去掉回车
        $url = str_replace("\"","",$url); //去掉双引号
        // $url = str_replace("'","",$url); //去掉单引号
        $url = trim($url);
        return $url;
    }

    /**
     * Check url to be used as internal
     *
     * @param   string $url
     * @return  bool
     */
    static function _isInternalUrl($url){
        if(strpos($url, 'http') !== false){
            // Url must start from base url
            if(strpos($url, kernel::base_url(1)) === 0 ){
                return true;
            }
        }
        return false;
    }

    static function gzdecode($data) {
        $len = strlen($data);
        if ($len < 18 || strcmp(substr($data,0,2),"\x1f\x8b")) {
           return null; // Not GZIP format (See RFC 1952)
        }
        $method = ord(substr($data,2,1)); // Compression method
        $flags = ord(substr($data,3,1)); // Flags
        if ($flags & 31 != $flags) {
           // Reserved bits are set -- NOT ALLOWED by RFC 1952
           return null;
        }
        // NOTE: $mtime may be negative (PHP integer limitations)
        $mtime = unpack("V", substr($data,4,4));
        $mtime = $mtime[1];
        $xfl = substr($data,8,1);
        $os    = substr($data,8,1);
        $headerlen = 10;
        $extralen = 0;
        $extra    = "";
        if ($flags & 4) {
           // 2-byte length prefixed EXTRA data in header
           if ($len - $headerlen - 2 < 8) {
             return false;    // Invalid format
           }
           $extralen = unpack("v",substr($data,8,2));
           $extralen = $extralen[1];
           if ($len - $headerlen - 2 - $extralen < 8) {
             return false;    // Invalid format
           }
           $extra = substr($data,10,$extralen);
           $headerlen += 2 + $extralen;
        }

        $filenamelen = 0;
        $filename = "";
        if ($flags & 8) {
           // C-style string file NAME data in header
           if ($len - $headerlen - 1 < 8) {
             return false;    // Invalid format
           }
           $filenamelen = strpos(substr($data,8+$extralen),chr(0));
           if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
             return false;    // Invalid format
           }
           $filename = substr($data,$headerlen,$filenamelen);
           $headerlen += $filenamelen + 1;
        }

        $commentlen = 0;
        $comment = "";
        if ($flags & 16) {
           // C-style string COMMENT data in header
           if ($len - $headerlen - 1 < 8) {
             return false;    // Invalid format
           }
           $commentlen = strpos(substr($data,8+$extralen+$filenamelen),chr(0));
           if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
             return false;    // Invalid header format
           }
           $comment = substr($data,$headerlen,$commentlen);
           $headerlen += $commentlen + 1;
        }

        $headercrc = "";
        if ($flags & 1) {
           // 2-bytes (lowest order) of CRC32 on header present
           if ($len - $headerlen - 2 < 8) {
             return false;    // Invalid format
           }
           $calccrc = crc32(substr($data,0,$headerlen)) & 0xffff;
           $headercrc = unpack("v", substr($data,$headerlen,2));
           $headercrc = $headercrc[1];
           if ($headercrc != $calccrc) {
             return false;    // Bad header CRC
           }
           $headerlen += 2;
        }

        // GZIP FOOTER - These be negative due to PHP's limitations
        $datacrc = unpack("V",substr($data,-8,4));
        $datacrc = $datacrc[1];
        $isize = unpack("V",substr($data,-4));
        $isize = $isize[1];

        // Perform the decompression:
        $bodylen = $len-$headerlen-8;
        if ($bodylen < 1) {
           // This should never happen - IMPLEMENTATION BUG!
           return null;
        }
        $body = substr($data,$headerlen,$bodylen);
        $data = "";
        if ($bodylen > 0) {
           switch ($method) {
             case 8:
               // Currently the only supported compression method:
               $data = gzinflate($body);
               break;
             default:
               // Unknown compression method
               return false;
           }
        } else {
           // I'm not sure if zero-byte body content is allowed.
           // Allow it for now... Do nothing...
        }

        // Verifiy decompressed size and CRC32:
        // NOTE: This may fail with large data sizes depending on how
        //      PHP's integer limitations affect strlen() since $isize
        //      may be negative for large sizes.
        if ($isize != strlen($data) || crc32($data) != $datacrc) {
           // Bad format! Length or CRC doesn't match!
           return false;
        }
        return $data;
    }

    static function addslashes_array($value){
        if(empty($value)){
            return $value;
        }else{
            if(is_array($value)){
                foreach($value as $k=>$v){
                    if(is_array($v)){
                        $value[$k] = self::addslashes_array($v);
                    }else{
                        $value[$k] = addslashes($v);
                    }
                }
                return $value;
            }else{
                return addslashes($value);
            }
        }
    }

    static function stripslashes_array($value){
        if(empty($value)){
            return $value;
        }else{
            if(is_array($value)){
                $tmp = $value;
                foreach($tmp as $k=>$v){
                    $k = stripslashes($k);
                    $value[$k] = $v;

                    if(is_array($v)){
                        $value[$k] = self::stripslashes_array($v);
                    }else{
                        $value[$k] = stripslashes($v);
                    }
                }
                return $value;
            }else{
                return stripslashes($value);
            }
        }
    }

    //过滤掉emoji表情
    static function filterEmoji($value){
        $value = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
        $value);

        return $value;
    }

    /**
     * 生成验证token
     * Time：2018/04/26 09:02:19
     * @author li
    */
    function setTokenInput($keyCode=''){
        if(!$keyCode){
            $keyCode = $_GET['controller'].'@'.$_GET['action'];
        }
        $now = time().rand(10000,99999);
        $code = md5($keyCode.md5($now));
        $_SESSION['INPUT_TOKEN_CUR'][$keyCode] = $code;
        FLEA::writeCache('INPUT_TOKEN_CUR.'.$keyCode,$code);
        return $code;
    }

    /**
     * 生成验证token
     * Time：2018/04/26 09:02:19
     * @author li
    */
    function verfiTokenInput($keyCode='' ,$token='' ,&$msg=''){
        if(!$keyCode){
            $msg = "keyCode不能为空";
            return false;
        }
        if(!$token){
            $msg = "token不能为空";
            return false;
        }

        if($_SESSION['INPUT_TOKEN_CUR'][$keyCode] != $token){
            $msg = 'token验证失败';
            return false;
        }
        return true;
    }

    /**
     * 生成验证token(缓存)
     * Time：2018/05/04 14:32:19
     * @author zou
    */
    function verfiTokenInputCache($keyCode='' ,$token='' ,&$msg=''){
        if(!$keyCode){
            $msg = "keyCode不能为空";
            return false;
        }
        if(!$token){
            $msg = "token不能为空";
            return false;
        }

        $_cache = FLEA::getCache('INPUT_TOKEN_CUR.'.$keyCode);
        if($_cache != $token){
            $msg = 'token验证失败';
            return false;
        }
        return true;
    }

    /**
     * 生成验证token
     * Time：2018/04/26 09:02:19
     * @author li
    */
    function verifyTokenString($string='' ,$keyCode='_EQINFO_E7_'){
        $code = md5($string.md5($keyCode));
        return $code;
    }


    /////////验证身份证入口函数
    //$id_card 身份证号码
    function validation_filter_id_card($id_card){
        if(strlen($id_card)==18){
            return self::idcard_checksum18($id_card);
        }elseif((strlen($id_card)==15)){
            $id_card = self::idcard_15to18($id_card);
            return self::idcard_checksum18($id_card);
        }else{
            return false;
        }
    }

    // 计算身份证校验码，根据国家标准GB 11643-1999
    function idcard_verify_number($idcard_base){
        if(strlen($idcard_base)!=17){
            return false;
        }
        //加权因子
        $factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
        //校验码对应值
        $verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
        $checksum=0;
        for($i=0;$i<strlen($idcard_base);$i++){
            $checksum += substr($idcard_base,$i,1) * $factor[$i];
        }
        $mod=$checksum % 11;
        $verify_number=$verify_number_list[$mod];
        return $verify_number;
    }
    // 将15位身份证升级到18位
    function idcard_15to18($idcard){
        if(strlen($idcard)!=15){
            return false;
        }else{
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){
                $idcard=substr($idcard,0,6).'18'.substr($idcard,6,9);
            }else{
                $idcard=substr($idcard,0,6).'19'.substr($idcard,6,9);
            }
        }
        $idcard=$idcard.self::idcard_verify_number($idcard);
        return $idcard;
    }
    // 18位身份证校验码有效性检查
    function idcard_checksum18($idcard){
        if(strlen($idcard)!=18){
            return false;
        }
        $idcard_base=substr($idcard,0,17);
        if(self::idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 签名验证
     * Time：2018/07/05 10:05:06
     * @author li
    */
    function buildTokenParams($string = '' ,$keyCode = '_e7_eqinfo'){
        return md5(md5($string.$keyCode).md5($keyCode));
    }

    function verfiTokenParams($token = '' ,$string = '' ,$keyCode = '_e7_eqinfo'){
        $tokenSelf = self::buildTokenParams($string ,$keyCode);
        // echo $token;exit;
        if($token != $tokenSelf){
            return false;
        }

        return true;
    }

    //验证手机号是否合法
    function verfiMobile($tel){
        if(preg_match("/^1[345678]{1}\d{9}$/",$tel)){
            return true;
        }else{
            return false;
        }
    }
}
?>