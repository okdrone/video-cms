<?php
namespace Video\Model;

use Common\Model\CommonModel;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/17/17
 * Time: 11:19 PM
 */
class WechatUserModel extends CommonModel {

    protected $tableName = 'wechat_users';

    protected function _before_write(&$data) {
        parent::_before_write($data);
    }
}