<?php
include_once './config/common.php';
include_once './config/config.php';
include_once './config/sql.php';
  class Create_categorys {
    public function set ($cats_name,$store_id) {
      $sql = new Sql();
      $db = new DB();
      /*
       status: -1 未开通 0 已开通 1 已注销
      */
      $data = array(
        'cats_name' => $cats_name,
        'store_id' => $store_id,
        'create_time' => time()
       );
      $categorysql = $sql -> createProductcategory($data);
      $result = $db -> query($categorysql);
      if ($result) {
          $res = (object)array('data' => (object)array(),'msg'=>'', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array(),'msg'=>$db->links->error, 'status'=>403);
      }
      $db->links->close();
      return $res;
    }
  }


  $ischeck = true;
  $cats_name=trim($_POST['cats_name']);
  $store_id=trim($_POST['store_id']);
  if(empty($cats_name)) {
      $ischeck = array('data' => (object)array(),'msg'=>'请填写分类名称', 'status'=>401);
  } else if(empty($store_id)) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'店铺id未知', 'status'=>401);
  }
  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $categorys = new Create_categorys();
    $res = $categorys->set($cats_name,$store_id);
    echo json_encode($res);
  }
