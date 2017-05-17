<?php
/*
 *      _______ _     _       _     _____ __  __ ______
 *     |__   __| |   (_)     | |   / ____|  \/  |  ____|
 *        | |  | |__  _ _ __ | | _| |    | \  / | |__
 *        | |  | '_ \| | '_ \| |/ / |    | |\/| |  __|
 *        | |  | | | | | | | |   <| |____| |  | | |
 *        |_|  |_| |_|_|_| |_|_|\_\\_____|_|  |_|_|
 */
/*
 *     _________  ___  ___  ___  ________   ___  __    ________  _____ ______   ________
 *    |\___   ___\\  \|\  \|\  \|\   ___  \|\  \|\  \ |\   ____\|\   _ \  _   \|\  _____\
 *    \|___ \  \_\ \  \\\  \ \  \ \  \\ \  \ \  \/  /|\ \  \___|\ \  \\\__\ \  \ \  \__/
 *         \ \  \ \ \   __  \ \  \ \  \\ \  \ \   ___  \ \  \    \ \  \\|__| \  \ \   __\
 *          \ \  \ \ \  \ \  \ \  \ \  \\ \  \ \  \\ \  \ \  \____\ \  \    \ \  \ \  \_|
 *           \ \__\ \ \__\ \__\ \__\ \__\\ \__\ \__\\ \__\ \_______\ \__\    \ \__\ \__\
 *            \|__|  \|__|\|__|\|__|\|__| \|__|\|__| \|__|\|_______|\|__|     \|__|\|__|
 */
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Video\Controller;
use Common\Controller\WechatController;
use Think\Exception;

/**
 * 首页
 */
class IndexController extends WechatController {

    protected $video_model;
    protected $doctor_model;
    protected $disease_model;

    protected $question_model;

    protected $_openid = '';

    function _initialize() {
        parent::_initialize();
        $this->video_model = D("Portal/Video");
        $this->doctor_model = D("Portal/Doctor");
        $this->disease_model = D("Portal/Disease");

        $this->_openid = cookie('openid');

        // Just for test
//        $this->_openid = 'otyIXt8PtLekJxF3eskU0GyNDTYI';
//        $this->_openid = 'ok0umxE337MOwahme84mXJ5AnXF4';
    }

    public function api(){
        if($this->checkSignature())
			exit($_GET['echostr']);
		else 
			echo 'error';
    }

    private function checkSignature() {
		$signature = $_GET['signature'];
		$timestamp = $_GET['timestamp'];
		$nonce = $_GET['nonce'];
		
		if($timestamp == '' || $nonce == '')
			return false;
		
		$token = 'quandao8800';
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);
		
		if($tmpStr == $signature) 
			return true;
		else
			return false;
	}

    //首页
	public function index() {

        $videos = $this->video_model->where('`status` = 1')->select();

        dump($videos);

    	$this->display();
    }

    public function test(){

        $id=  I("get.id",0,'intval');

        $wechat = A('Common/Wechat');

        if(empty($this->_openid)){
            $wechat->getWebCode($id);
        } else {
            $exists = $this->wechat_user_exists($this->_openid);
            var_dump($exists);
            if(!$exists){
                $wechat->getWebCode($id);
            }
        }

        echo 'This is a video page'. $id;
    }

    public function del(){
        cookie('openid', null);
        cookie('source', null);
    }

    public function receiveCode(){
        $code=  I("get.code", '');

        if(!empty($code)){
            $wechat = A('Common/Wechat');

            $video_id = I("get.state", '');

            $tokenData = $wechat->getWebAccessToken($code);
            $tokenArr = explode('#', $tokenData);

            if(!empty($tokenArr) && count($tokenArr) > 1){
                $token = $tokenArr[0];
                $openId = $tokenArr[1];
                cookie('openid', $openId, 3600);

                //$source = cookie('source');

                $exists = $this->wechat_user_exists($openId);

                if(!$exists){

                    $user_info = $wechat->getWebUserInfo($token, $openId);

                    if(count($user_info) > 0){
                        //$user_info['source'] = $source;
                        $this->wechat_user_add($user_info);
                    }
                }

                // Is agent user or not
                $cu = $this->isChannelUser($openId);

                if(!empty($video_id)){
                    if($video_id == 'some'){
                        $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Video/Index/some');
                    } else {
                        $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Video/Index/video?id='.$video_id.'&cu='.($cu === true ? 1 : 0));
                    }
                } else {
                    $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Video/Index/video_list');
                }
                header('Location: '. $url);
            } else {
                exit('error: 10003');
            }
        }
    }

    protected function wechat_user_exists($openId){
        $exists = false;

        $wechatUser = D('Video/WechatUser');

        $data = $wechatUser->where(array('openid' => $openId))->find();

        if($data !== null && count($data) > 0)
            $exists = true;

        return $exists;
    }

    protected function isChannelUser($openId){
        $exists = false;

        $wechatUser = D('Video/WechatUser');

        $data = $wechatUser->field('count(openid) num')->where('openid=\'' . $openId . '\' and source != \'\' and real_name != \'\'')->find();

        if(is_array($data) && key_exists('num', $data) && $data['num'] === '0' )
            $exists = true;

        return $exists;
    }

    protected function updateUserLoginTime($openId){
        $wechatUser = D('Video/WechatUser');
        $wechatUser->last_login_time = date('Y-m-d H:i:s');
        $wechatUser->where(array('openid' => $openId))->save();
    }

    protected function setUserSource($openId, $source){
        if(!empty($openId) && !empty($source)) {
            $wechatUser = D('Video/WechatUser');
            $wechatUser->setSource($openId, $source);
        }
    }

    protected function wechat_user_add($userInfo){
        $wechatUser = D('Video/WechatUser');

        $userData = array();
        $userData['openid'] = $userInfo['openid'];
        $userData['nickname'] = $userInfo['nickname'];
        $userData['sex'] = $userInfo['sex'];
        $userData['province'] = $userInfo['province'];
        $userData['city'] = $userInfo['city'];
        $userData['country'] = $userInfo['country'];
        //$userData['source'] = $userInfo['source'];
        $userData['create_time'] = date('Y-m-d H:i:s');

        $num = $wechatUser->add($userData);

        return $num;
    }

    public function complete_info(){
        $retData = array('code' => '-1');

        try {

            $real_name = I('get.real_name', '');
            $sex = I('get.sex', '');
            $age = I('get.age', 0, 'intval');
            $phone = I('get.phone', 0, 'intval');
            $identity = I('get.identity', 0, 'intval');
            $isAgree = I('get.agreement', 0, 'intval');

            if(empty($real_name) || empty($age) || empty($phone) || $phone == 0){
                throw new Exception("error");
            }

            $wechatUser = D('Video/WechatUser');
            $wechatUser->real_name = $real_name;
            $wechatUser->sex = $sex;
            $wechatUser->age = $age;
            $wechatUser->phone = $phone;
            $wechatUser->role_type = $identity;
            if($isAgree === 1)
                $wechatUser->is_agree = 1;

            $num = $wechatUser->where(array('openid' => $this->_openid))->save();

            if($num > 0){
                $retData['code'] = 0;
            }

        } catch (Exception $e){
            $retData['msg'] = $e->getMessage();
        }

        echo json_encode($retData);
    }

    public function video(){
        $id = I("get.id",0,'intval');

//        $source = I("get.source", '');
        $cu = I("get.cu", '');

//        if(!empty($source)) {
//            cookie('source', $source, 3600);
//        }

        $wechat = A('Common/Wechat');

        if(empty($this->_openid)){
            $wechat->getWebCode($id);
        } else {
            $exists = $this->wechat_user_exists($this->_openid);
            if(!$exists){
                $wechat->getWebCode($id);
            }

//            $isCU = $this->isChannelUser($this->_openid);

//            if($isCU)
//                $cu = 1;
//            else
//                $cu = 0;
        }

        $this->updateUserLoginTime($this->_openid);

        if($id < 1){
            $this->error("Video not found!");
        }

        $video = $this->video_model
            ->alias("v")
            ->join("LEFT JOIN __DOCTORS__ do ON v.doctor = do.id")
            ->join("LEFT JOIN __DISEASE__ di ON v.disease = di.id")
            ->field('v.*,do.`name` doctor_name,do.province,do.city,do.hospital,di.disease disease_name')
            ->where('v.id=' . $id . ' and v.`status` = 1')->find();

        if(!is_array($video)){
            $this->error("Video not found!");
        }

        //dump($video);

        $smeta = json_decode($video['smeta'], true);

        $recommend = array();

        if(!empty($video['recommend'])){
            $recommend = $this->video_model->where('id in (' . $video['recommend'] . ')')->select();
        }

        //dump($recommend);

        foreach ($recommend as &$v){
            $v['smeta'] = json_decode($v['smeta'], true);
        }

        //dump($recommend);

        $this->assign('smeta',  $smeta);
        $this->assign('video',  $video);
        $this->assign('recommend',  $recommend);
        //$this->assign('cu',  $cu);
        $this->display();
    }

    public function some(){

        $cu = I("get.cu", '');

        $wechat = A('Common/Wechat');

        if(empty($this->_openid)){
            $wechat->getWebCode('some');
        } else {
            $exists = $this->wechat_user_exists($this->_openid);
            if(!$exists){
                $wechat->getWebCode('some');
            }

            $isCU = $this->isChannelUser($this->_openid);

            if($isCU)
                $cu = 1;
            else
                $cu = 0;
        }

        $this->updateUserLoginTime($this->_openid);

        $doc_data = $this->getAPI('http://manger.yuntehu.com/Interface/getUserDoctor/openId/' . $this->_openid);
//var_dump($doc_data);exit;
        if($doc_data === false){
            $this->redirect('Video/Index/video_list', array(), 0, '页面跳转中...');
        }

        // Compatible with the not regular data return from third-part api
        $doc_data = trim($doc_data);

        $start = strpos($doc_data, '{');

        if($start > 0)
            $doc_data = substr($doc_data, $start);

        $doc_arr = json_decode($doc_data, true);

//        $doc_arr['code'] = 0;
//        $doc_arr['data']['doctor_name'] = 'MFS';

        if(!is_array($doc_arr) || $doc_arr['code'] !== 0){
            $this->redirect('Video/Index/video_list', array(), 0, '页面跳转中...');
        }

        //var_dump($doc_arr['data']);exit;

        $doc_name = $doc_arr['data']['doctor_name'];
//        foreach($doc_data['data'] as $doc){
//            $doc_name = $doc['doctor_name'];
//        }

        $this->setUserSource($this->_openid, $doc_name);

        $video = $this->video_model
            ->alias("v")
            ->join("LEFT JOIN __DOCTORS__ do ON v.doctor = do.id")
            ->join("LEFT JOIN __DISEASE__ di ON v.disease = di.id")
            ->field('v.*,do.`name` doctor_name,do.province,do.city,do.hospital,di.disease disease_name')
            ->where('do.code=\'' . $doc_name . '\' and v.`status` = 1')->order('v.last_modified desc')->find();

//        var_dump($video);
//        var_dump($this->video_model->getLastSql());exit;

        if(!is_array($video)){
            //$this->error("Video not found!");
            $this->redirect('Video/Index/video_list', array(), 0, '页面跳转中...');
        }

        //dump($video);



        $smeta = json_decode($video['smeta'], true);

        $recommend = array();

        if(!empty($video['recommend'])){
            $recommend = $this->video_model->where('id in (' . $video['recommend'] . ')')->select();
        }

        //dump($recommend);

        foreach ($recommend as &$v){
            $v['smeta'] = json_decode($v['smeta'], true);
        }

        //dump($recommend);

        $this->assign('smeta',  $smeta);
        $this->assign('video',  $video);
        $this->assign('recommend',  $recommend);
        $this->assign('cu',  $cu);
        $this->display('video');
    }

    public function question(){
        $this->question_model = D("Portal/Question");

        $id=  I("get.id",0,'intval');

        if($id < 1){
            $this->error("Video not found!");
        }

        $quesRet = $this->video_model->field('questions')->where('id = ' . $id)->find();

        $questionIDs = array();
        if(count($quesRet) > 0){
            $questionIDs = array_filter(explode(',', $quesRet['questions']));
        }

        if(count($questionIDs) < 1){
            $this->error("Question not found!");
        }

        $questions = $this->question_model->where('id in ('. implode(',', $questionIDs) . ') ')->select();

        //dump($questions);

        foreach($questions as &$ques){
            $ques['options'] = $this->optionByQues($ques['id']);
        }

        //dump($questions);

        $this->assign('questions', $questions);
        $this->assign('video_id', $id);
        $this->display();
    }

    protected function optionByQues($ques_id){
        $opts = array();

        if(!empty($ques_id)){
            $opt_model = D('Portal/Option');
            //$opts = $opt_model->where(array('ques_id' => $ques_id, 'status' => '1'))->select();
            $opts = $opt_model->where(array('ques_id' => $ques_id))->select();
        }

        return $opts;
    }

    public function answer() {
        $id=  I("get.id",0,'intval');

        if($id < 1){
            $this->ajaxReturn(array('code' => 1, 'data' => array()), 'JSON');
        }

        $jsonString = trim(file_get_contents('php://input'));

        if($jsonString != ''){
            $data = json_decode($jsonString, true);
            if(is_array($data) && count($data) > 0){

                $answers = $this->getAnswers($id);

                $result_score = 0;
                $ques_num = count($data);
                $right_num = 0;

                if($ques_num > 0) {
                    foreach ($data as $key => $item) {
                        if (array_key_exists($key, $answers) && $item == $answers[$key]) {
                            $right_num++;
                        }
                    }

                    $result_score = $right_num / $ques_num * 100;
                }

                if(strlen($result_score) > 5){
                    $result_score = sprintf("%.2f",$result_score);
                }

                $recordRet = $this->recordUserAnswers($id, $result_score);

                if($recordRet) {
                    $this->ajaxReturn(array('code' => 0, 'data' => array('score' => $result_score)), 'JSON');
                } else {
                    $this->ajaxReturn(array('code' => 1, 'data' => array()), 'JSON');
                }
            } else {
                $this->ajaxReturn(array('code' => 1, 'data' => array()), 'JSON');
            }
        } else {
            $this->ajaxReturn(array('code' => 1, 'data' => array()), 'JSON');
        }
    }

    protected function recordUserAnswers($id, $score = 0){
        $ret = false;

        $userQuesModel = D('Video/UserQuestions');

        if(!empty($id) && $id > 0) {
            $userQuesModel->openid = $this->_openid;
            $userQuesModel->video_id = $id;
            $userQuesModel->score = $score;
            $userQuesModel->create_time = date('Y-m-d H:i:s');
            $num = $userQuesModel->add('', array(), true);

            if($num != false && $num > 0){
                $ret = true;
            }
        }

        return $ret;
    }

    protected function getAnswers($id){
        $answers = array();

        if(!empty($id) && $id > 0) {
            $quesIds = $this->video_model->field('questions')->where(array('id' => $id))->buildSql();
            $optionModel = D('Portal/Option');
            $data = $optionModel->field('ques_id, id opt_id')-> where(array('answer' => 1, 'find_in_set(ques_id,' . $quesIds . ')'))->select();

            if(count($data) > 0){
                foreach($data as $val){
                    $answers[$val['ques_id']] = $val['opt_id'];
                }
            }
        }

        return $answers;
    }

    public function video_list(){

        $video_list = $this->video_model
            ->alias("v")
            ->join("LEFT JOIN __DOCTORS__ do ON v.doctor = do.id")
            ->join("LEFT JOIN __DISEASE__ di ON v.disease = di.id")
            ->field('v.*,do.`name` doctor_name,do.province,do.city,do.hospital,di.disease disease_name')
            ->where(' v.`status` = 1')->select();

        if(!is_array($video_list)){
            $this->error("Video not found!");
        }

        //dump($video_list);

        foreach ($video_list as &$v){
            $v['smeta'] = json_decode($v['smeta'], true);
        }

        //dump($video_list);

        $diseaseData = $this->getAllDisease();

        //dump($diseaseData);

        $doctorData = $this->getAllDoctor();

        //dump($doctorData);

        $this->assign('video_list',  $video_list);
        $this->assign('doctorList', $doctorData);
        $this->assign('diseaseList', $diseaseData);
        $this->display();
    }

    public function search(){
        $keyword = I("get.keyword", '', 'htmlspecialchars');

        $where = '';
        if(!empty($keyword)){
            $where = 'and ( v.title like \'%' . $keyword . '%\' or do.`name` like \'%' . $keyword . '%\' or di.disease like \'%' . $keyword . '%\' )';
        }

        $video_list = $this->video_model
            ->alias("v")
            ->join("LEFT JOIN __DOCTORS__ do ON v.doctor = do.id")
            ->join("LEFT JOIN __DISEASE__ di ON v.disease = di.id")
            ->field('v.*,do.`name` doctor_name,do.province,do.city,do.hospital,di.disease disease_name')
            ->where(' v.`status` = 1 ' . $where)->select();

        if(!is_array($video_list)){
            $this->error("Video not found!");
        }

        //dump($video_list);

        foreach ($video_list as &$v){
            $v['smeta'] = json_decode($v['smeta'], true);
        }

        $this->assign('video_list',  $video_list);
        $this->assign('keyword', $keyword);
        $this->display();
    }

    public function search_list(){

        try {

            $doctor = I("get.doctor", '', 'htmlspecialchars');
            $disease = I("get.disease", '', 'htmlspecialchars');

            $where = ' v.`status` = 1 ';
            if (!empty($doctor)) {
                $where .= 'and do.`name` like \'%' . $doctor . '%\' ';
            }

            if (!empty($disease)) {
                $where .= 'and di.disease like \'%' . $disease . '%\' ';
            }

            $video_list = $this->video_model
                ->alias("v")
                ->join("LEFT JOIN __DOCTORS__ do ON v.doctor = do.id")
                ->join("LEFT JOIN __DISEASE__ di ON v.disease = di.id")
                ->field('v.*,do.`name` doctor_name,do.province,do.city,do.hospital,di.disease disease_name')
                ->where($where)->select();

            if (!is_array($video_list)) {
                throw new Exception("Video not found!");
            }

            //dump($video_list);

            foreach ($video_list as &$v) {
                $v['smeta'] = json_decode($v['smeta'], true);
            }

            $this->ajaxReturn(array('code' => 0, 'data' => $video_list), 'JSON');
        } catch (\Exception $e) {
            $this->ajaxReturn(array('code' => 1, 'data' => array()), 'JSON');
        }
    }

    public function getAllDoctor(){
        return $this->doctor_model->field('distinct `name` doctor_name')->select();
    }

    public function getAllDisease(){
        return $this->disease_model->field('distinct disease')->select();
    }

    public function agreement(){
        $this->display();
    }
}


