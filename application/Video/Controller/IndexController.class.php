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
/**
 * 首页
 */
class IndexController extends WechatController {

    protected $video_model;
    protected $doctor_model;
    protected $disease_model;

    function _initialize() {
        parent::_initialize();
        $this->video_model = D("Portal/Video");
        $this->doctor_model = D("Portal/Doctor");
        $this->disease_model = D("Portal/Disease");
    }

    //首页
	public function index() {

        $videos = $this->video_model->where('`status` <> 3')->select();

        dump($videos);

    	$this->display();
    }

    public function video(){
        $id=  I("get.id",0,'intval');

        if($id < 1){
            $this->error("Video not found!");
        }

        $video = $this->video_model
            ->alias("v")
            ->join("LEFT JOIN __DOCTORS__ do ON v.doctor = do.id")
            ->join("LEFT JOIN __DISEASE__ di ON v.disease = di.id")
            ->field('v.*,do.`name` doctor_name,do.province,do.city,do.hospital,di.disease disease_name')
            ->where('v.id=' . $id . ' and v.`status` <> 3')->find();

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
        $this->display();
    }

    public function video_list(){

        $video_list = $this->video_model
            ->alias("v")
            ->join("LEFT JOIN __DOCTORS__ do ON v.doctor = do.id")
            ->join("LEFT JOIN __DISEASE__ di ON v.disease = di.id")
            ->field('v.*,do.`name` doctor_name,do.province,do.city,do.hospital,di.disease disease_name')
            ->where(' v.`status` <> 3')->select();

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

    public function getAllDoctor(){
        return $this->doctor_model->field('distinct `name` doctor_name')->select();
    }

    public function getAllDisease(){
        return $this->disease_model->field('distinct disease')->select();
    }
}


