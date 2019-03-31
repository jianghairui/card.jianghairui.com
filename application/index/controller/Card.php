<?php
namespace app\index\controller;
use think\Db;
use think\Exception;
class Card extends Common
{
    //首页
    public function index() {
        $param = input('param.');
        $bind = 0;
        if(isset($param['key'])) {
            $decryted_data = authcode($param['key'],'DECODE','jiang');

            if($decryted_data === '') {
                $this->assign('msg','二维码已过期');
                return $this->fetch('error');
            }

            try {
                $exist = Db::table('card')->where('card_no',$decryted_data)->find();
                if(!$exist) {
                    $this->assign('msg','非法参数' . $decryted_data);
                    return $this->fetch('error');
                }
                if($exist['master_id'] !== $exist['user_id'] && $exist['user_id'] !== $this->myinfo['openid']) {
                    $this->assign('msg','此卡已绑定持卡人');
                    return $this->fetch('error');
                }
                Db::table('card')->where('card_no',$decryted_data)->update([
                    'user_id' => $this->myinfo['openid']
                ]);
                $map = [
                    ['default_card','=',$decryted_data],
                    ['openid','<>',$this->myinfo['openid']]
                ];
                Db::table('user')->where($map)->update([
                    'default_card' => ''
                ]);
            }catch (\Exception $e) {
                die($e->getMessage());
            }
            $bind = 1;
        }


        $map = [
            ['master_id|user_id','=',$this->myinfo['openid']]
        ];
        try {
            $hasDefault = Db::table('user')->where('openid',$this->myinfo['openid'])->value('default_card');
            $list = Db::table('card')->where($map)->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('bind',$bind);
        $this->assign('list',$list);
        $this->assign('hasDefault',$hasDefault);
        return $this->fetch();
    }
    //我的卡
    public function mycard() {
        try {
            $map = [
                ['c.master_id|c.user_id','=',$this->myinfo['openid']]
            ];
            $hasDefault = Db::table('user')->where('openid',$this->myinfo['openid'])->value('default_card');

            $list = Db::table('card')->alias('c')
                ->join('user u','c.user_id=u.openid','left')
                ->where($map)
                ->order(['c.create_time'=>'DESC'])
                ->field('c.*,u.nickname')
                ->select();
        }catch (\Exception $e) {
            die($e->getMessage());
        }

        $this->assign('hasDefault',$hasDefault);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function setDefault() {
        $card_no = input('post.card_no','');
        try {
            $map = [
                ['user_id','=',$this->myinfo['openid']],
                ['card_no','=',$card_no]
            ];

            $exist = Db::table('card')->where($map)->find();
            if(!$exist) {
                return ajax('您不是持卡人，不能把此卡设置为默认卡',-2);
            }
            Db::table('user')->where('openid',$this->myinfo['openid'])->update(['default_card'=>$card_no]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();

    }
    //添加卡页
    public function cardAdd() {
        return $this->fetch();
    }
    //添加卡POST
    public function cardAddPost() {
        $val['card_no'] = input('post.card_no');
        $val['address'] = input('post.address');
        $val['desc'] = input('post.desc');
        $this->checkPost($val);
        try {
            $exist = Db::table('card')->where('card_no',$val['card_no'])->find();
            if($exist) {
                return ajax('卡号已存在',-1);
            }

            $val['create_time'] = time();
            $val['master_id'] = $this->myinfo['openid'];
            $val['user_id'] = $this->myinfo['openid'];
            Db::table('card')->insert($val);

            $default = Db::table('user')->where([
                ['openid','=',$this->myinfo['openid']]
            ])->value('default_card');
            if(!$default) {
                Db::table('user')->where([
                    ['openid','=',$this->myinfo['openid']]
                ])->update(['default_card'=>$val['card_no']]);
            }

        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
    //卡详情页
    public function cardDetail() {
        $id = input('param.id');
        try {
            $map = [
                ['c.master_id|c.user_id','=',$this->myinfo['openid']],
                ['c.id','=',$id]
            ];

            $exist = Db::table('card')->alias('c')
                ->join('user u','c.user_id=u.openid','left')
                ->where($map)
                ->field('c.*,u.nickname')
                ->find();
        }catch (\Exception $e) {
            die($e->getMessage());
        }
        if(!$exist) {
            return $this->fetch('errmsg');
        }
        $this->assign('openid',$this->myinfo['openid']);
        $this->assign('info',$exist);
        return $this->fetch();
    }

    public function cardMod() {
        $id = input('param.id');
        $map = [
            ['master_id','=',$this->myinfo['openid']],
            ['id','=',$id]
        ];
        $exist = Db::table('card')->where($map)->find();
        if(!$exist) {
            return $this->fetch('errmsg');
        }

        $this->assign('info',$exist);
        return $this->fetch();
    }

    public function cardModPost() {
        $val['card_no'] = input('post.card_no');
        $val['address'] = input('post.address');
        $val['desc'] = input('post.desc');
        $val['id'] = input('post.card_id');
        $this->checkPost($val);
        try {
            $map = [
                ['id','=',$val['id']],
                ['master_id','=',$this->myinfo['openid']]
            ];
            $exist = Db::table('card')->where($map)->find();
            if(!$exist) {
                return ajax('非法参数',-3);
            }

            $map = [
                ['id','<>',$val['id']],
                ['card_no','=',$val['card_no']]
            ];
            $exist = Db::table('card')->where($map)->find();
            if($exist) {
                return ajax('卡号已存在',-1);
            }

            Db::table('card')->where('id',$val['id'])->update($val);

        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function cardDel() {
        $id = input('post.card_id');

        Db::startTrans();
        try {
            $map = [
                ['master_id','=',$this->myinfo['openid']],
                ['id','=',$id]
            ];
            $exist = Db::table('card')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('card')->where($map)->delete();

            $map = [
                ['default_card','=',$exist['card_no']]
            ];
            Db::table('user')->where($map)->update(['default_card'=>'']);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function cardBack() {
        $id = input('post.card_id');

        Db::startTrans();
        try {
            $map = [
                ['master_id','=',$this->myinfo['openid']],
                ['id','=',$id]
            ];
            $exist = Db::table('card')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('card')->where($map)->update(['user_id'=>$this->myinfo['openid']]);
            $map = [
                ['default_card','=',$exist['card_no']],
                ['openid','<>',$this->myinfo['openid']]
            ];
            Db::table('user')->where($map)->update(['default_card'=>'']);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }
    //发出卡二维码页
    public function sendOut() {
        $id = input('param.id');
        try {
            $where = [
                ['master_id','=',$this->myinfo['openid']],
                ['id','=',$id]
            ];
            $exist = Db::table('card')->where($where)->find();
            if(!$exist) {
                return $this->fetch('errmsg');
            }
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $encryted_data = authcode($exist['card_no'],'ENCODE','jiang',60*5);
        $this->assign('card_no',$encryted_data);
        return $this->fetch();
    }

    public function qrcode() {
        $card_no = input('param.card_no','');
        include ROOT_PATH . '/extend/phpqrcode/phpqrcode.php';
        $value= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/card?key='.urlencode($card_no);
        $errorCorrectionLevel = "L"; // 纠错级别：L、M、Q、H
        $matrixPointSize = "6"; // 点的大小：1到10
        header('Content-Type:image/png');
        \QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize);
        exit;
    }

    public function indexQrcode() {
        include ROOT_PATH . '/extend/phpqrcode/phpqrcode.php';
        $value = input('param.data','NULL');
        $errorCorrectionLevel = "L"; // 纠错级别：L、M、Q、H
        $matrixPointSize = "6"; // 点的大小：1到10
        header('Content-Type:image/png');
        \QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize);
        exit;
    }

    public function unbind() {
        $id = input('post.card_id');

        Db::startTrans();
        try {
            $map = [
                ['user_id','=',$this->myinfo['openid']],
                ['id','=',$id]
            ];
            $exist = Db::table('card')->where($map)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('card')->where($map)->update(['user_id'=>$exist['master_id']]);
            $map = [
                ['default_card','=',$exist['card_no']],
                ['openid','=',$this->myinfo['openid']]
            ];
            Db::table('user')->where($map)->update(['default_card'=>'']);
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(),-1);
        }
        return ajax();
    }

    public function cardid2data() {
//        $val['card_no'] = input('post.card_no');
        try {
            $hasDefault = Db::table('user')->where('openid',$this->myinfo['openid'])->value('default_card');
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        $cardid = $hasDefault;
        $time = 1;
        $count = 1;
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        $random = mt_rand(10000,99999);
        $url = 'https://wx.gdhdtech.com/services/cardid2data/'.$cardid.'/'.$time.'/'.$count.'/'.$msectime.'/' . $random;

        $result = file_get_contents($url);
        $result_arr = json_decode($result,true);
        if($result_arr['status'] == 200) {
            return ajax($result_arr['info'],1);
        }else {
            return ajax('',-1);
        }

    }





    public function test1() {
//        return ajax();
        $cardid = '13102163019';
        $time = 1;
        $count = 1;
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        $random = mt_rand(10000,99999);
        $url = 'https://wx.gdhdtech.com/services/cardid2data/'.$cardid.'/'.$time.'/'.$count.'/'.$msectime.'/' . $random;

        $result = file_get_contents($url);
        $result_arr = json_decode($result,true);
        halt($result_arr);

    }

    public function test2() {
//        $encryted_data = authcode('13102163019','ENCODE','jiang',60);
//        dump($encryted_data);
//        echo 'test1';
//
//        $decryted_data = authcode('416afpkLugfU8mC+MNXE5iuOj83D2jwawUaETnGuLBTYq/rsO/GHEQ','DECODE','jiang');
//        dump($decryted_data);
//        echo 'test2';
    }






    /**
     * 1、获取微信用户信息，判断有没有code，有使用code换取access_token，没有去获取code。
     * @return array 微信用户信息数组
     */
    public function auth(){
        $query = http_build_query(input('param.'));

        if(!session('openid')) {
            if (!isset($_GET['code'])){ //没有code，去微信接口获取code码
                $callback = $_SERVER['REQUEST_SCHEME'] . '://'.$_SERVER['HTTP_HOST'] . '/index/card/auth?'.$query;//微信服务器回调url，这里是本页url http://fx.jianghairui.com/index/index/get_user_all
                $this->get_code($callback);
            } else {    //获取code后跳转回来到这里了
                $code = $_GET['code'];
                $data = $this->get_access_token($code);//获取网页授权access_token和用户openid
                $data_all = $this->get_user_info($data['access_token'],$data['openid']);//获取微信用户信息

                /*保存用户信息到数据库并设置session*/
                $insert_data = [
                    'openid' => $data_all['openid'],
                    'nickname' => $data_all['nickname'],
                    'avatar' => $data_all['headimgurl']
                ];
                $user_exist = Db::table('user')->where('openid',$data_all['openid'])->find();

                try {
                    if($user_exist) {
                        Db::table('user')->where('openid',$data_all['openid'])->update($insert_data);
                    }else {
                        $insert_data['create_time'] = time();
                        Db::table('user')->insert($insert_data);
                    }
                }catch (\Exception $e) {
                    die('系统错误,请联系管理员 :' . $e->getMessage());
                }

                session('userinfo',$data_all);
            }
        }
        $url = $_SERVER['REQUEST_SCHEME'] . '://'.$_SERVER['HTTP_HOST'] . '/card?' . $query;
        header("Location:".$url);exit;

    }

    /**
     * 2、用户授权并获取code
     * @param string $callback 微信服务器回调链接url
     */
    private function get_code($callback){
        $appid = $this->appid;
        $scope = 'snsapi_userinfo';
        $state = md5(uniqid(rand(), true));//唯一ID标识符绝对不会重复
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($callback) .  '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
        header("Location:".$url);exit;
    }
    /**
     * 3、使用code换取access_token
     * @param string 用于换取access_token的code，微信提供
     * @return array access_token和用户openid数组
     */
    private function get_access_token($code){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
        $user = json_decode(file_get_contents($url));
        if (isset($user->errcode)) {
            if($user->errcode == '40163') {
                echo 'Code been used!!!';exit;
            }
            echo 'error:' . $user->errcode.'<hr>msg :' . $user->errmsg;exit;
        }
        $data = json_decode(json_encode($user),true);//返回的json数组转换成array数组
        return $data;
    }
    /**
     * 4、使用access_token获取用户信息
     * @param string access_token
     * @param string 用户的openid
     * @return array 用户信息数组
     */
    private function get_user_info($access_token,$openid){
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $user = json_decode(file_get_contents($url));
        if (isset($user->errcode)) {
            echo 'error:' . $user->errcode.'<hr>msg  :' . $user->errmsg;exit;
        }
        $data = json_decode(json_encode($user),true);//返回的json数组转换成array数组
        return $data;
    }

    public function frozen() {
        return $this->fetch();
    }


}
