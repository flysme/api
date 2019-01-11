<?php
include_once './config/common.php';
include_once './config/config.php';
include_once './config/sql.php';
  class Get_categorys {
    public function get ($store_id) {
      $sql = new Sql();
      $db = new DB();
      /*
       status: -1 未开通 0 已开通 1 已注销
      */
      $categorysql = $sql -> getProductcategory($store_id);
      $result = $db -> getAll($categorysql);
      if (!empty($result)) {
        $resarr = array();
        foreach ($result as $item) {
          $resarr[] = array(
            'category_id'=> $item['id'],
            'category_name'=> $item['cats_name'],
            'create_time'=> date("Y-m-d H:i:s",$item['create_time'])
          );
        }
        $res = (object)array('data' => (object)array('categorys'=>$resarr),'msg'=>'', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array('categorys'=>array()),'msg'=>'暂无分类', 'status'=>0);
      }
      $db->links->close();
      return $res;
    }
  }


  $ischeck = true;
  $store_id=trim($_GET['store_id']);
  if(empty($store_id)) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'店铺id未知', 'status'=>401);
  }
  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $categorys = new Get_categorys();
    $res = $categorys->get($store_id);
    echo json_encode($res);
  }
