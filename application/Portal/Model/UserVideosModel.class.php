<?php
namespace Portal\Model;

use Common\Model\CommonModel;

class UserVideosModel extends CommonModel {
    
	protected $_auto = array (

	);

	protected function _before_write(&$data) {
		parent::_before_write($data);
	}

	public function visitsByIds($ids){
	    return $this->field('video_id, count(id) num')->where(array('video_id' => array('IN', $ids), 'action' => 'open'))->group('video_id')->select();
    }

    public function playByIds($ids){
        return $this->field('video_id, count(id) num')->where(array('video_id' => array('IN', $ids), 'action' => 'play'))->group('video_id')->select();
    }
}