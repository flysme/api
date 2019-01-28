<?php
     class DB {
         public $links;//链接名称
         //构造方法的参数和属性名字一致，但是含义不同
         public function connect(){
             $db_host = "********";//localhost
             $db_user = "********";//用户名
             $db_pwd = "********";//密码
             $db_name = "********";//数据库名
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
             $result = self::query($sql);
             $arr = mysqli_fetch_assoc($result);
             return $arr;
         }
         public function getNums ($sql) {
             $result = self::query($sql);
             $nums = mysqli_num_rows($result);
             return $nums;
         }
         public function getAll($sql){//得到多条记录的二维数组
             $result = self::query($sql);
             $rows = array();
             while($rs = mysqli_fetch_array($result)){
                 $rows[] = $rs;
             }
             return $rows;
         }
     }
