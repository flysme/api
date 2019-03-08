<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './config/sql.php';
  class Get_storeList {
    public $utils;
    public $lat;
    public $lng;
    public $cursor;
    function __construct($lat,$lng,$cursor){
       include_once './utils/utils.php';
       $this -> utils = new Utils();
       $this -> lat = $lat;
       $this -> lng = $lng;
       $this -> cursor = $cursor;
    }
    public function computeDeuceDistance ($result) {
      $resetdata = array();
      foreach ($result as $value) {
        $item = array(
          '_id' => $value['_id'],
          'name' => $value['store_name'],
          'img' => $value['store_image'],
          'street' => $value['street'],
          'lat' => $value['lat'],
          'lng' => $value['lng'],
          'setting' => array(
            'business_start_times' => $value['business_start_times'],
            'business_end_times' => $value['business_end_times'],
            'delivery_price' => intval($value['delivery_price']),
            'start_delivery_price' => intval($value['start_delivery_price']),
            'distance' =>$this -> utils-> getDistance( $value['lng'],$value['lat'],  $this -> lng,$this -> lat,2,1),
            'discounts' => unserialize($value['discounts']),
            'is_business' => $this -> utils->checkIsBetweenTime($value['business_start_times'],$value['business_end_times']) //判断是否营业
          ),
         );
         $resetdata[] = array_merge($item);
      }
      return $resetdata;
    }
    public function getStoreList () {
      $radius = 10; //单位公里
      $pageSize = 10;
      $offset =  ($this -> cursor -1) * $pageSize; //单位公里
      $scope = $this -> utils->returnSquarePoint($this -> lat, $this -> lng, $radius);
      $storesql = Sql::getUserNearStoreList($this -> lat,$this -> lng,$scope,$offset,$pageSize);
      $DB = new DB();
      $DB->connect();//连接数据库
      $result = $DB->getAll($storesql);
      if (!empty($result)) {
        $computeresult = $this->computeDeuceDistance($result);
        $res = (object)array('data' => (object)array('store'=>$computeresult),'msg'=>'', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array('store'=>array()),'msg'=>'附近暂无商店', 'status'=>0);
      }
      $DB->links->close();
      return $res;
    }
  }
    $lat=$_GET['lat'];
    $lng=$_GET['lng'];
    $cursor= empty($_GET['cursor']) ?  1 : $_GET['cursor'];
    $stores = new Get_storeList($lat,$lng,$cursor);
    $res = $stores->getStoreList();
    echo json_encode($res);
