<?php
namespace Admin\Model;
use Think\Model;
/**
 * 权限规则model
 */
class AuthGroupAccess extends BaseModel{
	/**
	 * 根据group_id获取全部用户id
	 * @param  int $group_id 用户组id
	 * @return array         用户数组
	 */
	public function getUidsByGroupId($group_id){
		$user_ids=$this
			->where(array('group_id'=>$group_id))
			->getField('uid',true);
		return $user_ids;
	}
	/**
	 * 获取管理员权限列表
	 */
	public function getAllData(){
		$data=$this
			->field('u.id,u.account,u.state,u.name,aga.group_id,ag.title')
			->alias('aga')
			->join('__ADMIN__ u ON aga.uid=u.id','RIGHT')
			->join('__AUTH_GROUP__ ag ON aga.group_id=ag.id','LEFT')
			->select();
		return $data;
	}
}
