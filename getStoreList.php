<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './config/sql.php';
  class Get_storeList {
    public $utils;
    function __construct(){
       include_once './utils/utils.php';
       $this -> utils = new Utils();
    }
    public function getStoreList ($lat,$lng) {
      $radius = 5000;
      $scope = $this -> utils->calcScope($lat, $lng, $radius);
      $storesql = Sql::getUserNearStoreList($lat,$lng,$scope);
      $DB = new DB();
      $DB->connect();//连接数据库
      $result = $DB->getAll($storesql);
      if (!empty($result)) {
        $res = (object)array('data' => (object)array('store'=>$result),'msg'=>'', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array('store'=>array()),'msg'=>'附近暂无商店', 'status'=>0);
      }
      $DB->links->close();
      return $res;
    }
  }
    $lat=$_GET['lat'];
    $lng=$_GET['lng'];

    $stores = new Get_storeList();
    $res = $stores->getStoreList($lat,$lng);
    echo json_encode($res);
