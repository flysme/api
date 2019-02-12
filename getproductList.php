<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/oauth.php';
  class Get_products {
    public function resetProduct ($result) {
      $resarr = array();
      foreach ($result as $item) {
        $resarr[$item['product_id']] = array(
          'product_id'=> $item['product_id'],
          'title'=> base64_decode($item['product_name']),
          'desc'=>base64_decode($item['product_desc']),
          'img'=> $item['product_img'],
          'category_id'=> $item['category_id'],
          'store_id'=> $item['store_id'],
          'num'=> empty($resarr[$item['product_id']]) ? $item['product_num'] : $resarr[$item['product_id']]['num']+=$item['product_num'],
          'status'=> $item['status'],
          'product_unit'=> $item['product_unit'],
          'create_time'=> date("Y-m-d H:i:s",$item['create_time']),
          'skus'=> empty($resArr[$item['product_id']]['skus']) ? array() : $resArr[$item['product_id']]['skus'],
        );
        $skus = array('sku_id'=>$item['sku_id'],'price'=>$item['product_price'],'num'=>$item['product_num'],'product_specs'=>$item['product_specs']);
      }
      return array_values($resarr);
    }
    public function get ($data) {
      $pageSize = 10;
      $data['currentPage'] = ($data['cursor'] - 1) * $pageSize;
      $data['pageSize'] = $pageSize;
      /*
       status: -1 未开通 0 已开通 1 已注销
      */
      $productsql = Sql::getProductsList($data);
      $DB = new DB();
      $DB->connect();//连接数据库
      $result = $DB->getAll($productsql);
      if (!empty($result)) {
        $res = (object)array('data' => (object)array('products'=>$this->resetProduct($result)),'msg'=>'', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array('products'=>array()),'msg'=>'暂无商品', 'status'=>0);
      }
      $DB->links->close();
      return $res;
    }
  }

  /*验证登录*/
  Oauth::checkLogin();
  $ischeck = true;
  $store_id=$_GET['store_id'];
  $catesgory_id= isset($_GET['catesgory_id']) ? trim($_GET['catesgory_id']): NULL;
  $product_name= isset($_GET['product_name']) ? base64_encode($_GET['product_name']): NULL;
  $Page= isset($_GET['cursor']) ? trim($_GET['cursor']): 1;
  $Page= isset($_GET['cursor']) ? trim($_GET['cursor']): 1;
  if(empty($store_id)) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'店铺id未知', 'status'=>400);
  }
  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $products = new Get_products();
    $data = array(
      'store_id'=>$store_id,
      'cursor'=>$Page,
      'product_name'=>$product_name,
      'catesgory_id'=>$catesgory_id,
    );
    $res = $products->get($data);
    echo json_encode($res);
  }
