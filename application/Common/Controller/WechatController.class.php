<?php
namespace Common\Controller;
use Think\Controller;

class WechatController extends Controller {

    function _initialize() {

    }

    /**
     * Get code for web WeChat api.
     */
    function getWebCode(){
        $redirect = 'http://' . $_SERVER['HTTP_HOST'] . U('Video/Index/receiveCode');

        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.C('WE_APPID').'&redirect_uri=' . urlencode($redirect) . '&response_type=code&scope=snsapi_userinfo&state=code#wechat_redirect';

        header('Location: '. $url);
    }

    function getWebAccessToken($code){
        $token = S('web_access_token');

        if(!$token){
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.C('WE_APPID').'&secret='.C('WE_SECRET') . '&code=' .$code. '&grant_type=authorization_code';

            $response = $this->getAPI($url);

            var_dump($response);

            if($response !== false && !empty($response)){
                $arr = json_decode($response, true);
                $token = $arr['access_token'];
                S('web_access_token', $token, $arr['expires_in'] - 10);
                $_COOKIE['openid'] = $arr['openid'];
                setcookie('openid', $arr['openid'], $arr['expires_in'] - 10);
            } else {
                $token = '';
            }
        }

        return $token;
    }

    function getWebUserInfo(){
        $token = S('web_access_token');
        $openId = $_COOKIE['openid'];

        if(!empty($token) && !empty($openId)){
            $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$openId.'&lang=zh_CN';

            $response = $this->getAPI($url);

            var_dump($response);exit;

            if(!$response){
                $arr = json_decode($response, true);
                $token = $arr['access_token'];
                S('web_access_token', $token, $arr['expires_in'] - 10);
                cookie('openid', $arr['openid'], $arr['expires_in'] - 10);
            } else {
                $token = '';
            }
        }

        return $token;
    }

    /**
     * WeChat background api AccessToken
     * @return mixed|string
     */
    function getAccessToken(){
        $token = S('access_token');

        if(!$token){
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.C('WE_APPID').'&secret='.C('WE_SECRET');

            $response = $this->getAPI($url);

            if(!$response){
                $arr = json_decode($response, true);
                $token = $arr['access_token'];
                S('access_token', $token, $arr['expires_in'] - 10);
            } else {
                $token = '';
            }
        }

        return $token;
    }

    function getAPI($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}