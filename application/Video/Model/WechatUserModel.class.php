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

    public function setSource($openId, $source){
        $num = $this->field('count(`source`) num')->where('openid=\''.$openId.'\' and `source` is not null')->find();
        var_dump($num);
        var_dump($num['num']);exit;
        if($num != false && $num['num'] > 0){
            $this->where('openid=\'' . $openId . '\'')->save(array('source' => $source));
        }
    }
}