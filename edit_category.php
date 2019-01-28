<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/utils.php';
include_once './utils/oauth.php';
  class Create_categorys {
    public $DB;
    public function mysql () {
      $this->DB = new DB();
      $this->DB->connect();//连接数据库
    }
    public function set ($cats_name,$store_id) {
      $this->mysql();
      $utils = new Utils();
      /*
       status: -1 未开通 0 已开通 1 已注销
      */
      $data = array(
        'cats_name' => $cats_name,
        'cates_id' => $utils->generateUid(),
        'store_id' => $store_id,
        'create_time' => time(),
        'upts_time' => time(),
        'status' => 1
       );
      $categorysql = Sql::createProductcategory($data);
      $result = $this->DB->query($categorysql);
      if ($result) {
          $res = (object)array('data' => (object)array(),'msg'=>'', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array(),'msg'=>$this->DB->links->error, 'status'=>403);
      }
      $this->DB->links->close();
      return $res;
    }
    public function updatecatesgorys ($catesgory_id,$cats_name) {
      $this->mysql();
      $data = array(
        'catesgory_id'=>$catesgory_id,
        'cats_name'=>$cats_name,
        'upts_time'=>time()
      );
      $upcategorysql = Sql::updateProductcategory($data);
      $result = $this->DB->query($upcategorysql);
      if ($result) {
          $res = (object)array('data' => (object)array(),'msg'=>'', 'status'=>0);
      } else {
        $res = (object) array('data' => (object)array(),'msg'=>$this->DB->links->error, 'status'=>400);
      }
      $this->DB->links->close();
      return $res;
    }
    public function deletecatesgorys ($catesgory_id) {
      $this->mysql();
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
  $categorys = new Create_categorys();
  $data = $utils->getParams();
  /*更新分类*/
  if($utils->isPut()) {
    if(empty($data['catesgory_id'])) {
        $ischeck = array('data' => (object)array(),'msg'=>'分类id未知', 'status'=>400);
    } else if(empty($data['cats_name'])) {
        $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'请填写分类名称', 'status'=>400);
    }
    $res = $categorys->updatecatesgorys($data['catesgory_id'],$data['cats_name']);
  } elseif ($utils->isDelete()) { /*删除分类*/
    if(empty($data['catesgory_id'])) {
        $ischeck = array('data' => (object)array(),'msg'=>'分类id未知', 'status'=>400);
    }
    $res = $categorys->deletecatesgorys($data['catesgory_id']);
  }else {/*新增分类*/
    $cats_name=trim($_POST['cats_name']);
    $store_id=trim($_POST['store_id']);
    if(empty($cats_name)) {
        $ischeck = array('data' => (object)array(),'msg'=>'请填写分类名称', 'status'=>400);
    } else if(empty($store_id)) {
        $ischeck = $ischeck = array('data' => (object)array(),'msg'=>'店铺id未知', 'status'=>400);
    }
    $res = $categorys->set($cats_name,$store_id);
  }

  if (is_array($ischeck)) {
    echo json_encode($ischeck);
  } else {
    echo json_encode($res);
  }
