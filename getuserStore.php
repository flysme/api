<?php
include_once './config/common.php';
include_once './config/sql.php';
include_once './utils/utils.php';
include_once './utils/oauth.php';
include_once './service/session/session.php';

  class GetuserInfo {
    public $utils;
    public $DB;
    function __construct(){
       include_once './config/db.php';
       $this -> DB = new DB();
       $this-> utils = new Utils();
    }
    public function getstorestatus ($user_id) {
      $this->DB->connect();//连接数据库
      $getStoresql = Sql::getUserdefaultStroe($user_id);
      $resultstore = $this->DB->getData($getStoresql);
      if (is_array($resultstore)) {  // 存在店铺信息
          $resstore = array(
            'store_id' =>$resultstore['store_id'],
            'name' =>$resultstore['store_name'],
            'img' =>$resultstore['store_image'],
            'create_time' =>$resultstore['create_time'],
            'address' =>$resultstore['address'],
            'street' =>$resultstore['street'],
            'status' =>$resultstore['status'],
          );
           $res = (object)array('data' => (object)array('store'=>$resstore),'msg'=>'', 'status'=>0);
      } else {
        $res = (object)array('data' => (object)array(),'msg'=>$DB->links->error, 'status'=>400);
      }
      $this->DB->links->close();
      return $res;
    }

  }
  /*验证登录*/
  Oauth::checkLogin();
  $user_id = Session::get('uid'); //获取保存的user_id
  $Getsstore = new GetuserInfo();
 $res = $Getsstore->getstorestatus($user_id);
 echo json_encode($res);
