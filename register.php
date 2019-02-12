<?php
include_once './config/common.php';
include_once './service/checkreg.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/utils.php';
  class Register {
    public $utils;
    public $DB;
    public function getUser ($username) {
        $usercount = Sql::checkUser($username);
        $resultcount =  $this->DB->getData($usercount);
      return $resultcount;
    }
    public function set ($username,$password) {
      $this->DB = new DB();
      $this->DB->connect();//连接数据库
      $this-> utils = new Utils();
      $rescount = $this->getUser($username); //检查用户信息
      if (is_array($rescount)) {  // 存在用户信息
        if ($rescount['username'] == $username) {
           $res = (object)array('data' => (object)array(),'msg'=>'已注册此账户', 'status'=>400);
        } else {
           $res = (object)array('data' => (object)array(),'msg'=>$this->DB->links->error, 'status'=>400);
        }
      }
      else
      {
        $user_id = $this->utils->generateUid();
        $data = array(
          'user_id' => $user_id,
          'username' => $username,
          'password' => md5(md5($password).md5($password)),
          'create_time' => time(),
          'status' => '-1' /*status: -1 未开通 0 已开通 1 已注销*/
         );
        $usercount = Sql::setUser($data);
        $result =  $this->DB->query($usercount);
        $getrescount = $this->getUser($username);
        if (is_array($getrescount)) {
           $res = (object)array('data' => (object)array('username'=>$getrescount['username'],'user_id'=> $getrescount['user_id']),'msg'=>'注册成功', 'status'=>0);
        } else {
           $res = (object)array('data' => (object)array(),'msg'=>'注册失败', 'status'=>400);
        }
      }
      $this->DB->links->close();
      return $res;
    }
  }
  $ischeck = true;
  $username= trim($_POST['user_name']);
  $password= trim($_POST['password']);
  if(!empty($username) && !empty($password)) {
     $check = new Match();
     if ($check->checkMobile($username)=='') {
       $register = new Register();
       $res = $register->set($username,$password);
       echo json_encode($res);
     } else {
       echo json_encode(array('data' => (object)array(),'msg'=>$check->checkMobile($username), 'status'=>400));
     }
  }
  else
  {
    if (empty($username)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'请填写手机号', 'status'=>400));
      exit();
    }
    if (empty($password)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'请填写密码', 'status'=>400));
    }
  }
