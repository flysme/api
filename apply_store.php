<?php
include_once './config/common.php';
include_once './config/config.php';
include_once './config/sql.php';
  class Applystore {
    public $sql;
    public $db;
    public function checkStores ($store_name) {
      $ressql= $this-> sql-> checkStore($store_name); //检查店铺信息sql
      $res= $this-> db-> getData($ressql); //检查店铺信息
      return $res;
    }
    public function set ($store_name,$address,$user_id,$privileges) {
      $this-> sql = new Sql();
      $this-> db = new DB();
      $rescount = $this->checkStores($store_name);
      if (is_array($rescount)) {  // 存在店铺信息
        if ($rescount['store_name'] == $store_name) {
          return $res = (object)array('data' => (object)array(),'msg'=>'此店铺名称已被注册', 'status'=>403);
        } else {
          return $res = (object)array('data' => (object)array(),'msg'=>$this-> db->links->error, 'status'=>403);
        }
      } else {
        $data = array(
          'store_name' => $store_name,
          'address' => $address,
          'create_time' => time(),
          'status' => '0', /*status: -1 未开通 0 已开通 1 已注销*/
         );
        /*insert店铺信息*/
        $storecount = $this-> sql -> apply($data);
        $result =  $this-> db -> query($storecount);
        // /*检查店铺信息*/
        $rescheckcount = $this-> checkStores($store_name);

        if (!empty($rescheckcount)) {
          $params = array(
            'user_id'=> $user_id,
            'store_id'=> $rescheckcount['store_id'],
            'create_time'=> time(),
            'privileges'=> $privileges, /*privileges: 0 采购 1 店员 2 运营 3 店长*/
          );
          /*insert 关联店铺与用户*/
          $userstoresql = $this-> sql -> relevancyUserandStroe($params);
          $userstoreresult =  $this-> db -> query($userstoresql);
          if (!empty($userstoreresult)) {
            $this-> db->links->close();
            return $res = (object)array('data' => (object)array('storename'=>$rescheckcount['store_name'],'store_id'=> $rescheckcount['store_id'],'status'=>$rescheckcount['status']),'msg'=>'开通成功', 'status'=>0);
          } else {
            return $res = (object)array('data' => (object)array(),'msg'=>'店铺与用户关联失败', 'status'=>401);
          }
        } else {
          return $res = (object)array('data' => (object)array(),'msg'=>'开通失败', 'status'=>403);
        }
      }
    }
  }

  $store_name = trim($_POST['store_name']);
  $address = trim($_POST['address']);
  $user_id = trim($_POST['user_id']);
  $privileges = trim($_POST['privileges']);
  if(!empty($store_name) && !empty($address)) {
       $regisstore = new Applystore();
       $res = $regisstore->set($store_name,$address,$user_id,$privileges);
       echo json_encode($res);
  } else {
    if (empty($store_name)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'店铺名称不能为空', 'status'=>401));
    }
    if (empty($address)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'地址不能为空', 'status'=>401));
    }
    if (empty($user_id)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'user_id不能为空', 'status'=>401));
    }
    if (empty($privileges)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'用户身份不能为空', 'status'=>401));
    }
  }
