<?php
namespace app\index\controller;     
use think\Controller;
use app\common\model\Subject;
use think\Request;
use think\Db;
use app\common\model\Detail;
class SubjectController extends Index1Controller
{
    public function index()
    {
        
        // 获取查询信息
        $name = Request::instance()->get('name');
        
        //分页数目
    	$pageSize= 2;
        
        // 实例化
    	$subject = new Subject;
         
        // 按条件查询数据并调用分页
    	$subjects= $subject->where('name', 'like', '%' . $name . '%')->paginate($pageSize, false, [
            'query'=>[
                'name' => $name,
                ],
            ]); 
    	$Subjects = Db::name('subject')->select();
        $this->assign('Subjects',$Subjects);

    	$this->assign('subjects',$subjects);

        // 将数据返回给用户
        return $this->fetch();
    }
     public function add()
    {
        $Subjects = Db::name('subject')->select();
        $this->assign('Subjects',$Subjects);
        return $this->fetch();
    }

    public function insert()
    {
        $postData = Request::instance()->post();

        $subject = new Subject;

        $subject->name = $postData['name'];

       $result= $subject->validate()->save();

        if (false === $result)
        {   
            // 验证未通过，发生错误
            $message = '新增失败:' . $subject->getError();
            return $this->error($message);
        } else {
            // 提示操作成功，并跳转至管理列表
            return $this->success( $subject->name . '新增成功。', url('index'));
        } 
    }
    public function save()
    {
        $postData = Request::instance()->post();

        $subject =Subject::get($postData['id']);

        $subject->name = $postData['name'];

       $result= $subject->validate()->save();

        if (false === $result)
        {   
            // 验证未通过，发生错误
            $message = '新增失败:' . $subject->getError();
            return $this->error($message);
        } else {
            // 提示操作成功，并跳转至管理列表
            return $this->success('编辑成功。', url('index'));
        } 
    }

    public function edit()
    {
        // 获取传入ID
        $id = Request::instance()->param('id/d'); 

        // 判断是否成功接收
        if (0 === $id || is_null($id) ) {
             return $this->error('未获取到ID信息');
        }

        // 获取要删除的对象
        $subject = Subject::get($id);

        // 要删除的对象不存在
        if (is_null($subject)) {
            return $this->error('不存在id为' . $id . '的科目，删除失败');
        }
        $Subjects = Db::name('subject')->select();
        $this->assign('Subjects',$Subjects);
        $this->assign('subject1',$subject);
        return $this->fetch();
    }

    public function delete()
    {
        
        // 获取传入ID
        $id = Request::instance()->param('id/d'); 

        // 判断是否成功接收
        if (0 === $id || is_null($id) ) {
             return $this->error('未获取到ID信息');
        }

        // 获取要删除的对象
        $subject = Subject::get($id);

        // 要删除的对象不存在
        if (is_null($subject)) {
            return $this->error('不存在id为' . $id . '的科目，删除失败');
        }

        // 删除对象
        if (!$subject->delete()) {
            return $this->error('删除失败:' . $subject->getError());
        }
         //将对象的记录删除
        $details = Db::name('detail')->select();

        foreach ($details as $detail) {
        	if($detail['subject']===$id)
        	{
        		$detail1 = Detail::get($detail['id']);
        		$detail1->delete();
        	}
        }
        return $this->success('删除成功',url('index')); 
    }
}

