<?php
include_once './config/common.php';
include_once './config/db.php';
include_once './config/sql.php';
//使用redis实现一个购物车功能
class Cart
{
    /**
     *  购物车有功能： 1、 将商品添加到购物车中  2、改变购物车商品数量  3、显示购物车的信息
     *
     *
     * 将商品添加到购物车中功能分析如下：
     * 1. 接收到商品ID
     * 2. 根据商品ID查询商品信息
     * 3. 将商品信息加入到购物车中
     *
     *         a. 判断购物车是否已有对应商品
     *         b. 如果购物车中没有对应的商品，直接加入
     *         c. 如果购物车中有对应的商品，只要修改商品数量
     */
    private $session_id;
    private $store_id;
    public function __construct($session_id,$store_id)
    {
        include_once './config/db.php';
        $this ->DB = new DB();
        $this->session_id = $session_id;
        $this->store_id = $store_id;
        //如果成员属性没有声明，默认就是公有属性
        $this->redis = new Redis;
        $this->redis->connect('127.0.0.1', 6379);
    }
    public function reduceCart ($store_id,$sku_id, $cartNum=1)
    {
      if (empty($sku_id)) return array('status'=>401,'msg'=>'暂无sku_id');
      //购物车有对应的商品，只需要添加对应商品的数量
      $originNum = $this->redis->hget($key, 'num');
      if ($originNum <=0) return array('status'=>401,'msg'=>'数量最低为0');
      //原来的数量加上用户新加入的数量
      $newNum = $originNum - $cartNum;
      $this->redis->hset($key, 'num', $newNum);
      return array('status'=>0,'msg'=>'','data'=>(object)array());
    }
    /*添加购物车*/
    public function addToCart($store_id,$sku_id, $cartNum=1)
    {
        if (empty($sku_id)) return array('status'=>401,'msg'=>'暂无sku_id');
        //根据商品sku查询商品数据
        $goodData = $this->goodsData($sku_id);
        // 判断sku是否有效
        if (empty($goodData)) return array('status'=>401,'msg'=>'sku不存在');

        $key = 'cart:'.$this->session_id.':'.$this->store_id.':'.$sku_id;//id 说明：1、不仅仅要区分商品  2、 用户

        // $data = $this->redis->hget($key, 'id');
        $data = $this->redis->exists($key);
        //判断购物车中是否有无商品，然后根据情况加入购物车
        if (!$data) { //购物车之前没有对应的商品的

            $goodData['num'] = $cartNum;//购物车的商品数量

            $this->redis->hmset($key, $goodData);//将商品数据存放到redis中hash

            $key1 = 'cart:ids:set:'.$this->session_id.'store:'.$this->store_id;

            $this->redis->sadd($key1, $sku_id);//将商品ID存放集合中,是为了更好将用户的购物车的商品给遍历出来

        } else {
            //购物车有对应的商品，只需要添加对应商品的数量
            $originNum = $this->redis->hget($key, 'num');
            //原来的数量加上用户新加入的数量
            $newNum = $originNum + $cartNum;
            $this->redis->hset($key, 'num', $newNum);
        }
        return array('status'=>0,'msg'=>'','data'=>(object)array());
    }

    /*获取购物车数据*/
    public function showCartList()
    {
        $list = array();
        $key = 'cart:ids:set:'.$this->session_id.'store:'.$this->store_id;
        //先根据集合拿到商品ID
        $sku_idArr =  $this->redis->sMembers($key);
        for ($i=0; $i<count($sku_idArr); $i++) {
            $k  = 'cart:'.$this->session_id.':'.$this->store_id.':'.$sku_idArr[$i];//id
            $list[] = $this->redis->hGetAll($k);
        }
        return $list;
    }
    /*获取sku数据*/
    public function goodsData($sku_id)
    {
        $this->DB->connect();//连接数据库
        $cartsku = Sql::getCartSku($sku_id);
        $skuData= $this->DB->getData($cartsku);
        $this->DB->links->close();
        return $skuData;
    }
}
parse_str($_SERVER['QUERY_STRING']);
$session_id = isset($_SERVER['HTTP_X_SESSION_TOKEN']) ? $_SERVER['HTTP_X_SESSION_TOKEN'] :null;
$cart = new Cart($session_id,$store_id);
$sku_id = trim($_GET['sku_id']);
$result = isset($carhandle) ? $cart->reduceCart($store_id,$sku_id) : $cart->addToCart($store_id,$sku_id);
echo json_encode($result);
