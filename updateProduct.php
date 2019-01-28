<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/utils.php';
include_once './utils/oauth.php';
  class Update_products {
    public function get ($data) {
      /*
       status: -1 未开通 0 已开通 1 已注销
      */
      $DB = new DB();
      $DB->connect();//连接数据库
      $updatesql = Sql::updateProductstatus($data['status'],$data['product_ids']);
      $result = $DB->query($updatesql);
      if (!empty($result)) {
        $res = (object)array('data' => (object)array(),'msg'=>'处理成功', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array('products'=>array()),'msg'=>'暂无商品', 'status'=>0);
      }
      $DB->links->close();
      return $res;
    }
  }
  /*验证登录*/
  Oauth::checkLogin();
  $utils = new Utils();
  $data = $utils->getParams();
  $ischeck = true;
  $product_ids= $data['product_ids'];
  $status= $data['status'];
  if(!is_array($product_ids)) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'商品id未知', 'status'=>400);
  }
  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $products = new Update_products();
    $data = array(
      'status'=>$status,
      'product_ids'=>$product_ids,
    );
    $res = $products->get($data);
    echo json_encode($res);
  }
