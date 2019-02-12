<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/oauth.php';
  class Get_products_details {
    public function resetProduct ($result,$productDetailSpecData) {
      $resarr = array();
      $skuSpecList = array();
      foreach ($result as $item) {
          $skuSpecItem = explode(',',$item['product_specs']);
          $skumap_key = implode(';', $skuSpecItem);
          $sku_map_k = '';
          foreach ($skuSpecItem as  $value) {
            $sku_map_k.= end(explode(':', $value));
          }
          $resarr[$item['product_id']] = array(
            'product_id'=> $item['product_id'],
            'title'=> base64_decode($item['product_name']),
            'desc'=>base64_decode($item['product_desc']),
            'img'=> $item['product_img'],
            'category_id'=> $item['category_id'],
            'store_id'=> $item['store_id'],
            'num'=> $item['product_num'],
            'status'=> $item['status'],
            '规格;'=> $item['status'],
            'product_unit'=> $item['product_unit'],
            'create_time'=> date("Y-m-d H:i:s",$item['create_time']),
            'skus'=> !is_array($resarr[$item['product_id']]['skus']) ? array() : $resarr[$item['product_id']]['skus'],
            'skusMap'=> $productDetailSpecData,
          );
          $resarr[$item['product_id']]['skus'][$sku_map_k] = array('sku_id'=>$item['sku_id'],'price'=>$item['product_price'],'num'=>$item['product_num'],'product_specs'=>$skuSpecItem);
      }
      return reset(array_values($resarr));
    }
    /**
    * 处理sku 层级
    */
    public function resetProductskuspec ($data) {
      $resArr = array();
      foreach ($data as $item) {
        $resArr[$item['attr_k_id']] = array(
          'id' =>$item['attr_k_id'],
          'name' =>$item['attr_key_name'],
          'skuSpecValueList' => empty($resArr[$item['attr_k_id']]['skuSpecValueList']) ? array() : $resArr[$item['attr_k_id']]['skuSpecValueList'],
        );
        $resArr[$item['attr_k_id']]['skuSpecValueList'][] =array(
          'id'=>$item['attr_v_id'],
          'skuSpecId'=>$item['attr_k_id'],
          'value'=>$item['attr_values_name'],
          'picUrl'=>$item['picUrl'],
        );
      };
      return $resArr;
    }
    public function get ($product_id) {
      $p_detail_sql = Sql::getProductsDetail($product_id); //获取商品详情
      $DB = new DB();
      $DB->connect();//连接数据库
      $result = $DB->getAll($p_detail_sql);
      if (!empty($result)) {
        $p_detail_skuspec_sql = Sql::getProductsDetailSkuSpec($product_id); //获取商品详情
        $resultSpec = $DB->getAll($p_detail_skuspec_sql);
        $productDetailSpecData = $this->resetProductskuspec($resultSpec);
        $productDetailData = $this->resetProduct($result,$productDetailSpecData);
        $res = (object)array('data' => $productDetailData,'msg'=>'', 'status'=>0);
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
  $product_id=$_GET['product_id'];
  if(empty($product_id)) {
      $ischeck = array('data' => (object)array(),'msg'=>'商品id未知', 'status'=>400);
  }
  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $productsdetails = new Get_products_details();
    $res = $productsdetails->get($product_id);
    echo json_encode($res);
  }
