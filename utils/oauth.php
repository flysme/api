<?php
include_once './service/session/session.php';
class Oauth {
  public static function checkLogin () {
    if ( empty(Session::get('uid')) ) {
      echo json_encode((object)array('data' => (object)array(),'msg'=>'未授权，请重新登录', 'status'=>401));
      exit();
    }
    if ( empty(Session::get('token')) ) {
      echo json_encode((object)array('data' => (object)array(),'msg'=>'非法访问', 'status'=>401));
      exit();
    }
    var_dump($_SERVER);
    exit();
    // if ( !empty(Session::get('token')) && empty(Session::get('token')) !== ) {
    //   echo json_encode((object)array('data' => (object)array(),'msg'=>'非法访问', 'status'=>401));
    //   exit();
    // }
  }
}
