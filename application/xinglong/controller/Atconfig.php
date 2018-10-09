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
        $postData = input();
        
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
       
        $postData['attrmodifytime'] = date ('Y-m-d'); //属性更新时间

        $errMsg = ''; //定义错误提示

        //检查转台ip与其他设备ip是否重复
        if ( !isset($postData['ip']) || ( !preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $postData['ip']) && !preg_match('/^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/', $postData['ip'])) )
        {//未提交ip,或者IP不符合ipv4和ipv6
            $errMsg .= '转台ip输入有误<br>';
        }
    
        if ( $errMsg !== '' ) return $errMsg;

        $data = Db::table('gimbalconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {   //已有配置数据 进行update，使用事务，同时对atlist表的数据进行操作
            //获取$postData中望远镜属性数据 存入新数组，用以更新atlist表
            $at_data['atname'] = $postData['atname'];
            $at_data['address'] = $postData['address'];
            $at_data['longitude'] = $postData['longitude'];
            $at_data['latitude'] = $postData['latitude'];
            $at_data['altitude'] = $postData['altitude'];

            //判断转台IP是否被其他设备占用
            //首先 查到此望远镜转台在表devipid中的主键id
            $id = Db::table('devipid')->where('dev', 'gimbal')->where('teleid', $postData['teleid'])->field('id')->find();
            //然后 在表devipid中查所有不是此id的数据
            $ips = Db::table('devipid')->where('id', '<>', $id['id'])->column ('ip');
         
            if ( $ips && in_array($postData['ip'], $ips))
            {
                return '转台ip与其他设备ip重复';
            }
            //判断转台IP是否被其他设备占用 结束

            //开启事务 同时操作atlist表 和 gimbalconf 和 devipid表
            Db::startTrans();

            $atlist_res = Db::table('atlist')->where('id', $postData['teleid'])->strict(false)->update($at_data);
            $gimbal_res = Db::table('gimbalconf')->where('teleid', $postData['teleid'])->strict(false)->update($postData);
            $ipid_res = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'gimbal')->strict(false)->update( [ 'ip'=>$postData['ip'] ]);
            
            if ( $atlist_res && $gimbal_res && $ipid_res ) //若同时更新ok
            {
                Db::commit(); //执行提交
                $res = true;
            }else{
                Db::rollback();
                $res = false;
            }
        }else{//还无配置数据 进行insert       
            //获取$postData中望远镜属性数据 存入新数组 用以更新atlist表
            $at_data['atname'] = $postData['atname'];
            $at_data['address'] = $postData['address'];
            $at_data['longitude'] = $postData['longitude'];
            $at_data['latitude'] = $postData['latitude'];
            $at_data['altitude'] = $postData['altitude'];

            //判断转台IP是否被其他设备占用
            $ips = Db::table('devipid')->where('ip', $postData['ip'])->find();
            if ( $ips )
            {
                return '转台ip与其他设备ip重复';
            }
            //判断转台IP是否被其他设备占用 结束

            //开启事务 同时操作atlist表 和 gimbalconf表
            Db::startTrans();

            $atlist_res = Db::table('atlist')->where('id', $postData['teleid'])->strict(false)->update($at_data);
            $gimbal_res = Db::table('gimbalconf')->strict(false)->insert($postData);
            $ipid_res = Db::table('devipid')->strict(false)
                        ->insert( ['ip' => $postData['ip'],
                                    'teleid' => $postData['teleid'],
                                    'dev' => 'gimbal',
                                    'devnum' => '1' ] ); //默认给转台的序号置为1
 
            if ( $atlist_res && $gimbal_res && $ipid_res ) //若同时执行ok 
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
            $errMsg .= '转台配置存数据库失败!<br>';
        }
        //处理上传文件
        $dir = 'gimbal' . $postData['teleid']; //每个望远镜的每个设备建1个目录，如gimbal1, gimbal2.....
        /*处理测试报告*/
        $reportFile = $this->request->file('report'); //获取测试报告的上传数据

        if ( $reportFile !== null ) //有文件被上传
        {
            //验证测试报告
		    /*$file_valid = $this->validate(
            ['file' => $reportFile],
            ['file' => 'file|require|fileExt:pdf|fileMime:application/pdf'],
            ['file.require' => '请选择上传文件',
            'file.fileExt' => '文件后缀名必须为pdf',
            'file.fileMime' => '文件格式不是pdf']);

            if (true !== $file_valid)
            {
                return $file_valid;
            }*///验证测试报告 结束

            //将上传文件命名为: 出厂测试报告
            $fileName = '出厂测试报告';
            $fileName = iconv ('UTF-8', 'GBK', $fileName);
            //如果已有此名字的文件，则先删除
            /*if ( file_exists($this->file_path . "/$dir/$fileName") )
            {
                $delReport = unlink($this->file_path ."/$dir/$fileName");
                if ($delRport === false)
                {
                    return "删除原{$fileName}失败!";
                }
            }*/
        
            //将新上传的测试报告移至指定目录
            $info = $reportFile->move($this->file_path."/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "{$fileName}上传失败!<br>";
            }
        }/*处理测试报告 结束*/

        /*接下来 处理说明书*/
        $instruFile = $this->request->file('instruction'); //获取说明书的上传数据
        if ( $instruFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明书
            $fileName = '说明书';
            $fileName = iconv ('UTF-8', 'GBK', $fileName);

            //将新上传的说明书移至指定目录
            $info = $instruFile->move($this->file_path . "/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "{$fileName}上传失败!";
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

    public function ccd_config() /*获取ccd配置项表单 存入表ccdconf中*/
    {   
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();
    
       //处理表单数据中若干个 复选框
       /*处理 读出模式*/
       if ( isset($postData['readoutmode']) ) //有此数据
       {
           $postData['readoutmode'] = implode ('#', $postData['readoutmode']);
       }/*处理 读出模式 结束*/

       /*处理 bin*/
       if ( isset($postData['binarray']) ) //有此数据
       {
           $postData['binarray'] = implode ('#', $postData['binarray']);
       }/*处理 bin 结束*/

       /*处理 读出速度*/
       if ( isset($postData['readoutspeed']) ) //有此数据
       {
           $postData['readoutspeed'] = implode ('#', $postData['readoutspeed']);
       }/*处理 读出速度 结束*/

       /*处理 转移速度*/
       if ( isset($postData['transferspeed']) ) //有此数据
       {
           $postData['transferspeed'] = implode ('#', $postData['transferspeed']);
       }/*处理 转移速度 结束*/

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

        $errMsg = '';  //定义错误提示

        if ( !isset($postData['ip']) || ( !preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $postData['ip']) && !preg_match('/^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/', $postData['ip'])) )
        {//未提交ip,或者IP不符合ipv4和ipv6
            $errMsg .= 'CCD ip输入有误<br>';
        }
   
        if ( !isset($postData['ccdid']) || strlen($postData['ccdid']) === 0 )
        {
            $errMsg .= 'CCD id输入有误<br>';
        }

        if ( !isset($postData['name']) || strlen($postData['name']) === 0 )
        {
            $errMsg .= 'CCD名称输入有误<br>';
        }

        if ( $errMsg !== '' ) return $errMsg;

        $data = Db::table('ccdconf')->where('teleid', $postData['teleid'])->where('ccdno', $postData['ccdno'])->find();
       
        if ( $data )
        {//已有配置数据 进行update
            //检查ccd的ip id 名称是否重复
            //首先 查到当前设备在表devipid中的主键
            $id = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'ccd')->where('devnum', $postData['ccdno'])->field('id')->find();
            //然后 在表devipid中查不是此id对应的数据
            $ips = Db::table('devipid')->where('id', '<>', $id['id'])->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= 'ccd的ip与其他设备重复<br>';
                }

                if ( in_array( $postData['ccdid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= 'ccd的id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= 'ccd的名称与其他设备重复<br>';
                }
            }
            //检查ccd的ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作ccdconf 和 devipid表
            Db::startTrans();
            $ccd_res = Db::table('ccdconf')->where('teleid', $postData['teleid'])->strict(false)->update($postData);
            $ipid_res = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'ccd')
                        ->where('devnum', $postData['ccdno'])->strict(false)
                        ->update([
                                'ip'=>$postData['ip'],
                                'devid'=>$postData['ccdid'],
                                'devname'=>$postData['name'] ] );

            if ( $ccd_res && $ipid_res ) //若同时更新ok
            {
                Db::commit(); //执行提交
                $res = true;
            }else{
                Db::rollback();
                $res = false;
            }
        }else{//还无配置数据 进行insert
            //检查当前设备Ip id 名称是否重复
            $ips = Db::table('devipid')->select();
            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= 'ccd的ip与其他设备重复<br>';
                }

                if ( in_array( $postData['ccdid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= 'ccd的id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= 'ccd的名称与其他设备重复<br>';
                }
            }
            //检查当前设备Ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作ccdconf 和 devipid表
            Db::startTrans();
            $ccd_res = Db::table('ccdconf')->strict(false)->insert($postData);
            $ipid_res = Db::table('devipid')->strict(false)
                        ->insert(
                            ['ip' => $postData['ip'],
                            'devid' => $postData['ccdid'],
                            'devname' => $postData['name'],
                            'teleid' => $postData['teleid'],
                            'dev' => 'ccd',
                            'devnum' => $postData['ccdno'] ] );

            if ( $ccd_res && $ipid_res ) //若同时更新ok
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
            $errMsg .= 'ccd配置存数据库失败!<br>';
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
        
            //将新上传的量子效率曲线移至指定目录
            $info = $qecurveFile->move($this->file_path."/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "上传{$fileName}失败!<br>";
            }
        }/*处理量子效率曲线 结束*/

        /*接下来 出厂测试报告*/
        $reportFile = $this->request->file('specification'); //获取出厂测试报告的上传数据
        if ( $reportFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 出厂测试报告
            $fileName = '出厂测试报告';
            $fileName = iconv ('UTF-8', 'GBK', $fileName);

            //将新上传的出厂测试报告移至指定目录
            $info = $reportFile->move($this->file_path . "/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "{$fileName}上传失败!";
            }
        }/*处理出厂测试报告 结束*/

        /*接下来 说明书*/
        $reportFile = $this->request->file('manualbook'); //获取说明书的上传数据
        if ( $reportFile !== null ) //有文件被上传
        {
            //将上传文件命名为: 说明书
            $fileName = '说明书';
            $fileName = iconv ('UTF-8', 'GBK', $fileName);

            //将新上传的说明书移至指定目录
            $info = $reportFile->move($this->file_path . "/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "{$fileName}上传失败!";
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

    //将vue对象中gain_noise数据对象存入ccdconf表中gain_noise字段中（以json字符串的形式）
    public function gainNoiseConfig ()
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();
        //halt($postData);
        if ( !isset( $postData['gain_noise'], $postData['teleid'], $postData['ccdno'] ) || count($postData['gain_noise']) < 1 )
        {//表单数据内无gain_noise，或$postData['gain_noise']内无数据
            return '提交的增益-读出噪声数据有误';
        }
        
        //看提交的teleid是否在atlist表内
        $teleId =  Db::table('atlist')->column('id');

        if ( !in_array($postData['teleid'], $teleId) ) return '提交的增益-读出噪声数据有误';
        //接下来 处理存储读出速度、转移速度、增益模式、增益挡位
        if ( isset($postData['fourData']['readOut']) && count($postData['fourData']['readOut']) > 0  )
        {
           $postData['readoutspeed'] = implode ('#', $postData['fourData']['readOut']);
        }

        if ( isset($postData['fourData']['transfer']) && count($postData['fourData']['transfer']) > 0 )
        {
           $postData['transferspeed'] = implode ('#', $postData['fourData']['transfer']);
        }

        if ( isset($postData['fourData']['gain']) && count($postData['fourData']['gain']) > 0 )
        {
           $postData['gainmode'] = implode ('#', $postData['fourData']['gain']);
        }

        if ( isset($postData['fourData']['gear']) )
        {
           $postData['gainnumber'] = $postData['fourData']['gear'];
        }
        //处理 存储读出速度、转移速度、增益模式、增益挡位 结束

       $postData['gain_noise'] = json_encode ($postData['gain_noise']);
        
       $postData['attrmodifytime'] = date ('Y-m-d');   //属性更新时间

       $data = Db::table('ccdconf')->where('teleid', $postData['teleid'])->where('ccdno', $postData['ccdno'])->find();

        if ( $data )
        {//已有配置数据 进行update
            $res = Db::table('ccdconf')->where('teleid', $postData['teleid'])->where('ccdno', $postData['ccdno'])->strict(false)->update($postData);
        }else{//还无配置数据 进行insert
            $res = Db::table('ccdconf')->strict(false)->insert($postData);
        }

        if ( !$res )
        {
            return '增益值-读出噪声值存储失败!';
        }

        $result['msg'] = '增益值-读出噪声值配置ok!';
        $result['attrmodifytime'] = $postData['attrmodifytime'];
        return json_encode ($result);

    }//将vue对象中gain_noise数据对象存入ccdconf表中gain_noise字段中 结束

    //对ccd增益-读出噪声值表格相关字段 升序、降序排列 此方法暂不使用
   /* public function gainNoiseSort ()
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();

        if ( count($postData) < 4 ) return '请求参数有误';

        //看提交的teleid是否在atlist表内
        $teleId =  Db::table('atlist')->column('id');

        if ( !in_array($postData['teleid'], $teleId) ) return '请求参数有误';

        //检查ccdconf表中是否已有需要排序的数据
        $data = Db::table('ccdconf')->where('teleid', $postData['teleid'])->where('ccdno', $postData['ccdno'])->field('gain_noise')->find();

        if ( !$data['gain_noise'] ) //未查询到gain_noise字段数据
        {
            return '未查询到配置数据';
        }else{//有数据，根据提交参数对gain_noise字段数据排序后，返回给ajax排序后的json数据
            return $this->sort_data ($data['gain_noise'], $postData['field'], $postData['order']);
        }
    }//对ccd增益-读出噪声值表格相关字段 升序、降序排列 结束*/

    public function filter_config() /*获取滤光片配置项表单 存入表filterconf中*/
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

       $postData = input();
       
       $errMsg = ''; //定义错误提示
       
       $postData['attrmodifytime'] = date ('Y-m-d');   //属性更新时间
       //接下来处理 各插槽的 滤光片类型-名称-偏差值
       if ( !isset($postData['numberoffilter']) || $postData['numberoffilter'] < 1 )
       {
            $errMsg .= '未填写插槽数目<br>';
       }

       $filter_type_data = Db::table('confoption')->where('conf', 'FilterSystem')->select();
       
       $filter_type_data = array_column($filter_type_data, 'conf_val'); //获取confoption表中的滤光片类型

       for ( $slot_i = 0; $slot_i < $postData['numberoffilter']; $slot_i++ ) //逐一检查每个插槽的 滤光片类型-名称-偏差值
       {
            //首先检查滤光片类型
            if ( !in_array( $postData['filterType'][$slot_i],  $filter_type_data)  )
            {
                $errMsg .= '插槽' . ($slot_i+1) . '滤光片类型有误<br>';
            }
            //检查滤光片名称
            if ( preg_match('/[\x{4e00}-\x{9af5} 0-9]/u', $postData['filterName'][$slot_i]) || $postData['filterName'][$slot_i] === '' )
            {
                $errMsg .= '插槽' . ($slot_i+1) . '滤光片名称有误<br>';
            }
            //检查焦距偏差值
            if ( !preg_match('/\d+/', $postData['filterComp'][$slot_i]) || $postData['filterComp'][$slot_i] < 1 )
            {
                $errMsg .= '插槽' . ($slot_i+1) . '焦距偏差值有误<br>';
            }
       }

        if ( !isset($postData['ip']) || ( !preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $postData['ip']) && !preg_match('/^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/', $postData['ip'])) )
        {//未提交ip,或者IP不符合ipv4和ipv6
            $errMsg .= '滤光片ip输入有误<br>';
        }
   
        if ( !isset($postData['filterid']) || strlen($postData['filterid']) === 0 )
        {
            $errMsg .= '滤光片id输入有误<br>';
        }

        if ( !isset($postData['name']) || strlen($postData['name']) === 0 )
        {//判断滤光片的名称是否重复
            $errMsg .= '滤光片名称输入有误<br>';
        }

       if ( $errMsg !== '' ) return $errMsg;

       $slot_temp['slot_num'] = $postData['numberoffilter'];
       $slot_temp['filterType'] = $postData['filterType'];
       $slot_temp['filterName'] = $postData['filterName'];
       $slot_temp['filterComp'] = $postData['filterComp'];

       $postData['filtersystem'] = json_encode ($slot_temp);
       //处理 各插槽的 滤光片类型-名称-偏差值 结束

        $data = Db::table('filterconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            //检查滤光片的ip id 名称是否重复
            //首先 查到当前设备在表devipid中的主键
            $id = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'filter')->field('id')->find();
            //然后 在表devipid中查不是此id对应的数据
            $ips = Db::table('devipid')->where('id', '<>', $id['id'])->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '滤光片的ip与其他设备重复<br>';
                }

                if ( in_array( $postData['filterid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '滤光片的id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '滤光片的名称与其他设备重复<br>';
                }
            }
            //检查滤光片的ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作filterconf 和 devipid表
            Db::startTrans();
            $filter_res = Db::table('filterconf')->where('teleid', $postData['teleid'])->strict(false)->update($postData);
            $ipid_res = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'filter')->strict(false)
                        ->update(
                            ['ip'=>$postData['ip'],
                            'devid'=>$postData['filterid'],
                            'devname'=>$postData['name'] ] );

            if ( $filter_res && $ipid_res ) //若同时更新ok
            {
                Db::commit(); //执行提交
                $res = true;
            }else{
                Db::rollback();
                $res = false;
            }
        }else{//还无配置数据 进行insert
            //检查滤光片的ip id 名称是否重复
            $ips = Db::table('devipid')->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '滤光片的ip与其他设备重复<br>';
                }

                if ( in_array( $postData['filterid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '滤光片的id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '滤光片的名称与其他设备重复<br>';
                }
            }
            //检查滤光片的ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作filterconf 和 devipid表
            Db::startTrans();
            $filter_res = Db::table('filterconf')->strict(false)->insert($postData);

            $ipid_res = Db::table('devipid')->strict(false)
                        ->insert(
                            ['ip' => $postData['ip'],
                            'devid' => $postData['filterid'],
                            'devname' => $postData['name'],
                            'teleid' => $postData['teleid'],
                            'dev' => 'filter',
                            'devnum'=> '1' ] );

            if ( $filter_res && $ipid_res ) //若同时更新ok
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
            $errMsg .= '滤光片配置存数据库失败!<br>';
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
        
            //将新上传的说明文件移至指定目录
            $info = $filterFile->move($this->file_path . "/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "上传{$fileName}失败!<br>";
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

    public function slaveDome_config()  /*获取随动圆顶配置项表单 存入表sdomeconf中*/
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();

        $postData['attrmodifytime'] = date ('Y-m-d'); //属性更新时间

        $errMsg = ''; //定义错误提示

        //验证及判断slaveDome的ip是否重复
        if ( !isset($postData['ip']) || ( !preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $postData['ip']) && !preg_match('/^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/', $postData['ip'])) )
        {//未提交ip,或者IP不符合ipv4和ipv6
            $errMsg .= '随动圆顶ip输入有误<br>';
        }

        if ( !isset($postData['sdomeid']) || strlen($postData['sdomeid']) === 0 )
        {
            $errMsg .= '随动圆顶id输入有误<br>';
        }

        if ( !isset($postData['name']) || strlen($postData['name']) === 0 )
        {
            $errMsg .= '随动圆顶名称输入有误<br>';
        }

        if ( $errMsg !== '' ) return $errMsg;

        $data = Db::table('sdomeconf')->where('teleid', $postData['teleid'])->find();
        
        if ( $data )
        {//已有配置数据 进行update
            //检查随动圆顶的ip id 名称是否重复
            //首先 查到当前设备在表devipid中的主键
            $id = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'sdome')->field('id')->find();
            //然后 在表devipid中查不是此id对应的数据
            $ips = Db::table('devipid')->where('id', '<>', $id['id'])->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '随动圆顶ip与其他设备重复<br>';
                }

                if ( in_array( $postData['sdomeid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '随动圆顶id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '随动圆顶名称与其他设备重复<br>';
                }
            }
            //检查随动圆顶ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;

            //开启事务 同时操作sdomeconf 和 devipid表
            Db::startTrans();
            $sdome_res = Db::table('sdomeconf')->where('teleid', $postData['teleid'])->strict(false)->update($postData);
            $ipid_res = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'sdome')->strict(false)
                        ->update([
                            'ip'=>$postData['ip'],
                            'devid'=>$postData['sdomeid'],
                            'devname'=>$postData['name'] ] );

            if ( $sdome_res && $ipid_res ) //若同时更新ok
            {
                Db::commit(); //执行提交
                $res = true;
            }else{
                Db::rollback();
                $res = false;
            }
        }else{//还无配置数据 进行insert
            //检查随动圆顶的ip id 名称是否重复
            $ips = Db::table('devipid')->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '随动圆顶ip与其他设备重复<br>';
                }

                if ( in_array( $postData['sdomeid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '随动圆顶id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '随动圆顶名称与其他设备重复<br>';
                }
            }
            //检查随动圆顶ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作sdomeconf 和 devipid表
            Db::startTrans();
            $sdome_res = Db::table('sdomeconf')->strict(false)->insert($postData);
            $ipid_res = Db::table('devipid')->strict(false)
                        ->insert([
                            'ip' => $postData['ip'],
                            'devid' => $postData['sdomeid'],
                            'devname' => $postData['name'],
                            'teleid' => $postData['teleid'],
                            'dev' => 'sdome',
                            'devnum' => '1' ] );

            if ( $sdome_res && $ipid_res ) //若同时更新ok
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
            $errMsg .= '随动圆顶配置存数据库失败!<br>';
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
        
            //将新上传的说明文件移至指定目录
            $info = $sDomeFile->move($this->file_path . "/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "上传{$fileName}失败!<br>";
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

    public function oDome_config()   /*获取全开圆顶配置项表单 存入表odomeconf中*/
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();

        $postData['attrmodifytime'] = date ('Y-m-d');  //属性更新时间

        $errMsg = '';  //定义错误提示

        //验证及判断全开圆顶的ip是否重复
        if ( !isset($postData['ip']) || ( !preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $postData['ip']) && !preg_match('/^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/', $postData['ip'])) )
        {//未提交ip,或者IP不符合ipv4和ipv6
            $errMsg .= '全开圆顶ip输入有误<br>';
        }

        if ( !isset($postData['odomeid']) || strlen($postData['odomeid']) === 0 )
        {
            $errMsg .=  '全开圆顶id输入有误<br>';
        }

        if ( !isset($postData['name']) || strlen($postData['name']) === 0 )
        {
            $errMsg .=  '全开圆顶名称输入有误<br>';
        }

        if ( $errMsg !== '' ) return $errMsg;

        $data = Db::table('odomeconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            //检查全开圆顶的ip id 名称是否重复
            //首先 查到当前设备在表devipid中的主键
            $id = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'odome')->field('id')->find();
            //然后 在表devipid中查不是此id对应的数据
            $ips = Db::table('devipid')->where('id', '<>', $id['id'])->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '全开圆顶ip与其他设备重复<br>';
                }

                if ( in_array( $postData['odomeid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '全开圆顶id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '全开圆顶名称与其他设备重复<br>';
                }
            }
            //检查全开圆顶ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 odomeconf 和 devipid表
            Db::startTrans();
            $odome_res = Db::table('odomeconf')->where('teleid', $postData['teleid'])->strict(false)->update($postData);
            $ipid_res = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'odome')->strict(false)
                        ->update([
                            'ip' => $postData['ip'],
                            'devid' => $postData['odomeid'],
                            'devname' => $postData['name'] ] );

            if ( $odome_res && $ipid_res ) //若同时更新ok
            {
                Db::commit(); //执行提交
                $res = true;
            }else{
                Db::rollback();
                $res = false;
            }
        }else{//还无配置数据 进行insert
            //检查全开圆顶的ip id 名称是否重复
            $ips = Db::table('devipid')->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '全开圆顶ip与其他设备重复<br>';
                }

                if ( in_array( $postData['odomeid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '全开圆顶id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '全开圆顶名称与其他设备重复<br>';
                }
            }
            //检查全开圆顶ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作sdomeconf 和 devipid表
            Db::startTrans();
            $odome_res = Db::table('odomeconf')->strict(false)->insert($postData);
            $ipid_res = Db::table('devipid')->strict(false)
                        ->insert([
                            'ip' => $postData['ip'],
                            'devid' => $postData['odomeid'],
                            'devname' => $postData['name'],
                            'teleid' => $postData['teleid'],
                            'dev' => 'odome',
                            'devnum' => '1' ] );

            if ( $odome_res && $ipid_res ) //若同时更新ok
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
            $errMsg .= '全开圆顶配置存数据库失败!<br>';
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
        
            //将新上传的说明文件移至指定目录
            $info = $oDomeFile->move($this->file_path . "/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "上传{$fileName}失败!<br>";
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
    
    public function focus_config() /*获取调焦器配置项表单 存入表focusconf中*/
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();

        $postData['attrmodifytime'] = date ('Y-m-d');   //属性更新时间

        $errMsg = '';   //定义错误提示

        //验证及判断调焦器的ip是否重复
        if ( !isset($postData['ip']) || ( !preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $postData['ip']) && !preg_match('/^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/', $postData['ip'])) )
        {//未提交ip,或者IP不符合ipv4和ipv6
            $errMsg .= '调焦器ip输入有误<br>';
        }

        if ( !isset($postData['focusid']) || strlen($postData['focusid']) === 0 )
        {
            $errMsg .= '调焦器id输入有误<br>';
        }

        if ( !isset($postData['name']) || strlen($postData['name']) === 0 )
        {
            $errMsg .= '调焦器名称输入有误<br>';
        }

        if ( $errMsg !== '' ) return $errMsg;

        $data = Db::table('focusconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            //检查调焦器的ip id 名称是否重复
            //首先 查到当前设备在表devipid中的主键
            $id = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'focus')->field('id')->find();
            //然后 在表devipid中查不是此id对应的数据
            $ips = Db::table('devipid')->where('id', '<>', $id['id'])->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '调焦器ip与其他设备重复<br>';
                }

                if ( in_array( $postData['focusid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '调焦器id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '调焦器名称与其他设备重复<br>';
                }
            }
            //检查调焦器ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作focusconf 和 devipid表
            Db::startTrans();
            $focus_res = Db::table('focusconf')->where('teleid', $postData['teleid'])->strict(false)->update($postData);
            $ipid_res = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'focus')->strict(false)
                        ->update([
                            'ip'=>$postData['ip'],
                            'devid'=>$postData['focusid'],
                            'devname'=>$postData['name'] ] );

            if ( $focus_res && $ipid_res ) //若同时更新ok
            {
                Db::commit(); //执行提交
                $res = true;
            }else{
                Db::rollback();
                $res = false;
            }
        }else{//还无配置数据 进行insert
            //检查调焦器的ip id 名称是否重复
            $ips = Db::table('devipid')->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '调焦器ip与其他设备重复<br>';
                }

                if ( in_array( $postData['focusid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '调焦器id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '调焦器名称与其他设备重复<br>';
                }
            }
            //检查调焦器ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作focusconf 和 devipid表
            Db::startTrans();
            $focus_res = Db::table('focusconf')->strict(false)->insert($postData);
            $ipid_res = Db::table('devipid')->strict(false)
                        ->insert([
                            'ip' => $postData['ip'],
                            'devid' => $postData['focusid'],
                            'devname' => $postData['name'],
                            'teleid' => $postData['teleid'],
                            'dev' => 'focus',
                            'devnum' => '1' ] );

            if ( $focus_res && $ipid_res ) //若同时更新ok
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
            $errMsg .= '调焦器配置存数据库失败!<br>';
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
        
            //将新上传的说明文件移至指定目录
            $info = $focusFile->move($this->file_path . "/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "上传{$fileName}失败!<br>";
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

        $postData = input();
        $errMsg = ''; //定义错误提示

        //处理焦点类型--焦距
        $guide_focus_num = isset($postData['guide_focus']) ? count ($postData['guide_focus']) : 0; //被选择的
        if ( $guide_focus_num == 0 )  $errMsg .= '您未选择焦点类型<br>';
        
        for ( $g_i = 0; $g_i < $postData['focus_n']; $g_i++)
        { //将焦距数据整理为一个数组[ 'v0'=>'11', 'v1'=>'22' ]
            if ( isset( $postData['focusLeng'.$g_i] ) && is_numeric($postData['focusLeng'.$g_i]) && $postData['focusLeng'.$g_i] > 0 ) 
            {
                $focus_temp['v'.$g_i] = [ 'focusLeng' => $postData['focusLeng'.$g_i] ];
            }elseif ( isset( $postData['focusLeng'.$g_i] ) && !(is_numeric($postData['focusLeng'.$g_i]) && $postData['focusLeng'.$g_i] > 0) )
            {
                $errMsg .= $postData['guide_focus'][$g_i] . ': 焦距输入有误<br>';
            }
        }

        if ( !isset($postData['ip']) || ( !preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $postData['ip']) && !preg_match('/^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/', $postData['ip'])) )
        {//未提交ip,或者IP不符合ipv4和ipv6
            $errMsg .= '导星镜ip输入有误<br>';
        }

        if ( !isset($postData['guidescopeid']) || strlen($postData['guidescopeid']) === 0 )
        {
            $errMsg .= '导星镜id输入有误';
        }

        //判断导星望远镜的名称是否重复
        if ( !isset($postData['name']) || strlen($postData['name']) === 0 )
        {
            $errMsg .= '导星镜名称输入有误';
        }

        if ( $errMsg !== '' ) return $errMsg;

        $focus_temp['focus'] = $postData['guide_focus'];

        $postData['focuslength'] = json_encode ($focus_temp); //将整理后的数组转为json字串，存入focustype字段
       
        $postData['attrmodifytime'] = date ('Y-m-d');  //属性更新时间

        $data = Db::table('guideconf')->where('teleid', $postData['teleid'])->find();

        if ( $data )
        {//已有配置数据 进行update
            //检查导星镜的ip id 名称是否重复
            //首先 查到当前设备在表devipid中的主键
            $id = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'guide')->field('id')->find();
            //然后 在表devipid中查不是此id对应的数据
            $ips = Db::table('devipid')->where('id', '<>', $id['id'])->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '导星镜ip与其他设备重复<br>';
                }

                if ( in_array( $postData['guidescopeid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '导星镜id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '导星镜名称与其他设备重复<br>';
                }
            }
            //检查导星镜ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作guideconf 和 devipid表
            Db::startTrans();
            $guide_res = Db::table('guideconf')->where('teleid', $postData['teleid'])->strict(false)->update($postData);
            $ipid_res = Db::table('devipid')->where('teleid', $postData['teleid'])->where('dev', 'guide')->strict(false)
                        ->update([
                            'ip' => $postData['ip'],
                            'devid' => $postData['guidescopeid'],
                            'devname' => $postData['name'] ] );

            if ( $guide_res && $ipid_res ) //若同时更新ok
            {
                Db::commit(); //执行提交
                $res = true;
            }else{
                Db::rollback();
                $res = false;
            }
        }else{//还无配置数据 进行insert
            //检查导星镜的ip id 名称是否重复
            $ips = Db::table('devipid')->select();

            if ( $ips )
            {
                if ( in_array( $postData['ip'], array_column($ips, 'ip') )  )
                {
                    $errMsg .= '导星镜ip与其他设备重复<br>';
                }

                if ( in_array( $postData['guidescopeid'], array_column($ips, 'devid') )  )
                {
                    $errMsg .= '导星镜id与其他设备重复<br>';
                }

                if ( in_array( $postData['name'], array_column($ips, 'devname') )  )
                {
                    $errMsg .= '导星镜名称与其他设备重复<br>';
                }
            }
            //检查导星镜ip id 名称是否重复 结束
            if ( $errMsg !== '' ) return $errMsg;
            //开启事务 同时操作guideconf 和 devipid表
            Db::startTrans();
            $guide_res = Db::table('guideconf')->strict(false)->insert($postData);
            $ipid_res = Db::table('devipid')->strict(false)
                        ->insert([
                            'ip' => $postData['ip'],
                            'devid' => $postData['guidescopeid'],
                            'devname' => $postData['name'],
                            'teleid' => $postData['teleid'],
                            'dev' => 'guide',
                            'devnum' =>'1' ] );

            if ( $guide_res && $ipid_res ) //若同时更新ok
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
            $errMsg .= '导星望远镜配置存数据库失败!<br>';
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
        
            //将新上传的说明文件移至指定目录
            $info = $guideScopeFile->move($this->file_path . "/$dir", $fileName, true);
            if (!$info) //移动文件失败
            {
                $errMsg .= "上传{$fileName}失败!<br>";
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

     //各设备文件下载 ，此方法用路由请求
     public function downLoadFlie ($dir, $filename)
     {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }
     
        $file = $this->file_path  . DS . $dir . DS . $filename;
        $file = iconv('UTF-8', 'GBK', $file); //将整个路径转为GBK字符集
        if ( file_exists($file) )
        {
            header("Content-type:application/octet-stream"); //设置内容类型
            header("Content-Disposition:attachment;filename = ". $filename); //下载弹框的默认文件名
            header('Content-Transfer-Encoding: binary'); //设置传输方式
            //header("Accept-ranges:bytes");
            header("Content-length:".filesize($file)); //获取文件字节数
            //header("Accept-length:".filesize($file));
            readfile($file);
        }else{
            return 0;
        }
        
     }//各设备文件下载 结束

    //对ccd 增益-读出噪声值 页面表格相关字段进行排序，排序后返回json数据 此方法暂不使用
    /*protected function sort_data ($data, $col, $order)
    {
        switch ($col) {//根据提交的数据对相应列进行排序
            case 'readOut'://对读出速度字段排序
                //对$data排序
                $temp = json_decode ($data, true); //将json数据转为数组
               
                foreach ($temp as $k => $v)
                {
                    $arr[$k] = $v['readOut_speed']; //获取读出速度这一列
                }

                if ( $order == 'asc' )
                {
                    array_multisort ($arr, SORT_ASC, $temp);
                }elseif ( $order == 'desc' )
                {
                    array_multisort ($arr, SORT_DESC, $temp);
                }
                foreach ($temp as $k => $v) { //返回键名从1开始的数组
                    $a[$k+1] = $temp[$k];
                }
                return json_encode($a, JSON_FORCE_OBJECT);  break;
            case 'transfer'://对转移速度字段排序
                //对$data排序
                $temp = json_decode ($data, true); //将json数据转为数组
  
                foreach ($temp as $k => $v)
                {
                    $arr[$k] = $v['transfer_speed']; //获取转移速度这一列
                }

                if ( $order == 'asc' )
                {
                    array_multisort ($arr, SORT_ASC, $temp);
                }elseif ( $order == 'desc' )
                {
                    array_multisort ($arr, SORT_DESC, $temp);
                }
               
                foreach ($temp as $k => $v) { //返回键名从1开始的数组
                    $a[$k+1] = $temp[$k];
                }
                return json_encode($a, JSON_FORCE_OBJECT);  break;
            case 'gear'://对增益挡位字段排序
                //对$data排序
                $temp = json_decode ($data, true); //将json数据转为数组
  
                foreach ($temp as $k => $v)
                {
                    $arr[$k] = $v['gain_gear']; //获取增益挡位这一列
                }

                if ( $order == 'asc' )
                {
                    array_multisort ($arr, SORT_ASC, $temp);
                }elseif ( $order == 'desc' )
                {
                    array_multisort ($arr, SORT_DESC, $temp);
                }
               
                foreach ($temp as $k => $v) { //返回键名从1开始的数组
                    $a[$k+1] = $temp[$k];
                }
                return json_encode($a, JSON_FORCE_OBJECT);  break;
            case 'gain'://对增益值字段排序
                //对$data排序
                $temp = json_decode ($data, true); //将json数据转为数组
    
                foreach ($temp as $k => $v)
                {
                    $arr[$k] = $v['gainVal']; //获取增益值这一列
                }

                if ( $order == 'asc' )
                {
                    array_multisort ($arr, SORT_ASC, $temp);
                }elseif ( $order == 'desc' )
                {
                    array_multisort ($arr, SORT_DESC, $temp);
                }
            
                foreach ($temp as $k => $v) { //返回键名从1开始的数组
                    $a[$k+1] = $temp[$k];
                }
                return json_encode($a, JSON_FORCE_OBJECT);  break;
            case 'noise'://对噪声值字段排序
                //对$data排序
                $temp = json_decode ($data, true); //将json数据转为数组
    
                foreach ($temp as $k => $v)
                {
                    $arr[$k] = $v['noiseVal']; //获取噪声值这一列
                }

                if ( $order == 'asc' )
                {
                    array_multisort ($arr, SORT_ASC, $temp);
                }elseif ( $order == 'desc' )
                {
                    array_multisort ($arr, SORT_DESC, $temp);
                }
            
                foreach ($temp as $k => $v) { //返回键名从1开始的数组
                    $a[$k+1] = $temp[$k];
                }
                return json_encode($a, JSON_FORCE_OBJECT);  break;
        }
    }//对ccd 增益-读出噪声值 页面表格相关字段进行排序，排序后返回json数据 结束*/

    //判断各设备id是否重复
    /*protected function devIdSame ($devId)
    {
        $ids = file_get_contents('devId.txt'); //获取devId.txt文件内的各设备id

        if ( $ids === false ) return true; //打开文件失败，返回true

        if ( strpos($ids, $devId) !== false ) //$devId在$ids内
        {
            return true;
        }

        $devId .= '#'; //子设备id连上'#'
        $res = file_put_contents('devId.txt', $devId, FILE_APPEND | LOCK_EX); //写入文件

        if ( $res === false ) //写入失败
        {
            return true;
        }
    }*///判断各设备id是否重复 结束
}