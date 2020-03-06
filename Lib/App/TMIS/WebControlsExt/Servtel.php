<?php
/**
 * @desc ：获取服务器电话
 * Time：2018/04/08 15:03:24
 * @author Wuyou
 * @return string
*/
function _ctlServTel($name,$params)    {
    $servTel = FLEA::getCache('Service.Tel' ,-1);
    // 若缓存被删，则从登录的NewLogin_config中取
    if(!$servTel){
        require_once('Config/NewLogin_config.php');
        $servTel = $_login_config['servTel'];
    }
    return $servTel;
}
?>