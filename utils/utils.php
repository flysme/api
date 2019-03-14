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
    //获取随机数
    function GetRandStr($length){
      $str='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      $len=strlen($str)-1;
      $randstr='';
      for($i=0;$i<$length;$i++){
        $num=mt_rand(0,$len);
        $randstr .= $str[$num];
      }
      return $randstr;
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
     *计算某个经纬度的周围某段距离的正方形的四个点
     *
     *@param lng float 经度
     *@param lat float 纬度
     *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为 2千米
     *@return array 正方形的四个点的经纬度坐标
     */
     function returnSquarePoint($lat,$lng,$distance = 2){
       define(EARTH_RADIUS, 6371);//地球半径，平均半径为6371km
        $dlng =  2 * asin(sin($distance / (2 * EARTH_RADIUS)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);

        $dlat = $distance/EARTH_RADIUS;
        $dlat = rad2deg($dlat);

        return array(
          'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
          'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
          'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
          'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
        );
     }
     /*
       * 1.，经度1,纬度1，经度2,纬度2
       * 2.返回结果是单位是KM。
       * 3.保留一位小数
       */
      public function Distance($lng1,$lat1,$lng2,$lat2)
      {
      	//将角度转为狐度
      	$radLat1 = deg2rad($lat1);//deg2rad()函数将角度转换为弧度
      	$radLat2 = deg2rad($lat2);
      	$radLng1 = deg2rad($lng1);
      	$radLng2 = deg2rad($lng2);
      	$a = $radLat1 - $radLat2;
      	$b = $radLng1 - $radLng2;
      	$s = 2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6371;
      	return round($s,1);
      }
      /**
       * 计算两点地理坐标之间的距离
       * @param  Decimal $longitude1 起点经度
       * @param  Decimal $latitude1  起点纬度
       * @param  Decimal $longitude2 终点经度
       * @param  Decimal $latitude2  终点纬度
       * @param  Int     $unit       单位 1:米 2:公里
       * @param  Int     $decimal    精度 保留小数位数
       * @return Decimal
       */
      public function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit=2, $decimal=2){

          $EARTH_RADIUS = 6370.996; // 地球半径系数
          $PI = 3.1415926;

          $radLat1 = $latitude1 * $PI / 180.0;
          $radLat2 = $latitude2 * $PI / 180.0;

          $radLng1 = $longitude1 * $PI / 180.0;
          $radLng2 = $longitude2 * $PI /180.0;

          $a = $radLat1 - $radLat2;
          $b = $radLng1 - $radLng2;

          $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
          $distance = $distance * $EARTH_RADIUS * 1000;

          if($unit==2){
              $distance = $distance / 1000;
          }

          return round($distance, $decimal);

      }
    /**
     * 获取两个经纬度之间的距离
     * @param  string $lat1 纬一
     * @param  String $lng1 经一
     * @param  String $lat2 纬二
     * @param  String $lng2 经二
     * @return float  返回两点之间的距离
     */
    public function calcDistance($lat1, $lng1, $lat2, $lng2) {
        /** 转换数据类型为 double */
        $lat1 = doubleval($lat1);
        $lng1 = doubleval($lng1);
        $lat2 = doubleval($lat2);
        $lng2 = doubleval($lng2);
        /** 以下算法是 Google 出来的，与大多数经纬度计算工具结果一致 */
        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344);
    }
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

      /**
       * 判断当前的时分是否在指定的时间段内
       * @param $start 开始时分  eg:10:30
       * @param $end  结束时分   eg:15:30
       * @author:mzc
       * @date:2018/8/9 10:46
       * @return: bool  1：在范围内，0:没在范围内
       */
      public function checkIsBetweenTime($start,$end){
          $date= date('H:i');
          $curTime = strtotime($date);//当前时分
          $assignTime1 = strtotime($start);//获得指定分钟时间戳，00:00
          $assignTime2 = strtotime($end);//获得指定分钟时间戳，01:00
          $result = 0;
          if($curTime>$assignTime1&&$curTime<$assignTime2){
              $result = 1;
          }
          return $result;
      }
      public function objectToarray($object) {
        if (is_object($object)) {
          foreach ($object as $key => $value) {
            $array[$key] = $value;
          }
        }
        else {
          $array = $object;
        }
        return $array;
      }

  }
