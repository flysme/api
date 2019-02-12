<?php
include_once './config/common.php';
include_once './service/checkreg.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './service/session/session.php';
  class Login {
    public function getStoreData ($store) {
      $res = array();
      if (is_array($store) && count($store) > 0) {
        foreach ($store as  $value) {
          $address = array_values(explode("/",$value['address']));
          $district = array(
            'province'=>$address[0],
            'city'=>$address[1],
            'name'=>$address[2],
          );
          $item = array(
            '_id'=>$value['store_id'],
            'name'=>$value['store_name'],
            'address'=>$district,
          );
          array_push($res, $item);
        }
      }
      return $res;
    }
    public function set ($username,$password) {
      /*
       status: -1 未开通 0 已开通 1 已注销
      */
      $data = array(
        'username' => $username
       );
      $DB = new DB();
      $DB->connect();//连接数据库

      $usercheck = Sql::loginUser($data);
      $result = $DB->getData($usercheck);
      if (is_array($result)) {
        if (md5(md5($password).md5($password))!= $result['password']) {
          $res =  (object)array('data' => (object)array(),'msg'=>'密码不正确', 'status'=>400);
        } else {
          $getStoresql = Sql::getStoreList($result['user_id']);
          $resultstore = $DB->getAll($getStoresql);
          $info = self::getStoreData($resultstore);
          $getdefaultStoresql = Sql::getUserdefaultStroe($result['user_id']);
          $resultdefaultstore = $DB->getData($getdefaultStoresql);
          $store_id = isset($resultdefaultstore['store_id']) ?  $resultdefaultstore['store_id'] :'';
          $resinfo = (object)array('user_name'=>$username,'store_id'=>$store_id,'store_info'=>$info);
          Session::set('uid', $result['user_id'], 1800); //设置session
          $res =  (object)array('data' =>$resinfo,'msg'=>'登录成功', 'status'=>0);
        }
      }
       else
      {
         $res =  (object) array('data' => (object)array(),'msg'=>'未注册', 'status'=>403);
      }
      $DB->links->close();
      return $res;
    }
  }

  $ischeck = true;
  $username="";
  $password="";
  if(empty(trim($_POST['user_name']))) {
      $ischeck = array('data' => (object)array(),'msg'=>'请填写手机号', 'status'=>400);
  } else if(empty(trim($_POST['password']))) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'请填写密码', 'status'=>400);
  }
  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $check = new Match();
    if ($check->checkMobile($_POST['user_name'])!='') {
      echo json_encode(array('data' => (object)array(),'msg'=>$check->checkMobile($_POST['user_name']), 'status'=>400));
    }
    $login = new Login();
    $username = trim($_POST['user_name']);
    $password = trim($_POST['password']);
    $res = $login->set($username,$password);
    echo json_encode($res);
  }
