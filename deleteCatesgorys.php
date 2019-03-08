<?php
include_once './config/common.php';
include_once './config/sql.php';
include_once './utils/utils.php';
include_once './utils/oauth.php';
  class Dlete_categorys {
    public $DB;
    function __construct(){
       include_once './config/db.php';
       $this -> DB = new DB();
       $this->DB->connect();//连接数据库
    }
    public function deletecatesgorys ($catesgory_id) {
      $delecategorysql = Sql::deletecatesgorys($catesgory_id);
      $result = $this->DB->query($delecategorysql);
      if ($result) {
          $res = (object)array('data' => (object)array(),'msg'=>'', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array(),'msg'=>$this->DB->links->error, 'status'=>400);
      }
      $this->DB->links->close();
      return $res;
    }
  }
  /*验证登录*/
  Oauth::checkLogin();
  $utils = new Utils();
  $ischeck = true;
  $categorys = new Dlete_categorys();
  $data = $utils->getParams();
  if(empty($data['catesgory_id'])) {
      $ischeck = array('data' => (object)array(),'msg'=>'分类id未知', 'status'=>400);
  }

  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    $res = $categorys->deletecatesgorys($data['catesgory_id']);
    echo json_encode($res);
  }
