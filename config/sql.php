<?php

  class Sql {
    public function checkUser($username){
      return "select * from store_user where username in ('{$username}')";
    }
    public function setUser($data){
      return "insert into store_user(`username`, `password`, `create_time`,`status`,`store_id`)values('{$data["username"]}','{$data["password"]}','{$data["create_time"]}','{$data["status"]}','{$data["store_id"]}')";
    }
    public function loginUser($data){
      return "select username, password from store_user where username in ('{$data["username"]}') and password in ('{$data["password"]}')";
    }
  }
?>
