<?php
  class Sql {
    /*注册*/
    public function setUser($data){
      return "insert into store_user(`username`, `password`, `create_time`,`status`)values('{$data["username"]}','{$data["password"]}','{$data["create_time"]}','{$data["status"]}')";
    }
    /*校验用户*/
    public function checkUser($username){
      return "select * from store_user where username in ('{$username}')";
    }
    /*更新用户店铺信息*/
    public function updateUserstoreInfo($store_id,$user_id){
      return "update store_user set store_id= concat(store_id,'{$store_id} ') where admin_id= '{$user_id}'";
    }
    /*登录*/
    public function loginUser($data){
      return "select * from store_user where username in ('{$data["username"]}')";
    }
    /*校验店铺*/
    public function checkStore($store_name){
      return "select * from store where store_name in ('{$store_name}')";
    }
    /*开通店铺*/
    public function apply($data){
      return "insert into store(`store_name`, `create_time`, `status`,`address`)values('{$data["store_name"]}','{$data["create_time"]}','{$data["status"]}','{$data["address"]}')";
    }
  }
