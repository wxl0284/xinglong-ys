<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Db;
use think\Cache;
//use think\Session;

/*此控制器 负责各望远镜的转台指令发送*/
class Status extends Base
{
    //定义变量
    protected $at = 0; //望远镜
    protected $at_num = 0; //望远镜编号
    protected $path = ROOT_PATH . 'public' . DS; //读取文件的根路径

    /******实时获取各子设备的状态信息 以json格式返回 有时间把获取字段给精简下*******/
   public function get_status ()
    {
        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

        $png_dir = config('at_pic_dir');
       //首先 判断需要获取哪个望远镜状态信息
       switch ( input('at_aperture') ) { //根据望远镜口径，给 $this->$at及$this->at_num赋值
        case '50cm':
            $this->at = 50;
            $this->at_num = 38;
            $png_dir = $png_dir['at50']; break;
        case '60cm':
            $this->at = 60;
            $this->at_num = 37;
            $png_dir = $png_dir['at60']; break;
        case '80cm':
            $this->at = 80;
            $this->at_num = 36;
            $png_dir = $png_dir['at80']; break;
        case '85cm':
            $this->at = 85;
            $this->at_num = 35;
            $png_dir = $png_dir['at85']; break;
        case '100cm':
            $this->at = 100;
            $this->at_num = 34;
            $png_dir = $png_dir['at100']; break;
        case '126cm':
            $this->at = 126;
            $this->at_num = 33;
            $png_dir = $png_dir['at126']; break;
        case '216cm':
            $this->at = 216;
            $this->at_num = 32;
            $png_dir = $png_dir['at216']; break;
        default:
            return '提交的望远镜参数有误!';
    }
    
       $status = [];	//存放状态信息的数组

       //获取utc时间
       //获取UTC时间 格式：12:30:30.5
        //$hms = gmdate('H:i:s');
        //$usec = substr(microtime(), 2, 1);
        //$UTC = $hms . '.' . $usec;
        //$status['UTC'] = $UTC;

       //获取转台状态信息
       $status['gimbal'] = $this->gimbal_status($this->at);
       //获取ccd状态信息
       $status['ccd'] = $this->ccd_status($this->at);
       //获取调焦器状态信息
       $status['focus'] = $this->focus_status($this->at);
       //获取随动圆顶状态信息
       $status['sDome'] = $this->sDome_status($this->at);
       //获取滤光片状态信息
       $status['filter'] = $this->filter_status($this->at);
       //获取协同计划信息
       $status['plan_cooper'] = $this->plan_cooper($this->at_num);
       //获取ToO计划信息
       $status['plan_too'] = $this->plan_too($this->at_num);
       
       if ( $status['plan_too']['data'] != '无ToO计划' ) //有ToO计划数据
       {
            $status['plan_cooper']['data'] = '无协同计划'; //告诉页面无协同计划
            //然后将协同计划表中的import字段改为1
            Db::table('plancooper')->where('at', $this->at_num)->where('import', '0')->setField('import', '1');
       }

       $this->get_new_png($png_dir, $this->at); //获取最新png图片并存入cache
       $status['new_png_pic'] = Cache::get($this->at . 'png'); //将cache中最新的png图片名（带路径） 返回页面
       
       return json_encode ($status);
    }/******实时获取各子设备的状态信息 以json格式返回  结束*******/

    /*获取转台状态信息*/
    protected function gimbal_status ($at)
    {
        $gimbal_table = 'at' . $at . 'gimbalstatus';   //at 转台状态表
        $gimbalStatus = Db::table($gimbal_table)->order('id desc')->find();
		
		if ( !$gimbalStatus['sec'] )
		{
			$status['UTC'] = '未获取到';
		}else{
			$status['UTC'] = date('Y-m-d H:i:s', $gimbalStatus['sec']);
		}
        //60cm望远镜 当前状态信息/////////////////////////////
        switch ($gimbalStatus['curstatus'])
        {
            case 1:
                $status['curstatus'] = '离线';break;
            case 2:
                $status['curstatus'] = '连接中';break;
            case 3:
                $status['curstatus'] = '断开中'; break;
            case 4:
                $status['curstatus'] = '未找零';break;
            case 5:
                $status['curstatus'] = '找零中';break;
            case 6:
                $status['curstatus'] = '停止中'; break;
            case 7:
                $status['curstatus'] = '停止';break;
            case 8:
                $status['curstatus'] = '急停中';break;
            case 9:
                $status['curstatus'] = '急停';break;
            case 10:
                $status['curstatus'] = '复位中';break;
            case 11:
                $status['curstatus'] = '复位'; break;
            case 12:
                $status['curstatus'] = '等待恒速跟踪'; break;
            case 13:
                $status['curstatus'] = '恒速跟踪';break;
            case 14:
                $status['curstatus'] = '速度修正中';break;
            case 15:
                $status['curstatus'] = '位置修正中';break;
            case 16:
                $status['curstatus'] = '等待变速跟踪';break;
            case 17:
                $status['curstatus'] = '变速跟踪中';break;
            case 18:
                $status['curstatus'] = '指向中';break;
            case 19:
                $status['curstatus'] = '指向到位';break;
            case 20:
                $status['curstatus'] = '等待恒速跟踪';break;
            case 21:
                $status['curstatus'] = '恒速跟踪';break;
			case 22:
                $status['curstatus'] = '错误'; break;               
			default:
				$status['curstatus'] = '未获取到';
                break;	
        }//转台当前状态结束///////////////////////////////////////

        $status['trackError'] = 2.11; //位置信息：跟踪误差（？咋获取）

        //位置信息：当前时角
        if (is_numeric($gimbalStatus['hourAngle']))
        {
            $gimbalStatus['hourAngle'] = floatval ($gimbalStatus['hourAngle']);
            $status['hourAngle'] = data2Time($gimbalStatus['hourAngle']);
        }else{
            $status['hourAngle'] = $gimbalStatus['hourAngle'];
        }
        
        //获取镜盖 开/关 情况
        if ($gimbalStatus['coverIndex'] === 1)
        {
            $status['coverStatus'] = '关闭'; //位置信息：镜盖状态
        }elseif ($gimbalStatus['coverIndex'] === 0){
            $status['coverStatus'] = '打开';
        }
        
        if (is_numeric($gimbalStatus['rightAscension'])) //当前赤经
        {
            $gimbalStatus['rightAscension'] = floatval ($gimbalStatus['rightAscension']);
            $status['rightAscension'] = data2Time($gimbalStatus['rightAscension']/15); 
        }else{
            $status['rightAscension'] = $gimbalStatus['rightAscension'];
        }

        if (is_numeric($gimbalStatus['declination'])) //当前赤纬
        {
            $gimbalStatus['declination'] = floatval ($gimbalStatus['declination']);
            $status['declination'] = data2Time($gimbalStatus['declination']); 
        }else{
            $status['declination'] = $gimbalStatus['declination'];
        }

        $status['trackObjectName'] = $gimbalStatus['trackObjectName']; //目标名

        
        switch ($gimbalStatus['trackType'])  //获取跟踪目标类型
        {
            case 0:
                $status['trackType'] = '恒星'; break;
            case 1:
                $status['trackType'] = '太阳'; break;
            case 2:
                $status['trackType'] = '月亮'; break;
            case 3:
                $status['trackType'] = '彗星'; break;
            case 4:
                $status['trackType'] = '行星'; break;
            case 5:
                $status['trackType'] = '卫星'; break;
            case 6:
                $status['trackType'] = '固定位置'; break;
			default:
				$status['trackType'] = '未获取到';   break;	
        }
        
        if (is_numeric($gimbalStatus['targetRightAscension']))  //目标赤经
        {
            $gimbalStatus['targetRightAscension'] = floatval ($gimbalStatus['targetRightAscension']);
            $status['targetRightAscension'] = data2Time($gimbalStatus['targetRightAscension']/15);
        }else{
            $status['targetRightAscension'] = $gimbalStatus['targetRightAscension'];
        }
        
        if (is_numeric($gimbalStatus['targetDeclination'])) //目标赤纬
        {
            $gimbalStatus['targetDeclination'] = floatval ($gimbalStatus['targetDeclination']);
            $status['targetDeclination'] = data2Time($gimbalStatus['targetDeclination']);
        }else{
            $status['targetDeclination'] = $gimbalStatus['targetDeclination'];
        }
        $status['azmiuth'] = round($gimbalStatus['azmiuth'],5); //当前方位
        $status['elevation'] = round($gimbalStatus['elevation'],5); //当前俯仰
        $status['RightAscensionSpeed'] = round($gimbalStatus['axis1Speed'],2); //赤经速度 ?
        $status['declinationSpeed'] = round($gimbalStatus['axis2Speed'], 2); //赤纬速度 ?
        $status['axis3Speed'] = round($gimbalStatus['axis3Speed'], 2); //轴3速度
        $status['derotatorPositon'] = round($gimbalStatus['derotatorPositon'], 4); //当前消旋位置
        $status['targetDerotatorPosition'] = round($gimbalStatus['targetDerotatorPosition'], 4); //目标消旋位置
        $status['axis1TrackError'] = round($gimbalStatus['axis1TrackError'], 5); //轴1跟踪误差
        $status['axis2TrackError'] = round($gimbalStatus['axis2TrackError'], 5); //轴2跟踪误差
        $status['axis3TrackError'] = round($gimbalStatus['axis3TrackError'], 5); //轴3跟踪误差
        
        if (is_numeric($gimbalStatus['siderealTime']))
        {//当前恒星时
            $status['siderealTime'] = data2Time($gimbalStatus['siderealTime']);
        }else{
            $status['siderealTime'] = $gimbalStatus['siderealTime'];
        }
        //接下来：转台可变属性
        //$status['timeStamp'] = time(); //时间戳
                
        // if (is_numeric($gimbalStatus['J2000RightAscension']))
        // {//j2000赤经
        //     $gimbalStatus['J2000RightAscension'] = floatval ($gimbalStatus['J2000RightAscension']);
        //     $status['J2000RightAscension'] = round(data2Time($gimbalStatus['J2000RightAscension']/15), 5);
        // }else{
        //     $status['J2000RightAscension'] = $gimbalStatus['J2000RightAscension'];
        // }
        
        // if (is_numeric($gimbalStatus['J2000Declination']))
        // {//j2000赤纬
        //     $gimbalStatus['J2000Declination'] = floatval($gimbalStatus['J2000Declination']);
        //     $status['J2000Declination'] = round(data2Time($gimbalStatus['J2000Declination']), 5);
        // }else{
        //     $status['J2000Declination'] = $gimbalStatus['J2000Declination'];
        // }
        
        // if (is_numeric($gimbalStatus['targetJ2000RightAscension']))
        // {//目标j2000赤经
        //     $gimbalStatus['targetJ2000RightAscension'] = floatval($gimbalStatus['targetJ2000RightAscension']);
        //     $status['targetJ2000RightAscension'] = round(data2Time($gimbalStatus['targetJ2000RightAscension']/15), 5);
        // }else{
        //     $status['targetJ2000RightAscension'] = $gimbalStatus['targetJ2000RightAscension'];
        // }
        
        // if (is_numeric($gimbalStatus['targetJ2000Declination']))
        // {//目标j2000赤纬
        //     $gimbalStatus['targetJ2000Declination'] = floatval($gimbalStatus['targetJ2000Declination']);
        //     $status['targetJ2000Declination'] = round(data2Time($gimbalStatus['targetJ2000Declination']), 5);
        // }else{
        //     $status['targetJ2000Declination'] = $gimbalStatus['targetJ2000Declination'];
        // }

        //返回数据
        return $status;
    }/*获取转台状态信息 结束*/

    protected function ccd_status ($at)  /*获取ccd状态信息*/
    {
        $ccd_table = 'at' . $at . 'ccdstatus';   //at ccd状态表
        $ccdStatus = Db::table($ccd_table)->order('id desc')->find();

        $status['frameTotal'] = $ccdStatus['frameTotal']; //总帧数
        $status['frameSequence'] = $ccdStatus['frameSequence']; //
        switch ($ccdStatus['curstatus'])
        {
            case 1:
                $status['ccdCurStatus'] = '离线';
                break;
            case 2:
                $status['ccdCurStatus'] = '连接中';
                break;
            case 3:
                $status['ccdCurStatus'] = '断开中';
                break;
            case 4:
                $status['ccdCurStatus'] = '等待曝光';
                break;
            case 5:
                $status['ccdCurStatus'] = '曝光中';
                break;
            case 6:
                $status['ccdCurStatus'] = '读出中';
                break;
            case 7:
                $status['ccdCurStatus'] = '图像存储中';
                break;
            case 8:
                $status['ccdCurStatus'] = '终止中';
                break;
            case 9:
                $status['ccdCurStatus'] = '空闲';
                break;
			default:
				$status['ccdCurStatus'] = '未获取到';
                break;
        }

        $status['coolerT'] = $ccdStatus['coolerT'];  //ccd当前温度
        $status['errorString'] = $ccdStatus['errorString'];  //错误标识
        switch ($ccdStatus['error']) //错误状态
        {
            case 0:
                $status['ccdError'] = '正常';
                break;
            case 1:
                $status['ccdError'] = '相机通信失败';
                break;
            case 2:
                $status['ccdError'] = '曝光开始超时异常';
                break;
            case 4:
                $status['ccdError'] = '曝光超时异常';
                break;
            case 8:
                $status['ccdError'] = '读出超时';
                break;
            case 10:
                $status['ccdError'] = '停止曝光超时';
                break;
            case 20:
                $status['ccdError'] = '中止曝光超时';
                break;
            case 40:
                $status['ccdError'] = '图像存储空间小于10张';
                break;
			default:
				$status['ccdError'] = '未获取到';
                break;
        }

        switch ($ccdStatus['warning']) //警告状态
        {
            case 0:
                $status['ccdWarning'] = '正常';
                break;
            case 1:
                $status['ccdWarning'] = '上位机通信中断';
                break;
            case 2:
                $status['ccdWarning'] = '制冷温度未达设定值';
                break;
            case 4:
                $status['ccdWarning'] = '风扇停转';
                break;
            case 8:
                $status['ccdWarning'] = '图像存储超时';
                break;
            case 10:
                $status['ccdWarning'] = '存储空间小于100张';
                break;
			default:
				$status['ccdWarning'] = '未获取到';
                break;
        }
        //$status['ccdReadOutMode'] = $ccdStatus['readMode'];  //ccd可变属性：读出模式
        //$status['ccdObserveBand'] = $ccdStatus['band'];  //ccd可变属性：当前拍摄波段
        
        // if (is_numeric($ccdStatus['J2000RightAscension']))
        // {
        //     $ccdStatus['J2000RightAscension'] = floatval($ccdStatus['J2000RightAscension']);
        //     $status['ccdJ2000RightAscension'] = data2Time($ccdStatus['J2000RightAscension']/15);  //ccd可变属性：当前拍摄目标赤经
        // }else{
        //     $status['ccdJ2000RightAscension'] = $ccdStatus['J2000RightAscension']; 
        // }

        // if (is_numeric($ccdStatus['J2000Declination']))
        // {
        //     $ccdStatus['J2000Declination'] = floatval($ccdStatus['J2000Declination']);
        //     $status['ccdJ2000Declination'] = data2Time($ccdStatus['J2000Declination']);  //ccd可变属性：当前拍摄目标赤纬
        // }else{
        //     $status['ccdJ2000Declination'] = $ccdStatus['J2000Declination'];
        // }
        //返回数据
        return $status;
    }/*获取ccd状态信息 结束*/

    protected function focus_status ($at) /*获取调焦器状态信息*/
    {
        $focus_table = 'at' . $at . 'focusstatus';   //at focus状态表
        $focusStatus = Db::table($focus_table)->order('id desc')->find();
        
        //调焦器当前状态////////////////////////////////////////////
        switch ($focusStatus['curstatus'])
        {
            case 1:
                $status['focusCurStatus'] = '离线';
                break;
            case 2:
                $status['focusCurStatus'] = '连接中';
                break;
            case 3:
                $status['focusCurStatus'] = '断开中';
                break;
            case 4:
                $status['focusCurStatus'] = '停止中';
                break;
            case 5:
                $status['focusCurStatus'] = '已停止';
                break;
            case 6:
                $status['focusCurStatus'] = '转动中';
                break;
            case 7:
                $status['focusCurStatus'] = '转动到位';
                break;
            case 8:
                $status['focusCurStatus'] = '未找零';
                break;
            case 9:
                $status['focusCurStatus'] = '找零中';
                break;
            case 10:
                $status['focusCurStatus'] = '急停中';
                break;
            case 11:
                $status['focusCurStatus'] = '急停';
                break;
			default:
				$status['focusCurStatus'] = '未获取到';
                break;
        }
        //调焦器当前状态 结束/////////////////////////////////////////
        
        // $status['focusPosition'] = $focusStatus['position'];  //当前位置
        // $status['focusTargetPos'] = $focusStatus['targetPosition'];  //目标位置
           $status['focusIsHomed'] = $focusStatus['isHomed'];  //找零状态
        // $status['focusIsTCompensation'] = $focusStatus['isTCompensation'] ? '是' : '否';  //是否进行温度补偿
        // $status['focusTCompenensation'] = $focusStatus['TCompenensation']; //温度补偿系数

        //返回数据
        return $status;
    }/*获取调焦器状态信息 结束*/

    protected function sDome_status ($at) /*获取随动圆顶状态信息*/
    {
        $sDome_table = 'at' . $at . 'slavedomestatus';   //at slavedome状态表
        $slaveDomeStatus = Db::table($sDome_table)->order('id desc')->find();

        switch ($slaveDomeStatus['curstatus'])
        {
            case 1:
                $status['sDomeCurstatus'] = '离线';
                break;
            case 2:
                $status['sDomeCurstatus'] = '连接中';
                break;
            case 3:
                $status['sDomeCurstatus'] = '断开中';
                break;
            case 4:
                $status['sDomeCurstatus'] = '停止中';
                break;
            case 5:
                $status['sDomeCurstatus'] = '已停止';
                break;
            case 6:
                $status['sDomeCurstatus'] = '复位中';
                break;
            case 7:
                $status['sDomeCurstatus'] = '复位';
                break;
            case 8:
                $status['sDomeCurstatus'] = '指向中';
                break;
            case 9:
                $status['sDomeCurstatus'] = '指向到位';
                break;
            case 10:
                $status['sDomeCurstatus'] = '等待随动';
                break;
            case 11:
                $status['sDomeCurstatus'] = '随动中';
                break;
            case 12:
                $status['sDomeCurstatus'] = '异常';
                break;
			default:
				$status['sDomeCurstatus'] = '未获取到';
                break;
        }//圆顶当前状态结束////////////////////////////////////////
		
		switch ($slaveDomeStatus['scuttleStatus']) //天窗状态
		{
			case 1:
				$status['sDomeScuttleStatus'] = '开';
				break;
			case 2:
				$status['sDomeScuttleStatus'] = '关';
				break;
			case 3:
				$status['sDomeScuttleStatus'] = '正在开';
				break;
			case 4:
				$status['sDomeScuttleStatus'] = '正在关';
                break;
            case 5:
				$status['sDomeScuttleStatus'] = '停止';
				break;
			default:
				$status['sDomeScuttleStatus'] = '未获取到';
				break;
		}
		
		// switch ($slaveDomeStatus['shadeStatus']) //风帘状态
		// {
		// 	case 1:
		// 		$status['slaveDomeShadeStatus'] = '运动中';
		// 		break;
		// 	case 2:
		// 		$status['slaveDomeShadeStatus'] = '到位';
		// 		break;
		// 	default:
		// 		$status['slaveDomeShadeStatus'] = '未获取到';
		// 		break;
		// }
		
        //$status['slaveDomeErrorStatus'] = $slaveDomeStatus['errorString']; //错误标识

        return $status;
    }/*获取随动圆顶状态信息 结束*/

    /*获取滤光片状态信息*/
    protected function filter_status ($at)
    {
        $filter_table = 'at' . $at . 'filterstatus';   //at filter状态表
        $filterStatus = Db::table($filter_table)->order('id desc')->find();

        //滤光片当前状态///////////////////////////////////////////
        switch ($filterStatus['curstatus'])
        {
            case 1:
                $status['filterCurstatus'] = '离线';
                break;
            case 2:
                $status['filterCurstatus'] = '连接中';
                break;
            case 3:
                $status['filterCurstatus'] = '断开中';
                break;
            case 4:
                $status['filterCurstatus'] = '停止中';
                break;
            case 5:
                $status['filterCurstatus'] = '已停止';
                break;
            case 6:
                $status['filterCurstatus'] = '转动中';
                break;
            case 7:
                $status['filterCurstatus'] = '转动到位';
                break;
            case 8:
                $status['filterCurstatus'] = '未找零';
                break;
            case 9:
                $status['filterCurstatus'] = '找零中';
                break;
            case 10:
                $status['filterCurstatus'] = '急停中';
                break;
            case 11:
                $status['filterCurstatus'] = '急停';
                break;
			default:
				$status['filterCurstatus'] = '未获取到';
                break;
        }
        //滤光片当前状态 结束 /////////////////////////////////////
        $status['filterIsHomed'] = $filterStatus['isHomed'] ==1 ? '是' : '否'; //是否找零
        $status['filterErrorStatus'] = $filterStatus['errorString']; //错误标识
        //filter当前哪个片
        $status['position'] = $filterStatus['filterPosition']; //数字
        return $status;
    }/*获取滤光片状态信息 结束*/

    protected function plan_cooper ($at_num) //根据望远镜编号查协同计划数据
    {
        $data = Db::table('plancooper')->where('at', $at_num)->where('import', '0')->where('giveup', '0')->order('id', 'desc')->select();
        //halt($data);
        if ($data) //将计划数据转为
        //if (false) //将计划数据转为
        {
            foreach ($data as $k => $v)
            {
                $temp[$k]['target'] = $v['target'];
                $temp[$k]['type'] = $v['type'];

                $tem = data2Time ($v['rightascension']);//赤经
                $tem = explode(':', $tem);
                
                $temp[$k]['rightAscension1'] = $tem[0];
                $temp[$k]['rightAscension2'] = $tem[1];
                $temp[$k]['rightAscension3'] = $tem[2];

                $tem = data2Time ($v['declination']);//赤纬
                $tem = explode(':', $tem);

                $temp[$k]['declination1'] = $tem[0];
                $temp[$k]['declination2'] = $tem[1];
                $temp[$k]['declination3'] = $tem[2];

                $temp[$k]['epoch'] = $v['epoch'];
                $temp[$k]['exposureTime'] = $v['exposuretime'];
                $temp[$k]['delayTime'] = $v['delaytime'];
                $temp[$k]['exposureCount'] = $v['exposurecount'];
                $temp[$k]['filter'] = $v['filter'];
                $temp[$k]['gain'] = $v['gain'];
                $temp[$k]['bin'] = $v['bin'];
                $temp[$k]['readout'] = $v['readout'];
            }
   
            $status['data'] = $temp;
            $status['exemode'] = $data[0]['exemode']; //执行模式
        }else{//无协同计划数据
            $status['data'] = '无协同计划';
        }
        return $status;
    }//plan_cooper()结束

    protected function plan_too ($at_num) //根据望远镜编号查ToO计划数据
    {
        $data = Db::table('plantoo')->where('at', $at_num)->where('import', '0')->where('giveup', '0')->order('id', 'desc')->select();
        //halt($data);
        if ($data) //将计划数据转为
        {
            foreach ($data as $k => $v)
            {
                $temp[$k]['target'] = $v['target'];
                $temp[$k]['type'] = $v['type'];

                $tem = data2Time ($v['rightascension']);//赤经
                $tem = explode(':', $tem);
                
                $temp[$k]['rightAscension1'] = $tem[0];
                $temp[$k]['rightAscension2'] = $tem[1];
                $temp[$k]['rightAscension3'] = $tem[2];

                $tem = data2Time ($v['declination']);//赤纬
                $tem = explode(':', $tem);

                $temp[$k]['declination1'] = $tem[0];
                $temp[$k]['declination2'] = $tem[1];
                $temp[$k]['declination3'] = $tem[2];

                $temp[$k]['epoch'] = $v['epoch'];
                $temp[$k]['exposureTime'] = $v['exposuretime'];
                $temp[$k]['delayTime'] = $v['delaytime'];
                $temp[$k]['exposureCount'] = $v['exposurecount'];
                $temp[$k]['filter'] = $v['filter'];
                $temp[$k]['gain'] = $v['gain'];
                $temp[$k]['bin'] = $v['bin'];
                $temp[$k]['readout'] = $v['readout'];
            }
   
            $status['data'] = $temp;
            $status['exemode'] = $data[0]['exemode']; //执行模式
        }else{//无ToO计划数据
            $status['data'] = '无ToO计划';
        }
        return $status;
    }//plan_too()结束

    //ajax 将协同计划表中import字段变为1 导入时
    public function change_cooper_import()
    {
        $postData = input();
        
        switch ( $postData['at_aperture'] )
        { //根据望远镜口径，给 $this->$at及$this->at_num赋值
            case '50cm':
                $this->at = 50;
                $this->at_num = 38; break;
            case '60cm':
                $this->at = 60;
                $this->at_num = 37; break;
            case '80cm':
                $this->at = 80;
                $this->at_num = 36; break;
            case '85cm':
                $this->at = 85;
                $this->at_num = 35; break;
            case '100cm':
                $this->at = 100;
                $this->at_num = 34; break;
            case '126cm':
                $this->at = 126;
                $this->at_num = 33; break;
            case '216cm':
                $this->at = 216;
                $this->at_num = 32; break;
            default:
                return '提交的望远镜参数有误!';
        }
        //halt($this->at_num);
        if ( $postData['import'] == 1 ) //点击导入时
        {
            Db::table('plancooper')->where('at', $this->at_num)->where('import', '0')->setField('import', '1');
        }elseif( $postData['give_up'] == 1 ){//点不导入时
            Db::table('plancooper')->where('at', $this->at_num)->where('giveup', '0')->setField('giveup', '1');
        }
        
    }//ajax 将协同计划表中import字段变为1 结束

    //ajax 将协同计划表中import字段或giveup字段变为1
    public function change_too_import()
    {
        $postData = input();
        
        switch ( $postData['at_aperture'] )
        { //根据望远镜口径，给 $this->$at及$this->at_num赋值
            case '50cm':
                $this->at = 50;
                $this->at_num = 38;
            case '60cm':
                $this->at = 60;
                $this->at_num = 37; break;
            case '80cm':
                $this->at = 80;
                $this->at_num = 36; break;
            case '85cm':
                $this->at = 85;
                $this->at_num = 35; break;
            case '100cm':
                $this->at = 100;
                $this->at_num = 34; break;
            case '126cm':
                $this->at = 126;
                $this->at_num = 33; break;
            case '216cm':
                $this->at = 216;
                $this->at_num = 32; break;
            default:
                return '提交的望远镜参数有误!';
        }
        //halt($this->at_num);
        if ( $postData['import'] == 1 ) //点导入时
        {
            Db::table('plantoo')->where('at', $this->at_num)->where('import', '0')->setField('import', '1');
        }elseif( $postData['give_up'] == 1 ){//点不导入时
            Db::table('plantoo')->where('at', $this->at_num)->where('giveup', '0')->setField('giveup', '1');
        }
        
    }//ajax 将ToO计划表中import字段变为1 结束

    protected function get_new_png($png_dir, $at)//获取各望远镜最新png图片
    {
        $day = date('Ymd'); //月和日均有前导零
        $png_dir = $png_dir . $day . '/'; 
        $file_path = $this->path . $png_dir;
        $err = ''; //错误信息
        $file_time = 0; //最新的文件创建时间
        $file_name = ''; //最新的文件名

        try{
			$res = scandir ( $file_path );
		}catch(\Exception $e){
			$err = '读取文件异常';
        }

        if ( $err === '' ) //读取文件时无异常
        {
            if ( $res !== false && count($res) > 2 )
            {
                //逐一比对文件创建时间 获取最新的png图片
                foreach ($res as $v)
                {
                    $temp = filemtime ( './' . png_dir . $v); //每个文件的创建/修改时间
                    if ( $temp > $file_time )
                    {
                        $file_time = $temp;
                        $file_name = '/'. png_dir . $v;
                    }

                }
            }
        }

        //将最新文件名存入Cache
        if ( Cache::get( $at . 'png' ) !== $file_name )
        {
            Cache::set($at . 'png', $file_name);
        }
    }//get_new_png() 结束

} /******class Status 结束*******/