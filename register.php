<?php
header('Content-type: application/json');
include_once './service/checkreg.php';
include_once './config/config.php';
include_once './config/sql.php';
  class Register {
    public function set ($username,$password) {
      $sql = new Sql();
      $db = new DB();
      /*
       status: -1 未开通 0 已开通 1 已注销
      */
      $usercount = $sql -> checkUser($username);
      $result =  $db -> getData($usercount);
      if (empty($result)) {
        $data = array(
          'username' => $username,
          'password' => md5($password),
          'create_time' => strtotime('now'),
          'status' => '-1', /*status: -1 未开通 0 已开通 1 已注销*/
          'store_id' => '' /*status: -1 未开通 0 已开通 1 已注销*/
         );
        $usercount = $sql -> setUser($data);
        $result =  $db -> query($usercount);
        if (!empty($result)) {
          return $res = (object)array('data' => (object)array(),'msg'=>'注册成功', 'status'=>0);
        } else {
          return $res = (object)array('data' => (object)array(),'msg'=>'注册失败', 'status'=>403);
        }
      } else {
        if ($result['username'] == $username) {
          return $res = (object)array('data' => (object)array(),'msg'=>'已注册此账户', 'status'=>403);
        }
      }
    }
  }
  $ischeck = true;
  $username="";
  $password="";
  if(isset($_POST['user_name']) && isset($_POST['password']) ) {
     $check = new Match();
     if ($check->checkMobile($_POST['user_name'])=='') {
       $register = new Register();
       $username = $_POST['user_name'];
       $password = $_POST['password'];
       $res = $register->set($username,$password);
       echo json_encode($res);
     } else {
       echo json_encode(array('data' => (object)array(),'msg'=>$check->checkMobile($_POST['user_name']), 'status'=>401));
     }
  }
  else {
    if (empty($_POST['user_name'])) {
      echo json_encode(array('data' => (object)array(),'msg'=>'请填写手机号', 'status'=>401));
      exit();
    }
    if (empty($_POST['password'])) {
      echo json_encode(array('data' => (object)array(),'msg'=>'请填写密码', 'status'=>401));
    }
  }
?>
