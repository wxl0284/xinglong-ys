<?php
/*此控制器：对各望远镜的固定属性进行配置*/
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Db;

class Atconfig extends Base
{
    //定义存储上传文件的路径
    protected $file_path = ROOT_PATH . 'public' . DS . 'uploads/';

    /*获取转台配置项表单 存入表atlist中*/
    public function gimbal_config()
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        //$postData = input('maxAxis3Speed');
        //$file = request()->file('instruction');
        //dump($postData);
        //dump($file);
        // $postData = input();

        // if ( !isset($postData['maxAxis3Speed']) )
        // {
        //     $postData['maxAxis3Speed'] = null;
        // }
        // dump($postData['maxAxis3Speed']);

        //处理表单数据，如果表单中：无某项数据或某项数据为空字符串，则将$postData中某项数据置为NULL
        // if ( $postData['ip'] === '')
        // {
        //     $postData['ip'] = null;
        // }
        $postData = input();
        //halt(isset($postData['maxaxis3speed']));
        
        //处理表单数据，若无轴3，则轴3相关input框禁用，则将$postData轴3相关之置为空字符串
        if ( $postData['haveaxis3'] == 0 && !isset($postData['maxaxis3speed']) )
        {
            $postData['maxaxis3speed'] = '';    //轴3最大速度
            $postData['maxaxis3acceleration'] = ''; //轴3最大加速度
            $postData['axis3parkposition'] = '';    //轴3复位位置
        }

        //定义错误提示
        $errMsg = '';

        $res = Db::table('atlist')->where('id', $postData['id'])->update($postData);

        if ( !$res )
        {
            $errMsg += '转台配置存数据库失败!<br>';
        }
        //处理上传文件
        $dir = 'at' . $postData['id']; //一个望远镜建1个目录，如at1, at2.....
        /*处理测试报告*/
        $reportFile = $this->request->file('report'); //获取测试报告的上传数据
        if ( $reportFile !== null ) //有文件被上传
        {
            //验证上传文件 暂时不做验证
		    // $result = $this->validate(
            // ['file' => $file],
            // ['file' => 'file|require|fileExt:txt|fileSize:4096000000|fileMime:text/plain'],
            // ['file.require' => '请选择上传文件',
            //  'file.fileExt' => '文件后缀名必须为txt',
            //  'file.fileSize' => '文件大小超出限制',
            //  'file.fileMime' => '文件格式或文件内容不符合要求']);
            // 移动到框架应用根目录/public/uploads/ 目录下
            //$dir = date('Ymd', time());
            //$name = time(). session('login');

            //将上传文件命名为: 出厂测试报告
            $fileName = iconv ('UTF-8', 'GBK', '出厂测试报告');
            //如果已有此名字的文件，则先删除
            if ( file_exists($this->file_path . "/$dir/$fileName") )
            {
                $delReport = unlink($this->file_path ."/$dir/$fileName");
                if ($delRport === false)
                {
                    return '删除原测试报告失败!';
                }
            }
        
            //将新上传的测试报告移至指定目录
            $info = $reportFile->move($this->file_path."/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += '测试报告上传失败!<br>';
            }
        }/*处理测试报告 结束*/

        /*接下来 处理说明书*/
        $instruFile = $this->request->file('instruction'); //获取测试报告的上传数据
        if ( $instruFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明书
            $fileName = iconv ('UTF-8', 'GBK', '说明书');
            //如果已有此名字的文件，则先删除
            if ( file_exists($this->file_path . "/$dir/$fileName") )
            {
                $delReport = unlink($this->file_path . "/$dir/$fileName");
                if ( $delRport === false )
                {
                    return '删除原说明书失败!';
                }
            }

            //将新上传的说明书移至指定目录
            $info = $instruFile->move($this->file_path . "/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += '说明书上传失败!';
            }
        }/*处理说明书 结束*/

        if ($errMsg !== '')
        {
            return $errMsg;
        }else{//数据入库 和 文件上传都ok 获取已上传的文件名以json格式返回页面
            //return '转台配置ok!';
            $res = scandir ($this->file_path."/$dir");

            if ( $res !== false && count($res) > 2 )
            {
                unset ($res[0], $res[1]); //删除前2个数据
                foreach ( $res as $k)
                {
                    $result['file'][] = iconv('GBK', 'UTF-8', $k);  //将文件名转为utf-8
                }
            }

            $result['msg'] = '转台配置ok!';
            return json_encode ($result);
        }
    }/*获取转台配置项表单 存入表atlist中 结束*/
}