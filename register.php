<?php
include_once './config/common.php';
include_once './service/checkreg.php';
include_once './config/config.php';
include_once './config/sql.php';


  class Register {
    public $sql;
    public $db;
    public function getUser ($username) {
        $usercount = $this-> sql -> checkUser($username);
        $resultcount =  $this-> db -> getData($usercount);
      return $resultcount;
    }
    public function set ($username,$password) {
      $this-> sql = new Sql();
      $this-> db = new DB();
      $rescount = $this->getUser($username); //检查用户信息
      if (is_array($rescount)) {  // 存在用户信息
        if ($rescount['username'] == $username) {
          return $res = (object)array('data' => (object)array(),'msg'=>'已注册此账户', 'status'=>403);
        } else {
          return $res = (object)array('data' => (object)array(),'msg'=>$this-> db->links->error, 'status'=>403);
        }
      } else {
        $data = array(
          'username' => $username,
          'password' => md5(md5($password).md5($password)),
          'create_time' => strtotime('now'),
          'status' => '-1' /*status: -1 未开通 0 已开通 1 已注销*/
         );
        $usercount = $this-> sql -> setUser($data);
        $result =  $this-> db -> query($usercount);
        $rescount = $this->getUser($username);
        $this-> db->links->close();
        if (!empty($rescount)) {
          return $res = (object)array('data' => (object)array('username'=>$rescount['username'],'user_id'=> $rescount['admin_id'],'store_id'=>$rescount['store_id']),'msg'=>'注册成功', 'status'=>0);
        } else {
          return $res = (object)array('data' => (object)array(),'msg'=>'注册失败', 'status'=>403);
        }
      }
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
       echo json_encode(array('data' => (object)array(),'msg'=>$check->checkMobile($username), 'status'=>401));
     }
  }
  else
  {
    if (empty($username)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'请填写手机号', 'status'=>401));
      exit();
    }
    if (empty($password)) {
      echo json_encode(array('data' => (object)array(),'msg'=>'请填写密码', 'status'=>401));
    }
  }
