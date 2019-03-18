<?php
header("Content-type:application/json;charset=utf-8"); //设置请求头
/**
 * 对微信小程序用户加密数据的解密示例代码.
 *
 * @copyright Copyright (c) 1998-2014 Tencent Inc.
 */


include_once "errorCode.php";


class WXBizDataCrypt
{
  private $appid;
	private $sessionKey;

	/**
	 * 构造函数
	 * @param $sessionKey string 用户在小程序登录后获取的会话密钥
	 * @param $appid string 小程序的appid
	 */
	public function __construct( $appid, $sessionKey)
	{
		$this->sessionKey = $sessionKey;
		$this->appid = $appid;
	}
  public function decode($text)
  {
        define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
        define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
        define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
        define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
        define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));
        $first2 = substr($text, 0, 2);
        $first3 = substr($text, 0, 3);
        $first4 = substr($text, 0, 3);
        $encodType = "";
        if ($first3 == UTF8_BOM)
            $encodType = 'UTF-8 BOM';
        else if ($first4 == UTF32_BIG_ENDIAN_BOM)
            $encodType = 'UTF-32BE';
        else if ($first4 == UTF32_LITTLE_ENDIAN_BOM)
            $encodType = 'UTF-32LE';
        else if ($first2 == UTF16_BIG_ENDIAN_BOM)
            $encodType = 'UTF-16BE';
        else if ($first2 == UTF16_LITTLE_ENDIAN_BOM)
            $encodType = 'UTF-16LE';

        //下面的判断主要还是判断ANSI编码的·
        if ($encodType == '') {//即默认创建的txt文本-ANSI编码的
            $content = iconv("GBK", "UTF-8", $text);
        } else if ($encodType == 'UTF-8 BOM') {//本来就是UTF-8不用转换
            $content = $text;
        } else {//其他的格式都转化为UTF-8就可以了
            $content = iconv($encodType, "UTF-8", $text);
        }
        return $content;
  } 　


	/**
	 * 检验数据的真实性，并且获取解密后的明文.
	 * @param $encryptedData string 加密的用户数据
	 * @param $iv string 与用户数据一同返回的初始向量
	 * @param $data string 解密后的原文
     *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function decryptData( $encryptedData, $iv, &$data )
	{
		if (strlen($this->sessionKey) != 24) {
			return ErrorCode::$IllegalAesKey;
		}
		$aesKey=base64_decode($this->sessionKey);


		if (strlen($iv) != 24) {
			return ErrorCode::$IllegalIv;
		}
		$aesIV=base64_decode($iv);
    $encryptedData = str_replace(' ','+',$encryptedData);
		$aesCipher=base64_decode($encryptedData);
    echo $this->decode($encryptedData);
    exit();
		$result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
    var_dump($result);
    //
		// $dataObj=json_decode( $result );
		// if( $dataObj  == NULL )
		// {
		// 	return ErrorCode::$IllegalBuffer;
		// }
		// if( $dataObj->watermark->appid != $this->appid )
		// {
		// 	return ErrorCode::$IllegalBuffer;
		// }
		// $data = $result;
		// // return ErrorCode::$OK;
    // // var_dump($result);
		// return $result;
	}

}
