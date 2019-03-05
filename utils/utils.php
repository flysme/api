<?php
  class Utils {
    /*封装获取put参数*/
    public function getParams(){
      $_Array = array();
      parse_str(file_get_contents('php://input'), $_Array);
      return $_Array;
    }
    //获取唯一序列号
    public function generateUid() {
         //strtoupper转换成全大写的
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid = substr($charid, 0, 8).substr($charid, 8, 4).substr($charid,12, 4).substr($charid,16, 4).substr($charid,20,12);
      return $uuid;
    }
    /*判断put请求*/
    public function isPut(){
      return $_SERVER['REQUEST_METHOD'] == 'PUT' ? true : false;
    }
    /*判断post请求*/
    public function isPost(){
      return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
    }
    /*判断get请求*/
    public function isGet(){
      return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }
    /*判断delete请求*/
    public function isDelete(){
      return $_SERVER['REQUEST_METHOD'] == "DELETE" ? true : false;
    }
    /**
     * 获取两个经纬度之间的距离
     * @param  string $lat1 纬一
     * @param  String $lng1 经一
     * @param  String $lat2 纬二
     * @param  String $lng2 经二
     * @return float  返回两点之间的距离
     */
    // public function calcDistance($lat1, $lng1, $lat2, $lng2) {
    //     /** 转换数据类型为 double */
    //     $lat1 = doubleval($lat1);
    //     $lng1 = doubleval($lng1);
    //     $lat2 = doubleval($lat2);
    //     $lng2 = doubleval($lng2);
    //     /** 以下算法是 Google 出来的，与大多数经纬度计算工具结果一致 */
    //     $theta = $lng1 - $lng2;
    //     $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    //     $dist = acos($dist);
    //     $dist = rad2deg($dist);
    //     $miles = $dist * 60 * 1.1515;
    //     return ($miles * 1.609344);
    // }
    /**
     * 根据经纬度和半径计算出范围
     * @param string $lat 纬度
     * @param String $lng 经度
     * @param float $radius 半径
     * @return Array 范围数组
     */
    public function calcScope($lat, $lng, $radius) {
        $degree = (24901*1609)/360.0;
        $dpmLat = 1/$degree;

        $radiusLat = $dpmLat*$radius;
        $minLat = $lat - $radiusLat;       // 最小纬度
        $maxLat = $lat + $radiusLat;       // 最大纬度

        $mpdLng = $degree*cos($lat * (PI/180));
        $dpmLng = 1 / $mpdLng;
        $radiusLng = $dpmLng*$radius;
        $minLng = $lng - $radiusLng;      // 最小经度
        $maxLng = $lng + $radiusLng;      // 最大经度

        /** 返回范围数组 */
        $scope = array(
            'minLat'    =>  $minLat,
            'maxLat'    =>  $maxLat,
            'minLng'    =>  $minLng,
            'maxLng'    =>  $maxLng,
            );
        return $scope;
    }
  }
