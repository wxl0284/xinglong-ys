<?php
/*此控制器：对各望远镜的固定属性进行配置*/
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Db;

class Atconfig extends Base
{
    //定义存储上传文件的路径
    protected $file_path = ROOT_PATH . 'public' . DS . 'uploads';

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
        $dir = 'gimbal' . $postData['id']; //每个望远镜的每个设备建1个目录，如gimbal1, gimbal2.....
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
            $fileName = '出厂测试报告';
            $fileName = iconv ('UTF-8', 'GBK', $fileName);
            //如果已有此名字的文件，则先删除
            if ( file_exists($this->file_path . "/$dir/$fileName") )
            {
                $delReport = unlink($this->file_path ."/$dir/$fileName");
                if ($delRport === false)
                {
                    return "删除原{$fileName}失败!";
                }
            }
        
            //将新上传的测试报告移至指定目录
            $info = $reportFile->move($this->file_path."/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "{$fileName}上传失败!<br>";
            }
        }/*处理测试报告 结束*/

        /*接下来 处理说明书*/
        $instruFile = $this->request->file('instruction'); //获取测试报告的上传数据
        if ( $instruFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明书
            $fileName = '说明书';
            $fileName = iconv ('UTF-8', 'GBK', $fileName);
            //如果已有此名字的文件，则先删除
            if ( file_exists($this->file_path . "/$dir/$fileName") )
            {
                $delReport = unlink($this->file_path . "/$dir/$fileName");
                if ( $delRport === false )
                {
                    return "删除原{$fileName}失败!";
                }
            }

            //将新上传的说明书移至指定目录
            $info = $instruFile->move($this->file_path . "/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "{$fileName}上传失败!";
            }
        }/*处理说明书 结束*/

        if ($errMsg !== '')
        {
            return $errMsg;
        }else{//数据入库 和 文件上传都ok 获取已上传的文件名以json格式返回页面
            ////若存放文件的目录还未创建 则先创建之
            if ( !file_exists($this->file_path."/$dir") )
            {
                mkdir ($this->file_path."/$dir");
            }

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
    }/*获取转台配置项表单 存入表ccdconf中 结束*/

    public function ccd_config()
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
        //halt($postData['readoutmode']);
       //处理表单数据中若干个 复选框
       /*处理 读出模式*/
       if ( isset($postData['readoutmode']) ) //有此数据
       {
           $postData['readoutmode'] = implode ('#', $postData['readoutmode']);
       }/*处理 读出模式 结束*/

       /*处理 增益模式*/
       if ( isset($postData['gainmode']) ) //有此数据
       {
           $postData['gainmode'] = implode ('#', $postData['gainmode']);
       }/*处理 增益模式 结束*/

       /*处理 快门模式*/
       if ( isset($postData['shuttermode']) ) //有此数据
       {
            $postData['shuttermode'] = implode ('#', $postData['shuttermode']);
       }/*处理 快门模式 结束*/

       /*处理 接口类型*/
       if ( isset($postData['interfacetype']) ) //有此数据
       {
            $postData['interfacetype'] = implode ('#', $postData['interfacetype']);
       }/*处理 接口类型 结束*/

       /*处理 曝光触发模式*/
       if ( isset($postData['exposetriggermode']) ) //有此数据
       {
            $postData['exposetriggermode'] = implode ('#', $postData['exposetriggermode']);
       }/*处理 曝光触发模式 结束*/

        //定义错误提示
        $errMsg = '';

        $data = Db::table('ccdconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            $res = Db::table('ccdconf')->where('teleid', $postData['teleid'])->where('ccdno', $postData['ccdno'])->update($postData);
        }else{//还无配置数据 进行insert
            $res = Db::table('ccdconf')->insert($postData);
        }

        if ( !$res )
        {
            $errMsg += 'ccd配置存数据库失败!<br>';
        }
        //处理上传文件
        $dir = 'ccd' . $postData['teleid']; //每个望远镜的每个设备建1个目录，如ccd1, focus2.....
        /*处理测试报告*/
        $qecurveFile = $this->request->file('qecurve'); //获取量子效率曲线的上传数据
        if ( $qecurveFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 量子效率曲线
            $fileName = '量子效率曲线';
            $fileName = iconv ('UTF-8', 'GBK', $fileName);
            //如果已有此名字的文件，则先删除
            if ( file_exists($this->file_path . "/$dir/$fileName") )
            {
                $delReport = unlink($this->file_path ."/$dir/$fileName");
                if ($delRport === false)
                {
                    return "删除原{$fileName}文件失败!";
                }
            }
        
            //将新上传的量子效率曲线移至指定目录
            $info = $qecurveFile->move($this->file_path."/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "上传{$fileName}失败!<br>";
            }
        }/*处理量子效率曲线 结束*/

        /*接下来 出厂测试报告*/
        $reportFile = $this->request->file('specification'); //获取出厂测试报告的上传数据
        if ( $reportFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 出厂测试报告
            $fileName = '出厂测试报告';
            $fileName = iconv ('UTF-8', 'GBK', $fileName);
            //如果已有此名字的文件，则先删除
            if ( file_exists($this->file_path . "/$dir/$fileName") )
            {
                $delReport = unlink($this->file_path . "/$dir/$fileName");
                if ( $delRport === false )
                {
                    return "删除原{$fileName}失败!";
                }
            }

            //将新上传的出厂测试报告移至指定目录
            $info = $reportFile->move($this->file_path . "/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "{$fileName}上传失败!";
            }
        }/*处理出厂测试报告 结束*/

        /*接下来 说明书*/
        $reportFile = $this->request->file('manualbook'); //获取说明书的上传数据
        if ( $reportFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明书
            $fileName = '说明书';
            $fileName = iconv ('UTF-8', 'GBK', $fileName);
            //如果已有此名字的文件，则先删除
            if ( file_exists($this->file_path . "/$dir/$fileName") )
            {
                $delReport = unlink($this->file_path . "/$dir/$fileName");
                if ( $delRport === false )
                {
                    return "删除原{$fileName}失败!";
                }
            }

            //将新上传的说明书移至指定目录
            $info = $reportFile->move($this->file_path . "/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "{$fileName}上传失败!";
            }
        }/*处理说明书 结束*/

        if ($errMsg !== '')
        {
            return $errMsg;
        }else{//数据入库 和 文件上传都ok 获取已上传的文件名以json格式返回页面
            //若存放文件的目录还未创建 则先创建之
            if ( !file_exists($this->file_path."/$dir") )
            {
                mkdir ($this->file_path."/$dir");
            }

            $res = scandir ($this->file_path."/$dir");

            if ( $res !== false && count($res) > 2 )
            {
                unset ($res[0], $res[1]); //删除前2个数据
                foreach ( $res as $k)
                {
                    $result['file'][] = iconv('GBK', 'UTF-8', $k);  //将文件名转为utf-8
                }
            }

            $result['msg'] = 'ccd配置ok!';
            return json_encode ($result);
        }
    }/*获取ccd配置项表单 存入表ccdconf中 结束*/
}