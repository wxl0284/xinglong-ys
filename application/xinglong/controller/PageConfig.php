<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Cache;
use think\Session;
use think\Request;
use think\Cookie;
use think\Db;
use app\xinglong\model\At60config;
//use think\Config;


class PageConfig extends Controller
{
	//检测是否登录////////////////////////////////////////////////
    public function _initialize ()
    {		
		//未登录
        if (!Session::has('login'))
        {
			if (Request::instance()->isAjax())
			{
				return '请完成登录后，再进行相关操作！';
			}
            $request = Request::instance();
            Cookie::set('url', $request->url(true));
            $this->error('请完成登录后，再进行相关操作！', '/');
        }   
    }
	
	//显示60cm望远镜配置选择页
	public function at60config ()
	{
		return view('pageConfig/at60config');
	}
	
	//根据用户提交的选项，进行60cm望远镜
	public function doAt60config ()
	{
		//未选中任何选项
		$option = input();
		
		if (isset($option['viewOpt']))
		{
			$num = count($option['viewOpt']);
			if ($num > 0)
			{
				foreach ($option['viewOpt'] as $v)
				{
					//组装入库数组
					$data[$v] = 1;
					$data[$v.'val'] = $option[$v.'val'];
				}
			}
		}
		
		//halt($n);
		//if (!isset($option['canstop'])) return 'no';
		
		
	//将单选按钮值存入数组
	/***********转台之配置 开始***************/
		//有无 第3轴
		if (isset($option['haveaxis3']))
		{
			$data['haveaxis3'] = $option['haveaxis3'];
		}
		
		//有无 镜盖（轴5）
		if (isset($option['haveaxis5']))
		{
			$data['haveaxis5'] = $option['haveaxis5'];
		}
		
		//支持连接指令
		if (isset($option['gimbalcanconnect']))
		{
			$data['gimbalcanconnect'] = $option['gimbalcanconnect'];
		}
		
		//支持找零
		if (isset($option['canfindhome']))
		{
			$data['canfindhome'] = $option['canfindhome'];
		}
		
		//支持跟踪恒星
		if (isset($option['cantrackstar']))
		{
			$data['cantrackstar'] = $option['cantrackstar'];
		}
		
		//设置目标名称
		if (isset($option['cansetobjectname']))
		{
			$data['cansetobjectname'] = $option['cansetobjectname'];
		}
		
		//指向固定位置
		if (isset($option['canslewazel']))
		{
			$data['canslewazel'] = $option['canslewazel'];
		}
		
		//轴3指向固定位置
		if (isset($option['canslewderotator']))
		{
			$data['canslewderotator'] = $option['canslewderotator'];
		}
		
		//设置轴3工作模式
		if (isset($option['canconfigderotator']))
		{
			$data['canconfigderotator'] = $option['canconfigderotator'];
		}
		
		//支持停止指令
		if (isset($option['canstop']))
		{
			$data['canstop'] = $option['canstop'];
		}
		
		//设置跟踪速度
		if (isset($option['cansettrackspeed']))
		{
			$data['cansettrackspeed'] = $option['cansettrackspeed'];
		}
		
		//支持复位
		if (isset($option['canpark']))
		{
			$data['canpark'] = $option['canpark'];
		}
		
		//支持恒速运动
		if (isset($option['canfixedmove']))
		{
			$data['canfixedmove'] = $option['canfixedmove'];
		}
		
		//支持位置修正
		if (isset($option['canpositioncorrect']))
		{
			$data['canpositioncorrect'] = $option['canpositioncorrect'];
		}
		
		//支持镜盖操作
		if (isset($option['cancoveroperation']))
		{
			$data['cancoveroperation'] = $option['cancoveroperation'];
		}
		
		//焦点切换镜操作
		if (isset($option['canfocusoperation']))
		{
			$data['canfocusoperation'] = $option['canfocusoperation'];
		}
		
		//支持急停
		if (isset($option['canemergencystop']))
		{
			$data['canemergencystop'] = $option['canemergencystop'];
		}
		
		//保存同步数据
		if (isset($option['cansavesyncdata']))
		{
			$data['cansavesyncdata'] = $option['cansavesyncdata'];
		}
		
		//支持跟踪卫星
		if (isset($option['cantracksatellite']))
		{
			$data['cantracksatellite'] = $option['cantracksatellite'];
		}
	/***********转台之配置 结束***************/
	
	/***********CCD之配置 开始****************/
		//支持开窗
		if (isset($option['issupportroi']))
		{
			$data['issupportroi'] = $option['issupportroi'];
		}
		
		//设置Roi
		if (isset($option['cansetroi']))
		{
			$data['cansetroi'] = $option['cansetroi'];
		}
		
		//支持帧转移
		if (isset($option['issupportfullframe']))
		{
			$data['issupportfullframe'] = $option['issupportfullframe'];
		}
		
		//设置帧转移
		if (isset($option['cansetfullframe']))
		{
			$data['cansetfullframe'] = $option['cansetfullframe'];
		}
		
		//支持EM
		if (isset($option['issupportem']))
		{
			$data['issupportem'] = $option['issupportem'];
		}
		
		//设置EM
		if (isset($option['cansetem']))
		{
			$data['cansetem'] = $option['cansetem'];
		}
		
		//支持base line
		if (isset($option['issupportbaseline']))
		{
			$data['issupportbaseline'] = $option['issupportbaseline'];
		}
		
		//设置base line
		if (isset($option['cansetbaseline']))
		{
			$data['cansetbaseline'] = $option['cansetbaseline'];
		}
		
		//支持Over scan
		if (isset($option['issupportoverscan']))
		{
			$data['issupportoverscan'] = $option['issupportoverscan'];
		}
		
		//设置Over scan
		if (isset($option['cansetoverscan']))
		{
			$data['cansetoverscan'] = $option['cansetoverscan'];
		}
		
		//支持连接指令
		if (isset($option['ccdcanconnect']))
		{
			$data['ccdcanconnect'] = $option['ccdcanconnect'];
		}
		
		//设置制冷温度
		if (isset($option['cansetcoolert']))
		{
			$data['cansetcoolert'] = $option['cansetcoolert'];
		}
		
		//设置曝光策略
		if (isset($option['cansetexposureparam']))
		{
			$data['cansetexposureparam'] = $option['cansetexposureparam'];
		}
		
		//开始曝光指令
		if (isset($option['canstartexposure']))
		{
			$data['canstartexposure'] = $option['canstartexposure'];
		}
		
		//停止曝光指令
		if (isset($option['canstopexposure']))
		{
			$data['canstopexposure'] = $option['canstopexposure'];
		}
		
		//终止曝光指令
		if (isset($option['canabortexposure']))
		{
			$data['canabortexposure'] = $option['canabortexposure'];
		}
		
		//设置增益
		if (isset($option['cansetgain']))
		{
			$data['cansetgain'] = $option['cansetgain'];
		}
		
		//设置Bin
		if (isset($option['cansetbin']))
		{
			$data['cansetbin'] = $option['cansetbin'];
		}
		
		//设置快门
		if (isset($option['cansetshutter']))
		{
			$data['cansetshutter'] = $option['cansetshutter'];
		}
		
		//设置读出速度模式值
		if (isset($option['cansetreadoutspeedmode']))
		{
			$data['cansetreadoutspeedmode'] = $option['cansetreadoutspeedmode'];
		}
		
		//设置转移速度模式值
		if (isset($option['cansettransferspeedmode']))
		{
			$data['cansettransferspeedmode'] = $option['cansettransferspeedmode'];
		}
		
		//支持CMOS noise
		if (isset($option['issupportscmosnoisefilter']))
		{
			$data['issupportscmosnoisefilter'] = $option['issupportscmosnoisefilter'];
		}
		
		//设置CMOS noise
		if (isset($option['cannoisefilter']))
		{
			$data['cannoisefilter'] = $option['cannoisefilter'];
		}
	/***********CCD之配置 结束****************/
	
	/***********调焦器之配置 开始***************/
		//支持找零
		if (isset($option['focuscanfindhome']))
		{
			$data['focuscanfindhome'] = $option['focuscanfindhome'];
		}
		
		//支持连接指令
		if (isset($option['focuscanconnect']))
		{
			$data['focuscanconnect'] = $option['focuscanconnect'];
		}
		
		//设置目标位置
		if (isset($option['focussetposition']))
		{
			$data['focussetposition'] = $option['focussetposition'];
		}
		
		//设置恒速运动
		if (isset($option['focuscansetspeed']))
		{
			$data['focuscansetspeed'] = $option['focuscansetspeed'];
		}
		
		//支持停止运动
		if (isset($option['focuscanstop']))
		{
			$data['focuscanstop'] = $option['focuscanstop'];
		}
		
		//支持温度补偿
		if (isset($option['cantemperturecompensate']))
		{
			$data['cantemperturecompensate'] = $option['cantemperturecompensate'];
		}
		
		//使能温度补偿
		if (isset($option['canenabletemperturecompensate']))
		{
			$data['canenabletemperturecompensate'] = $option['canenabletemperturecompensate'];
		}
		
		//设置温度补偿系数
		if (isset($option['cansettempertcompensatecoefficient']))
		{
			$data['cansettempertcompensatecoefficient'] = $option['cansettempertcompensatecoefficient'];
		}
	/***********调焦器之配置 结束***************/
	
	/***********随动圆顶之配置 开始**************/
		//具备风帘
		if (isset($option['hasshade']))
		{
			$data['hasshade'] = $option['hasshade'];
		}
		
		//设置风帘位置
		if (isset($option['cansetshadeposition']))
		{
			$data['cansetshadeposition'] = $option['cansetshadeposition'];
		}
		
		//控制风帘运动
		if (isset($option['cansetshadespeed']))
		{
			$data['cansetshadespeed'] = $option['cansetshadespeed'];
		}
		
		//设置目标方位
		if (isset($option['cansetdomepositin']))
		{
			$data['cansetdomepositin'] = $option['cansetdomepositin'];
		}
		
		//设置转动速度
		if (isset($option['cansetrotatespeed']))
		{
			$data['cansetrotatespeed'] = $option['cansetrotatespeed'];
		}
		
		//停止运动指令
		if (isset($option['sdomecanstop']))
		{
			$data['sdomecanstop'] = $option['sdomecanstop'];
		}
		
		//打开天窗指令
		if (isset($option['canopenshutter']))
		{
			$data['canopenshutter'] = $option['canopenshutter'];
		}
		
		//支持连接指令
		if (isset($option['sdomecanconnect']))
		{
			$data['sdomecanconnect'] = $option['sdomecanconnect'];
		}
	/***********随动圆顶之配置 结束*************/
	
	/***********滤光片之配置 结束*************/
		//支持连接指令
		if (isset($option['filtercanconnect']))
		{
			$data['filtercanconnect'] = $option['filtercanconnect'];
		}
		
		//支持找零指令
		if (isset($option['filtercanfindhome']))
		{
			$data['filtercanfindhome'] = $option['filtercanfindhome'];
		}
		
		//设置滤光片位置
		if (isset($option['cansetfilterposition']))
		{
			$data['cansetfilterposition'] = $option['cansetfilterposition'];
		}
	/***********滤光片之配置 结束*************/
		$At60config = new At60config;
		$configData = $At60config->all();
		
		if (empty($configData))
		{	//首次配置，进行数据新增
			$res = $At60config->save($data);
			if ($res)
			{
				return '60CM望远镜配置成功!';
			}else{
				return '60CM望远镜配置失败!';
			}
		}else{//已有配置，进行数据更新
			$res = $At60config->save($data,['pk_at60config' => $configData[0]['pk_at60config']]);
			if ($res)
			{
				return '60CM望远镜配置成功!';
			}else{
				return '60CM望远镜配置失败!';
			}
		}
	
	}//保存60CM配置数据 结束////////////////////////////////
	
	//显示80cm望远镜配置选择页
	public function at80config ()
	{
		return view('pageConfig/at80config');
	}
	
	//显示80cm望远镜配置选择页
	public function doAt80config ()
	{
		return 'ok';
	}
}