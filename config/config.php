<?php
     class DB {
         private $db_host;//localhost
         private $db_user;//用户名
         private $db_pwd;//密码
         private $db_name;//数据库名
         public $links;//链接名称
         //构造方法的参数和属性名字一致，但是含义不同
         function __construct(){
             $this -> db_host = "hdm131470340.my3w.com";
             $this -> db_user = "hdm131470340";
             $this -> db_pwd = "zfxyyc0822,,";
             $this -> db_name = "hdm131470340_db";
             //链接数据库代码
             $this -> links = new mysqli($this -> db_host,$this -> db_user,$this -> db_pwd,$this -> db_name);
             !mysqli_connect_error() or die("连接失败！！");
             $this -> links->query("SET NAMES 'UTF8'");
             //echo $this -> links;打印是资源
         }
         function query($sql){    //执行各种sql，inert update delete执行，如果执行select返回结果集
             return $this -> links->query($sql);
         }
         function getData($sql){//select的记录数据
             $result = $this -> query($sql);
             $arr = mysqli_fetch_assoc($result);
             return $arr;
         }
         function getNums ($sql) {
           $result = $this -> query($sql);
           $nums = mysqli_num_rows($result);
           return $nums;
         }
         function getAll($sql){//得到多条记录的二维数组
             $result = $this -> query($sql);
             $rows = array();
             while($rs = mysqli_fetch_array($result)){
                 $rows[] = $rs;
             }
             return $rows;
         }
         function __destruct(){
             $this -> db_host = db_host;
             $this -> db_user = db_user;
             $this -> db_pwd = db_pwd;
             $this -> db_name = db_name;
         }
     }

     // $db = new DB("hdm131470340.my3w.com","hdm131470340","zfxyyc0822,,","hdm131470340_db");
     // $sql = "insert into category(categoryName)values('常熟seo')";
     // $db -> query($sql);
     // return $db
     //返回select的记录数
     // $sql = "select * from category";
     // $count = $db -> numRows($sql);
     // echo $count;

     //得到一条记录的一维数组
     // $sql = "select * from category where categoryId=1";
     // $arr = $db -> getOne($sql);
     // print_r($arr);

     //得到多条记录的二维数组
     // $sql = "select * from category";
     // $rs = $db -> getAll($sql);
     // print_r($rs);
