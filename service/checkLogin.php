<?php
session_start();
if (!isset($_SESSION['uid'])) {
  echo json_encode(array('data' => (object)array(),'msg'=>'您还没有登录', 'status'=>403));
  exit;
}
