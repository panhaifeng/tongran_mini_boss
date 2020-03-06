<?php
/////////////////////////////////////////////////////////////////////////////
// FleaPHP Framework
//
// Copyright (c) 2005 - 2008 QeeYuan China Inc. (http://www.qeeyuan.com)
//
// 许可协议，请查看源代码中附带的 LICENSE.txt 文件，
// 或者访问 http://www.fleaphp.org/ 获得详细信息。
/////////////////////////////////////////////////////////////////////////////

/**
 * 定义 FLEA_Helper_ImgCode 类
 *
 * @copyright Copyright (c) 2005 - 2008 QeeYuan China Inc. (http://www.qeeyuan.com)
 * @author 起源科技 (www.qeeyuan.com)
 * @package Core
 * @version $Id: ImgCode.php 972 2007-10-09 20:56:54Z qeeyuan $
 */

/**
 * FLEA_Helper_ImgCode 类实现了一个简单的图像验证码生成器，并带有检查验证方法
 *
 * 当启用了 session 时，验证码会保存在 session 中。用法：
 *
 * 模版页面中，在需要显示验证码的地方使用
 * <code>
 * <img src="<?php echo $this->_url('imgcode'); ?>" />
 * </code>
 *
 * 接下来为显示验证码的控制器编写 imgcode 方法：
 * <code>
 * function actionImgcode() {
 *     $imgcode =& FLEA::getSingleton('FLEA_Helper_ImgCode');
 *     $imgcode->image();
 * }
 * </code>
 *
 * 最后，对于用户提交的表单做如下验证：
 * <code>
 * function actionSubmit() {
 *     $imgcode =& FLEA::getSingleton('FLEA_Helper_ImgCode');
 *     // 假定验证码在表单中的字段名是 imgcode
 *     if ($imgcode->check($_POST['imgcode'])) {
 *         // 验证通过
 *     }
 * }
 * </code>
 *
 * @package Core
 * @author 起源科技 (www.qeeyuan.com)
 * @version 1.0
 */
class FLEA_Helper_ImgCode
{
    /**
     * 生成的验证码
     *
     * @var string
     */
    var $_code;

    /**
     * 验证码过期时间
     *
     * @var string
     */
    var $_expired;

    /**
     * 验证码图片的类型（默认为 jpeg）
     *
     * @var string
     */
    var $imagetype = 'jpeg';

    /**
     * 指示是否在生成验证码图片时保留已有的验证码
     *
     * 保留已有的验证码可以让用户在各个不同的页面都看到一致的验证码。
     * 只有这个验证码使用后，已有的验证码才会失效。
     *
     * @var boolean
     */
    var $keepCode = false;

    /**
     * 构造函数
     */
    function FLEA_Helper_ImgCode()
    {
        if(!isset($_SESSION)){
            @session_start();
        }

        $this->_code = isset($_SESSION['IMGCODE']) ?
                $_SESSION['IMGCODE'] : '';
        $this->_expired = isset($_SESSION['IMGCODE_EXPIRED']) ?
                $_SESSION['IMGCODE_EXPIRED'] : 0;
    }

    /**
     * 检查图像验证码是否有效
     *
     * @param string $code
     *
     * @return boolean
     */
    function check($code)
    {
        $time = time();
        if ($time >= $this->_expired || strtoupper($code) != strtoupper($this->_code)) {
            return false;
        }
        return true;
    }

    /**
     * 检查图像验证码是否有效（区分大小写）
     *
     * @param string $code
     *
     * @return boolean
     */
    function checkCaseSensitive($code)
    {
        $time = time();
        if ($time >= $this->_expired || $code != $this->_code) {
            return false;
        }
        return true;
    }

    /**
     * 清除 session 中的 imgcode 相关信息
     */
    function clear()
    {
        unset($_SESSION['IMGCODE']);
        unset($_SESSION['IMGCODE_EXPIRED']);
    }

    /**
     * 利用 GD 库产生验证码图像
     *
     * 目前 $options 参数支持下列选项：
     * -  paddingLeft, paddingRight, paddingTop, paddingBottom
     * -  border, borderColor
     * -  font, color, bgcolor
     *
     * 如果 font 为 0-5，则使用 GD 库内置的字体。
     * 如果要指定字体文件，则 font 选项必须为字体文件的绝对路径，例如：
     * <code>
     * $options = array('font' => '/var/www/example/myfont.gdf');
     * image($type, $length, $lefttime, $options);
     * </code>
     *
     * @param int $type 验证码包含的字符类型，0 - 数字、1 - 字母、其他值 - 数字和字母
     * @param int $length 验证码长度
     * @param int $leftime 验证码有效时间（秒）
     * @param array $options 附加选项，可以指定字体、宽度和高度等参数
     */
    function image($type = 0, $length = 4, $lefttime = 900, $options = null)
    {
        if ($this->keepCode && $this->_code != '') {
            $code = $this->_code;
        } else {
            // 生成验证码
            switch ($type) {
            case 0:
                $seed = '0123456789';
                break;
            case 1:
                $seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            default:
                $seed = '346789ABCDEFGHJKLMNPQRTUVWXYabcdefghjklmnpqrtuvwxy';
            }
            if ($length <= 0) { $length = 4; }
            $code = '';
            list($usec, $sec) = explode(" ", microtime());
            srand($sec + $usec * 100000);
            $len = strlen($seed) - 1;
            for ($i = 0; $i < $length; $i++) {
                $code .= substr($seed, rand(0, $len), 1);
            }
            $_SESSION['IMGCODE'] = $code;
        }
        $_SESSION['IMGCODE_EXPIRED'] = time() + $lefttime;

        // 设置选项
        $paddingLeft = isset($options['paddingLeft']) ?
                (int)$options['paddingLeft'] : 3;
        $paddingRight = isset($options['paddingRight']) ?
                (int)$options['paddingRight'] : 3;
        $paddingTop = isset($options['paddingTop']) ?
                (int)$options['paddingTop'] : 2;
        $paddingBottom = isset($options['paddingBottom']) ?
                (int)$options['paddingBottom'] : 2;
        $color = isset($options['color']) ? $options['color'] : '0x666666';
        $bgcolor = isset($options['bgcolor']) ? $options['bgcolor'] : '0xacacac';
        $border = isset($options['border']) ? (int)$options['border'] : 1;
        $bdColor = isset($options['borderColor']) ? $options['borderColor'] : '0xacacac';

        // 确定要使用的字体
        if (!isset($options['font'])) {
            $font = 5;
        } else if (is_int($options['font'])) {
            $font = (int)$options['font'];
            if ($font < 0 || $font > 5) { $font = 5; }
        } else {
            $font = imageloadfont($options['font']);
        }

        // 确定字体宽度和高度
        $fontWidth = imagefontwidth($font);
        $fontHeight = imagefontheight($font);

        // 确定图像的宽度和高度
        $width = $fontWidth * strlen($code) + $paddingLeft + $paddingRight +
                $border * 2 + 1;
        $height = $fontHeight + $paddingTop + $paddingBottom + $border * 2 + 1;

        // 创建图像
        $img = imagecreate($width, $height);

        // 绘制边框
        if ($border) {
            list($r, $g, $b) = $this->_hex2rgb($bdColor);
            $borderColor = imagecolorallocate($img, $r, $g, $b);
            imagefilledrectangle($img, 0, 0, $width, $height, $borderColor);
        }

        // 绘制背景
        list($r, $g, $b) = $this->_hex2rgb($bgcolor);
        $backgroundColor = imagecolorallocate($img, $r, $g, $b);
        imagefilledrectangle($img, $border, $border,
                $width - $border - 1, $height - $border - 1, $backgroundColor);

        //画点
        for($i=0;$i<140;$i++) {
          $color2 = imagecolorallocate($img, rand(40, 170), rand(40, 170),rand(40, 170));
          imagesetpixel($img, rand(0,10000)%74, rand(0,10000)%36, $color2);
        }

        //画线
        $lineCnt = rand(1,2);
        for($i=0;$i<$lineCnt;$i++) {
          $color2 = imagecolorallocate($img, rand(30,130), rand(30,130),rand(30,130));
          imageline($img, rand(0,40*$i), 0, 74, rand(0,36), $color2);
        }

        // 绘制文字
        // list($r, $g, $b) = $this->_hex2rgb($color);
        $textX = 0;
        for ($i=0; $i < strlen($code); $i++) {
            $textColor = imagecolorallocate($img, rand(50,130), rand(50,130), rand(50,130));
            $textX += $i == 0 ? ($paddingLeft + $border) : ($fontWidth);
            $textY = $paddingTop + $border;
            imagestring($img, $font, $textX, $textY, $code{$i}, $textColor);
        }

        // 输出图像
        switch (strtolower($this->imagetype)) {
        case 'png':
            header("Content-type: " . image_type_to_mime_type(IMAGETYPE_PNG));
            imagepng($img);
            break;
        case 'gif':
            header("Content-type: " . image_type_to_mime_type(IMAGETYPE_GIF));
            imagegif($img);
            break;
        case 'jpg':
        default:
            header("Content-type: " . image_type_to_mime_type(IMAGETYPE_JPEG));
            imagejpeg($img);
        }

        imagedestroy($img);
        unset($img);
    }

    /**
     * 汉字验证码
     * Time：2018/04/26 13:06:06
     * @author li
     * @param 参数类型
     * @return 返回值类型
    */
    function imageZh($fontSize=14,$lefttime = 900 ){
        //背景
        $image = imagecreatetruecolor(74, 36);
        $background = imagecolorallocate($image, 180, 180, 180);
        imagefill($image, 0, 0, $background);

        //画线
        for($i=0;$i<3;$i++) {
          $color2 = imagecolorallocate($image, rand(20, 100), rand(20, 100),rand(20, 100));
          imageline($image, rand(0,40*$i), 0, 74, rand(0,36), $color2);
        }

        //画点
        for($i=0;$i<50;$i++) {
          $color2 = imagecolorallocate($image, rand(80, 100), rand(80, 100),rand(80, 100));
          imagesetpixel($image, rand(0,10000)%74, rand(0,10000)%36, $color2);
          // imagefilledellipse($image,rand(0,10000)%74,rand(0,10000)%36,2,rand(2,6),$color2);
        }
        //画大点
        for($i=0;$i<6;$i++) {
          $color2 = imagecolorallocate($image, rand(80, 100), rand(80, 100),rand(80, 100));
          imagefilledellipse($image,rand(0,10000)%74,rand(0,10000)%36,rand(2,4),rand(2,6),$color2);
        }

        //词语
        if ($this->keepCode && $this->_code != '') {
            $text = $this->_code;
            $_SESSION['IMGCODE'] = $text;
        }else{
            $texts = '科教,绿能,能源,阳光,柔媚,娇婉,秋恋,夕颜,淡泊,玲珑,轻灵,飘逸,清味,冷月,清雅,幽静,温馨,芳香,青味,迷人,可爱,宜人,朦胧,朗逸,琉璃,火锅,空灵,微凉,暖阳,绚丽,绮丽,华丽,极致,耀眼,璀璨,沐夏,梦幻,花香,庞大,众山,高大,阵雨,骤雨,雨帘,冷雨,细雨,冻雨,梅雨,雨丝,春雨,阴雨,突然,应该,急雨,暴雨,透雨,开心,淡云,晶莹,香甜,精细,美丽,茉莉,甘甜,漂亮,活泼,和谐,晴空,娇嫩,优雅,幽雅,护士,艳丽,秋音,淡雅,清新,月光,清爽,额外,水晶,琉璃,炫彩,可控,冰晶,落花,飞雪,落英,缤纷,唯美,秀丽,魅力,俏丽,桎梏,飘逸,轻灵,悠远,易奇,张三,万达,常州,南京,背景,北京,熊猫,企业,企鹅,清泠,剔透,淡泊,清幽,清欢,幽邃,提供,苍茫,融入,缠绵,眷恋,空忆,渺茫,出尘,悠然,清透,青涩,流程,评论,凄婉,妙清,高铁,凹凸,胸怀,清微,清味,总是,早上,晚上,完善,网速,施工,证书,真实,阵势,真帅,证明,期望,但是,清寒,冬天,夏天,广告,知识,赠送,暂时,展示,正式,老婆,依依,蝶衣,口味,闲淡,清玄,单独,解决,苔痕,好久,规格,灵犀,风骨,涟漪,倾心,宽广,自私,采采,古调,清平,优化,青味,水墨,心砚,不符,浪迹,孩子,医生,暖风,仪式,陷阱,魔法,装备,地形,永续,速攻,反击,彼岸,共鸣,列车,元旦,元素,命运,悟空,回家,收到,控件,刘海,红泥,订单,风格,放到,现场,办公,鱼人,面积,沟通,合计,淘气,娱乐,梅林,孔明,请求,筛选,地方,事务,爱国,爱家,爱人,思考,言语,枝叶,疼痛,猜测,奔腾,丑陋,长久,模仿,固定,寒冷,恐惧,奇怪,整齐,漂浮,特殊,虚假,遥望,微弱,消灭,珍贵,祝愿,缠绕,颤抖,批量,拉扯,柔软,明亮,宽敞,黑暗,破旧,美好,飞翔,挖掘,搜索,期待,盼望,祈祷,微小,捕捉,思念,幻想,沉思,寻思,回忆,回顾,设想,希望,憧憬,友爱,团结,合作,愉快,勤奋,认真,专注,钻研,踏实,勤恳,潇洒,坚强,美丽,漂亮,自信,干净,壮观,小巧,健壮,慈祥,温柔,赞许,欣喜,安详,坦然,腼腆,害羞,优美,努力,爱心,甜蜜,积极,动人,典雅,豁达,美满,简洁,秀丽,妩媚,可爱,幸福,兴奋,英俊,关切,清净,欢喜,消瘦,陶醉,吟诵,风铃,悦耳,失望,结实,强壮,匀称,标致,大方,俏丽,俊秀,帅气,耳轮,耳垂,浓黑,细长,狡诈,早春,初春,阳春,春节,春联,春雷,春季,春意,俊目,秀目,朗目,星眸,彪壮,强健,刚健,单薄,单薄,憔悴,纤弱,秀丽,巍峨,高耸,陡峭,聪慧,眺望,慧眼,秋波,消息,小溪,东西,南北,西南,东北,东南,苹果,梨花,下雪,学习,口子,流水,银行,个人,服务,输入,高山,投诉,提高,能力,杰多,花开,花落,话痨,统一,体验,通用,土豪,框架,累了,藕片,同意,怕冷,凭空,留言,情况,不过,不够,不管,办公,如果,人工,风格,环境,计划,凉快,立刻,里面,另外,预计,语句,预警,预警,好玩,功能,考虑,科技,空间,去年,今年,很难,哈喽,户口,浴巾,然后,可怜,衣服,月份,软件,菊花,莲花,荷花,牡丹,大小,多少,高度,艺术,阳光,开朗,内向,屋子,屋顶,结构,勘察,喝茶,上门,报价,管理,学生,几个,记住,电脑,笔记,本子,毛笔,纸张,银行,手机,房子,私人,容量,不平,关系,大爱,故事,股市,古诗,估值,固执,工资,估算,江苏,移动,图片,歌曲,歌声,歌手,萝卜,白菜,提示,大学,兄弟,姐妹,乌龟,大象,狮子,老虎,小猫,小狗,刺猬,青蛙,蛤蟆,饕餮,青龙,白虎,玄武,朱雀,红色,黄色,绿色,蓝色,轻松,青色,橙色,紫色,褐色,黑色,白色,百岁,松鼠,松树,送书,两个,三个,人品,测试,地图,不行,跑步,走路,跳高,游泳,吃饭,喝水,河水,电器,厉害,徐州,老子,孔子,诗人,画家,绘画,表演,邋遢,日子,月亮,火星,满目';
            $textArr = explode(',',$texts);

            //处理随机获取
            $count = count($textArr)-1;
            $randArr = range(0, $count);
            shuffle($randArr);
            shuffle($randArr);
            $key = reset($randArr);
            //获取key
            $text = preg_split('/(?<!^)(?!$)/u', $textArr[$key]);
            $_SESSION['IMGCODE'] = $textArr[$key];
        }

        $_SESSION['IMGCODE_EXPIRED'] = time() + $lefttime;

        // dump($text);exit;
        //画字
        $textX = 0;
        for ($i=0; $i < 2; $i++) {
          $textColor = imagecolorallocate($image, rand(0, 50), rand(0, 50), rand(0, 50));
          $textX += rand(13,28);
          $textY = rand(16, 32);
          imagettftext($image, $fontSize, rand(15, 30), $textX, $textY, $textColor, "Resource/ttf/msyh.ttf", $text[$i]);
        }
        //输出图片
        header("Content-type: image/jpeg;charset=utf-8");
        imagejpeg($image);
        imagedestroy($image);//释放图片所占内存
        unset($image);
    }

    /**
     * 将 16 进制颜色值转换为 rgb 值
     *
     * @param string $hex
     *
     * @return array
     */
    function _hex2rgb($color, $defualt = 'ffffff')
    {
        $color = strtolower($color);
        if (substr($color, 0, 2) == '0x') {
            $color = substr($color, 2);
        } elseif (substr($color, 0, 1) == '#') {
            $color = substr($color, 1);
        }
        $l = strlen($color);
        if ($l == 3) {
            $r = hexdec(substr($color, 0, 1));
            $g = hexdec(substr($color, 1, 1));
            $b = hexdec(substr($color, 2, 1));
            return array($r, $g, $b);
        } elseif ($l != 6) {
            $color = $defualt;
        }

        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
        return array($r, $g, $b);
    }
}
