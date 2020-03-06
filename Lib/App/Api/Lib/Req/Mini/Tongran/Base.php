<?php
class Api_Lib_Req_Mini_Tongran_Base  {

    function __construct() {
       /* $this->_model = FLEA::getSingleton('Model_Acm_Qrcodeverify');*/
       $this->_testApiUrl = 'https://sev7.eqinfo.com.cn/tongran_mini_demo/api.php';
    }

    function runTest($params,& $service){
        $service->send_user_error('ERROR_DATA' ,array(2));
        return array(1,2,3,4,5);
    }

    /**
     * @desc ：合同汇总数据接口(柱状图数据)
     * Time：2019年8月28日 14:48:33
     * @author Shenhao
     */
    function BarMonthData($params = array() ,& $service){
        $params['api_url'] = $params['api_url']?$params['api_url']:$this->_testApiUrl;//项目数据接口地址

        //实例化api request
        $requestServer = FLEA::getSingleton('Api_Request');
        $params['api_url'] && $requestServer->set_api_url_domain($params['api_url']);

        //组织参数
        $data = array();
        $data['method'] = 'get.BarMonth.data';
        $data['token'] = '608e870bde43bbb273807f5bee766f8f';
        $data['params'] = array(
            'year'     =>$params['year']?$params['year']:date('Y'),
            'account'    =>$params['account']?$params['account']:'',
        );

        $reponse = $requestServer->api_caller($data); //请求服务
        $arr = json_decode($reponse,true);
        if($arr['params'] && $arr['success']){
            $rowset = $arr['params'];
        }else{
            $rowset['data'] = array();
        }

        $result = array(
            'success'   => true,
            'msg'       => '请求成功',
            'errorcode' => '0',
            'params'    => $rowset,
        );

        return $result;
    }

    /**
     * @desc ：月度数据接口
     * Time：2019年8月29日 16:26:12
     * @author Shenhao
     */
    function MonthData($params,& $service){
        $params['api_url'] = $params['api_url']?$params['api_url']:$this->_testApiUrl;//项目数据接口地址

        //实例化api request
        $requestServer = FLEA::getSingleton('Api_Request');
        $params['api_url'] && $requestServer->set_api_url_domain($params['api_url']);

        //组织参数
        $data = array();
        $data['method'] = $params['method']?$params['method']:'get.Month.data';
        $data['token'] = '608e870bde43bbb273807f5bee766f8f';
        $data['params'] = array(
            'year'       =>$params['year']?$params['year']:date('Y'),
            'month'      =>$params['month']?$params['month']:date('M'),
            'pageNum'    =>$params['pageNum']?$params['pageNum']:'',
            'type'       =>$params['type']?$params['type']:'Month',
            'vatNum' =>$params['vatNum']?$params['vatNum']:'',
            'salesName'  =>$params['salesName']?$params['salesName']:'',
            'account'    =>$params['account']?$params['account']:'',
        );

        $reponse = $requestServer->api_caller($data); //请求服务
        $arr = json_decode($reponse,true);
        if($arr['params'] && $arr['success']){
            $rowset = $arr['params'];
        }else{
            $rowset['data'] = array();
        }

        $result = array(
            'success'   => true,
            'msg'       => '请求成功',
            'errorcode' => '0',
            'params'    => $rowset,
        );
        return $result;
    }


    /**
     * @desc ：生产进度报表
     * Time：2019年8月29日 16:26:12
     * @author pan
     */
    function ClientData($params,& $service){
        $params['pageNum'] = $params['pageNum']?$params['pageNum']:1;
        $params['type'] = 'Client';
        $params['method'] = 'get.Client.data';
        $params['vatNum'] = $params['vatNum']?$params['vatNum']:'';
        return $this->MonthData($params,$service);
    }

    /**
     * @desc ：业务员月度数据接口
     * Time：2019年8月29日 16:26:12
     * @author Shenhao
     */
    function SalerData($params,& $service){
        $params['pageNum'] = $params['pageNum']?$params['pageNum']:1;
        $params['type'] = 'Saler';
        $params['method'] = 'get.Saler.data';
        $params['salesName'] = $params['salesName']?$params['salesName']:'';
        return $this->MonthData($params,$service);
    }

    /**
     * @desc ：利润汇总数据
     * Time：2019年8月30日 12:47:17
     * @author Shenhao
     */
    function ProfitData($params,& $service){
        $params['api_url'] = $params['api_url']?$params['api_url']:$this->_testApiUrl;//项目数据接口地址

        //实例化api request
        $requestServer = FLEA::getSingleton('Api_Request');
        $params['api_url'] && $requestServer->set_api_url_domain($params['api_url']);

        //组织参数
        $data = array();
        $data['method'] = 'get.Profit.data';
        $data['token'] = '608e870bde43bbb273807f5bee766f8f';
        $data['params'] = array(
            'dateFrom'   =>$params['dateFrom']?$params['dateFrom']:date('Y-m-1'),
            'dateTo'     =>$params['dateTo']?$params['dateTo']:date('Y-m-d'),
            'pageNum'    =>$params['pageNum']?$params['pageNum']:'1',
            'account'    =>$params['account']?$params['account']:'',
        );

        $reponse = $requestServer->api_caller($data); //请求服务
        $arr = json_decode($reponse,true);
        if($arr['params'] && $arr['success']){
            $rowset = $arr['params'];
        }else{
            $rowset['data'] = array();
        }

        $result = array(
            'success'   => true,
            'msg'       => '请求成功',
            'errorcode' => '0',
            'params'    => $rowset,
        );
        return $result;
    }

    /**
     * @desc ：销售额汇总数据(当天总销售额，当月总销售额，当天下货数量(M)，当月下货数量(M))
     * Time：2019年8月30日 12:47:17
     * @author Shenhao
     */
    function SalesData($params,& $service){
        $params['api_url'] = $params['api_url']?$params['api_url']:$this->_testApiUrl;//项目数据接口地址

        //实例化api request
        $requestServer = FLEA::getSingleton('Api_Request');
        $params['api_url'] && $requestServer->set_api_url_domain($params['api_url']);

        //组织参数
        $data = array();
        $data['method'] = 'get.Sales.data';
        $data['token'] = '608e870bde43bbb273807f5bee766f8f';
        $data['params'] = array(
            'account'    =>$params['account']?$params['account']:'',
        );
        $reponseArr = $requestServer->api_caller($data); //请求服务
        $data = json_decode($reponseArr,true);
        if($data['params'] && $data['success']){
            $rowset = $data['params'];
        }else{
            $rowset['data'] = array();
        }

        $result = array(
            'success'   => true,
            'msg'       => '请求成功',
            'errorcode' => '0',
            'params'    => $rowset,
        );
        return $result;
    }


    /**
     * @desc 获取订单年份数据
     * Time：2019年8月30日 12:47:17
     * @author Shenhao
     */
    function YearsData($params,& $service){
        $params['api_url'] = $params['api_url']?$params['api_url']:$this->_testApiUrl;//项目数据接口地址

        //实例化api request
        $requestServer = FLEA::getSingleton('Api_Request');
        $params['api_url'] && $requestServer->set_api_url_domain($params['api_url']);

        //组织参数
        $data = array();
        $data['method'] = 'get.Year.list';
        $data['token'] = '608e870bde43bbb273807f5bee766f8f';
        $data['params'] = $params;

        $reponse = $requestServer->api_caller($data); //请求服务
        $arr = json_decode($reponse,true);
        if($arr['params'] && $arr['success']){
            $rowset = $arr['params'];
        }else{
            $rowset['data'] = array();
        }

        $result = array(
            'success'   => true,
            'msg'       => '请求成功',
            'errorcode' => '0',
            'params'    => $rowset,
        );
        return $result;
    }

    /**
     * @desc 获取项目的配置信息
     * Time：2020年2月2日 13:57:05
     * @author Shenhao
     */
    function SettingData($params,& $service){
        $params['api_url'] = $params['api_url']?$params['api_url']:$this->_testApiUrl;//项目数据接口地址

        //实例化api request
        $requestServer = FLEA::getSingleton('Api_Request');
        $params['api_url'] && $requestServer->set_api_url_domain($params['api_url']);

        //组织参数
        $data = array();
        $data['method'] = 'get.Setting.data';
        $data['token'] = '608e870bde43bbb273807f5bee766f8f';
        $data['params'] = $params;

        $reponse = $requestServer->api_caller($data); //请求服务
        $arr = json_decode($reponse,true);
        if($arr['params'] && $arr['success']){
            $rowset = $arr['params'];
        }else{
            $rowset['data'] = array();
        }

        $result = array(
            'success'   => true,
            'msg'       => '请求成功',
            'errorcode' => '0',
            'params'    => $rowset,
        );
        return $result;
    }

}
?>