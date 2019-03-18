<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/oauth.php';
include_once './service/session/session.php';


class Wxlogin {
  private $appId = 'wx6e219488e53a4991';
  public $DB;
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
  public function UsersEncrypt ($session_key) {
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
      $msg = $this->UsersEncrypt($session_key); //获取微信用户信息（openid）
      if ($msg['errcode'] == 0)
      {
        $msgData = $this ->Utils->objectToarray($msg['data']);
        $open_id = $msgData['openId']; //open_id;
        $username = $msgData['nickName']; //nickName;
        $avatar= $msgData['avatarUrl']; //avatarUrl;
        $info = $this->getUserInfo($open_id);
        if(!is_array($info))
        {
          $query_res = $this->addUser($open_id,$username,$avatar); //用户信息入库
          if (!empty($query_res))
          {
            $currentInfo = $this->getUserInfo($open_id);                  //获取用户信息
            if (!empty($currentInfo))
            {
              $session_id= system(`head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 168`);  //生成3rd_session
              Session::set($session_id, array('open_id'=>$openid,'session_key'=>$session_key), 8800); //设置session
              return array('error_code' => 0,'sessionid' => $session_id,'msg' => '');
            }
          }
          else
          {
            return array('error_code' => 401,'msg' => '用户登录失败');
          }
        }
        if($session_id){
            return array('error_code' => 0,'sessionid' => $session_id,'msg' => ''); //把3rd_session返回给客户端
        }
        // else
        // {
        //   return array('error_code' => 0,'sessionid' => $session_id,'msg' => ''); //把3rd_session返回给客户端
        //   $this->ajaxReturn(['error_code'=>0,'sessionid'=>$session_db->getSid($info['id'])]);
        // }
      }
      else
      {
        return array('error_code' => $msg['errcode'],'msg' => $msg['errmsg']);
      }
    }
  }
  // 添加用户入库
  public function addUser ($open_id,$username,$avatar) {
    $this->DB->connect();//连接数据库
    $queryData = array(
      'user_id' => $open_id,
      'username' => base64_encode($username),
      'avatar' => $avatar,
      'open_id' => $open_id,
      'create_time' => time(),
      'status' => 1,
    );
    $addUsersql = Sql::addUser($queryData);
    $data= $this->DB->query($addUsersql);
    $this->DB->links->close();
    if (!empty($data))return $data;
    return null;
  }
  public function getUserInfo ($open_id) {
    $this->DB->connect();//连接数据库
    $userSql =  Sql::getUserInfo($open_id);
    $data = $this -> DB ->getData($userSql);
    $this-> DB->links->close();
    return empty($data) ? null :$data;
  }
  public function _3rd_session($len) {
      $fp = @fopen('/dev/urandom', 'rb');
      $result = '';
      if ($fp !== FALSE) {
          $result .= @fread($fp, $len);
          @fclose($fp);
      } else {
          trigger_error('Can not open /dev/urandom.');
      }
      // convert from binary to string
      $result = base64_encode($result);
      // remove none url chars
      $result = strtr($result, '+/', '-_');
      return substr($result, 0, $len);
   }
}

$code = $_SERVER['HTTP_X_WX_CODE'];
$iv = $_SERVER['HTTP_X_WX_IV'];
$encryptedData = $_SERVER['HTTP_X_WX_ENCRYPTEDATA'];
$signature = $_SERVER['HTTP_X_WX_SIGNATURE'];
$Login = new Wxlogin($code,$iv,$encryptedData,$signature);
$result = $Login->Login();
echo json_encode($result);
