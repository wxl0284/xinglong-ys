<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Db;
//use think\Session;

/*此控制器 负责各望远镜的转台指令发送*/
class Status extends Base
{
    //定义变量
    protectd $at = 0; //望远镜

    /******实时获取各子设备的状态信息 以json格式返回*******/
   public function get_status ()
    {
        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

       //首先 判断需要获取哪个望远镜状态信息
       $this->at = input ('at');

       $status = [];	//存放状态信息的数组

       //获取utc时间
       //获取UTC时间 格式：12:30:30.5
        $hms = gmdate('H:i:s');
        $usec = substr(microtime(), 2, 1);
        $UTC = $hms . '.' . $usec;
        $status['UTC'] = $UTC;

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

       return json_encode ($status);
    }/******实时获取各子设备的状态信息 以json格式返回  结束*******/

    /*获取转台状态信息*/
    protected function gimbal_status ($at)
    {
        $gimbal_table = 'at' . $at . 'gimbalstatus';   //at 转台状态表
        $gimbalStatus = Db::table($gimbal_table)->order('id desc')->find();

        //60cm望远镜 当前状态信息/////////////////////////////
        switch ($gimbalStatus['curstatus'])
        {
            case 1:
                $status['curstatus'] = '离线';
                break;
            case 2:
                $status['curstatus'] = '连接中';
                break;
            case 3:
                $status['curstatus'] = '断开中';
                break;
            case 4:
                $status['curstatus'] = '未找零';
                break;
            case 5:
                $status['curstatus'] = '找零中';
                break;
            case 6:
                $status['curstatus'] = '停止中';
                break;
            case 7:
                $status['curstatus'] = '停止';
                break;
            case 8:
                $status['curstatus'] = '急停中';
                break;
            case 9:
                $status['curstatus'] = '急停';
                break;
            case 10:
                $status['curstatus'] = '急停中';
                break;
            case 11:
                $status['curstatus'] = '急停';
                break;
            case 12:
                $status['curstatus'] = '等待恒速跟踪';
                break;
            case 13:
                $status['curstatus'] = '恒速跟踪';
                break;
            case 14:
                $status['curstatus'] = '速度修正中';
                break;
            case 15:
                $status['curstatus'] = '等待变速跟踪';
                break;
            case 16:
                $status['curstatus'] = '变速跟踪中';
                break;
            case 17:
                $status['curstatus'] = '指向中';
                break;
            case 18:
                $status['curstatus'] = '指向到位';
                break;
            case 19:
                $status['curstatus'] = '等待恒速';
                break;
            case 20:
                $status['curstatus'] = '等待恒速';
                break;
            case 21:
                $status['curstatus'] = '异常';
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
        if ($gimbalStatus['coverIndex'] === 0)
        {
            $status['coverStatus'] = '关闭'; //位置信息：镜盖状态
        }elseif ($gimbalStatus['coverIndex'] === 1){
            $status['coverStatus'] = '打开';
        }
        
        $status['rightAscension'] = round($gimbalStatus['rightAscension'], 5); //当前赤经
        $status['declination'] = round($gimbalStatus['declination'], 5); //当前赤纬
        $status['trackObjectName'] = $gimbalStatus['trackObjectName']; //目标名

        //获取跟踪目标类型
        switch ($gimbalStatus['trackType'])
        {
            case 0:
                $status['trackType'] = '恒星';
                break;
            case 1:
                $status['trackType'] = '太阳';
                break;
            case 2:
                $status['trackType'] = '月亮';
                break;
            case 3:
                $status['trackType'] = '彗星';
                break;
            case 4:
                $status['trackType'] = '行星';
                break;
            case 5:
                $status['trackType'] = '卫星';
                break;
            case 6:
                $status['trackType'] = '固定位置';
                break;
        }
        
        if (is_numeric($gimbalStatus['targetRightAscension']))
        {
            $gimbalStatus['targetRightAscension'] = floatval ($gimbalStatus['targetRightAscension']);
            $status['targetRightAscension'] = data2Time($gimbalStatus['targetRightAscension']/15); //目标赤经
        }else{
            $status['targetRightAscension'] = $gimbalStatus['targetRightAscension'];
        }
        
        if (is_numeric($gimbalStatus['targetDeclination']))
        {
            $gimbalStatus['targetDeclination'] = floatval ($gimbalStatus['targetDeclination']);
            $status['targetDeclination'] = data2Time($gimbalStatus['targetDeclination']); //目标赤纬
        }else{
            $status['targetDeclination'] = $gimbalStatus['targetDeclination'];
        }
        $status['azmiuth'] = round($gimbalStatus['azmiuth'],5); //当前方位
        $status['elevation'] = round($gimbalStatus['elevation'],5); //当前俯仰
        $status['RightAscensionSpeed'] = 12.3; //赤经速度 ?咋获取
        $status['declinationSpeed'] = 12.5; //赤纬速度 ?咋获取
        $status['derotatorPositon'] = $gimbalStatus['derotatorPositon']; //当前消旋位置
        $status['targetDerotatorPosition'] = $gimbalStatus['targetDerotatorPosition']; //目标消旋位置
        $status['axis1TrackError'] = $gimbalStatus['axis1TrackError']; //轴1跟踪误差
        $status['axis2TrackError'] = $gimbalStatus['axis2TrackError']; //轴2跟踪误差
        $status['axis3TrackError'] = $gimbalStatus['axis3TrackError']; //轴3跟踪误差
        
        if (is_numeric($gimbalStatus['siderealTime']))
        {//当前恒星时
            $gimbalStatus['siderealTime'] = floatval ($gimbalStatus['siderealTime']);
            $status['siderealTime'] = data2Time($gimbalStatus['siderealTime']);
        }else{
            $status['siderealTime'] = $gimbalStatus['siderealTime'];
        }
        //接下来：转台可变属性
        $status['timeStamp'] = time(); //时间戳
                
        if (is_numeric())
        {//j2000赤经
            $gimbalStatus['J2000RightAscension'] = floatval ($gimbalStatus['J2000RightAscension']);
            $status['J2000RightAscension'] = data2Time($gimbalStatus['J2000RightAscension']/15);
        }else{
            $status['J2000RightAscension'] = $gimbalStatus['J2000RightAscension'];
        }
        
        if (is_numeric($gimbalStatus['J2000Declination']))
        {//j2000赤纬
            $gimbalStatus['J2000Declination'] = floatval($gimbalStatus['J2000Declination']);
            $status['J2000Declination'] = data2Time($gimbalStatus['J2000Declination']);
        }else{
            $status['J2000Declination'] = $gimbalStatus['J2000Declination'];
        }
        
        if (is_numeric($gimbalStatus['targetJ2000RightAscension']))
        {//目标j2000赤经
            $gimbalStatus['targetJ2000RightAscension'] = floatval($gimbalStatus['targetJ2000RightAscension']);
            $status['targetJ2000RightAscension'] = data2Time($gimbalStatus['targetJ2000RightAscension']/15);
        }else{
            $status['targetJ2000RightAscension'] = $gimbalStatus['targetJ2000RightAscension'];
        }
        
        if (is_numeric($gimbalStatus['targetJ2000Declination']))
        {//目标j2000赤纬
            $gimbalStatus['targetJ2000Declination'] = floatval($gimbalStatus['targetJ2000Declination']);
            $status['targetJ2000Declination'] = data2Time($gimbalStatus['targetJ2000Declination']);
        }else{
            $status['targetJ2000Declination'] = $gimbalStatus['targetJ2000Declination'];
        }

        //返回数据
        return $status;
    }/*获取转台状态信息 结束*/

    /*获取ccd状态信息*/
    protected function ccd_status ($at)
    {
        $ccd_table = 'at' . $at . 'ccdstatus';   //at ccd状态表
        $ccdStatus = Db::table($ccd_table)->order('id desc')->find();

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
        }

        $status['ccdBaseline'] = $ccdStatus['baseline'];  //ccd可变属性：baseline值
        $status['ccdReadOutMode'] = $ccdStatus['readMode'];  //ccd可变属性：读出模式
        $status['ccdObserveBand'] = $ccdStatus['band'];  //ccd可变属性：当前拍摄波段
        
        if (is_numeric($ccdStatus['J2000RightAscension']))
        {
            $ccdStatus['J2000RightAscension'] = floatval($ccdStatus['J2000RightAscension']);
            $status['ccdJ2000RightAscension'] = data2Time($ccdStatus['J2000RightAscension']/15);  //ccd可变属性：当前拍摄目标赤经
        }else{
            $status['ccdJ2000RightAscension'] = $ccdStatus['J2000RightAscension']; 
        }

        if (is_numeric($ccdStatus['J2000Declination']))
        {
            $ccdStatus['J2000Declination'] = floatval($ccdStatus['J2000Declination']);
            $status['ccdJ2000Declination'] = data2Time($ccdStatus['J2000Declination']);  //ccd可变属性：当前拍摄目标赤纬
        }else{
            $status['ccdJ2000Declination'] = $ccdStatus['J2000Declination'];
        }
        //返回数据
        return $status;
    }/*获取ccd状态信息 结束*/

    /*获取调焦器状态信息*/
    protected function focus_status ($at)
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
        }
        //调焦器当前状态 结束/////////////////////////////////////////
        
        $status['focusPosition'] = $focusStatus['position'];  //当前位置
        $status['focusTargetPos'] = $focusStatus['targetPosition'];  //目标位置
        $status['focusIsHomed'] = $focusStatus['isHomed'];  //找零状态
        $status['focusIsTCompensation'] = $focusStatus['isTCompensation'];  //是否进行温度补偿
        $status['focusTCompenensation'] = $focusStatus['TCompenensation']; //温度补偿系数

        //返回数据
        return $status;
    }/*获取调焦器状态信息 结束*/

    /*获取随动圆顶状态信息*/
    protected function sDome_status ($at)
    {
        $sDome_table = 'at' . $at . 'slavedomestatus';   //at slavedome状态表
        $slaveDomeStatus = Db::table($sDome_table)->order('id desc')->find();

        switch ($slaveDomeStatus['curstatus'])
        {
            case 1:
                $status['slaveDomeCurstatus'] = '离线';
                break;
            case 2:
                $status['slaveDomeCurstatus'] = '连接中';
                break;
            case 3:
                $status['slaveDomeCurstatus'] = '断开中';
                break;
            case 4:
                $status['slaveDomeCurstatus'] = '停止中';
                break;
            case 5:
                $status['slaveDomeCurstatus'] = '已停止';
                break;
            case 6:
                $status['slaveDomeCurstatus'] = '复位中';
                break;
            case 7:
                $status['slaveDomeCurstatus'] = '复位';
                break;
            case 8:
                $status['slaveDomeCurstatus'] = '指向中';
                break;
            case 9:
                $status['slaveDomeCurstatus'] = '指向到位';
                break;
            case 10:
                $status['slaveDomeCurstatus'] = '等待随动';
                break;
            case 11:
                $status['slaveDomeCurstatus'] = '随动中';
                break;
            case 12:
                $status['slaveDomeCurstatus'] = '异常';
                break;
        }//圆顶当前状态结束////////////////////////////////////////
        
        $status['slaveDomeScuttleStatus'] = $slaveDomeStatus['scuttleStatus']; //天窗状态
        $status['slaveDomeShadeStatus'] = $slaveDomeStatus['shadeStatus']; //风帘状态
        $status['slaveDomeErrorStatus'] = $slaveDomeStatus['errorString']; //错误标识

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
        }
        //滤光片当前状态 结束 /////////////////////////////////////
        $status['filterIsHomed'] = $filterStatus['isHomed']; //是否找零
        //错误标识
        $status['filterErrorStatus'] = $filterStatus['errorString']; 

        return $status;
    }/*获取滤光片状态信息 结束*/

} /******class Status 结束*******/