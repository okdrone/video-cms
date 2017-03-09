<?php
namespace Portal\Model;

use Common\Model\CommonModel;

class DiseaseModel extends CommonModel {

    //protected $tableName = '';

    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('departments', 'require', '科室不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('disease', 'require', '疾病不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH)
    );
    
	protected $_auto = array (

	);

	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
}