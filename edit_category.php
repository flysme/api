<?php
include_once './config/common.php';
include_once './config/sql.php';
include_once './utils/utils.php';
include_once './utils/oauth.php';
  class Create_categorys {
    public $DB;
    function __construct(){
       include_once './config/db.php';
       $this -> DB = new DB();
       $this->DB->connect();//连接数据库
    }
    public function updatecatesgorys ($catesgory_id,$cats_name) {
      $data = array(
        'catesgory_id'=>$catesgory_id,
        'cats_name'=>$cats_name,
        'upts_time'=>time()
      );
      $upcategorysql = Sql::updateProductcategory($data);
      echo $upcategorysql;
      exit();
      $result = $this->DB->query($upcategorysql);
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
  $categorys = new Create_categorys();
  $data = $utils->getParams();
  /*更新分类*/
  parse_str($_SERVER['QUERY_STRING']);
  if($utils->isPut()) {
    if(empty($catesgory_id)) {
        $ischeck = array('data' => (object)array(),'msg'=>'分类id未知', 'status'=>400);
    } else if(empty($data['cats_name'])) {
        $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'请填写分类名称', 'status'=>400);
    }
    $res = $categorys->updatecatesgorys($data['catesgory_id'],$data['cats_name']);
  }

  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    echo json_encode($res);
  }
