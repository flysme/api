
<?php
require_once './upLoad/autoload.php';
use OSS\OssClient;  //引入命名空间
use OSS\Core\OssException; //引入命名空间
class Alioss{
    /**
     * 阿里云 OSS云存储类
     * 参考 阿里云链接      https://help.aliyun.com/document_detail/32101.html?spm=5176.doc32103.6.758.d8QJSr
     * $accessKeyId         <从OSS获得的    AccessKeyId>
     * $accessKeySecret     <从OSS获得的    AccessKeySecret>
     * $endpoint            <选定的OSS数据中心访问域名，例如  http://oss-cn-hangzhou.aliyuncs.com>
     * $bucket              <使用的存储空间名称，注意命名规范>
     * */
          private    $accessKeyId = "LTAIRt1Tuazsc1bY";//阿里云统一$accessKeyId
          private    $accessKeySecret = "xaJUxvBXd0ghj9Pi6o9XQ8Kyx0ZtRj";//阿里云统一$accessKeySecret
          private    $endpoint = "oss-cn-beijing.aliyuncs.com";// 新建的bucket的endpoint，测试使用
          private    $bucket = "firsthome";// 新建的bucket，测试使用

    /**
     * 新建一个存储空间(Bucket)
     * $bucket  存储空间名称，名称规范参考url：   https://help.aliyun.com/document_detail/31827.html?spm=5176.doc32101.2.5.PMp0so
     * */
    public function create_Bucket($bucket){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        $result = $ossClient->createBucket($bucket);
        if(is_array($result['info'])){
            return $result['info'];
        }else{
            return false;
        }

    }
    /**
     * 新建一个创建虚拟目录(dir)
     * $bucket  存储空间名称，名称规范参考url：   https://help.aliyun.com/document_detail/31827.html?spm=5176.doc32101.2.5.PMp0so
     * 已存在的文件夹不会覆盖
     * */
    public function create_ObjectDir($dir){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        $result = $ossClient->createObjectDir($this->bucket,$dir);
        if(is_array($result)){
            return $result;
        }else{
            return false;
        }

    }
    /**
     * 上传本地文件至 OSS
     * $filename  需要上传文件的文件名及后缀   例：test.txt
     * $filename  可指定存放的文件夹，默认存放在$bucket主目录下  例：test/test.txt
     * $path      需要上传的本地文件路径，绝对路径
     * */
    public function upload_File($filename,$path){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        $result = $ossClient->uploadFile($this->bucket, $filename,$path);
       if(is_array($result['info'])){
           return $result['info'];
       }else{
           return false;
       }

    }
    /**
     * 上传变量到 OSS
     * $filename  需要上传文件的文件名及后缀   例：test.txt
     * $filename  可指定存放的文件夹，默认存放在$bucket主目录下  例：test/test.txt
     * $content   变量内容，只支持字符串格式。
     * */
    public function put_Object($filename,$content){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        $result = $ossClient->putObject($this->bucket, $filename, $content);
        if(is_array($result['info'])){
            return $result['info'];
        }else{
            return false;
        }
    }
    /**
     * 将文件从服务器下载到本地
     * $filename  需要下载的文件名（包含文件夹） 例：test/test.txt
     * $path   下载文件的保存路径
     * 详情参考  https://help.aliyun.com/document_detail/32104.html?spm=5176.doc32101.2.9.ieZCNh#h2-u4E0Bu8F7Du6587u4EF6u5230u672Cu5730u6587u4EF6
     * */
    public function get_Object($filename,$path){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $path,
        );
        $result=$ossClient->getObject($this->bucket, $filename, $options);
            return $result;
    }
    /**
     * 判断object是否存在
     * $filename  object的文件名（包含文件夹） 例：test/test.txt
     * $result    返回值为ture时表示文件存在，false文件不存在。
     * */
    public function does_ObjectExist($filename){

        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        $result=$ossClient->doesObjectExist($this->bucket, $filename);
        return $result;
    }
    /**
     * 删除object
     * $filename  object的文件名（包含文件夹） 例：test/test.txt
     * */
    public function delete_Object($filename){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        $result=$ossClient->deleteObject($this->bucket, $filename);
        if(is_array($result['info'])){
            return $result['info'];
        }else{
            return false;
        }
    }
    /**
     * 列出Bucket内所有目录和文件, 注意如果符合条件的文件数目超过设置的max-keys， 用户需要使用返回的nextMarker作为入参，通过
     * 循环调用ListObjects得到所有的文件，具体操作见下面的 listAllObjects 示例
     *
     * @param OssClient $ossClient OssClient实例
     * @param string $bucket 存储空间名称
     * @return null
     */
    public function list_Objects($prefix='',$nextMarker='',$maxkeys=1000,$delimiter = '/'){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        //$prefix = 'black/';   //需要查询的目录
        //$delimiter = '/';       //去除文件夹
        //$nextMarker = '';     //从文件为名为$nextMarker的下一条开始查询。
        //$maxkeys = 1000;      //最大返回条数
        $options = array(
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $nextMarker,
        );
        $listObjectInfo=$ossClient->listObjects($this->bucket, $options);
        $objectList = $listObjectInfo->getObjectList(); // 文件列表
        if (!empty($objectList)) {
            foreach ($objectList as $objectInfo) {
                $filelist[]=$objectInfo->getKey();//文件列表
            }
        }
            return $filelist;
    }
    /**
     * 列出Bucket内所有目录和文件, 注意如果符合条件的文件数目超过设置的max-keys， 用户需要使用返回的nextMarker作为入参，通过
     * 循环调用ListObjects得到所有的文件，具体操作见下面的 listAllObjects 示例
     *
     * @param OssClient $ossClient OssClient实例
     * @param string $bucket 存储空间名称
     * @return null
     */
    public function list_dir_Objects($prefix='',$nextMarker='',$maxkeys=1000,$delimiter = '/'){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        //$prefix = 'black/';   //需要查询的目录,默认主目录
        //$delimiter = '/';     //去除文件夹
        //$nextMarker = '';     //从文件为名为$nextMarker的下一条开始查询。
        //$maxkeys = 1000;      //最大返回条数
        $options = array(
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $nextMarker,
        );
        $listObjectInfo=$ossClient->listObjects($this->bucket, $options);
        $prefixList = $listObjectInfo->getPrefixList(); // 目录列表
        if (!empty($prefixList)) {
            foreach ($prefixList as $prefixInfo) {
                $dirlist[]=$prefixInfo->getPrefix();//文件夹列表
            }
        }

        return $dirlist;
    }
}
