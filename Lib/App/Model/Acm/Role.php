<?php
FLEA::loadClass('TMIS_TableDataGateway');
class Model_Acm_Role extends TMIS_TableDataGateway {
	var $tableName = 'acm_roledb';
	var $primaryKey = 'id';
	var $primaryName = 'roleName';
	var $manyToMany = array(
		array (
			'tableClass' => 'Model_Acm_User' ,
			'mappingName' => 'users',
			'joinTable' => 'acm_user2role',
			'foreignKey' => 'roleId',
			'assocForeignKey' => 'userId'
		),
//		array (
//			'tableClass' => 'Model_Acm_Func' ,
//			'mappingName' => 'funcs',
//			'joinTable' => 'acm_func2role',
//			'foreignKey' => 'roleId',
//			'assocForeignKey' => 'funcId'
//		)
	);
	function getFuncs($funcId) {
		$arr = $this->find($funcId);
		return $arr['funcs'];
	}

	/**
	 * 获取select options使用的数据
	 * Time：2018/12/13 13:32:27
	 * @author li
	 * @param 参数类型
	 * @return 返回值类型
	*/
	public function getOptions()
	{
		$list = $this->findAll();

		$rows = array();
		foreach ($list as $key => &$v) {
			$rows[] = array(
				'text'  =>$v['roleName'],
				'value' =>$v['id'],
			);
		}

		return $rows;
	}
}
?>