<?php
session_start();
include_once './config/common.php';
include_once './service/session/session.php';
  class Loginout {
    public function set () {
      Session::clear('uid'); //清除session
      return $res = (object)array('data' => (object)array(),'msg'=>'退出成功', 'status'=>0);
    }
  }

  $loginout = new Loginout();
  $res = $loginout->set(); //退出登录
  echo json_encode($res);
