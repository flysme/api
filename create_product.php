<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/utils.php';
include_once './utils/oauth.php';
  /*验证登录*/
  Oauth::checkLogin();
  $DB = new DB();
  $DB->connect();//连接数据库
  $utils = new Utils();
  $ischeck = true;
  $product_name=base64_encode(trim($_POST['products_name']));
  $products_desc=base64_encode(trim($_POST['products_desc']));
  $product_image= $_POST['products_image'];
  $store_id=trim($_POST['store_id']);
  $attribute_list=$_POST['attributes'];
  $product_unit=trim($_POST['products_unit']);
  $category_id=trim($_POST['category_id']);
  $skus=$_POST['skus'];
    /*组织sku数据格式*/
    $skusspecItem = array();
    foreach($skus as $key=>$value){
      foreach($value as $item){
        if ($item['k']!='num' && $item['k']!='price') {
          if (!is_array($skusspecItem[$key]['skuspecs'])) {
            $skusspecItem[$key]['skuspecs'] = array();
          }
          $skusspecItem[$key]['skuspecs'][] = $item['n'].':'.$item['v'];
        }
        if ($item['k']=='num') {
          $skusspecItem[$key]['num'] = $item['v'];
        }
        if ($item['k']=='price') {
          $skusspecItem[$key]['price'] = $item['v'];
        }
      }

    }
    /*校验必填字段*/
  if(empty($product_name)) {
      $ischeck = array('data' => (object)array(),'msg'=>'请填写商品名称', 'status'=>400);
  } else if(empty($products_desc)) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'请填写商品描述', 'status'=>400);
  } else if(empty($store_id)) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'店铺id未知', 'status'=>400);
  } else if(empty($skus)) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'sku信息未知', 'status'=>400);
  } else if(empty($product_unit)) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'库存单位未知', 'status'=>400);
  } else if(empty($category_id)) {
      $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'分类id未知', 'status'=>400);
  }
  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $data = array('store_id' => $store_id,'product_name'=> $product_name);
    $selectProductsql = Sql::selectProducts($data);
    $result = $DB->getData($selectProductsql);
    if (is_array($result)) {
        $res = (object)array('data' => (object)array(),'msg'=>'商品已存在同名', 'status'=>400);
    } else {
      $params = array(
        'product_name' => $product_name,
        'product_id' =>  $utils->generateUid(),
        'product_desc' => $products_desc,
        'product_img' => $product_image ? serialize($product_image) : '',
        'store_id' => $store_id,
        'category_id' => $category_id,
        'product_unit' => $product_unit,
        'attribute_list' => serialize($attribute_list),
        'status' => 1, /* 1 已上架  0 已下架*/
        'update_time' => time(),
        'create_time' => time(),
      );
      $createProductsql = Sql::createProducts($params);
      $createres = $DB->query($createProductsql);
      if ($createres) {
        $datas=array(
          'store_id'=>$store_id,
          'product_name'=>$product_name
        );
         $select = Sql::selectProducts($datas);
         $currentresult = $DB->getData($select);
         if (is_array($currentresult)) {
           $specsInsert = array(); //插入sql数组
           foreach ($attribute_list as $value) {
             $specsInsert[] = array(
               'attr_key_name'=>$value['key'],
               'product_id'=>$currentresult['product_id'],
             );
           };
           /*插入sku属性名*/
           $insertspecsAttrkey = Sql::createSkuSpecsAttrKey($specsInsert);
           $skusqecsAttrkeyres = $DB->query($insertspecsAttrkey);
           if ($skusqecsAttrkeyres) {
             /*获取插入sku属性值*/
             $selectspecsAttrkey = Sql::selectSkuSpecsAttrKey($currentresult['product_id']);
             $getspecsAttrkey = $DB->getAll($selectspecsAttrkey);
             if (is_array($getspecsAttrkey)) {
               $insertspecsAttrval = array();
               /*解析正确的insert数据*/
               foreach ($getspecsAttrkey as $key=> $values) {
                 if ($attribute_list[$key]['key'] == $values['attr_key_name']) {
                   foreach ($attribute_list[$key]['value'] as $ivalue) {
                    $insertspecsAttrval[] = array(
                      'attr_values_name'=>$ivalue,
                      'attr_keys_id'=>$values['attr_keys_id'],
                      'picUrl'=>'',
                      'upts_time'=>time(),
                      'create_time'=>time()
                    );
                  };
                 };
               };
               if (count($insertspecsAttrval) > 0) {
                 /*插入sku属性值*/
                 $insertspecsAttrvalues = Sql::selectSkuSpecsAttrValues($insertspecsAttrval);
                 $skusqecsAttrkeyres = $DB->query($insertspecsAttrvalues);
                 if ($skusqecsAttrkeyres) {
                   $skuInsertList = array();
                   foreach ($skusspecItem as $value) {
                     $skuInsertList[] = array(
                       'product_id'=>$currentresult['product_id'],
                       'product_num'=>$value['num'],
                       'product_price'=>$value['price'],
                       'product_specs'=>implode(",", $value['skuspecs']),
                       'product_img'=>'',
                     );
                   }
                   /*插入sku记录*/
                   $insertskuspecs = Sql::createSkuProducts($skuInsertList);
                   $skusqecsAttrkeyres = $DB->query($insertskuspecs);
                 };
               }
             };
           };
           $createres = $DB->query($createSkusqls);
           $res = (object) array('data' => (object)array(),'msg'=>'', 'status'=>0);
         } else {
           $res = (object) array('data' => (object)array(),'msg'=>$DB->links->error, 'status'=>400);
         }
      } else {
        $res = (object) array('data' => (object)array(),'msg'=>$DB->links->error, 'status'=>400);
      }
    }
    $DB->links->close();
    echo json_encode($res);
  }
