<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
include_once './utils/oauth.php';
include_once './service/session/session.php';

header('Content-Type:text/plain;charset=utf-8');

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
    if ($codeInfo['errcode'] == 0) {
      $openid = $codeInfo['openid'];
      $session_key = $codeInfo['session_key'];
      $this->Login($session_key);
      $_sessionKey = $this -> Utils->GetRandStr(16);
      Session::set($_sessionKey, array('open_id'=>$openid,'session_key'=>$_sessionKey), 4800); //设置session
      exit();
      return array('session_id'=>$_sessionKey);
    }
    return array('errcode'=>$codeInfo['errcode'],'errmsg'=>$codeInfo['errmsg']);
  }
  public function Login ($session_key) {
    include_once "./utils/wx/wxBizDataCrypt.php";
    $Wx = new WXBizDataCrypt($this->appId, $session_key);
    $errCode = $Wx->decryptData($this->encryptedata, $this->iv, $data );
    // if ($errCode == 0) {
        // print($data . "\n");
        echo $errCode;
        var_dump();
    // }
  }
}
$code = $_SERVER['HTTP_X_WX_CODE'];
$iv = $_SERVER['HTTP_X_WX_IV'];
$encryptedData = $_SERVER['HTTP_X_WX_ENCRYPTEDATA'];
$signature = $_SERVER['HTTP_X_WX_SIGNATURE'];
// $Login = new Wxlogin($code,$iv,$encryptedData,$signature);
// var_dump($_SERVER);
$key = base64_decode(str_replace(" ","+",$encryptedData));
echo $key;
exit();
// $_result = $Login->getSessionKey();
// echo json_encode($_result);
