<?php
namespace Home\Controller;
use Think\Controller;
define("TOKEN","enjoyit");


class IndexController extends Controller {

    protected $User;     //微信用户对象 
    protected $appid; 
    protected $appsecret;

    /*通用入口 构造方法
     *aunthor:pepper
     *date:2016-12-12
     */
    public function _initialize() {
        $this->appid = C("APPID");
        $this->appsecret = C("APPSECRET");
    }
    
    /**
     * 测试方法
     * @return [type] [description]
     */
    public function test(){
        $data = M('wechat')->find();
        echo "<h1>".TOKEN."</h1>";
    }
    
    /**
     * curl方法
     * @param  [type] $url  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function http_curl($url,$data){
	    //1.初始化curl
	    $ch = curl_init();
	    //2.设置curl的参数
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	    if($data){
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($data)));
	    }
	    //3.采集
	    $output = curl_exec($ch);
	    //4.关闭
	    curl_close($ch);
	    $jsoninfo = json_decode($output, true);
	    return $jsoninfo;
    }
    
    /**
     * 获取全局的access_token方法
     * @return [type] [description]
     */
    public function getAccessToken(){
    	$field = 'access_token,modify_time';
    	$condition = array('token'=>TOKEN,'appid'=>$this->appid,'appsecret'=>$this->appsecret);
    	$data = M('wechat')->field($field)->where($condition)->find();
    	if($data['access_token'] && time()-$data['modify_time']<7000){
    		$access_token = $data['access_token'];
    	}else{
    		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret.'';
    		$jsoninfo = $this->http_curl($url,null);
    		if(!$jsoninfo || $jsoninfo['errcode']){
    			var_dump($jsoninfo);
    		}else{
    			$access_token = $jsoninfo['access_token'];
    			$data = array('access_token' =>$access_token,'modify_time'=>time());
    			M('wechat')->where($condition)->save($data);
    		}
    	}
    	return $access_token;
    }


    /**
     * 微信接入验证
     * @return [type] [description]
     */
    public function index(){
    	//获得参数 signature nonce token timestamp echostr
	    $nonce     = $_GET['nonce'];
	    $timestamp = $_GET['timestamp'];
	    $echostr   = $_GET['echostr'];
	    $signature = $_GET['signature'];
	    //形成数组，然后按字典序排序
	    $array = array($nonce, $timestamp, TOKEN);
	    sort($array);
	    //拼接成字符串,sha1加密 ，然后与signature进行校验
	    $str = sha1( implode( $array ) );
	    if( $str  == $signature && $echostr ){
	      //第一次接入weixin api接口的时候
	      echo  $echostr;
	      exit;
	    }else{
	      $this->responseMsg();
	    }
    }

   
    /**
     * 发送消息
     * @return [type] [description]
     */
    public function responseMsg(){

    	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];               
        $postObj = simplexml_load_string($postStr,"SimpleXMLElement",LIBXML_NOCDATA);//XML转String
        //根据消息类型将信息分发
        if(strtolower( $postObj->MsgType) == 'event'){
            if(strtolower($postObj->Event) == 'subscribe'){
                $toUserName = $postObj->FromUserName;
                $fromUserName = $postObj->ToUserName;
                $content = '欢迎关注!';
                $msgType = 'text'; //消息类型
                $createTime = time();
                $template = "<xml>
                             <ToUserName><![CDATA[%s]]></ToUserName>
                             <FromUserName><![CDATA[%s]]></FromUserName>
                             <Content><![CDATA[%s]]></Content>
                             <MsgType><![CDATA[%s]]></MsgType>
                             <CreateTime>%s</CreateTime>
                             </xml>";
                $info = sprintf($template, $toUserName, $fromUserName, $content, $msgType,$createTime);
                echo $info;
            }

            if(strtolower($postObj->Event) == 'click' && $postObj->EventKey == 'V1001_TODAY_MUSIC'){
                $toUserName = $postObj->FromUserName;
                $fromUserName = $postObj->ToUserName;
                $createTime = time();
                $msgType = 'news';
                $articleCount = "1";
                $title = "have a relax";
                $description = "shall we talking?";
                $picurl = "http://1.pepper.applinzi.com/static/images/deer.jpg";
                $url = "http://1.pepper.applinzi.com/static/html/main.html";
                $template ="<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <ArticleCount>%s</ArticleCount>
                            <Articles>
                            <item>
                            <Title><![CDATA[%s]]></Title> 
                            <Description><![CDATA[%s]]></Description>
                            <PicUrl><![CDATA[%s]]></PicUrl>
                            <Url><![CDATA[%s]]></Url>
                            </item>
                            </Articles>
                            </xml>";
                $info = sprintf($template, $toUserName, $fromUserName,$createTime, $msgType,$articleCount,$title,$description,$picurl,$url);
                echo $info;
            }
        }
    }


    /**
     * 创建微信菜单
     * @return [type] [description]
     */
    public function createMenu(){
    	include_once(APP_PATH."Common/Conf/menu_config.php");
    	$access_token = $this->getAccessToken($this->appid,$this->$appsecret);
	    $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
	    $data = $menu_config;
	    $jsoninfo = $this->http_curl($url,$data);
	    var_dump($jsoninfo);
	    exit;
    }


    /**
     * 微信主页
     * @return [type] [description]
     */
    public function main(){
    	$code = $_GET['code'];
    	$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$code}&grant_type=authorization_code";
    	$jsoninfo = $this->http_curl($url,null);
    	$openid = $jsoninfo['openid'];
    	session('openid',$openid);
    	$condition = array('openid'=>$openid);
    	$user = M('user')->where($condition)->find();
    	if(!$user){
    		// header("location:"."http://localhost/pepper/static/html/register.html");
            header("location:"."http://1.pepper.applinzi.com/static/html/register.html");
    	}else{
    		// header("location:"."http://localhost/pepper/static/html/main.html");
            header("location:"."http://1.pepper.applinzi.com/static/html/main.html");
    	}
    }


    /**
     * 用户注册
     * @return [type] [description]
     */
    public function register(){
    	$rd = array("code"=>1,"msg"=>"success","data"=>array());
    	$_POST = json_decode(file_get_contents('php://input'),true);
    	$accountType = $_POST['accountType'];//注册时的账号类型
		$username = $_POST['username'];
        $password = md5($_POST['password']);
		$condition = "`password` = '{$password}' AND (username='{$username}' OR email = '{$username}' OR mobile = '{$username}')";
		$user = M('user')->where($condition)->find();
		if($user){
			$rd['code'] = 0;
			$rd['msg'] = '用户已存在！';
		}else{
			$user = array(
				'username'=>$username,
				'password'=>$password,
				'openid'=>session('openid'),
				'usertype'=>1,
				'userstatus'=>1
				);
			if($accountType == 1){//手机登录
				//TODO 检验手机验证码码
		    	$user['mobile'] = $username;
			}else if($accountType == 2){//邮箱登录
				$user['email'] = $username;
				//TODO 验证邮箱
			}
			M('user')->data($user)->add();
		}
		$this->ajaxReturn($rd);


    }





    /**
     * 用户绑定
     * @return [type] [description]
     */
    public function bind(){
		$rd = array("code"=>1,"msg"=>"success","data"=>array());
		$_POST = json_decode(file_get_contents('php://input'),true);
    	$openid = session('openid');
    	if(!$openid){
    		$rd['code'] = 0;
			$rd['msg'] = 'maybe a little problem occurred!';
			$this->ajaxReturn($rd);	
    	}
    	$username = $_POST['username'];
        $password = md5($_POST['password']);
        $condition = "`password` = '{$password}' AND (username='{$username}' OR email = '{$username}' OR mobile = '{$username}')";
        $user = M('user')->where($condition)->find();
		if(!$user){
			$rd['code'] = 0;
			$rd['msg'] = '用户不存在！';
		}else if($user['openid']){
			$rd['code'] = 0;
			$rd['msg'] = '您已经绑定了微信，请解绑后重新操作';	
		}else{
			$user['openid'] = $openid;
			$condition = array("userid"=>$user['userid']);
			M('user')->where($condition)->save($data);	
		}
		$this->ajaxReturn($rd);
    }



    /**
     * 获取用户基本信息
     * @return [type] [description]
     */
    public function userInfo(){
    	$rd = array("code"=>1,"msg"=>"success","data"=>array());
    	$openid = session('openid');
    	$access_token = $this->getAccessToken();
    	$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
    	$jsoninfo = $this->http_curl($url,null);

    	$condition = array('openid'=>$openid);
    	$user = M('user')->where($condition)->find();
    	$jsoninfo['email'] = $user['email'];
    	$jsoninfo['mobile'] = $user['mobile'];
    	$jsoninfo['username'] = $user['username'];
    	$jsoninfo['realname'] = $user['realname'];
    	$rd['data'] = $jsoninfo;
    	$this->ajaxReturn($rd);
    }


    /**
     * 用户解绑
     * @return [type] [description]
     */
    public function unbind(){
    	$openid = session('openid');
    	$data = array('openid'=>null);
    	$condition = array('openid'=>$openid);
    	M('user')->where($condition)->data($data)->save();
    }

    /**
     * 图片上传
     * @return [type] [description]
     */
    public function upload(){
        header("Content-Type:text/html; charset=utf-8"); //不然返回中文乱码
        $redata = array('code'=>1,'msg'=>'','data'=>array());
        $file = $_FILES['file'];//得到传输的数据
        //得到文件名称
        $filename = $file['name'];
        $type = strtolower(substr($filename,strrpos($filename,'.')+1)); //得到文件类型，并且都转化成小写
        $allow_type = array('jpg','jpeg','gif','png'); //定义允许上传的类型
        //判断文件类型是否被允许上传
        if(!in_array($type, $allow_type)){
            $redata['code'] = 0;
            $redata['msg'] = '文件类型不支持';
            $this->ajaxReturn($redata); 
        }
        // $upload_path = $_SERVER['DOCUMENT_ROOT']."pepper/upload";
        $upload_path = 'upload/'; //此行代码等同上面注释掉的
          if (!is_readable($upload_path)){
            $iscreate = mkdir($upload_path,0777); 
        }
        //使用时间戳对文件重命名
        $name = time().".".$type;
        //移动文件到相应的文件夹
        $returnUrl = str_replace("/", "\\", $upload_path.$name);
        if(move_uploaded_file($file['tmp_name'],$returnUrl)){
            $redata['msg'] = 'success';
            $redata['url'] =  $returnUrl;
        }else{
            $redata['code'] = '0';
            $redata['msg'] = 'failed!';
        }
        $this->ajaxReturn($redata); 
    }



	
}