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
  }
