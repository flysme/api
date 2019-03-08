<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './config/sql.php';
  class Get_storeTradings {
    public $utils;
    public $store_id;
    function __construct($store_id,$category_id){
       include_once './utils/utils.php';
       $this -> utils = new Utils();
       $this -> store_id = $store_id;
       $this -> category_id = $category_id;
    }
    public function resetTradings ($tradings) {
        $result = array();
          foreach($tradings as $item){
            $sku = array(
              '_id' => $item['sku_id'],
              'price' => (float)$item['product_price'],
              'num' => (int)$item['product_num'],
              'specs' => $item['product_specs'],
            );
            if (is_array($result[$item['id']])) {
              array_push($result[$item['id']]['sku'],$sku);
            } else {
              $result[$item['id']]=array(
                '_id' => $item['product_id'],
                'title' => $item['product_name'],
                'img' => $item['product_img'],
                'attribute_list' => unserialize($item['attribute_list']),
                'unit' => $item['product_unit'],
                'category_id' => $item['category_id'],
                'create_time' => (int)$item['create_time'],
                'sku' => array($sku),
              );
            }
         };
      return array_values($result);
    }
    public function getStoreTradings () {
      $productsql = Sql::getUserStoreTradingsList($this->store_id,$this->category_id);
      $DB = new DB();
      $DB->connect();//连接数据库
      $result = $this->resetTradings($DB->getAll($productsql));
      if (!empty($result)) {
        $res = (object)array('data' => (object)array('tradings'=>$result),'msg'=>'', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array('tradings'=>array()),'msg'=>'暂无商品', 'status'=>0);
      }
      $DB->links->close();
      return $res;
    }
  }
    $store_id=$_GET['_id'];
    $category_id=isset($_GET['category_id']) ? trim($_GET['category_id']) :'';
    $stores = new Get_storeTradings($store_id,$category_id);
    $res = $stores->getStoreTradings();
    echo json_encode($res);
