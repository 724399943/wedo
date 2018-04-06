<?php
namespace Shop\Model;
use Think\Model;

class AgentCategoryRelevanceModel extends Model {
	/**
	 * [insertToTable 插入数据库]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)          2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $agentId     [description]
	 * @param     [type]        $categoryids [description]
	 * @return    [type]                     [description]
	 */
	public function insertToTable($agentId, $categoryids) {
		if ( !is_array($categoryids) ) {
			$categoryids = explode(',', $categoryids);
		}
		foreach ($categoryids as $key => $value) {
            $temp[] = array(
                'agent_id'      => $agentId,
                'category_id'   => $value
            );
        }
        if ( $this->addAll($temp) !== false ) {
        	return true;
        } else {
        	return false;
        }
	}

	/**
	 * [updateToTable 更新数据库]
	 * @author kofu <418382595@qq.com>
	 * @copyright Copyright (c)          2017          Xcrozz (http://www.xcrozz.com)
	 * @param     [type]        $agentId     [description]
	 * @param     [type]        $categoryids [description]
	 * @return    [type]                     [description]
	 */
	public function updateToTable($agentId, $categoryids) {
		$categoryids = explode(',', $categoryids);
		// 删除旧分类关联
		$where['agent_id'] = $agentId;
		$oldCategoryIds = $this->where($where)->field('`id`, `category_id`')->select();
		$oldData = array();
		foreach ($oldCategoryIds as $key => $value) {
			if ( !in_array($value['category_id'], $categoryids) ) {
				$this->delete($value['id']);
			} else {
				$oldData[] = $value['category_id'];
			}
		}

		foreach ($categoryids as $key => $value) {
			if ( in_array($value, $oldData) ) {
				unset($categoryids[$key]);
			}
		}
		// 添加新分类关联
		if ( empty($categoryids) ) {
			$result = true;
		} else {
			$result = $this->insertToTable($agentId, $categoryids);
		}
		return $result;
	}

	public function getCategoryIds($agentId) {
		$categoryids = '';
		$where['agent_id'] = $agentId;
		$list = $this->where($where)->field('`category_id`')->select();
		foreach ($list as $key => $value) {
			$categoryids .= $value['category_id'] . ',';
		}
		return $categoryids;
	}
}
