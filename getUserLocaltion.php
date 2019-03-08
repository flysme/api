<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './config/sql.php';
  class Get_userLocaltion {
    public $utils;
    public $lat;
    public $lng;
    function __construct($lat,$lng){
       include_once './utils/utils.php';
       $this -> utils = new Utils();
       $this -> lat = $lat;
       $this -> lng = $lng;
       $this -> cursor = $cursor;
    }
    public function getUserLocationInfo () {
      $mapUrl = "https://apis.map.qq.com/ws/geocoder/v1/?location=".$this->lat."," .$this->lng."&key=F2YBZ-OCV3W-LETR6-R4VGT-4DPVS-SOFBG&get_poi=1";
      $result = file_get_contents($mapUrl);
      $locationInfo = json_decode($result,true);
      return array(
        'data' => array(
          'address'=>$locationInfo['result']['address'],
          'address_component'=>$locationInfo['result']['address_component'],
          'pois'=>$locationInfo['result']['pois'],
          'location'=>$locationInfo['result']['location'],
        ),
        'status' => $locationInfo['status'],
        'msg' => $locationInfo['message']
      );
    }
  }
    $lat=$_GET['lat'];
    $lng=$_GET['lng'];
    $local = new Get_userLocaltion($lat,$lng);
    $res = $local->getUserLocationInfo();
    echo json_encode($res);
