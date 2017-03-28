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

    function _initialize() {
        parent::_initialize();
        $this->video_model = D("Portal/Video");
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

        $video = $this->video_model->where('id=' . $id . ' and `status` <> 3')->find();

        if(!is_array($video)){
            $this->error("Video not found!");
        }

        //dump($video);

        $recommend = array();

        if(!empty($video['recommend'])){
            $recommend = $this->video_model->where('id in (' . $video['recommend'] . ')')->select();
        }

        //dump($recommend);

        $this->assign('video',  $video);
        $this->assign('recommend',  $recommend);
        $this->display();
    }

}


