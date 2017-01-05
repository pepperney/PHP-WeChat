<?php
namespace Home\Controller;
use Think\Controller;



class LoginController extends Controller {

    protected $user;     




    /**
     * 测试方法
     * @return [type] [description]
     */
    public function test(){
        $user = session('user');
        if($user){
            echo "session success";
        }else{
            echo "session failed";
        }
    }


    public function login(){
        $rd = array("code"=>1,"msg"=>"success","data"=>array());
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $condition = "`password` = '{$password}' AND (username='{$username}' OR email = '{$username}' OR mobile = '{$username}')";
        $user = M('user')->where($condition)->find();
        if(!$user){
            $rd['code'] = 0;
            $rd['msg'] = '用户名或密码错误';
        }else{
            session('user',$user);
            header("location:"."http://1.pepper.applinzi.com/static/html/main.html");
        }
        $this->ajaxReturn($rd);
    }
    


	
}