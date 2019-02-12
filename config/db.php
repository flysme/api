<?php
     class DB {
         public $links;//链接名称
         //构造方法的参数和属性名字一致，但是含义不同
         public function connect(){
             $db_host = "hdm131470340.my3w.com";//localhost
             $db_user = "hdm131470340";//用户名
             $db_pwd = "zfxyyc0822,,";//密码
             $db_name = "hdm131470340_db";//数据库名
             //链接数据库代码
             $this->links = new mysqli($db_host,$db_user,$db_pwd,$db_name);
             !mysqli_connect_error() or die("连接失败！！");
             $this->links->query("SET NAMES 'UTF8'");
             //echo $this -> links;打印是资源
         }
         public function query($sql){    //执行各种sql，inert update delete执行，如果执行select返回结果集
             return $this->links->query($sql);
         }
         public function getData($sql){//select的记录数据
             $result = $this->links->query($sql);
             $arr = mysqli_fetch_assoc($result);
             return $arr;
         }
         public function getNums ($sql) {
             $result = $this->links->query($sql);
             $nums = mysqli_num_rows($result);
             return $nums;
         }
         public function getAll($sql){//得到多条记录的二维数组
             $result = $this->links->query($sql);
             $rows = array();
             if (mysqli_num_rows($result) > 0) {
               while($rs = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                   $rows[] = $rs;
               }
             }
             return $rows;
         }
     }
