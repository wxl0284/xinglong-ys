<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;
use think\Session;
use think\Cache;

/*此控制器 负责协同观测计划发送*/
class Too extends Base
{
    //定义所需变量
    protected $sequence = 0;    //指令的序号
    protected $at = 0;  //望远镜序号
    protected $device = 66;  //设备对应序号
    protected $msg = 0;  //指令类型 计划数据时msg = 8; 执行 
    protected $magic = 439041101;  //转台对应序号
    protected $version = 1;  //版本号
    protected $plan = 0;  //计划
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port

    public function ToO ()  //显示协同计划页面
    {
		$vars = $this->get_too_data();
		//获取各望远镜的滤光片数据 结束
        return $this->fetch('too/too', $vars);
	}//ToO ()结束
	
	public function ToO_1 ()  //显示ToO计划页面
    {
		$vars = $this->get_too_data();
        return $this->fetch('too/too_1', $vars);
    }//ToO_1 ()结束

	protected function get_too_data () //ToO()、ToO_1()共用的方法
	{
		//先检查atlist表中记录的条数（即配置好的望远镜个数，若没有，则提示错误）
		$tele_data = Db::table('atlist')->select();
		$tele_num = count($tele_data);
		
		if ( $tele_num < 1 )
		{
			$this->error('未查到望远镜数据, 请先添加并配置望远镜');
		}

		//接下来 获取已添加的望远镜数据
		//首先 获取已添加的各望远镜之口径
		$aperture = array_column($tele_data, 'aperture'); // ['60cm', '80cm']
		//获取各望远镜的滤光片数据
		$filter_data = [];//存储各望远镜滤光片数据
		$readout_data = [];//存储各望远镜读出速度
		$bin_data = [];//存储各望远镜bin
		$gain_data = [];//存储各望远镜增益
		
		$temp_filter = Db::table('filterconf')->column('filtersystem', 'teleid');
		//接下来查各望远镜的读出速度、增益档位和bin的配置
		$ccd_conf = Db::table('ccdconf')->select();
		if ( !$ccd_conf )
		{
			$this->error('未查到ccd配置数据, 请去配置CCD');
		}else{
			foreach ($ccd_conf as $v) //循环取得各望远镜的读出速度的配置
			{
				$read_out[$v['teleid']] = explode('#',$v['readoutspeed']);
			}

			foreach ($ccd_conf as $v) //循环取得各望远镜bin的配置
			{
				$bin[$v['teleid']] = explode('#',$v['bin']);
			}

			foreach ($ccd_conf as $v) //循环取得各望远镜增益的配置
			{
				$gain[$v['teleid']] = $v['gainnumber'];
			}
		}
		//接下来查各望远镜的读出速度、增益档位和bin的配置 结束

		foreach ( $tele_data as $v )
		{
			if ( isset( $temp_filter[$v['id']] ) )//如果此望远镜已配置了滤光片数据
			{//组成一个以口径为索引，滤光片名称为值的二维数组
				$filter_arr = json_decode( $temp_filter[$v['id']], true );
				$filter_data[$v['aperture']] = $filter_arr['filterName'];
			}

			if ( isset( $read_out[$v['id']] ) )//如果有读出速度数据
			{//组成一个以口径为索引，读出速度为值的二维数组
				$readout_data[$v['aperture']] = $read_out[$v['id']];
			}

			if ( isset( $bin[$v['id']] ) )//如果有bin数据
			{//组成一个以口径为索引，bin为值的二维数组
				$bin_data[$v['aperture']] = $bin[$v['id']];
			}

			if ( isset( $gain[$v['id']] ) )//如果有增益数据
			{//组成一个以口径为索引，增益为值的二维数组
				$gain_data[$v['aperture']] = $gain[$v['id']];
			}
		}
		
		//模板赋值
		$vars['aperture'] = json_encode( $aperture ); //各望远镜口径
		//$vars['filter'] = json_encode ( [] ); //滤光片名称
		$vars['filter'] = json_encode ( $filter_data ); //滤光片名称
		$vars['readout'] = json_encode ( $readout_data ); //读出速度
		$vars['bin'] = json_encode ( $bin_data ); //bin
		$vars['gain'] = json_encode ( $gain_data ); //增益

		return $vars;
	}//get_too_data() 结束

    public function send_ToO_plan ()  //将协同计划数据存入plancooper表中
    {
		//首先判断是否已登录
		if ($this->ajaxAuthErr == 'not_log')
		{
			return '请先登录再进行操作!';
		}

		//判断是否有权限
		if ($this->ajaxAuthErr == 'no_auth')
		{
			return '您无权进行此操作!';
		}

        //halt($planData['plan_filter_option']);
		//定义全局$sequence 此变量在packHead()函数中要使用
		/*if (Cookie::has('sequence'))
		{
			$this->sequence = Cookie::get('sequence');
			Cookie::set('sequence', $this->sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$this->sequence = 0;
		}*/
        
		$postData = $this->input; //接收提交数据
		
		//halt($postData['planData']);
		$num = count ( $postData['planData'] );
		for ($i=0; $i < $num; $i++)
		{ 
			$postData['planData'][$i]['type'] = '0';
			$temp = $postData['planData'][$i]['rightAscension1'] .':' . $postData['planData'][$i]['rightAscension2'] . ':' . $postData['planData'][$i]['rightAscension3'];

			$postData['planData'][$i]['rightascension'] = time2Data($temp);

			$temp = $postData['planData'][$i]['declination1'] .':' . $postData['planData'][$i]['declination2'] . ':' . $postData['planData'][$i]['declination3'];

			$postData['planData'][$i]['declination'] = time2Data($temp);

			switch ($postData['planData'][$i]['at'])
			{
				case '60cm':
					$postData['planData'][$i]['at'] = 37; break;
				case '80cm':
					$postData['planData'][$i]['at'] = 36; break;
				case '50cm':
					$postData['planData'][$i]['at'] = 38; break;
				case '85cm':
					$postData['planData'][$i]['at'] = 35; break;
				case '100cm':
					$postData['planData'][$i]['at'] = 34; break;
				case '126cm':
					$postData['planData'][$i]['at'] = 33; break;
				case '216cm':
					$postData['planData'][$i]['at'] = 32; break;
			}

			$postData['planData'][$i]['exemode'] = $postData['exeMode'];
			$postData['planData'][$i]['exposurecount'] = $postData['planData'][$i]['exposureCount'];
			$postData['planData'][$i]['exposuretime'] = $postData['planData'][$i]['exposureTime'];
			$postData['planData'][$i]['delaytime'] = $postData['planData'][$i]['delayTime'];
			$postData['planData'][$i]['giveup'] = '0'; /*后期此字段要改为默认为0*/
		}

		$res = Db::table('plancooper')->strict(false)->insertAll($postData['planData']);

		if ( $res )
		{
			return '协同观测计划发送保存完毕!';
		}else{
			return '协同观测计划发送保存失败!';
		}

        /*$at60_data = []; //存储at60的计划数据
        $at80_data = []; //存储at80的计划数据

        foreach ($postData['planData'] as $k => $v) 
        {
            if ( $v['at'] == '60cm' ) //获取at60的计划数据
            {
                $at60_data[] = $v;
            }

            if ( $v['at'] == '80cm' ) //获取at80的计划数据
            {
                $at80_data[] = $v;
            }
        }

		//var_dump($at60_data); halt();
		$ip = config('ip');
        $port = config('port');
		//接下来开始给中控发送at60计划数据
		$this->msg = 8; $length =28 + 208; $this->at = 37; //37表示at60
		$this->ip = $ip['at60'];
		$this->port = $port['at60'];
	   
		$headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
        
        //接下来 给中控 发送数据 ///////////////
		foreach ( $at60_data as $k => $v )
		{
			$sendMsg = pack('L', $k+1);  //每条计划的tag unsigned int 
			
			$sendMsg .= pack('S', $this->at);
            $sendMsg .= pack('a48', $this->user); //user
			$sendMsg .= pack('a48', '02'); //project
			
			$target = $v['target']; //目标名		
			$sendMsg .= pack('a48', $target);
			
			$sendMsg .= pack('L', 0); //目标类型

			//赤经 rightAscension	
			$rightAscension = $v['rightAscension1'].':'.$v['rightAscension2'].':'.$v['rightAscension3'];			
			$rightAscension = time2Data($rightAscension);
			$sendMsg .= pack('d', $rightAscension);
			//赤纬
			$declination = $v['declination1'].':'.$v['declination2'].':'.$v['declination3'];
			$declination = time2Data($declination);
			$sendMsg .= pack('d', $declination);
			//历元
			$epoch = $v['epoch'];
		
			if ( preg_match('/^[0-9]$/', $epoch) ) //数字类型
			{
				$sendMsg .= pack('L', $epoch); 
			}else{//直接为字符类型数据 
				$epoch = strtolower ($epoch);
				if( $epoch == 'real' )
				{
					$sendMsg .= pack('L', 0);
				}elseif( $epoch == 'j2000' ){
					$sendMsg .= pack('L', 1); 
				}elseif( $epoch == 'b1950' ){
					$sendMsg .= pack('L', 2); 
				}elseif( $epoch == 'j2050' ){
					$sendMsg .= pack('L', 3); 
				}
			}	//历元结束

			//曝光时间
			$exposureTime = $v['exposureTime'];
			$sendMsg .= pack('d', $exposureTime); 

			//delayTime
			 $delayTime = $v['delayTime'];
			 $sendMsg .= pack('d', $delayTime);

			 //曝光数量
			 $exposureCount = $v['exposureCount'];
			 $sendMsg .= pack('L', $exposureCount);

			 //滤光片
			$filter = $v['filter'];
			$sendMsg .= pack('a8', $filter);
			// if ( preg_match('/^\d+$/', $filter) ) //数字类型
			// {
			// 	$sendMsg .= pack('a8', $filter_option[$filter]); 
			// }else{//直接为字符类型数据
			// 	$filter = strtoupper ($filter);
			// 	$sendMsg .= pack('a8', $filter);
			//  }	//滤光片

			//增益 gain
			$gain = $v['gain'];
			$sendMsg .= pack('S', $gain); 
			
			//bin
			$bin = $v['bin'];
			$sendMsg .= pack('S', $bin);
			
			//读出速度
			$readout = $v['readout'];
			$sendMsg .= pack('S', $readout);
			
			//发送 计划数据
        	$sendMsg = $headInfo . $sendMsg;
           
            udpSendPlan($sendMsg, $this->ip, $this->port); //无返回值
        }//给中控 发送at60数据 结束///////////////
        
        //接下来 给中控 发送at80数据 ///////////////
		$this->at = 36;
		$this->ip = $ip['at80'];
		$this->port = $port['at80'];

		foreach ( $at80_data as $k => $v )
		{
			$sendMsg = pack('L', $k+1);  //每条计划的tag unsigned int 
			
			$sendMsg .= pack('S', $this->at);
            $sendMsg .= pack('a48', $this->user); //user
			$sendMsg .= pack('a48', '02'); //project
			
			$target = $v['target']; //目标名		
			$sendMsg .= pack('a48', $target);
			
			//目标类型
			if (preg_match('/^[0-9]$/', $type)) //数字类型
			{
				$sendMsg .= pack('L', $type); 
			}else{//直接为汉字类型数据
				if($type == '恒星')
				{
					$sendMsg .= pack('L', 0); 
				}elseif($type == '太阳'){
					$sendMsg .= pack('L', 1); 
				}elseif($type == '月亮'){
					$sendMsg .= pack('L', 2); 
				}elseif($type == '彗星'){
					$sendMsg .= pack('L', 3); 
				}elseif($type == '行星'){
					$sendMsg .= pack('L', 4); 
				}elseif($type == '卫星'){
					$sendMsg .= pack('L', 5); 
				}elseif($type == '固定位置'){
					$sendMsg .= pack('L', 6); 
				}elseif($type == '本底'){
					$sendMsg .= pack('L', 7); 
				}elseif($type == '暗流'){
					$sendMsg .= pack('L', 8); 
				}elseif($type == '平场'){
					$sendMsg .= pack('L', 9); 
				}
			}

			//赤经 rightAscension	
			$rightAscension = $v['rightAscension1'].':'.$v['rightAscension2'].':'.$v['rightAscension3'];			
			$rightAscension = time2Data($rightAscension);
			$sendMsg .= pack('d', $rightAscension);
			//赤纬
			$declination = $v['declination1'].':'.$v['declination2'].':'.$v['declination3'];
			$declination = time2Data($declination);
			$sendMsg .= pack('d', $declination);
			//历元
			$epoch = $v['epoch'];
		
			if ( preg_match('/^[0-9]$/', $epoch) ) //数字类型
			{
				$sendMsg .= pack('L', $epoch); 
			}else{//直接为字符类型数据 
				$epoch = strtolower ($epoch);
				if( $epoch == 'real' )
				{
					$sendMsg .= pack('L', 0);
				}elseif( $epoch == 'j2000' ){
					$sendMsg .= pack('L', 1); 
				}elseif( $epoch == 'b1950' ){
					$sendMsg .= pack('L', 2); 
				}elseif( $epoch == 'j2050' ){
					$sendMsg .= pack('L', 3); 
				}
			}	//历元结束

			//曝光时间
			$exposureTime = $v['exposureTime'];
			$sendMsg .= pack('d', $exposureTime); 

			//delayTime
			 $delayTime = $v['delayTime'];
			 $sendMsg .= pack('d', $delayTime);

			 //曝光数量
			 $exposureCount = $v['exposureCount'];
			 $sendMsg .= pack('L', $exposureCount);

			 //滤光片
			$filter = $v['filter'];
			$sendMsg .= pack('a8', $filter);
			// if ( preg_match('/^\d+$/', $filter) ) //数字类型
			// {
			// 	$sendMsg .= pack('a8', $filter_option[$filter]); 
			// }else{//直接为字符类型数据
			// 	$filter = strtoupper ($filter);
			// 	$sendMsg .= pack('a8', $filter);
			//  }	//滤光片

			//增益 gain
			$gain = $v['gain'];
			$sendMsg .= pack('S', $gain); 
			
			//bin
			$bin = $v['bin'];
			$sendMsg .= pack('S', $bin);
			
			//读出速度
			$readout = $v['readout'];
			$sendMsg .= pack('S', $readout);
			
			//发送 计划数据
        	$sendMsg = $headInfo . $sendMsg;
           
            udpSendPlan($sendMsg, $this->ip, $this->port); //无返回值
		}//给中控 发送at80数据 结束///////////////
		*/
	}//send_ToO_plan () 结束
	
	public function send_ToO_1_plan ()  //保存ToO计划数据
    {
		//首先判断是否已登录
		if ($this->ajaxAuthErr == 'not_log')
		{
			return '请先登录再进行操作!';
		}

		//判断是否有权限
		if ($this->ajaxAuthErr == 'no_auth')
		{
			return '您无权进行此操作!';
		}
		
		//halt($planData['plan_filter_option']);
		//定义全局$sequence 此变量在packHead()函数中要使用
		/*if (Cookie::has('sequence'))
		{
			$this->sequence = Cookie::get('sequence');
			Cookie::set('sequence', $this->sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$this->sequence = 0;
		}*/
        
		$postData = $this->input; //接收提交数据
		
		//halt($postData['planData']);
		$num = count ( $postData['planData'] );
		for ($i=0; $i < $num; $i++)
		{ 
			$postData['planData'][$i]['type'] = '0';
			$temp = $postData['planData'][$i]['rightAscension1'] .':' . $postData['planData'][$i]['rightAscension2'] . ':' . $postData['planData'][$i]['rightAscension3'];

			$postData['planData'][$i]['rightascension'] = time2Data($temp);

			$temp = $postData['planData'][$i]['declination1'] .':' . $postData['planData'][$i]['declination2'] . ':' . $postData['planData'][$i]['declination3'];

			$postData['planData'][$i]['declination'] = time2Data($temp);

			switch ($postData['planData'][$i]['at'])
			{
				case '60cm':
					$postData['planData'][$i]['at'] = 37; break;
				case '80cm':
					$postData['planData'][$i]['at'] = 36; break;
				case '50cm':
					$postData['planData'][$i]['at'] = 38; break;
				case '85cm':
					$postData['planData'][$i]['at'] = 35; break;
				case '100cm':
					$postData['planData'][$i]['at'] = 34; break;
				case '126cm':
					$postData['planData'][$i]['at'] = 33; break;
				case '216cm':
					$postData['planData'][$i]['at'] = 32; break;
			}

			$postData['planData'][$i]['exemode'] = $postData['exeMode'];
			$postData['planData'][$i]['exposurecount'] = $postData['planData'][$i]['exposureCount'];
			$postData['planData'][$i]['exposuretime'] = $postData['planData'][$i]['exposureTime'];
			$postData['planData'][$i]['delaytime'] = $postData['planData'][$i]['delayTime'];
			$postData['planData'][$i]['giveup'] = '0'; /*后期此字段要改为默认为0*/
		}

		$res = Db::table('plantoo')->strict(false)->insertAll($postData['planData']);
		if ( $res )
		{
			return 'ToO观测计划发送保存完毕!';
		}else{
			return 'ToO观测计划发送保存失败!';
		}
    }//send_ToO_1_plan () 结束

    public function start_stop_ToO () //开始执行观测或停止
    {
		$ip = config('ip');
        $port = config('port');
        //分别给at60和at80发送开始指令
        
        if (Cookie::has('sequence'))
		{
			$this->sequence = Cookie::get('sequence');
			Cookie::set('sequence', $this->sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$this->sequence = 0;
		}
        
        $postData = $this->input; //接收提交数据
        $this->msg = 9; //指令类型
        $length =28 + 16; //数据长度
               
		if($postData['planOption'] == 'planStart')
		{	$this->at = 37; //at60
            $headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
			$sendMsg = pack('L', 1);
			$sendMsg .= pack('L', $postData['mode']);
			$sendMsg .= pack('L', $postData['start']);
			$sendMsg .= pack('L', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			$this->ip = $ip['at60'];
			$this->port = $port['at60'];

            udpSend($sendMsg, $this->ip, $this->port);

            $this->at = 36; //at80
            $headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
			$sendMsg = $headInfo . $sendMsg;
			$this->ip = $ip['at80'];
			$this->port = $port['at80'];
            udpSend($sendMsg, $this->ip, $this->port);

            return '计划开始指令发送成功!';
		}elseif($postData['planOption'] == 'planStop'){
            $this->at = 37; //at60
            $headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
			$sendMsg = pack('L', 2);
			$sendMsg .= pack('L', $postData['mode']);
			$sendMsg .= pack('L', $postData['start']);
			$sendMsg .= pack('L', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			$this->ip = $ip['at60'];
			$this->port = $port['at60'];
            udpSend($sendMsg, $this->ip, $this->port);

            $this->at = 37; //at60
            $headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
			$sendMsg = $headInfo . $sendMsg;
			$this->ip = $ip['at80'];
			$this->port = $port['at80'];
            udpSend($sendMsg, $this->ip, $this->port);

            return '计划停止指令发送成功!';
		}
    } //start_stop_ToO () 结束
}