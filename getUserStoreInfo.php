<?php
include_once './config/common.php';
include_once './config/sql.php';
include_once './utils/utils.php';

  class GetuserInfo {
    public $utils;
    public $DB;
    private $store_id;
    function __construct($store_id){
       include_once './config/db.php';
       $this -> DB = new DB();
       $this-> utils = new Utils();
       $this-> store_id = $store_id;
    }
    public function getstoreInfo () {
      $this->DB->connect();//连接数据库
      $getStoreInfosql = Sql::getStoreInfo($this->store_id);
      $resultstore = $this->DB->getData($getStoreInfosql);
      if (!empty($resultstore)) {  // 存在店铺信息
          $resstore = array(
            '_id' =>$resultstore['_id'],
            'title' =>$resultstore['store_name'],
            'img' =>$resultstore['store_image'],
            'setting' =>array(
              'discounts'=>unserialize($resultstore['discounts']),
              'start_delivery_price'=>($resultstore['start_delivery_price']),
              'delivery_price'=>($resultstore['delivery_price']),
              'business_start_times'=>($resultstore['business_start_times']),
              'business_end_times'=>($resultstore['business_end_times']),
              'discounts' => !empty($resultstore['discounts']) ? unserialize($resultstore['discounts']) :null,
              'is_business' =>  intval($resultstore['business_status'] && $this -> utils->checkIsBetweenTime($resultstore['business_start_times'],$resultstore['business_end_times'])) //判断是否营业
            ),
          );
           $res = (object)array('data' => (object)array('store'=>$resstore),'msg'=>'', 'status'=>0);
      } else {
        $res = (object)array('data' => (object)array(),'msg'=>$DB->links->error, 'status'=>400);
      }
      $this->DB->links->close();
      return $res;
    }

  }
  $store_id = trim($_GET['_id']);
  $Getsstore = new GetuserInfo($store_id);
 $res = $Getsstore->getstoreInfo();
 echo json_encode($res);
