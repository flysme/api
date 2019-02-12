
<?php
header("Content-Type:text/html;charset=utf-8");
/**
 * Created by PhpStorm.
 * User: DY040
 * Date: 2018/4/26
 * Time: 13:23
 */

/*
 * 支持多文件上传 检查所有的文件 是否符合要求 储存于 服务器 并返回图片地址
 * */

class ImgUpload1
{
    protected static $imgs_file = array();//要返回的数据 （储存的文件信息）

    function __construct($files)
    {

        foreach ($files as $k => $v) {
            $img_info = $files[$k];
            $arr = array();
            if (gettype($img_info['name']) === 'array') {

                foreach ($img_info['name'] as $key => $value) {
                    $arr['name'] = $img_info['name'][$key];
                    $arr['type'] = $img_info['type'][$key];
                    $arr['tmp_name'] = $img_info['tmp_name'][$key];
                    $arr['error'] = $img_info['error'][$key];
                    $arr['size'] = $img_info['size'][$key];
                    $this->_check($arr, $k);
                }
            } else {
                $this->_check($img_info, $k);
            }

        }
    }

    protected function _check($img_info, $index)
    {

        if (!isset(static::$imgs_file[$index])) {

            static::$imgs_file[$index] = $this->_img_store($img_info) . "*";
        } else {
            static::$imgs_file[$index] .= $this->_img_store($img_info) . "*";
        }


    }

    protected function _img_store($img_info)
    {

        //检测文件是否合法 ---文件名后缀
        $img_hzm = array('.gif', '.png', '.jpg', '.jpeg');
        $re = in_array(strrchr($img_info['name'], '.'), $img_hzm);
        if (!$re) {
            return '该图片不合法-文件名后缀';
        }
        //检测文件是否合法 ---MIME php检测上传信息的mime
        if (explode('/', $img_info['type'])[0] !== 'image') {
            return '该图片不合法-MIME';
        }
        //检测文件是否合法 ---MIME php检测本地的临时文件
        //        这里要修改php.ini 增加php_fileinfo.dll模块
        $file_info = new Finfo(FILEINFO_MIME_TYPE);
        $mime_type = $file_info->file($img_info['tmp_name']);
        if (explode('/', $mime_type)[0] !== 'image') {
            return '该图片不合法-MIME';
        }
        //检测文件大小 设为1mb
        if ($img_info['size'] > 1000000) {
            return '该图片不合法-文件大于1000kb';
        }
        //是否是http上传的临时文件
        if (!is_uploaded_file($img_info['tmp_name'])) {
            echo '文件名不合法';
            return false;
        };

        //设置上传文件地址
        $path = './imgs/';
        if (!is_dir($path)) {
            mkdir("$path");
        }
        $imgType = strrchr($img_info['name'], '.');//文件名后缀

        $imgName = MD5(uniqid(rand(), true)) . $imgType;
        $img_file = $path.$imgName;
        $upload_re = move_uploaded_file($img_info['tmp_name'], $img_file);
        if ($upload_re) {
            return $imgName;
        } else {
            return false;
        }
    }

    public function imgs_file()
    {
        $arr = static::$imgs_file;
        foreach ($arr as $k => $v) {


            if(gettype($_FILES[$k]['name'])==='string'){
                $arr[$k] = substr($v, 0, -1);
            }else{
                $arr[$k] = explode('*', substr($v, 0, -1));
            }
        }
        return $arr;
    }
}

$img_upload = new ImgUpload1($_FILES);
echo json_encode((object)array('data' => $img_upload->imgs_file()));