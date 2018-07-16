<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cache;
use think\Db;
use think\Session;

class Page extends Base
{
    //根据at参数显示不同望远镜页面
    public function at_page($at)
    {
        //根据$at获取相应望远镜的固定属性配置数据
        $config_data = $this->get_conf ($at);
        
        //判断$result中 转台 ccd 调焦器 是否有未配置的 
        $err_notice = ''; //错误提示

        if ( !isset($config_data['gimbal']) )
        {
            $err_notice .= '此望远镜转台的固定属性未配置或读取失败!<br>';
        }

        if ( !isset($config_data['ccd']) )
        {
            $err_notice .= '此望远镜CCD的固定属性未配置或读取失败!<br>';
        }

        if ( !isset($config_data['focus']) )
        {
            $err_notice .= '此望远镜调焦器的固定属性未配置或读取失败!<br>';
        }

        if ( $err_notice !== '')
        {
            $vars['err_notice'] = $err_notice;
            return view('atpage_notice', $vars);
        }

        //如果$result中无数据，即未进行任何配置
        if ( !$config_data )
        {
            $vars['not_config'] = 1;
            return view('atpage_notice', $vars);
        }

        //计算晨光始、昏影终
        $mjd = GetJD();  //修正儒略日
        
        $sunRise = 0; //晨光始
        $sunSet = 0; //昏影终
        
        sunTwilight ($sunRise, $sunSet, $mjd, 8);
        $sunRise = substr(data2Time ($sunRise), 1, 8);
        $sunSet = substr(data2Time ($sunSet), 1, 8);

        //将晨光始 昏影终 存入session
        Session::set('sunRise', $sunRise);
        Session::set('sunSet', $sunSet);
        //返回数据
        $result['at_id'] = $at;  //返回该望远镜在atlist表中的id字段
        $result['configData'] = json_encode ($config_data); //将配置数据以json格式返回

        $vars['data'] = $result;
        return view('atpage', $vars);
    }//根据at参数显示不同望远镜页面 结束

    //显示望远镜配置页面 /////////
    public function at_config()
    {
        //首先获取已添加的望远镜列表, 查字段id和atname
        $atList = Db::table('atlist')->field('id, atname')->order('id asc')->select();
        if (!$atList)
        {//还未添加望远镜
            $this->error('请先添加望远镜!');
        }

        $vars['atList'] = $atList;

        return view('config', $vars);  //之前配置页面的模板文件是page/config-0.html
    }//望远镜配置页面 结束

    //ajax请求 判断19个动态增减的固定是否已添加够 并获取相应望远镜的配置数据/////////
    public function config()
    {//halt(input());
        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

        $id = input('id'); //获取相应望远镜的id 在atlist表中此$id对应id字段,其他表中对应teleid字段
        if ( !$id ) return '提交的数据有误! 请重新提交!';
        /*如下开始
        *判断数据表'confoption'内19个动态增减的固定属性是否已全部添加，
         否则无法进行各望远镜的配置
        */
        $confOption = Db::table('confoption')->group('conf')->field('conf')->select();
        $confNum = count($confOption);  //动态增减的固定属性的数量
        
        $errMsg = '';  //错误提示
        if ( $confNum < 14 )    //不够14个时
        {//逐一判断缺少了哪个固定属性
            if ( !in_array(['conf' => 'focustype'], $confOption) )
            {//缺少了'焦点类型'
                $errMsg .= '固定属性还须添加：焦点类型!<br>';
            }
            if ( !in_array(['conf' => 'imageBits'], $confOption) )
            {//缺少了'图像位数'
                $errMsg .= '固定属性还须添加：图像位数!<br>';
            }
            if ( !in_array(['conf' => 'coolerMode'], $confOption) )
            {//缺少了'制冷方式'
                $errMsg .= '固定属性还须添加：制冷方式!<br>';
            }
            if ( !in_array(['conf' => 'readoutMode'], $confOption) )
            {//缺少了'读出模式'
                $errMsg .= '固定属性还须添加：读出模式!<br>';
            }
            if ( !in_array(['conf' => 'gainmode'], $confOption) )
            {//缺少了'增益模式'
                $errMsg .= '固定属性还须添加：增益模式!<br>';
            }
            if ( !in_array(['conf' => 'ShutterType'], $confOption) )
            {//缺少了'快门类型'
                $errMsg .= '固定属性还须添加：快门类型!<br>';
            }
            if ( !in_array(['conf' => 'ShutterMode'], $confOption) )
            {//缺少了'快门模式'
                $errMsg .= '固定属性还须添加：快门模式!<br>';
            }
            if ( !in_array(['conf' => 'InterfaceType'], $confOption) )
            {//缺少了'ccd接口类型'
                $errMsg .= '固定属性还须添加：ccd接口类型!<br>';
            }
            if ( !in_array(['conf' => 'ExposeTriggerMode'], $confOption) )
            {//缺少了'曝光触发模式'
                $errMsg .= '固定属性还须添加：曝光触发模式!<br>';
            }
            if ( !in_array(['conf' => 'FilterSystem'], $confOption) )
            {//缺少了'滤光片类型'
                $errMsg .= '固定属性还须添加：滤光片类型!<br>';
            }
            if ( !in_array(['conf' => 'FilterShape'], $confOption) )
            {//缺少了'滤光片形状'
                $errMsg .= '固定属性还须添加：滤光片形状!<br>';
            }
            if ( !in_array(['conf' => 'slaveDomeType'], $confOption) )
            {//缺少了'随动圆顶类型'
                $errMsg .= '固定属性还须添加：随动圆顶类型!<br>';
            }
            if ( !in_array(['conf' => 'openDomeType'], $confOption) )
            {//缺少了'全开圆顶类型'
                $errMsg .= '固定属性还须添加：全开圆顶类型!<br>';
            }
            if ( !in_array(['conf' => 'opticalStructure'], $confOption) )
            {//缺少了'导星镜焦点类型'
                $errMsg .= '固定属性还须添加：导星镜焦点类型!';
            }
        }/*检查判断数据表'confoption'内14个动态增减的固定属性  结束*/
       
        if ($errMsg != '')
        {//还须添加固定属性
            return $errMsg;
        }else{//获取相应望远镜的配置数据，以json格式返回
            /*1、获取动态增减的固定属性数据*/
            $result['confOption'] = $this->get_14confOption ();            
            /*获取动态增减的固定属性数据 结束*/

            /*查转台的配置数据 需要查atlist表、gimbalconf表*/
            $at_data = Db::table('atlist')->where('id', $id)->find();
            $gimbal_conf = Db::table('gimbalconf')->where('teleid', $id)->find();
            //合并以上2个数组
            if (!$at_data)
            {
                $at_data = [];
            }
            if (!$gimbal_conf)
            {
                $gimbal_conf = [];
            }
            //合并以上2个数据
            $gimbal_data = array_merge ($at_data, $gimbal_conf);
            $result['gimbal_data'] = $gimbal_data;

            /*查gimbal相关文件*/
            //根据$id查询去相应目录中查询上传的说明文件
            $file_path = ROOT_PATH . 'public' . DS . 'uploads'; //存储路径
            //gimbal目录
            $dir = 'gimbal' . $id;
            if ( file_exists($file_path . "/$dir") )
            {
                $res = scandir ($file_path . "/$dir");

                if ( $res !== false && count($res) > 2 )
                {
                    unset ($res[0], $res[1]); //删除前2个数据
                    foreach ( $res as $k)
                    {
                        $result['gimbal_file'][] = iconv('GBK', 'UTF-8', $k);  //将文件名转为utf-8
                    }
                }
            }/*查gimbal相关文件 结束*/
            /*查转台的配置数据 结束*/

            //据此$id去各自设备的固定属性表中获取数据
            /*查ccd配置数据*/
            $ccd_data = Db::table('ccdconf')->where('teleid', $id)->order('ccdno asc')->select();
            $ccd_num = count ($ccd_data);  //已配置的ccd数量

            if ( $ccd_num > 0 )  //循环遍历$ccd_data中的复选框配置数据
            {
                for ($ccd_i = 0; $ccd_i < $ccd_num; $ccd_i++)
                {
                    foreach ( $ccd_data[$ccd_i] as $v)
                    {
                        if ( is_string($ccd_data[$ccd_i]['readoutspeed']) )
                        {//处理读出速度模式
                            $ccd_data[$ccd_i]['readoutspeed'] = explode('#', $ccd_data[$ccd_i]['readoutspeed']);
                        }else if( $ccd_data[$ccd_i]['readoutspeed'] === null ){
                            $ccd_data[$ccd_i]['readoutspeed'] = [];
                        }

                        if ( is_string($ccd_data[$ccd_i]['readoutmode']) )
                        {//处理读出模式
                            $ccd_data[$ccd_i]['readoutmode'] = explode('#', $ccd_data[$ccd_i]['readoutmode']);
                        }else if( $ccd_data[$ccd_i]['readoutmode'] === null ){
                            $ccd_data[$ccd_i]['readoutmode'] = [];
                        }

                        if ( is_string($ccd_data[$ccd_i]['transferspeed']) )
                        {//处理转移速度模式
                            $ccd_data[$ccd_i]['transferspeed'] = explode('#', $ccd_data[$ccd_i]['transferspeed']);
                        }else if( $ccd_data[$ccd_i]['transferspeed'] === null ){
                            $ccd_data[$ccd_i]['transferspeed'] = [];
                        }

                        if ( is_string($ccd_data[$ccd_i]['gainmode']) )
                        {//处理增益模式
                            $ccd_data[$ccd_i]['gainmode'] = explode('#', $ccd_data[$ccd_i]['gainmode']);
                        }else if( $ccd_data[$ccd_i]['gainmode'] === null ){
                            $ccd_data[$ccd_i]['gainmode'] = [];
                        }

                        if ( is_string($ccd_data[$ccd_i]['shuttermode']) )
                        {//处理快门模式
                            $ccd_data[$ccd_i]['shuttermode'] = explode('#', $ccd_data[$ccd_i]['shuttermode']);
                        }else if( $ccd_data[$ccd_i]['shuttermode'] === null ){
                            $ccd_data[$ccd_i]['shuttermode'] = [];
                        }

                        if ( is_string($ccd_data[$ccd_i]['interfacetype']) )
                        {//处理接口类型
                            $ccd_data[$ccd_i]['interfacetype'] = explode('#', $ccd_data[$ccd_i]['interfacetype']);
                        }else if( $ccd_data[$ccd_i]['interfacetype'] === null ){
                            $ccd_data[$ccd_i]['interfacetype'] = [];
                        }

                        if ( is_string($ccd_data[$ccd_i]['exposetriggermode']) )
                        {//处理曝光触发模式
                            $ccd_data[$ccd_i]['exposetriggermode'] = explode('#', $ccd_data[$ccd_i]['exposetriggermode']);
                        }else if( $ccd_data[$ccd_i]['exposetriggermode'] === null ){
                            $ccd_data[$ccd_i]['exposetriggermode'] = [];
                        }
                    }
                }
            }//循环遍历处理ccd配置中checkbox 结束 

            $ccd_data['ccd_num'] = $ccd_num;
            $result['ccd_data'] = $ccd_data; //此$ccd_data中可能有多个配置数组
            
            /*查ccd-No1相关文件 每个望远镜的各ccd要单独建一个目录存放文件：如ccd1_1，ccd1_2*/
            //ccd目录
            $dir = 'ccd' . $id;
            //接下来 循环去查每个ccd对应目录中的文件
            for ($ccd_dir_i = 1; $ccd_dir_i <= $ccd_num; $ccd_dir_i++)
            {
                if ( file_exists($file_path . "/$dir" . '_' . $ccd_dir_i) )
                {
                    $res = scandir ($file_path . "/$dir" . '_' . $ccd_dir_i);

                    if ( $res !== false && count($res) > 2 )
                    {//有上传文件
                        unset ($res[0], $res[1]); //删除前2个数据
                        foreach ( $res as $k)
                        {
                            $result['ccd_file'][$ccd_dir_i][] = iconv('GBK', 'UTF-8', $k);  //将文件名转为utf-8
                        }
                    }else{//没有上传文件
                        $result['ccd_file'][$ccd_dir_i] = 0;
                    }
                }
            }/*查ccd-No1相关文件 结束*/
            /*查ccd配置数据 结束*/
            
            /*查滤光片配置数据*/
            $fiter_data = Db::table('filterconf')->where('teleid', $id)->find();
            //隶属望远镜的值 是at_data['atname'] $ccd_data['atname'] = $at_data['atname'];
            //if ( isset ( $at_data['atname'] ) ) $fiter_data['atname'] = $at_data['atname'];
         
            $result['filter_data'] = $fiter_data;

            /*查滤光片相关文件*/
            //滤光片之 目录
            $dir = 'filter' . $id;
            if ( file_exists($file_path . "/$dir") )
            {
                $res = scandir ($file_path . "/$dir");

                if ( $res !== false && count($res) > 2 )
                {
                    unset ($res[0], $res[1]); //删除前2个数据
                    foreach ( $res as $k)
                    {
                        $result['filter_file'][] = iconv('GBK', 'UTF-8', $k);  //将文件名转为utf-8
                    }
                }
            }/*查滤光片相关文件 结束*/
            /*查滤光片配置数据 结束*/

             /*查随动圆顶配置数据*/
             $sDome_data = Db::table('sdomeconf')->where('teleid', $id)->find();
             //if ( isset ( $at_data['atname'] ) ) $sDome_data['atname'] = $at_data['atname'];
          
             $result['sDome_data'] = $sDome_data;
 
             /*查随动圆顶相关文件*/
             //随动圆顶目录
             $dir = 'sDome' . $id;
             if ( file_exists($file_path . "/$dir") )
             {
                 $res = scandir ($file_path . "/$dir");
 
                 if ( $res !== false && count($res) > 2 )
                 {
                     unset ($res[0], $res[1]); //删除前2个数据
                     foreach ( $res as $k)
                     {
                        $result['sDome_file'][] = iconv('GBK', 'UTF-8', $k);  //将文件名转为utf-8
                     }
                 }
             }/*查随动圆顶相关文件 结束*/
             /*查随动圆顶配置数据 结束*/

             /*查全开圆顶配置数据*/
             $oDome_data = Db::table('odomeconf')->where('teleid', $id)->find();
             //if ( isset ( $at_data['atname'] ) ) $oDome_data['atname'] = $at_data['atname'];
          
             $result['oDome_data'] = $oDome_data;
 
             /*查全开圆顶相关文件*/
             //全开圆顶目录
             $dir = 'oDome' . $id;
             if ( file_exists($file_path . "/$dir") )
             {
                 $res = scandir ($file_path . "/$dir");
 
                 if ( $res !== false && count($res) > 2 )
                 {
                     unset ($res[0], $res[1]); //删除前2个数据
                     foreach ( $res as $k)
                     {
                        $result['oDome_file'][] = iconv('GBK', 'UTF-8', $k);  //将文件名转为utf-8
                     }
                 }
             }/*查全开圆顶相关文件 结束*/
             /*查全开圆顶配置数据 结束*/

             /*查调焦器配置数据*/
             $focus_data = Db::table('focusconf')->where('teleid', $id)->find();
             //if ( isset ( $at_data['atname'] ) ) $focus_data['atname'] = $at_data['atname'];
          
             $result['focus_data'] = $focus_data;
 
             /*查调焦器相关文件*/
             //调焦器目录
             $dir = 'focus' . $id;
             if ( file_exists($file_path . "/$dir") )
             {
                 $res = scandir ($file_path . "/$dir");
 
                 if ( $res !== false && count($res) > 2 )
                 {
                     unset ($res[0], $res[1]); //删除前2个数据
                     foreach ( $res as $k)
                     {
                        $result['focus_file'][] = iconv('GBK', 'UTF-8', $k);  //将文件名转为utf-8
                     }
                 }
             }/*查调焦器相关文件 结束*/
             /*查调焦器配置数据 结束*/

             /*查导星望远镜配置数据*/
             $guide_data = Db::table('guideconf')->where('teleid', $id)->find();
             //if ( isset ( $at_data['atname'] ) ) $guide_data['atname'] = $at_data['atname'];
          
             $result['guide_data'] = $guide_data;
 
             /*查导星望远镜相关文件*/
             //调焦器目录
             $dir = 'guideScope' . $id;
             if ( file_exists($file_path . "/$dir") )
             {
                 $res = scandir ($file_path . "/$dir");
 
                 if ( $res !== false && count($res) > 2 )
                 {
                     unset ($res[0], $res[1]); //删除前2个数据
                     foreach ( $res as $k)
                     {
                        $result['guide_file'][] = iconv('GBK', 'UTF-8', $k);  //将文件名转为utf-8
                     }
                 }
             }/*查调焦器相关文件 结束*/
             /*查调焦器配置数据 结束*/
            //据此$id去各自设备的固定属性表中获取数据 结束
            
            //return json数据给前端
            return json_encode ($result);
        }//获取相应望远镜的配置数据，以json格式返回 结束
    }//ajax请求 判断19个动态增减的固定是否已添加够 并获取相应望远镜的配置数据 结束

    //显示望远镜列表 /////////
    public function atlist()
    {
        $res = Db::table('atlist')->select(); //获取望远镜列表中的望远镜数量
        if(!$res)
        {
            $vars['noAt'] = '无望远镜!';
        }else{
            $vars['atList'] = $res;
        }
        return view('atlist', $vars);
    }//显示望远镜列表 结束

    //显示添加望远镜页面 /////////
    public function at_add()
    {
        return view('atadd');
    }//显示添加望远镜页面 结束

    //执行添加望远镜 /////////
    public function at_doadd()
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();
        if (!$postData)
        {
            return '提交数据失败!';
        }

        //生成一个唯一标识此望远镜的id, 此望远镜名称的md5值
        //$postData['atid'] = md5 ($postData['atname']);
        $postData['atid'] = '';

        /*验证望远镜添加表单的数据*/
        $errMsg = ''; //存储错误提示信息

        //验证望远镜id
        // if ( !$this->check_atId( $postData['atid']) )
        // {
        //     $errMsg .= '望远镜id格式错误!<br>';
        // }

        //验证望远镜名
        if ( !$this->check_name( $postData['atname']) )
        {
            $errMsg .=  '望远镜名格式错误!<br>';
        }

        //验证望远镜观测站
        if ( !$this->check_address( $postData['address']) )
        {
            $errMsg .=  '望远镜所属观测站格式错误!<br>';
        }

        //验证望远镜观 经度
        if ( !$this->check_longitude( $postData['longitude']) )
        {
            $errMsg .=  '望远镜经度格式错误!<br>';
        }

        //验证望远镜观 纬度
        if ( !$this->check_latitude( $postData['latitude']) )
        {
            $errMsg .=  '望远镜纬度格式错误!<br>';
        }

        //验证望远镜观 海拔
        if ( !$this->check_altitude( $postData['altitude']) )
        {
            $errMsg .=  '望远镜海拔格式错误!<br>';
        }

        //验证望远镜观 口径
        if ( !$this->check_aperture( $postData['aperture']) )
        {
            $errMsg .= '望远镜口径格式错误!<br>';
        }

        if ($errMsg != '')
        {
            return $errMsg;
        }/*验证望远镜添加表单的数据 结束*/

        //查询新提交的望远镜id或望远镜名 是否在数据表中唯一
        $old = Db::table('atlist')->where('atname', $postData['atname'])->find();
        
        if ($old)
        {
            return '望远镜名称重复,请重新选择!';
        }
        //执行数据添加
        $res = Db::table('atlist')->insert($postData);
        if ($res)
        {
            return '添加望远镜ok!';
        }else{
            return '添加望远镜失败!';
        }

    }//执行添加望远镜 结束

    //显示要编辑的望远镜数据  /////////
    public function at_edit($at)
    {   
        //查对应望远镜数据
        $res = Db::table('atlist')->where('id', $at)->find();
        if (!$res)
        {
           $this->error('读取数据失败!');
        }else{
            $vars['atData'] = $res;
            return view ('atedit', $vars);
        }

    }//显示要编辑的望远镜数据 结束

    //执行编辑望远镜数据  /////////
    public function at_doedit()
    {  
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();
        if (!$postData)
        {
            return '提交数据失败!';
        }

        /*验证望远镜添加表单的数据*/
        $errMsg = ''; //存储错误提示信息
        //验证望远镜id
        // if ( !$this->check_atId( $postData['atid']) )
        // {
        //     $errMsg .= '望远镜id格式错误!<br>';
        // }

        //验证望远镜名
        if ( !$this->check_name( $postData['atname']) )
        {
            $errMsg .=  '望远镜名格式错误!<br>';
        }

        //验证望远镜观测站
        if ( !$this->check_address( $postData['address']) )
        {
            $errMsg .=  '望远镜所属观测站格式错误!<br>';
        }

        //验证望远镜观 经度
        if ( !$this->check_longitude( $postData['longitude']) )
        {
            $errMsg .=  '望远镜经度格式错误!<br>';
        }

        //验证望远镜观 纬度
        if ( !$this->check_latitude( $postData['latitude']) )
        {
            $errMsg .=  '望远镜纬度格式错误!<br>';
        }

        //验证望远镜观 海拔
        if ( !$this->check_altitude( $postData['altitude']) )
        {
            $errMsg .=  '望远镜海拔格式错误!<br>';
        }

        //验证望远镜观 口径
        // if ( !$this->check_aperture( $postData['aperture']) )
        // {
        //     $errMsg .= '望远镜口径格式错误!<br>';
        // }

        if ($errMsg != '')
        {
            return $errMsg;
        }/*验证望远镜添加表单的数据 结束*/

        //查询新提交的望远镜id或望远镜名 是否在数据表中唯一
        $old = Db::table('atlist')->where('atname', $postData['atname'])->find();
        
        if ($old)
        {
            return '望远镜名称重复,请重新选择!';
        }

        //执行更新
        $res = Db::table('atlist')->where('id', $postData['id'])->update($postData);
        if ($res)
        {
            return '编辑望远镜信息ok!';
        }else{
            return '编辑望远镜信息失败!';
        }

    }//执行编辑望远镜数据 结束

    //删除望远镜 
    public function at_delete ($atid)
    {
        //首先删除望远镜列表中相应数据
        $res = Db::table('atlist')->where('atid', $atid)->delete();
        if ($res)
        {
            $this->success ('删除成功!', '/atlist');
        }else{
            $this->error ('删除失败!');
        }
        //然后开始事务 去删除此atid关联的此望远镜其他数据
        /*
        Db::startTrans();
        
        //删除其他表相关数据
        if ($res && 其他表删除ok)
        {
            Db::commit();
        }else{
            Db::rollback();
        }
        */

    }//删除望远镜  结束

    //显示首页
    public function front ()
    {
        //获取天气预报数据////////////////////////////////////////
        if ($weathStr = Cache::get('weather'))
        {
            //获取当天预报数据的html,$match[0]
            preg_match('/今天[\s\S]*今天/', $weathStr, $match);
            
            if (strpos($match[0],'colspan="2"')) //是晚上
            {
                $night = 1;
                preg_match_all('/> ?[^a-zA-Z\s]{2,} ?</', $match[0], $matchs);
                //获取天气图标
                preg_match('/http:[\S]*white[\S]{5,15}png/', $match[0],$matchImg);
                
                //晚上的天气图标
                $nightPic = $matchImg[0];        
                //天气状况
                $weatherNight = str_replace(['<','>'], '', $matchs[0][2]);
                //温度
                $tmpNight = str_replace(['<','>'], '', $matchs[0][3]);
                //风向
                $windNight = str_replace(['<','>'], '', $matchs[0][4]);
                //风力
                $windPowerNight = str_replace(['<','>'], '', $matchs[0][5]);
                
            }else{//白天
                $day = 1;
                preg_match_all('/> ?[^a-zA-Z\s]{2,} ?</', $match[0], $matchs);
                //获取天气图标
                preg_match_all('/http:[\S]*white[\S]{5,15}png/', $match[0],$matchImg);
               
                $dayPic = $matchImg[0][0];      //白天的气象图标
                $nightPic = $matchImg[0][1];    //晚上的气象图标
                
                //天气状况
                $weatherDay = str_replace(['<','>'], '', $matchs[0][2]);
                $weatherNight = str_replace(['<','>'], '', $matchs[0][3]);
                //温度
                $tmpDay = str_replace(['<','>'], '', $matchs[0][4]);
                $tmpNight = str_replace(['<','>'], '', $matchs[0][5]);
                //风向
                $windDay = str_replace(['<','>'], '', $matchs[0][6]);
                $windNight = str_replace(['<','>'], '', $matchs[0][7]);
                //风力
                $windPowerDay = str_replace(['<','>'], '', $matchs[0][8]);
                $windPowerNight = str_replace(['<','>'], '', $matchs[0][9]);
            }
        }else{//缓存获取失败，从网络抓取
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, 'http://www.nmc.cn/publish/forecast/AHE/xinglong.html');
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //返回字符串数据
			curl_setopt($ch, CURLOPT_FAILONERROR, 1); //出错时停止
			$weathStr = curl_exec($ch); //将远程数据存入变量

			if( curl_errno($ch) )
			{
				$weatherError = '网络异常，暂未获取天气预报,检查您的网络设置!';
			}
			curl_close($ch);
			
            if ($weathStr)
            {//获取成功，写入缓存
                Cache::set('weather', $weathStr, 3600);
				
				//获取当天预报数据的html,$match[0]
				preg_match('/今天[\s\S]*今天/', $weathStr, $match);
				
				if (strpos($match[0],'colspan="2"')) //是晚上
				{
					$night = 1;
					preg_match_all('/> ?[^a-zA-Z\s]{2,} ?</', $match[0], $matchs);
					//获取天气图标
					preg_match('/http:[\S]*white[\S]{5,15}png/', $match[0],$matchImg);
					
					//晚上的天气图标
					$nightPic = $matchImg[0];
					//天气状况
					$weatherNight = str_replace(['<','>'], '', $matchs[0][2]);
					//温度
					$tmpNight = str_replace(['<','>'], '', $matchs[0][3]);
					//风向
					$windNight = str_replace(['<','>'], '', $matchs[0][4]);
					//风力
					$windPowerNight = str_replace(['<','>'], '', $matchs[0][5]);
					
				}else{//白天
					$day = 1;
					preg_match_all('/> ?[^a-zA-Z\s]{2,} ?</', $match[0], $matchs);
					//获取天气图标
					preg_match_all('/http:[\S]*white[\S]{5,15}png/', $match[0],$matchImg);
				   
					$dayPic = $matchImg[0][0];      //白天的气象图标
					$nightPic = $matchImg[0][1];    //晚上的气象图标
					//天气状况
					$weatherDay = str_replace(['<','>'], '', $matchs[0][2]);
					$weatherNight = str_replace(['<','>'], '', $matchs[0][3]);
					//温度
					$tmpDay = str_replace(['<','>'], '', $matchs[0][4]);
					$tmpNight = str_replace(['<','>'], '', $matchs[0][5]);
					//风向
					$windDay = str_replace(['<','>'], '', $matchs[0][6]);
					$windNight = str_replace(['<','>'], '', $matchs[0][7]);
					//风力
					$windPowerDay = str_replace(['<','>'], '', $matchs[0][8]);
					$windPowerNight = str_replace(['<','>'], '', $matchs[0][9]);
				}
            }
            
        }//天气预报获取ok////////////////////////////////////////
		
        //白天 天气数据模板赋值
        if (isset($day))
        {
           $vars = [
                'day' => $day,
                'dayPic' => $dayPic,            
                'nightPic' => $nightPic,         
                'weatherDay' => $weatherDay,        
                'weatherNight' => $weatherNight,      
                'tmpDay' => $tmpDay,   
                'tmpNight' => $tmpNight,  
                'windDay' => $windDay, 
                'windNight' => $windNight,
                'windPowerDay' => $windPowerDay,
                'windPowerNight' => $windPowerNight,
            ];
        }
        
        //夜晚天气数据模板赋值
        if (isset($night))
        {
            $vars = [
                'night' => $night,                
                'nightPic' => $nightPic,                
                'weatherNight' => $weatherNight,                
                'tmpNight' => $tmpNight,
                'windNight' => $windNight,
                'windPowerNight' => $windPowerNight,               
            ];
        }
        //云图错误
        if (isset($wxytError))
        {
           $vars['wxytError'] = $wxytError;
        }
        
        //天气获取错误
        if (isset($weatherError))
        {
           $vars['weatherError'] = $weatherError;
        }

        //读取望远镜列表 将已添加的望远镜显示在下拉框中
        $atList = Db::table('atlist')->order('id', 'asc')->field('id, atname')->select();
        if ($atList)
        {
            $vars['atList'] = $atList;
        }
      		
        return view('front', $vars);
    }//显示首页 结束

    //显示天气详情页
    public function weather ()
    {		
		//获取卫星云图图片///////////////////////////////////////////// 
        if ($wxyt = Cache::get('wxyt'))    //缓存有效
        {
            preg_match('/<img id="imgpath"([\s\S]){50,260}不存在！\'">/', $wxyt, $match);
            preg_match('/src="http:\/\/([\s\S]){50,200}\d+"/', $match[0], $match1);
            $cloudPic = $match1[0];
        }else{//从网络抓取数据            
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, 'http://www.nmc.cn/publish/satellite/fy2.htm');
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //返回字符串数据
			curl_setopt($ch, CURLOPT_FAILONERROR, 1); //出错时停止
			$cloudPicStr = curl_exec($ch); //将远程数据存入变量

			if( curl_errno($ch) )
			{
				$wxytError = '网络异常，暂未获取卫星云图,检查您的网络设置!';
				$cloudPic  = null;
			}
			curl_close($ch);
			
            if ($cloudPicStr)
            {//抓取成功
                Cache::set('wxyt', $cloudPicStr, 3600); //写入缓存
				
				$wxyt = Cache::get('wxyt');
				preg_match('/<img id="imgpath"([\s\S]){50,260}不存在！\'">/', $wxyt, $match);
				preg_match('/src="http:\/\/([\s\S]){50,200}\d+"/', $match[0], $match1);
				$cloudPic = $match1[0];
            }
              
        } //气象云图获取ok//////////////////////////////////////////
		
		//云图错误
        if (isset($wxytError))
        {
           $vars['wxytError'] = $wxytError;
        }
        
        //卫星云图赋值
        if (isset($cloudPic))
        {
           $vars['cloudPic'] = $cloudPic;
        }

		return view('weather', $vars);
    }//显示天气详情页 结束///

    /*验证望远镜id格式*/
    /*protected function check_atId ($atId)
    {
        //合法格式：02000
        if ( strlen($atId) != 5 || !is_numeric($atId) || strpos($atId, '0') != 0 )
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜id格式 结束*/

    /*验证望远镜名格式*/
    protected function check_name ($name)
    {
        //合法格式：0.6m望远镜
        if ( strlen($name) < 2 )
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名格式 结束*/

    /*验证望远镜地址格式*/
    protected function check_address ($address)
    {
        //合法格式：字符长度不低于3
        if ( strlen($address) < 3 )
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜地址格式 结束*/

    /*验证望远镜 经度*/
    protected function check_longitude ($longitude)
    {
        //合法格式：-180 ~ 180
        if ( !is_numeric($longitude) || $longitude > 180 || $longitude < -180)
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名 经度 结束*/

    /*验证望远镜 纬度*/
    protected function check_latitude ($latitude)
    {
        //合法格式：-90 ~ 90
        if ( !is_numeric($latitude) || $latitude > 90 || $latitude < -90)
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名 纬度 结束*/

    /*验证望远镜 海拔*/
    protected function check_altitude ($altitude)
    {
        //合法格式：-1000 ~ 1000
        if ( !is_numeric($altitude) || $altitude > 6000 || $altitude < -1000)
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名 海拔 结束*/

    /*验证望远镜 口径*/
    protected function check_aperture ($aperture)
    {
        //合法格式：216.0
        if ( !is_numeric($aperture) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名 口径 结束*/

    /*获取19个动态增减的固定属性 数据*/
    protected function get_14confOption ()
    {
        /*获取所有配置选项 */
        $confOption = Db::table('confoption')->field('conf, conf_val')->select();
        /*遍历配置选项数据 组装为：配置项=>[配置数据]*/
        foreach ($confOption as $v)
        {
            $res[$v['conf']][]= $v['conf_val'];
        }
        return $res;
    }/*获取19个动态增减的固定属性 数据 结束*/

    /*获取相应望远镜固定属性数据*/
    protected function get_conf ($at)
    {
        $result = []; //定义存储数据的数组

        $atData = Db::table('atlist')->where('id', $at)->find();
        //halt($atData);
        //查转台配置数据
        $gimbal = Db::table('gimbalconf')->where('teleid', $at)->find();
        $gimbal['longitude'] = $atData['longitude']; //经度
        $gimbal['latitude'] = $atData['latitude']; //纬度
        $gimbal['aperture'] = $atData['aperture']; //口径
        if ( $gimbal )
        {
            $result['gimbal'] = $gimbal;
            $result['has_gimbal'] = 1; //表示有转台的配置数据
        }

        $ccd_num = Db::table('ccdconf')->where('teleid', $at)->count('ccdno'); //查ccd数量
        //查第ccd配置数据
        $ccd = Db::table('ccdconf')->where('teleid', $at)->order('ccdno', 'asc')->select();
        if ( $ccd )
        {
            foreach ($ccd as $k => $v) 
            {
                if ( $ccd[$k]['gainmode'] ) //处理增益模式，处理后直接在页面显示
                {
                    $ccd[$k]['gainmode'] = str_replace ('#', ', ', $ccd[$k]['gainmode']);
                }
                if ( $ccd[$k]['readoutmode'] ) //处理读出模式，处理后直接在页面显示
                {
                    $ccd[$k]['readoutmode'] = str_replace ('#', ', ', $ccd[$k]['readoutmode']);
                }
                if ( $ccd[$k]['shuttermode'] ) //处理快门模式，处理后直接在页面显示
                {
                    $ccd[$k]['shuttermode'] = str_replace ('#', ', ', $ccd[$k]['shuttermode']);
                }
                if ( $ccd[$k]['interfacetype'] ) //处理接口类型，处理后直接在页面显示
                {
                    $ccd[$k]['interfacetype'] = str_replace ('#', ', ', $ccd[$k]['interfacetype']);
                }
                if ( $ccd[$k]['exposetriggermode'] ) //处理曝光触发模式，处理后直接在页面显示
                {
                    $ccd[$k]['exposetriggermode'] = str_replace ('#', ', ', $ccd[$k]['exposetriggermode']);
                }
            }
            
            // if ( $ccd['gainmode'] )  //处理增益模式，处理后直接在页面显示
            // {
            //     $ccd['gainmode'] = str_replace ('#', ', ', $ccd['gainmode']);
            // }

            // if ( $ccd['readoutmode'] )  //处理读出模式，处理后直接在页面显示
            // {
            //     $ccd['readoutmode'] = str_replace ('#', ', ', $ccd['readoutmode']);
            // }

            // //处理快门模式
            // if ( $ccd['shuttermode'] )  //处理快门模式，处理后直接在页面显示
            // {
            //     $ccd['shuttermode'] = str_replace ('#', ', ', $ccd['shuttermode']);
            // }

            // //处理接口类型
            // if ( $ccd['interfacetype'] )  //处理接口类型，处理后直接在页面显示
            // {
            //     $ccd['interfacetype'] = str_replace ('#', ', ', $ccd['interfacetype']);
            // }

            // //处理曝光触发模式
            // if ( $ccd['exposetriggermode'] )  //处理曝光触发模式，处理后直接在页面显示
            // {
            //     $ccd['exposetriggermode'] = str_replace ('#', ', ', $ccd['exposetriggermode']);
            // }

            $result['ccd'] = $ccd;
            $result['ccd_num'] = $ccd_num; //表示有ccd的配置数据*/
        }//查ccd配置数据 结束
        
        //查滤光片配置数据
        $filter = Db::table('filterconf')->where('teleid', $at)->find();
        if ( $filter )
        {
            //将filtername字段处理为json数据，给atpage页面的js进行赋值
            if ( $filter['filtername'] )
            {
                $temp_filtername = explode ('/', $filter['filtername']);
                $filter['filter_name_json'] = json_encode ($temp_filtername);
            }
            $result['filter'] = $filter;
            $result['has_filter'] = 1; //表示有filter的配置数据
        }

        //查随动圆顶配置数据
        $sDome = Db::table('sdomeconf')->where('teleid', $at)->find();
        if ( $sDome )
        {
            $result['sDome'] = $sDome;
            $result['has_sDome'] = 1; //表示有 随动圆顶的配置数据
        }

        //查全开圆顶配置数据
        $oDome = Db::table('odomeconf')->where('teleid', $at)->find();
        if ( $oDome )
        {
            $result['oDome'] = $oDome;
            $result['has_oDome'] = 1; //表示有 全开圆顶的配置数据
        }

        //查调焦器配置数据
        $focus = Db::table('focusconf')->where('teleid', $at)->find();
        if ( $focus )
        {
            $result['focus'] = $focus;
            $result['has_focus'] = 1; //表示有 调焦器的配置数据
        }

        //查导星镜配置数据
        $guide = Db::table('guideconf')->where('teleid', $at)->find();
        if ( $guide )
        {
            $result['guide'] = $guide;
            $result['has_guide'] = 1; //表示有 导星镜的配置数据
        }

        return $result;
    }/*获取相应望远镜固定属性数据 结束*/
}