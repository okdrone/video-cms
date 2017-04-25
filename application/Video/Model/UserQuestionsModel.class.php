<?php
namespace Video\Model;

use Common\Model\CommonModel;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/25/17
 * Time: 11:19 PM
 */
class UserQuestionsModel extends CommonModel {

    protected function _before_write(&$data) {
        parent::_before_write($data);
    }
}