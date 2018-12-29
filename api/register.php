<?php
header('Content-type: application/json');
include_once './config/config.php';
include_once './config/sql.php';
  class Register {
    public function set ($sql,$db) {
      $userset = $sql -> setUser('2121222',md5('212121212312312312123'),strtotime('now'),0,'');
      $result =  $db -> query($userset);
      if ($result) {
        return $res = (object)array('data' => (object)array(),'msg'=>'注册成功', 'status'=>0);
      } else {
        return $res =(object) array('data' => array(),'msg'=>$sql->error, 'status'=>403);
      }
    }
  }



  $data = $_POST;
  $ischeck = true;
  // echo
  if(empty($_POST['user_name'])) {
      $ischeck = (object)array('data' => (object)array(),'msg'=>'请填写手机号', 'status'=>401);
  }
  if(empty($_POST['password'])) {
      $ischeck = $ischeck = (object)array('data' => (object)array(),'msg'=>'请填写密码', 'status'=>401);
  }
  if (is_object($ischeck)) {
    echo json_encode($ischeck);
    exit();
  } else {
    $sql = new Sql();
    $db = new DB();
    $register = new Register();
    $res = $register->set($sql,$db);
    echo json_encode($res);
  }



?>
