<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/utils.php';
include_once './utils/oauth.php';


  class Applystore {
    public $utils;
    public $DB;
    public function checkStores ($store_name) {
      $ressql= Sql::checkStore($store_name); //检查店铺信息sql
      $res= $this->DB->getData($ressql); //检查店铺信息
      return $res;
    }
    public function set ($store_name,$address,$street,$user_id,$privileges) {
      $this-> utils = new Utils();
      $this->DB = new DB();
      $this->DB->connect();//连接数据库
      $rescount = $this->checkStores($store_name);
      if (is_array($rescount)) {  // 存在店铺信息
        if ($rescount['store_name'] == $store_name) {
           $res = (object)array('data' => (object)array(),'msg'=>'此店铺名称已被注册', 'status'=>400);
        }
        else
        {
           $res = (object)array('data' => (object)array(),'msg'=>$DB->links->error, 'status'=>400);
        }
      }
      else
      {
        $data = array(
          'store_id' => $this->utils->generateUid(),
          'store_name' => $store_name,
          'address' => $address,
          'street' => $street,
          'create_time' => time(),
          'status' => '0', /*status: -1 未开通 0 已开通 1 已注销*/
         );
        /*insert店铺信息*/
        $storecount = Sql::apply($data);
        $result =  $this->DB->query($storecount);
        // /*检查店铺信息*/
        $rescheckcount = $this-> checkStores($store_name);
        // exit();
        if (!empty($rescheckcount)) {
          $params = array(
            'user_id'=> $user_id,
            'store_id'=> $rescheckcount['store_id'],
            'relation_id'=> $this->utils->generateUid(),
            'create_time'=> time(),
            'privileges'=> $privileges, /*privileges: 0 采购 1 店员 2 运营 3 店长*/
          );
          /*insert 关联店铺与用户*/
          $userstoresql = Sql::relevancyUserandStroe($params);
          $userstoreresult =  $this->DB->query($userstoresql);
          if (!empty($userstoreresult)) {
             $res = (object)array('data' => (object)array('storename'=>$rescheckcount['store_name'],'store_id'=> $rescheckcount['store_id'],'status'=>$rescheckcount['status']),'msg'=>'开通成功', 'status'=>0);
          } else {
             $res = (object)array('data' => (object)array(),'msg'=>'店铺与用户关联失败', 'status'=>400);
          }
        }
        else
        {
           $res = (object)array('data' => (object)array(),'msg'=>'开通失败', 'status'=>400);
        }
      }
      $this->DB->links->close();
      return $res;
    }
  }
  /*验证登录*/
  Oauth::checkLogin();
  $store_name = trim($_POST['store_name']);
  $address = trim($_POST['address']);
  $street = trim($_POST['street']);
  $user_id = trim($_POST['user_id']);
  $privileges = trim($_POST['privileges']);
  if(!empty($store_name) && !empty($address)) {
       $regisstore = new Applystore();
       $res = $regisstore->set($store_name,$address,$street,$user_id,$privileges);
       echo json_encode($res);
  } else {
    if (empty($store_name)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'店铺名称不能为空', 'status'=>400));
    }
    if (empty($address)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'地址不能为空', 'status'=>400));
    }
    if (empty($user_id)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'user_id不能为空', 'status'=>400));
    }
    if (empty($privileges)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'用户身份不能为空', 'status'=>400));
    }
  }
