<?php
include_once './config/common.php';
include_once './service/checkreg.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './service/session/session.php';
  class Switchstore {
    public function set ($user_id,$store_id) {
      $DB = new DB();
      $DB->connect();//连接数据库
      $data = array(
        'store_id'=>$store_id,
        'user_id'=>$user_id,
        'upts_time'=>time(),
      );
      $usercheck = Sql::updateUserandStroe($data);
      $result = $DB->query($usercheck);
      if ($result) {
          $resinfo = (object)array('store_id'=> $store_id);
          $res =  (object)array('data' =>$resinfo,'msg'=>'', 'status'=>0);
      } else {
          $res = (object) array('data' => (object)array(),'msg'=>$DB->links->error, 'status'=>400);
      }
      $DB->links->close();
      return $res;
    }
  }

  $ischeck = true;
  $user_id=Session::get('uid'); //获取保存的user_id
  $store_id="";
  if(empty($user_id)) {
      $ischeck = array('data' => (object)array(),'msg'=>'用户id未知', 'status'=>400);
  } else if(empty(trim($_POST['store_id']))) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'店铺id未知', 'status'=>400);
  }
  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $store = new Switchstore();
    $store_id = trim($_POST['store_id']);
    $res = $store->set($user_id,$store_id);
    echo json_encode($res);
  }
