<?php
  include_once '../utils/utils.php';
  class Sql {
    /*注册*/
    public static function setUser($data){
      return "insert into store_user(`user_id`,`username`, `password`, `create_time`,`status`)values('{$data["user_id"]}','{$data["username"]}','{$data["password"]}','{$data["create_time"]}','{$data["status"]}')";
    }
    /*校验用户*/
    public static function checkUser($username){
      return "select user_id,password,username,create_time,status from store_user where username in ('{$username}')";
    }
    /*登录*/
    public static function loginUser($data){
      return "select user_id,password,create_time,status from store_user where username in ('{$data["username"]}')";
    }
    /*校验店铺*/
    public static function checkStore($store_name){
      return "select store_id,store_name,create_time,status,address,street from store where store_name in ('{$store_name}')";
    }
    /*开通店铺*/
    public static function apply_geo($data){
      return "insert into store_geo(`store_id`, `lng`, `lat`)values('{$data["store_id"]}','{$data["lng"]}','{$data["lat"]}')";
    }
    /*店铺地址经纬度*/
    public static function apply($data){
      return "insert into store(`store_name`,`store_image`, `create_time`, `status`,`address`,`store_id`,`street`,`points`)values('{$data["store_name"]}','{$data["store_image"]}','{$data["create_time"]}','{$data["status"]}','{$data["address"]}','{$data["store_id"]}','{$data["street"]}',GeomFromText('POINT({$data["lng"]} {$data["lat"]})'))";
    }
    /*获取用户店铺*/
    public static function getStoreList($user_id){
      $sql = "select user_store_relation.*,store.* FROM store LEFT JOIN user_store_relation ON store.store_id = user_store_relation.store_id where user_id='{$user_id}'";
      return $sql;
    }
    /*关联店铺与用户*/
    public static function relevancyUserandStroe($data){
      return "insert into user_store_relation(`user_id`, `store_id`, `create_time`,`privileges`,`relation_id`)values('{$data["user_id"]}','{$data["store_id"]}','{$data["create_time"]}','{$data["privileges"]}','{$data["relation_id"]}')";
    }
    /*插入用户默认店铺*/
    public static function insertUserandStroe($data){
      return "insert into user_default_store(`user_id`, `store_id`, `create_time`,`upts_time`)values('{$data["user_id"]}','{$data["store_id"]}','{$data["create_time"]}','{$data["upts_time"]}')";
    }
    /*更新用户默认店铺*/
    public static function updateUserandStroe($data){
      return "update user_default_store set store_id='{$data['store_id']}',upts_time={$data['upts_time']} where user_id='{$data['user_id']}'";
    }
    /*获取用户默认店铺*/
    public static function getUserdefaultStroe($user_id){
      return "select user_default_store.*,user_default_store.store_id as default_store_id,store.* FROM user_default_store RIGHT OUTER JOIN store ON user_default_store.store_id = store.store_id where user_default_store.user_id in ('{$user_id}')";
    }
    /*新增商品分类*/
    public static function createProductcategory($data){
      return "insert into product_category(`cats_name`,`cates_id`, `store_id`, `create_time`,`status`,`upts_time`)values('{$data["cats_name"]}','{$data["cates_id"]}','{$data["store_id"]}','{$data["create_time"]}','{$data["status"]}','{$data["upts_time"]}')";
    }
    /*更新商品分类*/
    public static function updateProductcategory($data){
      return "update product_category set cats_name='{$data['cats_name']}',upts_time='{$data['upts_time']}' where cates_id='{$data['catesgory_id']}'";
    }
    /*更新商品分类*/
    public static function deletecatesgorys($catesgory_id){
      return "update product_category set status=0 where cates_id='{$catesgory_id}'";
    }
    /*获取店铺商品分类*/
    public static function getProductcategory($store_id){
      return "select cates_id as id,cats_name,store_id,create_time,status,upts_time from product_category where store_id in ('{$store_id}') and status in(1)";
    }
    /*新增商品*/
    public static function createProducts($data){
      return "insert into product(`product_id`,`product_name`,`product_desc`, `product_img`, `store_id`, `category_id`, `product_unit`, `attribute_list`, `status`, `update_time`, `create_time`)values('{$data["product_id"]}','{$data["product_name"]}','{$data["product_desc"]}','{$data["product_img"]}','{$data["store_id"]}','{$data["category_id"]}','{$data["product_unit"]}','{$data["attribute_list"]}','{$data["status"]}','{$data["update_time"]}','{$data["create_time"]}')";
    }
    /*更新商品*/
    public static function updateProducts($data){
      return "update product set product_name='{$data['product_name']}',product_desc='{$data['product_desc']}',product_img='{$data['product_img']}',category_id='{$data['category_id']}',product_unit='{$data['product_unit']}',attribute_list='{$data['attribute_list']}',update_time='{$data['update_time']}' where product_id='{$data['product_id']}'";
    }
    /*删除商品sku*/
    public static function delProductsSku($product_id){
      return "delete from product_specs where product_id='{$product_id}'";
    }
    /*删除商品sku_key and sku_value*/
    public static function delProductsSkuKey_val($product_id){
      return "delete product_specs_attr_key,product_specs_attr_values from product_specs_attr_key LEFT JOIN product_specs_attr_values ON product_specs_attr_key.id=product_specs_attr_values.attr_keys_id WHERE product_specs_attr_key.product_id='{$product_id}'";
    }
    /*根据商品id查询商品信息*/
    public static function selectProductIdProducts($product_id){
      return "select * from product where product_id ='{$product_id}'";
    }
    /*查询商品信息*/
    public static function selectProducts($data){
      return "select * from product where store_id ='{$data["store_id"]}' and product_name='{$data["product_name"]}'";
    }
    /*新增sku*/
    public static function createSkuProducts($data){
      $utils = new Utils();
      $insert = "insert into product_specs (`sku_id`,`product_id`,`product_num`,`product_price`,`product_cost_price`,`product_specs`,`product_img`, `upts_time`, `create_time`) values ";
      foreach($data as $value){
        $sku_id = $utils->generateUid();
        $insert .='("'.$sku_id.'","'.$value['product_id'].'",'.$value['product_num'].','.$value['product_price'].',"'.$value['product_cost_price'].'","'.$value['product_specs'].'","'.$value['product_img'].'",'.time().','.time().'),';
      };
      $insert = chop($insert,',');
      return $insert;
    }
    /*新增sku attr-key*/
    public static function createSkuSpecsAttrKey($data){
      $insert = "insert into product_specs_attr_key (`attr_key_name`,`product_id`, `upts_time`, `create_time`) values ";
      foreach($data as $value){
        $insert .='("'.$value['attr_key_name'].'","'.$value['product_id'].'",'.time().','.time().'),';
      };
      $insert = chop($insert,',');
      return $insert;
    }
    /*新增sku attr-key*/
    public static function selectSkuSpecsAttrKey($product_id){
      return "select product_specs_attr_key.*,product_specs_attr_key.id as attr_keys_id from product_specs_attr_key where product_id in('{$product_id}')";
    }
    /*新增sku attr-values*/
    public static function selectSkuSpecsAttrValues($data){
      $insert = "insert into product_specs_attr_values (`attr_values_name`,`attr_keys_id`,`picUrl`, `upts_time`, `create_time`) values ";
      foreach($data as $value){
        $insert .='("'.$value['attr_values_name'].'","'.$value['attr_keys_id'].'","'.$value['picUrl'].'",'.time().','.time().'),';
      };
      $insert = chop($insert,',');
      return $insert;
    }
    /*查询商品列表*/
    public static function getProductsList($data){
      $basesql = "select product.*,product_specs.product_id as sku_product_id,product_specs.product_img as product_sku_img, product_specs.sku_id,product_specs.product_num,product_specs.product_price,product_specs.product_specs FROM product RIGHT OUTER JOIN product_specs ON product.product_id = product_specs.product_id";
      $sql = "{$basesql} where store_id='{$data['store_id']}' and status in(0,1)  limit {$data['currentPage']}, {$data['pageSize']}";
      if (!empty($data['catesgory_id'])) {
        $sql = "{$basesql} where store_id='{$data['store_id']}' and status in(0,1) and category_id='{$data['catesgory_id']}' limit {$data['currentPage']}, {$data['pageSize']}";
      } else if (!empty($data['product_name'])) {
        $sql = "{$basesql} where store_id='{$data['store_id']}' and status in(0,1) and product_name like '%{$data['product_name']}%'  limit {$data['currentPage']}, {$data['pageSize']}";
      } else if (!empty($data['catesgory_id']) && !empty($data['product_name'])){
        $sql = "{$basesql} where store_id='{$data['store_id']}' and status in(0,1) and category_id='{$data['catesgory_id']}' and product_name like '%{$data['product_name']}%' limit {$data['currentPage']}, {$data['pageSize']}";
      }
      return $sql;
    }
    /*查询商品详情*/
    public static function getProductsDetail($product_id){
      $basesql = "select product.*,product_specs.product_id as sku_product_id,product_specs.product_img as product_sku_img, product_specs.sku_id,product_specs.product_cost_price as cost_price,product_specs.product_num,product_specs.product_price,product_specs.product_specs FROM product RIGHT OUTER JOIN product_specs ON product.product_id = product_specs.product_id";
      $sql = "{$basesql} where product.product_id='{$product_id}'";
      return $sql;
    }
    /*查询商品详情skumap*/
    public static function getProductsDetailSkuSpec($product_id){
      $basesql = "select product_specs_attr_key.*, product_specs_attr_key.id as attr_k_id, product_specs_attr_values.*,product_specs_attr_values.id as attr_v_id FROM product_specs_attr_key RIGHT OUTER JOIN product_specs_attr_values ON product_specs_attr_key.id = product_specs_attr_values.attr_keys_id";
      $sql = "{$basesql} where product_specs_attr_key.product_id='{$product_id}'";
      return $sql;
    }
    /*商品上下架*/
    public static function updateProductstatus($status,$product_ids){
      if (count($product_ids) == 1) {
        $product_id = reset($product_ids);
        $sql = "update product set status={$status} where product_id= '{$product_id}'";
      } else {
        $product_ids = implode("','",$product_ids);
        $sql = "update product set status={$status} where product_id in('{$product_ids}')";
      }
      return $sql;
    }
    /*新增店铺设置*/
    public static function storeSetting($data){
      return "replace into store_setting (`setting_id`,`store_id`, `delivery_price`, `start_delivery_price`,`business_start_times`,`business_end_times`,`business_status`,`create_time`)values('{$data["setting_id"]}','{$data["store_id"]}','{$data["delivery_price"]}','{$data["start_delivery_price"]}','{$data["business_start_times"]}','{$data["business_end_times"]}',{$data["business_status"]},'{$data["create_time"]}')";
    }
    /*获取店铺设置信息*/
    public static function getStoreSetting($store_id){
      return "select store_setting.setting_id,store_setting.business_end_times,store_setting.delivery_price,store_setting.delivery_price,store_setting.start_delivery_price,store_setting.business_start_times,store_setting.business_end_times,store_setting.business_status from store_setting where store_id in('{$store_id}')";
    }
    // /*----移动端---*/
    /*获取附近的店铺*/
    public static function getUserNearStoreList($storeName,$lng,$lat,$radius,$scope,$offset=0,$pagesize=10) {
      if (empty($storeName)) {
        return "select store.*,store.store_id as _id,store_setting.* from store LEFT JOIN store_setting ON store.store_id = store_setting.store_id where  MBRContains(LineString(Point({$lat} + {$radius} / ( 111.1 / COS(RADIANS({$lng}))),{$lng} + {$radius} / 111.1),Point({$lat} - {$radius} / ( 111.1 / COS(RADIANS({$lat}))), {$lng} - {$radius} / 111.1 )), points) and status in(1) limit {$offset},{$pagesize}";
      }
      return "select store.*,store.store_id as _id,store_setting.* from store LEFT JOIN store_setting ON store.store_id = store_setting.store_id where  MBRContains(LineString(Point({$lat} + {$radius} / ( 111.1 / COS(RADIANS({$lng}))),{$lng} + {$radius} / 111.1),Point({$lat} - {$radius} / ( 111.1 / COS(RADIANS({$lat}))), {$lng} - {$radius} / 111.1 )), points) and status in(1) and POSITION('{$storeName}' IN `store_name`) limit {$offset},{$pagesize}";
    }
    /*获取店铺商品*/
    public static function getUserStoreTradingsList($store_id,$category_id) {
        $basesql = "select product.*,product_specs.product_id as sku_product_id,product_specs.product_img as product_sku_img, product_specs.sku_id,product_specs.product_num,product_specs.product_price,product_specs.product_specs FROM product LEFT OUTER JOIN product_specs ON product.product_id = product_specs.product_id";
        $sql = isset($category_id) && !empty($category_id) ?   "{$basesql} where product.status in(1) and product.store_id='{$store_id}' and product.category_id='{$category_id}'" : "{$basesql} where product.status in(1) and product.store_id='{$store_id}'";
      return $sql;
    }
    /*获取店铺信息*/
    public static function getStoreInfo($store_id) {
        return "select store.store_id as _id,store.store_image,store.store_name,store_setting.start_delivery_price,store_setting.business_start_times,store_setting.business_start_times,business_end_times,store_setting.delivery_price,store_setting.business_status FROM store LEFT OUTER JOIN store_setting ON store.store_id = store_setting.store_id where store.store_id in('{$store_id}')";
    }
    /*用户登录*/
    public static function addUser($data){
      return "replace into user (`user_id`,`username`, `avatar`, `open_id`,`create_time`,`status`)values('{$data["user_id"]}','{$data["username"]}','{$data["avatar"]}','{$data["open_id"]}','{$data["create_time"]}','{$data["status"]}')";
    }
    /*获取用户信息*/
    public static function getUserInfo($open_id){
      return "select user.user_id as _id,user.username,user.avatar,user.status,user.create_time FROM user where open_id='{$open_id}'";
    }
  }
