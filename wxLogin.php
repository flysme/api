<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/oauth.php';
include_once './service/session/session.php';


class Wxlogin {
  private $appId = 'wx6e219488e53a4991';
  private $DB;
  private $Utils;
  private $appSecret = '6c6c994f9621881075cb910b14ff2848';
  private $encryptedata;
  private $code = '';
  private $code2SessionUrl = '';
  public function __construct($code,$iv,$encryptedata,$signature){
     include_once './config/db.php';
     include_once './utils/utils.php';
     $this -> DB = new DB();
     $this -> Utils = new Utils();
     $this-> code = $code;
     $this-> iv = $iv;
     $this-> signature = $signature;
     $this-> encryptedata = $encryptedata;
     $this-> code2SessionUrl = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->appId}&secret={$this->appSecret}&js_code={$this->code}&grant_type=authorization_code";
  }
  public function getSessionKey () {
    $result = file_get_contents($this-> code2SessionUrl);
    $codeInfo = $this ->Utils->objectToarray(json_decode($result));
    return array('errcode'=>$codeInfo['errcode'],'data'=>$codeInfo,'errmsg'=>$codeInfo['errmsg']);
  }
  public function Users ($session_key) {
    include_once "./utils/wx/wxBizDataCrypt.php";
    $Wx = new WXBizDataCrypt($this->appId, $session_key);
    $errCode = $Wx->decryptData($this->Utils->define_str_replace($this->encryptedata), $this->Utils->define_str_replace($this->iv), $data );
    return array('errcode'=>$errCode,'data'=>json_decode($data),'errmsg'=> $this -> Utils->define_str_replace($session_key));
  }
  public function Login () {
    $userSessionData = $this->getSessionKey();
    $session_key = $userSessionData['data']['session_key'];
    $session_id = $_SERVER['HTTP_SESSION_ID'];
    $session = !empty($session_id) ? Session::get($session_id) :null;
    if (!empty($session)) {
      return array('error_code' => 0,'sessionid' => $session_id,'msg' => '');
    } else {
      $msg = $this->Users($session_key); //获取微信用户信息（openid）
      if ($msg['errcode'] == 0) {
        $msgData = $this ->Utils->objectToarray($msg['data']);
        $open_id = $msgData['openId']; //open_id;
        $username = $msgData['nickName']; //nickName;
        $avatar= $msgData['avatarUrl']; //avatarUrl;
        exit();
        $info=$this->getUserInfo($open_id);
        if(!$info || empty($info)){
          $user = $this->Users();
          var_dump($user);
          exit();
          $isAdd = $this->addUser($open_id,$username,$avatar); //用户信息入库
          if (!empty($isAdd)) {
            $info=$this->getUserInfo($open_id);                  //获取用户信息
            if (!empty($info)) {
              $session_id=`head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 168`;  //生成3rd_session
              Session::set($session_id, array('open_id'=>$openid,'session_key'=>$_sessionKey), 8800); //设置session
              return array('error_code' => 0,'sessionid' => $session_id,'msg' => '');
            }
          } else {
            return array('error_code' => 401,'msg' => '用户登录失败');
          }
        }
        if($session_id){
          $this->ajaxReturn(['error_code'=>0,'sessionid'=>$session_id]);  //把3rd_session返回给客户端
        }else{
          $this->ajaxReturn(['error_code'=>0,'sessionid'=>$session_db->getSid($info['id'])]);
        }
      } else {
        return array('error_code' => $msg['errcode'],'msg' => $msg['errmsg']);
      }
    }
  }
  // 添加用户入库
  public function addUser ($open_id,$username,$avatar) {
    $queryData = array(
      'user_id' => $open_id,
      'username' => $username,
      'avatar' => $avatar,
      'open_id' => $open_id,
      'create_time' => time(),
      'status' => 1
    );
    $data=$this->DB->query(Sql::addUser($queryData));
    if (!empty($data)) {
      return $data;
    }
  }
  public function getUserInfo ($open_id) {
    $data = $this->DB->getData(Sql::getUserInfo($open_id));
    return $data;
  }
  // 微信登录
  // public function weixin_login(){
  //
  //   $session_db=D('Session');
  //
  //   $session_id=I('get.sessionid','');
  //
  //   $session=$session_db->getSession($session_id);
  //
  //   if( !empty( $session ) ){
  //
  //     $this->ajaxReturn(['error_code'=>0,'sessionid'=>$session_id]);
  //
  //   }else{
  //
  //     $iv=define_str_replace(I('get.iv')); //把空格转成+
  //
  //     $encryptedData=urldecode(I('get.encryptedData'));  //解码
  //
  //     $code=define_str_replace(I('get.code')); //把空格转成+
  //     $msg=D('Weixin')->getUserInfo($code,$encryptedData,$iv); //获取微信用户信息（openid）
  //     if($msg['errCode']==0){
  //       $open_id=$msg['data']->openId;
  //
  //       $users_db=D('Users');
  //
  //       $info=$users_db->getUserInfo($open_id);
  //
  //       if($info||empty($info)){
  //
  //         $users_db->addUser(['open_id'=>$open_id,'last_time'=>['exp','now()']]); //用户信息入库
  //
  //         $info=$users_db->getUserInfo($open_id);                  //获取用户信息
  //
  //         $session_id=`head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 168`;  //生成3rd_session
  //
  //         $session_db->addSession(['uid'=>$info['id'],'id'=>$session_id]); //保存session
  //
  //       }
  //       if($session_id){
  //         $this->ajaxReturn(['error_code'=>0,'sessionid'=>$session_id]);  //把3rd_session返回给客户端
  //       }else{
  //         $this->ajaxReturn(['error_code'=>0,'sessionid'=>$session_db->getSid($info['id'])]);
  //       }
  //     }else{
  //       $this->ajaxReturn(['error_code'=>'用户信息获取失败！']);
  //     }
  //
  //   }
  // }
}

$code = $_SERVER['HTTP_X_WX_CODE'];
$iv = $_SERVER['HTTP_X_WX_IV'];
$encryptedData = $_SERVER['HTTP_X_WX_ENCRYPTEDATA'];
$signature = $_SERVER['HTTP_X_WX_SIGNATURE'];
$Login = new Wxlogin($code,$iv,$encryptedData,$signature);
$result = $Login->Login();
// echo json_encode($result);
