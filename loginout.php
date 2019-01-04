<?php
session_start();
include_once './config/common.php';
  class Loginout {
    public function set () {
      $_SESSION = [];
      session_unset();
      session_destroy();
      return $res = (object)array('data' => (object)array(),'msg'=>'退出成功', 'status'=>0);
    }
  }

  $loginout = new Loginout();
  $loginout->set(); //退出登录
