<?php
namespace Portal\Model;

use Common\Model\CommonModel;

class DoctorModel extends CommonModel {

    protected $tableName = 'doctors';

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('title', 'require', '标题不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );
    
	protected $_auto = array (
		array('add_time', 'mGetDate', self::MODEL_INSERT, 'callback' ),
		array('last_modified', 'mGetDate',self::MODEL_BOTH, 'callback' )
	);
	
	// 获取当前时间
	public function mGetDate() {
		return time();
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
}