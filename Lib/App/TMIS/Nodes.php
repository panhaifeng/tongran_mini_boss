<?php
/////////////////////////////////////////////////////////////////////////////
// 这个文件是 FleaPHP 项目的一部分
//
// Copyright (c) 2005 - 2006 FleaPHP.org (www.fleaphp.org)
//
// 要查看完整的版权信息和许可信息，请查看源代码中附带的 COPYRIGHT 文件，
// 或者访问 http://www.fleaphp.org/ 获得详细信息。
/////////////////////////////////////////////////////////////////////////////

/**
 * 定义 Table_Nodes 类
 *
 * @copyright Copyright (c) 2005 - 2006 FleaPHP.org (www.fleaphp.org)
 * @author 廖宇雷 dualface@gmail.com
 * @package Example
 * @subpackage SHOP
 * @version $Id: Nodes.php 788 2007-04-03 05:43:58Z dualface $
 */

// {{{ includes
FLEA::loadClass('TMIS_TableDataGateway');
// }}}

/**
 * Table_Nodes 用“改进型先根遍历算法”在数据库中存储层次化的数据（通常所说的无限分类）
 *
 * 由于“改进型先根遍历算法”要求所有节点都是唯一一个根节点的子节点。
 * 所以 Table_Nodes 假定一个名为“_#_ROOT_NODE_#_”的节点为唯一的根节点。
 *
 * 应用程序在调用 Table_Nodes::create() 创建第一个节点时，会自动
 * 判断根节点是否存在，并创建根节点。
 *
 * 对于应用程序来说，“_#_ROOT_NODE_#_”节点是不存在的。所以，应用程序
 * 可以创建多个父节点 ID 为 0 的“顶级节点”。这些顶级节点实际上就是
 * “_#_ROOT_NODE_#_”节点的直接子节点。
 *
 * @package Example
 * @subpackage SHOP
 * @author 廖宇雷 dualface@gmail.com
 * @version 1.0
 */
class TMIS_Nodes extends FLEA_Db_TableDataGateway
{
    /**
     * 数据表名称
     *
     * @var string
     */
    var $tableName = 'nodes';

    /**
     * 主键字段名
     *
     * @var string
     */
    var $primaryKey = 'node_id';

	/**
     * 主键字段名
     *
     * @var string
     */
    var $primaryName = 'node_name';

	/**
     * 左右结点和父结点字段名定义
     *update by jeff 2007-4-24
     * @var string
     */
    var $_leftNodeFieldName = 'leftId';
	var $_rightNodeFieldName = 'rightId';
	var $_parentNodeFieldName = 'parentId';

    /**
     * 根节点名
     *
     * @var string
     */
    var $_rootNodeName = '_#_ROOT_NODE_#_';
	
	/**
     * 取得根节点
     *     
     */
    function getRootNode() {
		return parent::find(1);
	}
	
    /**
     * 添加一个节点，返回该节点的 ID
     *
     * @param array $node
     * @param int $parentId
     *
     * @return int
     */
    function create($node, $parentId=null) {
        $parentId = (int)$parentId;		
        if ($parentId) {
            $parent = parent::find($parentId);			
            if (!$parent) {
                // 指定的父节点不存在
                FLEA::loadClass('Exception_NodeNotFound');
                __THROW(new Exception_NodeNotFound($parentId));
                return false;
            }
        } else {
            // 如果 $parentId 为 0 或 null，则创建一个顶级节点
            $parent = parent::find(array($this->primaryName => $this->_rootNodeName));
            if (!$parent) {
                // 如果根节点不存在，则自动创建
                $parent = array(
                    $this->primaryName => $this->_rootNodeName,
                    $this->_leftNodeFieldName => 1,
                    $this->_rightNodeFieldName => 2,
                    $this->_parentNodeFieldName => -1,
                );
                if (!parent::create($parent)) {
                    return false;
                }
            }
            // 确保所有 _#_ROOT_NODE_#_ 的直接字节点的 ParentId 都为 0
            $parent[$this->primaryKey] = 0;
        }

        // 根据父节点的左值和右值更新数据
        $sql = "UPDATE {$this->fullTableName} SET {$this->_leftNodeFieldName} = {$this->_leftNodeFieldName} + 2 " .
               "WHERE {$this->_leftNodeFieldName} >= {$parent[$this->_rightNodeFieldName]}";
        $this->dbo->execute($sql);
        $sql = "UPDATE {$this->fullTableName} SET {$this->_rightNodeFieldName} = {$this->_rightNodeFieldName} + 2 " .
               "WHERE {$this->_rightNodeFieldName} >= {$parent[$this->_rightNodeFieldName]}";
        $this->dbo->execute($sql);

        // 插入新节点记录
        $node = array_merge($node,array(
            $this->primaryName => $node[$this->primaryName],
            $this->_leftNodeFieldName => $parent[$this->_rightNodeFieldName],
            $this->_rightNodeFieldName => $parent[$this->_rightNodeFieldName] + 1,
            $this->_parentNodeFieldName => $parent[$this->primaryKey],
        ));
        return parent::create($node);
    }

    /**
     * 更新节点信息
     *
     * @param array $node
     *
     * @return boolean
     */
    function update($node) {
        unset($node[$this->_leftNodeFieldName]);
        unset($node[$this->_rightNodeFieldName]);
        unset($node[$this->_parentNodeFieldName]);
        return parent::update($node);
    }

    /**
     * 删除一个节点及其子节点树
     *
     * @param array $node
     *
     * @return boolean
     */
    function remove($node) {
        $span = $node[$this->_rightNodeFieldName] - $node[$this->_leftNodeFieldName] + 1;
        $sql = "DELETE FROM {$this->fullTableName} " .
               "WHERE {$this->_leftNodeFieldName} >= {$node[$this->_leftNodeFieldName]} " .
               "AND {$this->_rightNodeFieldName} <= {$node[$this->_rightNodeFieldName]}";
        if (!$this->dbo->execute($sql)) {
            return false;
        }

        $sql = "UPDATE {$this->fullTableName} " .
               "SET {$this->_leftNodeFieldName} = {$this->_leftNodeFieldName} - {$span} " .
               "WHERE {$this->_leftNodeFieldName} > {$node[$this->_rightNodeFieldName]}";
        if (!$this->dbo->execute($sql)) {
            return false;
        }

        $sql = "UPDATE {$this->fullTableName} " .
               "SET {$this->_rightNodeFieldName} = {$this->_rightNodeFieldName} - {$span} " .
               "WHERE {$this->_rightNodeFieldName} > {$node[$this->_rightNodeFieldName]}";
        if (!$this->dbo->execute($sql)) {
            return false;
        }
        return true;
    }

    /**
     * 删除一个节点及其子节点树
     *
     * @param int $nodeId
     *
     * @return boolean
     */
    function removeByPkv($nodeId) {
        $node = parent::find((int)$nodeId);
        if (!$node) {
            js_alert(
				'结点不存在!',
				'window.history.go(-1)',
				''
			);
        }
        return $this->remove($node);
    }

    /**
     * 返回根节点到指定节点路径上的所有节点
     *
     * 返回的结果不包括“_#_ROOT_NODE_#_”根节点各个节点同级别的其他节点。
     * 结果集是一个二维数组，可以用 array_to_tree() 函数转换为层次结构（树型）。
     *
     * @param array $node
     *
     * @return array
     */
    function getPath($node) {
		
        $conditions = "{$this->_leftNodeFieldName} < {$node[$this->_leftNodeFieldName]} AND " .
                      "{$this->_rightNodeFieldName} > {$node[$this->_rightNodeFieldName]}";
        $sort = $this->_leftNodeFieldName . ' ASC';
        $rowset = $this->findAll($conditions, $sort);
        if (is_array($rowset)) {
            array_shift($rowset);
        }
        return $rowset;
    }

    /**
     * 返回指定节点的直接子节点
     *
     * @param array $node
     *
     * @return array
     */
    function getSubNodes($node) {
		if (is_array($node)) $pkv = $node[$this->primaryKey];
		else $pkv = $node;
        $conditions = "{$this->_parentNodeFieldName} = '$pkv'";
        $sort = $this->_leftNodeFieldName . ' ASC';
        return $this->findAll($conditions, $sort);
    }

    /**
     * 返回指定节点为根的整个子节点树
     *
     * @param array $node
     *
     * @return array
     */
    function getSubTree($node = NULL) {
		if ($node == NULL) $node = $this->getRootNode();		
        $conditions = "{$this->_leftNodeFieldName} BETWEEN {$node[$this->_leftNodeFieldName]} " .
                      "AND {$node[$this->_rightNodeFieldName]}";
        $sort = $this->_leftNodeFieldName . ' ASC';
        return $this->findAll($conditions, $sort);
    }

    /**
     * 获取指定节点同级别的所有节点
     *
     * @param array $node
     *
     * @return array
     */
    function getCurrentLevelNodes($node) {
        $conditions = "{$this->_parentNodeFieldName} = {$node[$this->_parentNodeFieldName]}";
        $sort = $this->_leftNodeFieldName . ' ASC';
        return $this->findAll($conditions, $sort);
    }

    /**
     * 取得所有节点
     *
     * @return array
     */
    function getAllNodes() {
        return parent::findAll('{$this->_leftNodeFieldName} > 1', $this->_leftNodeFieldName . ' ASC');
    }

    /**
     * 获取所有顶级节点（既 _#_ROOT_NODE_#_ 的直接子节点）
     *
     * @return array
     */
    function getAllTopNodes() {
        $conditions = "{$this->_parentNodeFieldName} = 0";
        $sort = $this->_leftNodeFieldName . ' ASC';
        return $this->findAll($conditions, $sort);
    }

	/**
     * 获取所有顶级节点（既 _#_ROOT_NODE_#_ 的直接子节点）
     *
     * @return array
     */
    function getAllBottomNodes() {
        $conditions = "{$this->_leftNodeFieldName}+1 = {$this->_rightNodeFieldName}";
        $sort = $this->_leftNodeFieldName . ' ASC';
        return $this->findAll($conditions, $sort);
    }

    /**
     * 计算所有子节点的总数
     *
     * @param array $node
     *
     * @return int
     */
    function calcAllChildCount($node) {
		//echo "asdf";exit;
		//dump($node);exit;
        return intval(($node[$this->_rightNodeFieldName] - $node[$this->_leftNodeFieldName] - 1) / 2);
    }



	 /**
     * 功能：移动节点（SL，SR）->（TL，TR）
     *
     * @param array $sourceNode
     * @param array $targetNode
     *
     * @return boolean
     */
    function moveNodeAndChild($sourceNode,$targetNode)  {
		//$this->calcAllChildCount($sourceNode);
        $childCount = $this -> calcAllChildCount($sourceNode);
        $targetChild = $this -> calcAllChildCount($targetNode);
        $addNum1 = ( $childCount + 1 ) * 2 ;

		$conditions = "{$this->_leftNodeFieldName} BETWEEN {$sourceNode[$this->_leftNodeFieldName]} " .
                      "AND {$sourceNode[$this->_rightNodeFieldName]}";
        $sort = "{$this->_leftNodeFieldName} ASC";
		//echo $conditions;exit;
        $sourceTree = $this->findAll($conditions, $sort); 
        foreach ($sourceTree as $a_node){
                        $sourceId[] = $a_node[$this->primaryKey];
        }
        if(isset($sourceId) && is_array($sourceId)){
                        $sourceQStr = implode(",",$sourceId);
        }
        if(($sourceNode[$this->_leftNodeFieldName]<$targetNode[$this->_leftNodeFieldName]) && ($sourceNode[$this->_rightNodeFieldName]<$targetNode[$this->_rightNodeFieldName])){
                        //parent to brother and S<'T
            $sql_left = "update {$this->fullTableName} SET
						{$this->_leftNodeFieldName} = {$this->_leftNodeFieldName} - ".$addNum1."
                        where {$this->_leftNodeFieldName} > {$sourceNode[$this->_rightNodeFieldName]}
                        and {$this->_leftNodeFieldName} < {$targetNode[$this->_rightNodeFieldName]}";

            $sql_right = "update {$this->fullTableName} SET
			{$this->_rightNodeFieldName} = {$this->_rightNodeFieldName} - ".$addNum1."
                         where {$this->_rightNodeFieldName} > {$sourceNode[$this->_rightNodeFieldName]}
                         and {$this->_rightNodeFieldName} < {$targetNode[$this->_rightNodeFieldName]}";

            $addNum = $targetNode[$this->_rightNodeFieldName] - $sourceNode[$this->_rightNodeFieldName]-1;
            $sql_stree = "UPDATE {$this->fullTableName} SET ".
                        "{$this->_leftNodeFieldName} = {$this->_leftNodeFieldName} + " . $addNum .
                        " , {$this->_rightNodeFieldName} = {$this->_rightNodeFieldName} + " . $addNum .
                        " WHERE " . $this->primaryKey . " in (".$sourceQStr.")";
        } elseif (($sourceNode[$this->_leftNodeFieldName]<$targetNode[$this->_leftNodeFieldName]) && ($sourceNode[$this->_rightNodeFieldName]>$targetNode[$this->_rightNodeFieldName])){
            //parent move to child . not allow
            return false;
        } elseif (($sourceNode[$this->_leftNodeFieldName]>$targetNode[$this->_leftNodeFieldName]) && ($sourceNode[$this->_rightNodeFieldName]<$targetNode[$this->_rightNodeFieldName])){
            //move to parents
            $sql_left = "update {$this->fullTableName} SET
						{$this->_leftNodeFieldName} = {$this->_leftNodeFieldName} - ".$addNum1."
                        where {$this->_leftNodeFieldName} > {$sourceNode[$this->_rightNodeFieldName]}
                        and {$this->_leftNodeFieldName} < {$targetNode[$this->_rightNodeFieldName]}";

            $sql_right = "update {$this->fullTableName} SET
			{$this->_rightNodeFieldName} = {$this->_rightNodeFieldName} - ".$addNum1."
                        where {$this->_rightNodeFieldName} > {$sourceNode[$this->_rightNodeFieldName]}
                        and {$this->_rightNodeFieldName} < {$targetNode[$this->_rightNodeFieldName]}";

            $addNum = $targetNode[$this->_rightNodeFieldName] - $sourceNode[$this->_rightNodeFieldName] - 1  ;
            $sql_stree = "UPDATE {$this->fullTableName} SET ".
                                "{$this->_leftNodeFieldName} = {$this->_leftNodeFieldName} + " . $addNum .
                                " , {$this->_rightNodeFieldName} = {$this->_rightNodeFieldName} + " . $addNum .
                                " WHERE " . $this->primaryKey . " in (".$sourceQStr.")";
        } elseif (($sourceNode[$this->_leftNodeFieldName]>$targetNode[$this->_leftNodeFieldName]) && ($sourceNode[$this->_rightNodeFieldName]>$targetNode[$this->_rightNodeFieldName])){
            //move to brother and S>T
            $sql_left = "update {$this->fullTableName} SET
			{$this->_leftNodeFieldName} = {$this->_leftNodeFieldName} + ".$addNum1."
                        where {$this->_leftNodeFieldName} > {$targetNode[$this->_rightNodeFieldName]}
                        and {$this->_leftNodeFieldName} < {$sourceNode[$this->_leftNodeFieldName]}";

            $sql_right = "update {$this->fullTableName} SET
			{$this->_rightNodeFieldName} = {$this->_rightNodeFieldName} + ".$addNum1."
                        where {$this->_rightNodeFieldName} >= {$targetNode[$this->_rightNodeFieldName]}
                        and {$this->_rightNodeFieldName} < {$sourceNode[$this->_leftNodeFieldName]}";

            //$addNum = $sourceNode[$this->_leftNodeFieldName] - $targetNode[$this->_rightNodeFieldName]-1  ;
			$addNum = $sourceNode[$this->_leftNodeFieldName] - $targetNode[$this->_rightNodeFieldName];
            $sql_stree = "UPDATE {$this->fullTableName} SET ".
                         "{$this->_leftNodeFieldName} = {$this->_leftNodeFieldName} - " . $addNum .
                         " , {$this->_rightNodeFieldName} = {$this->_rightNodeFieldName} - " . $addNum .
                         " WHERE " . $this->primaryKey . " in (".$sourceQStr.")";
        }
        //update parent_id
        $sql3 = "update {$this->fullTableName} SET {$this->_parentNodeFieldName}=".$targetNode[$this->primaryKey]." where " . $this->primaryKey . " = ".$sourceNode[$this->primaryKey];
		//echo $sql_left."<br>".$sql_right."<br>".$sql_stree."<br>".$sql3;exit;
        if($this->dbo->execute($sql_left)&&$this->dbo->execute($sql_right)&&$this->dbo->execute($sql_stree)&&$this->dbo->execute($sql3)){
            return true;
        }
        return false;
    }
}
