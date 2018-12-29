<?php
session_start();
// header('Content-type: application/json');
include_once './service/checkreg.php';
include_once './config/config.php';
include_once './config/sql.php';
  class Login {
    public function set ($username,$password) {
      $sql = new Sql();
      $db = new DB();
      /*
       status: -1 未开通 0 已开通 1 已注销
      */
      $data = array(
        'username' => $username,
        'password' => md5($password)
       );
      $usercheck = $sql -> loginUser($data);
      $result =  $db -> getData($usercheck);
      if (!empty($result)) {
        $_SESSION['username']=$username;
        return $res = (object)array('data' => (object)array('user_name'=>$result['username']),'msg'=>'登录成功', 'status'=>0);
      } else {
        return $res =(object) array('data' => (object)array(),'msg'=>'未注册', 'status'=>403);
      }
    }
  }


  $ischeck = true;
  $username="";
  $password="";
  var_dump($_POST);
  exit();
  if(empty($_POST['user_name'])) {
      $ischeck = array('data' => (object)array(),'msg'=>'请填写手机号', 'status'=>401);
  } else if(empty($_POST['password'])) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'请填写密码', 'status'=>401);
  }
  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $check = new Match();
    if ($check->checkMobile($_POST['user_name'])!='') {
      echo json_encode(array('data' => (object)array(),'msg'=>$check->checkMobile($_POST['user_name']), 'status'=>401));
    }
    $login = new Login();
    $username = $_POST['user_name'];
    $password = $_POST['password'];
    $res = $login->set($username,$password);
    echo json_encode($res);
  }
