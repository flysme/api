<?php
include_once './config/common.php';
include_once './config/sql.php';
include_once './utils/oauth.php';
include_once './service/session/session.php';
include_once './utils/utils.php';
  class Store_setting {
    public $DB;
    function __construct(){
       include_once './config/db.php';
       $this -> DB = new DB();
     }
    /*检查是否存在待审核的店铺*/
    public function getStoreSetting ($store_id) {
      $this->DB->connect();//连接数据库
      $get_store_setting = Sql::getStoreSetting($store_id);
      $result = $this->DB->getData($get_store_setting);
      $result['business_status'] = intval($result['business_status']);
      return $res = (object)array('data' => $result,'msg'=>'', 'status'=>0);
    }
    public function setting ($business_status,$setting_id,$store_id,$delivery_price,$start_delivery_price,$discounts,$business_start_times,$business_end_times) {
      $this->DB->connect();//连接数据库
      $data = array(
        'setting_id' => $setting_id,
        'store_id' => $store_id,//店铺id
        'delivery_price' => $delivery_price, //配送费
        'start_delivery_price' => $start_delivery_price, //起送金额
        'discounts' => $discounts,//满减列表
        'business_start_times' => $business_start_times, //开始营业时间
        'business_end_times' => $business_end_times, //结束营业时间
        'business_status' => intval($business_status), //是否营业
        'create_time' => time()
       );
      /*insert店铺设置信息*/
      $store_setting = Sql::storeSetting($data);
      $result = $this->DB->query($store_setting);
      // /*检查店铺信息*/
      if (!empty($result)) {
          $res = (object)array('data' => (object)array(),'msg'=>'设置成功', 'status'=>0);
      }
      else
      {
         $res = (object)array('data' => (object)array(),'msg'=>'设置失败', 'status'=>400);
      }
      $this->DB->links->close();
      return $res;
    }
  }
  /*验证登录*/
  Oauth::checkLogin();
  $storesetting = new Store_setting();
  $utils = new Utils();
  // 获取满减信息
  if ($utils->isGet()) {
    $store_id = trim($_GET['store_id']);
    if (!empty($store_id)) {
      $res = $storesetting->getStoreSetting($store_id);
      echo json_encode($res);
    }
  }
  // 新增满减信息
  if ($utils->isPost() || $utils->isPut()) {
      if ($utils->isPut()) {
        $data = $utils->getParams();
        $setting_id = $data['setting_id'];
        $store_id = $data['store_id'];
        $delivery_price = $data['delivery_price'];
        $start_delivery_price = $data['start_delivery_price'];
        $discounts = serialize($data['discounts']);
        $business_start_times = $data['business_start_times'];
        $business_end_times = $data['business_end_times'];
        $business_status = $data['business_status'];
      } else {
        $setting_id = $utils->generateUid();
        $store_id = trim($_POST['store_id']);
        $delivery_price = trim($_POST['delivery_price']);
        $start_delivery_price = trim($_POST['start_delivery_price']);
        $discounts = serialize($_POST['discounts']);
        $business_start_times = trim($_POST['business_start_times']);
        $business_end_times = trim($_POST['business_end_times']);
        $business_status = $_POST['business_status'];
      }
    if(!empty($store_id)) {
         $res = $storesetting->setting($business_status,$setting_id,$store_id,$delivery_price,$start_delivery_price,$discounts,$business_start_times,$business_end_times);
         echo json_encode($res);
    } else {
      if (empty($store_id)) {
        echo json_encode(array('data' => (object)array(),'msg'=>'店铺id未知', 'status'=>400));
      }
      if (empty($delivery_price)) {
        echo json_encode(array('data' => (object)array(),'msg'=>'配送费未知', 'status'=>400));
      }
      if (empty($start_delivery_price)) {
        echo json_encode(array('data' => (object)array(),'msg'=>'起送费未知', 'status'=>400));
      }
      if (empty($business_start_times) || empty($business_end_times)) {
        echo json_encode(array('data' => (object)array(),'msg'=>'营业时间未知', 'status'=>400));
      }
    }
  }
