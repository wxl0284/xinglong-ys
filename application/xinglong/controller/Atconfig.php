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
        //halt($postData);
        //halt(isset($postData['maxaxis3speed']));
        
        //处理表单数据，若无轴3，则轴3相关input框禁用，则将$postData轴3相关之置为空字符串
        if ( $postData['haveaxis3'] == 0 && !isset($postData['maxaxis3speed']) )
        {
            $postData['maxaxis3speed'] = '';    //轴3最大速度
            $postData['maxaxis3acceleration'] = ''; //轴3最大加速度
            $postData['axis3parkposition'] = '';    //轴3复位位置
        }

        //处理焦点类型-焦比-焦距
        $gimbal_focus_num = isset($postData['gimbal_focus']) ? count ($postData['gimbal_focus']) : 0; //被选择的
        if ( $gimbal_focus_num == 0 ) return '您未选择焦点类型';
        //halt($postData['focus_n']);
        for ( $f_i = 0; $f_i < $postData['focus_n']; $f_i++)
        { //将焦比-焦距数据整理为一个数组[ 'v0'=>['focusRatio'=>'[1 2]', 'focusLeng'=>'[1 2]'], 'v1'=>['focusRatio'=>'[1 2]', 'focusLeng'=>'[1 2]'] ]
            if ( isset( $postData['focusRatio'.$f_i], $postData['focusLeng'.$f_i] ) ) 
            {
                $focus_temp['v'.$f_i] = [ 'focusRatio' => $postData['focusRatio'.$f_i], 'focusLeng' => $postData['focusLeng'.$f_i] ];
            }
        }
        $focus_temp['focus'] = $postData['gimbal_focus'];
        $postData['focustype'] = json_encode ($focus_temp); //将整理后的数组转为json字串，存入focustype字段
        //属性更新时间
        $postData['attrmodifytime'] = date ('Y-m-d');

        //定义错误提示
        $errMsg = '';

        $data = Db::table('gimbalconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {   //已有配置数据 进行update，使用事务，同时对atlist表的数据进行操作
            //获取$postData中望远镜属性数据 存入新数组
            $at_data['atid'] = $postData['atid'];
            $at_data['atname'] = $postData['atname'];
            $at_data['address'] = $postData['address'];
            $at_data['longitude'] = $postData['longitude'];
            $at_data['latitude'] = $postData['latitude'];
            $at_data['altitude'] = $postData['altitude'];
            $at_data['aperture'] = $postData['aperture'];
            //删除$postData的上述数据
            unset($postData['atid'],$postData['atname'],$postData['address'],$postData['longitude']
                  ,$postData['latitude'],$postData['altitude'],$postData['aperture']);
            //开启事务 同时操作atlist表 和 gimbalconf表
            Db::startTrans();

            $atlist_res = Db::table('atlist')->where('id', $postData['teleid'])->update($at_data);
            $gimbal_res = Db::table('gimbalconf')->where('teleid', $postData['teleid'])->strict(false)->update($postData);
            
            if ( $atlist_res && $gimbal_res ) //若同时更新ok
            {
                Db::commit(); //执行提交
                $res = true;
            }else{
                Db::rollback();
                $res = false;
            }
        }else{//还无配置数据 进行insert
            //$res = Db::table('gimbalconf')->insert($postData);
            //获取$postData中望远镜属性数据 存入新数组
            $at_data['atid'] = $postData['atid'];
            $at_data['atname'] = $postData['atname'];
            $at_data['address'] = $postData['address'];
            $at_data['longitude'] = $postData['longitude'];
            $at_data['latitude'] = $postData['latitude'];
            $at_data['altitude'] = $postData['altitude'];
            $at_data['aperture'] = $postData['aperture'];
            //删除$postData的上述数据
            unset($postData['atid'],$postData['atname'],$postData['address'],$postData['longitude']
                  ,$postData['latitude'],$postData['altitude'],$postData['aperture']);
            //开启事务 同时操作atlist表 和 gimbalconf表
            Db::startTrans();

            $atlist_res = Db::table('atlist')->where('id', $postData['teleid'])->strict(false)->update($at_data);
            $gimbal_res = Db::table('gimbalconf')->strict(false)->insert($postData);

            //若同时执行ok 
            if ( $atlist_res && $gimbal_res )
            {
                Db::commit(); //执行提交
                $res = true;
            }else{
                Db::rollback();
                $res = false;
            }
        }


        if ( !$res )
        {
            $errMsg += '转台配置存数据库失败!<br>';
        }
        //处理上传文件
        $dir = 'gimbal' . $postData['teleid']; //每个望远镜的每个设备建1个目录，如gimbal1, gimbal2.....
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
        $instruFile = $this->request->file('instruction'); //获取说明书的上传数据
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
            $result['attrmodifytime'] = $postData['attrmodifytime'];
            return json_encode ($result);
        }
    }/*获取转台配置项表单 存入atlist表和gimbalconf中 结束*/

    /*获取ccd配置项表单 存入表ccdconf中*/
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

        $postData = input();
        //halt($postData);
       //处理表单数据中若干个 复选框
       /*处理 读出模式*/
       if ( isset($postData['readoutmode']) ) //有此数据
       {
           $postData['readoutmode'] = implode ('#', $postData['readoutmode']);
       }/*处理 读出模式 结束*/

       /*处理 读出速度模式*/
       if ( isset($postData['readoutspeed']) ) //有此数据
       {
           $postData['readoutspeed'] = implode ('#', $postData['readoutspeed']);
       }/*处理 读出速度模式 结束*/

       /*处理 转移速度模式*/
       if ( isset($postData['transferspeed']) ) //有此数据
       {
           $postData['transferspeed'] = implode ('#', $postData['transferspeed']);
       }/*处理 转移速度模式 结束*/

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

       //属性更新时间
       $postData['attrmodifytime'] = date ('Y-m-d');
       
       //根据提交上来的ccd数量（假如是1个ccd,则删除字段ccdno大于1的配置数据）
       //Db::table('ccdconf')->where('teleid', $postData['teleid'])->where('ccdno', '>', $postData['ccd_num'])->delete();
       //删除多余的$postData['ccd_num']字段
       //unset ($postData['ccd_num']);
        
        $errMsg = '';  //定义错误提示

        $data = Db::table('ccdconf')->where('teleid', $postData['teleid'])->where('ccdno', $postData['ccdno'])->find();

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
       
        //处理上传文件, 每个望远镜的每个设备建1个目录，如ccd1, focus2.....
        $dir = 'ccd' . $postData['teleid'] . '_' .$postData['ccdno'];   //每个望远镜的不同ccd, 各自建一个目录（如ccd1_1, ccd1_2...）
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
            $result['attrmodifytime'] = $postData['attrmodifytime'];
            return json_encode ($result);
        }
    }/*获取ccd配置项表单 存入表ccdconf中 结束*/

    /*获取滤光片配置项表单 存入表filterconf中*/
    public function filter_config()
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

        $postData = input();
        //halt($postData);
       //属性更新时间
       $postData['attrmodifytime'] = date ('Y-m-d');
       //接下来处理 各插槽的 滤光片类型-名称-偏差值
       if ( !isset($postData['numberoffilter']) || $postData['numberoffilter'] < 1 )
       {
           return '未填写插槽数目';
       }

       if ( !isset($postData['slot']) || count($postData['slot']) < 3 )
       {
           return '请各插槽';
       }

        //定义错误提示
        $errMsg = '';

        $data = Db::table('filterconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            $res = Db::table('filterconf')->where('teleid', $postData['teleid'])->strict(false)->update($postData);
        }else{//还无配置数据 进行insert
            $res = Db::table('filterconf')->strict(false)->insert($postData);
        }

        if ( !$res )
        {
            $errMsg += '滤光片配置存数据库失败!<br>';
        }
        //处理上传文件
        $dir = 'filter' . $postData['teleid']; //每个望远镜的每个设备建1个目录，如ccd1, focus2.....
        /*处理 说明文件*/
        $filterFile = $this->request->file('specification'); //获取说明文件的上传数据
        if ( $filterFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明文件
            $fileName = '说明文件';
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
        
            //将新上传的说明文件移至指定目录
            $info = $filterFile->move($this->file_path . "/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "上传{$fileName}失败!<br>";
            }
        }/*处理说明文件 结束*/

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

            $result['msg'] = '滤光片配置ok!';
            $result['attrmodifytime'] = $postData['attrmodifytime'];
            return json_encode ($result);
        }
    }/*获取滤光片配置项表单 存入表filterconf中 结束*/

    /*获取随动圆顶配置项表单 存入表sdomeconf中*/
    public function slaveDome_config()
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

        $postData = input();

       //属性更新时间
       $postData['attrmodifytime'] = date ('Y-m-d');

        //定义错误提示
        $errMsg = '';

        $data = Db::table('sdomeconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            $res = Db::table('sdomeconf')->where('teleid', $postData['teleid'])->update($postData);
        }else{//还无配置数据 进行insert
            $res = Db::table('sdomeconf')->insert($postData);
        }

        if ( !$res )
        {
            $errMsg += '随动圆顶配置存数据库失败!<br>';
        }
        //处理上传文件
        $dir = 'sDome' . $postData['teleid']; //每个望远镜的每个设备建1个目录，如ccd1, focus2.....
        /*处理 说明文件*/
        $sDomeFile = $this->request->file('specification'); //获取说明文件的上传数据
        if ( $sDomeFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明文件
            $fileName = '说明文件';
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
        
            //将新上传的说明文件移至指定目录
            $info = $sDomeFile->move($this->file_path . "/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "上传{$fileName}失败!<br>";
            }
        }/*处理说明文件 结束*/

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

            $result['msg'] = '随动圆顶配置ok!';
            $result['attrmodifytime'] = $postData['attrmodifytime'];
            return json_encode ($result);
        }
    }/*获取随动圆顶配置项表单 存入表sdomeconf中 结束*/

    /*获取全开圆顶配置项表单 存入表odomeconf中*/
    public function oDome_config()
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

        $postData = input();

       //属性更新时间
       $postData['attrmodifytime'] = date ('Y-m-d');

        //定义错误提示
        $errMsg = '';

        $data = Db::table('odomeconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            $res = Db::table('odomeconf')->where('teleid', $postData['teleid'])->update($postData);
        }else{//还无配置数据 进行insert
            $res = Db::table('odomeconf')->insert($postData);
        }

        if ( !$res )
        {
            $errMsg += '全开圆顶配置存数据库失败!<br>';
        }
        //处理上传文件
        $dir = 'oDome' . $postData['teleid']; //每个望远镜的每个设备建1个目录，如ccd1, focus2.....
        /*处理 说明文件*/
        $oDomeFile = $this->request->file('specification'); //获取说明文件的上传数据
        if ( $oDomeFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明文件
            $fileName = '说明文件';
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
        
            //将新上传的说明文件移至指定目录
            $info = $oDomeFile->move($this->file_path . "/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "上传{$fileName}失败!<br>";
            }
        }/*处理说明文件 结束*/

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

            $result['msg'] = '全开圆顶配置ok!';
            $result['attrmodifytime'] = $postData['attrmodifytime'];
            return json_encode ($result);
        }
    }/*获取全开圆顶配置项表单 存入表odomeconf中 结束*/

    /*获取调焦器配置项表单 存入表odomeconf中*/
    public function focus_config()
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

        $postData = input();

       //属性更新时间
       $postData['attrmodifytime'] = date ('Y-m-d');

        //定义错误提示
        $errMsg = '';

        $data = Db::table('focusconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            $res = Db::table('focusconf')->where('teleid', $postData['teleid'])->update($postData);
        }else{//还无配置数据 进行insert
            $res = Db::table('focusconf')->insert($postData);
        }

        if ( !$res )
        {
            $errMsg += '调焦器配置存数据库失败!<br>';
        }
        //处理上传文件
        $dir = 'focus' . $postData['teleid']; //每个望远镜的每个设备建1个目录，如ccd1, focus2.....
        /*处理 说明文件*/
        $focusFile = $this->request->file('specification'); //获取说明文件的上传数据
        if ( $focusFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明文件
            $fileName = '说明文件';
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
        
            //将新上传的说明文件移至指定目录
            $info = $focusFile->move($this->file_path . "/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "上传{$fileName}失败!<br>";
            }
        }/*处理说明文件 结束*/

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

            $result['msg'] = '调焦器配置ok!';
            $result['attrmodifytime'] = $postData['attrmodifytime'];
            return json_encode ($result);
        }
    }/*获取调焦器配置项表单 存入表odomeconf中 结束*/

    /*获取导星望远镜配置项表单 存入表guideconf中 结束*/
    public function guideScope_config()
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

        $postData = input();

       //属性更新时间
       $postData['attrmodifytime'] = date ('Y-m-d');

        //定义错误提示
        $errMsg = '';

        $data = Db::table('guideconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            $res = Db::table('guideconf')->where('teleid', $postData['teleid'])->update($postData);
        }else{//还无配置数据 进行insert
            $res = Db::table('guideconf')->insert($postData);
        }

        if ( !$res )
        {
            $errMsg += '导星望远镜配置存数据库失败!<br>';
        }
        //处理上传文件
        $dir = 'guideScope' . $postData['teleid']; //每个望远镜的每个设备建1个目录，如ccd1, focus2.....
        /*处理 说明文件*/
        $guideScopeFile = $this->request->file('specification'); //获取说明文件的上传数据
        if ( $guideScopeFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明文件
            $fileName = '说明文件';
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
        
            //将新上传的说明文件移至指定目录
            $info = $guideScopeFile->move($this->file_path . "/$dir", $fileName);
            if (!$info) //移动文件失败
            {
                $errMsg += "上传{$fileName}失败!<br>";
            }
        }/*处理说明文件 结束*/

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

            $result['msg'] = '导星望远镜配置ok!';
            $result['attrmodifytime'] = $postData['attrmodifytime'];
            return json_encode ($result);
        }
    }/*获取导星望远镜配置项表单 存入表guideconf中 结束*/
}