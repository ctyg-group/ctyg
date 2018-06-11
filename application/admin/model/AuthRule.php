<?php
namespace app\admin\model;
use think\Db;
use think\Loader;
use think\Model;
use modelapi\Data;
class AuthRule extends BaseModel {
    /**
     * 获取全部数据
     * @param  string $type  tree获取树形结构 level获取层级结构
     * @param  string $order 排序方式
     * @return array         结构数据
     */
    public function getTreeData($type='tree',$order='',$name='name',$child='id',$parent='pid'){

        // 判断是否需要排序
        if(empty($order)){
            $data= DB::name('AuthRule')->select();
        }else{
            $data=DB::name('AuthRule')->order($order.' is null,'.$order)->select();
        }
        // 获取树形或者结构数据
        if($type=='tree'){
            $datas=new Data();
            $datas = $datas->tree($data,$name,$child,$parent);
        }elseif($type="level"){
//            $data=new Data::channelLevel($data,0,'&nbsp;',$child);
        }
        return $datas;
    }


    /**
     * 删除数据
     * @param	array	$map	where语句数组形式
     * @return	boolean			操作是否成功
     */
    public function deleteData($map){
        $count=DB::name('AuthRule')
            ->where(array('pid'=>$map['id']))
            ->count();
        if($count!=0){
            return false;
        }
        $result=DB::name('AuthRule')->where($map)->delete();
        return $result;
    }
}

?>