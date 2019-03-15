
<?php
header("Content-Type:text/html;charset=utf-8");
/**
 * Created by PhpStorm.
 * User: DY040
 * Date: 2018/4/26
 * Time: 13:23
 */

 $imgname = $_FILES['file']['name'];
 $tmp = $_FILES['file']['tmp_name'];
 $filepath = './imgs/';
 $imgType = strrchr($imgname, '.');//文件名后缀
 $img_hzm = array('.gif', '.png', '.jpg', '.jpeg');
 $re = in_array($imgType, $img_hzm);
 if (!$re) {
    echo json_encode((object)array('data' => array(),"msg"=>'该图片不合法-文件名后缀'));
 }
 $imgName = MD5(uniqid(rand(), true)) . $imgType;
 $img_file = $filepath.$imgName;
 if(move_uploaded_file($tmp,$img_file)){
     echo json_encode((object)array('data' => array('file'=>$imgName),"msg"=>'上传成功'));
 }else{
     echo json_encode((object)array('data' => array(),"msg"=>'上传失败'));
 }
