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
 * User active data Collection
 */
class CollectController extends WechatController {

    protected $userVideoModel;

    protected $_openid = '';

    function _initialize() {
        parent::_initialize();
        $this->userVideoModel = D("Video/UserVideos");

        $this->_openid = cookie('openid');

        // Just for test
        $this->_openid = 'otyIXt8PtLekJxF3eskU0GyNDTYI';
    }

    public function video(){
        $id = I("get.id",0,'intval');
        $action = I("get.action",'');
        $time = I("get.time", 0.0,'float');

        try {

            if ($id == 0 || !in_array($action, array('play', 'pause', 'end'))) {
                throw new Exception('The input data invalid!');
            }

            $time = sprintf('%.2f', $time);

            $this->userVideoModel->openid = $this->_openid;
            $this->userVideoModel->video_id = $id;
            $this->userVideoModel->action = $action;
            $this->userVideoModel->video_time = $time;
            $this->userVideoModel->add();

        } catch (Exception $e){
            $e->getMessage();
        }

        echo 'ok';
    }

}


