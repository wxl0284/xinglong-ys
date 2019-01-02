<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;

/*此控制器 负责页面观测图像各功能*/
class Image extends Base
{
    protected $file_path = ROOT_PATH . 'public' . DS;
    protected $at_image_dir = ''; //各望远镜普通格式观测图片目录
    //protected $at_fits_image_dir = ''; //各望远镜fits格式观测图片目录

    public function get_image()
    {
        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

        $png_dir = config('at_pic_dir');
        $day = date("Ymd", time());//月 日有前导零
        //$day = '20181201';
        $err = ''; //错误提示
        //$fits_dir = config('at_fits_dir');

        $postData = input();
		
        if ( !isset($postData['at_aperture']) || empty($postData['at_aperture']) )
        {
            return '无效的请求!';
        }
        //halt($this->file_path);
        switch ($postData['at_aperture']) { //根据望远镜口径，给 $this->$at赋值
            case '50cm':
                $this->at_image_dir = $png_dir['at50'];
                $observeimg_at = 'at50';break;
                //$this->at = 38;  break;
            case '60cm':
                $this->at_image_dir = $png_dir['at60'];
                $observeimg_at = 'at60';break;
                //$this->at_fits_image_dir = 37;  break;
            case '80cm':
                $this->at_image_dir = $png_dir['at80'];
                $observeimg_at = 'at80';break;
                //$this->at_image_dir = 37;
                //$this->at_fits_image_dir = 37;  break;
            case '85cm':
                $this->at_image_dir = $png_dir['at85'];
                $observeimg_at = 'at85';break;
                //$this->at = 35;  break;
            case '100cm':
                $this->at_image_dir = $png_dir['at100'];
                $observeimg_at = 'at100';break;
            case '126cm':
                $this->at_image_dir = $png_dir['at126'];
                $observeimg_at = 'at126';break;
                //$this->at = 33;  break;
            case '216cm':
                $this->at_image_dir = $png_dir['at216'];
                $observeimg_at = 'at216';break;
                //$this->at = 32;  break;
            default:
                return '提交的望远镜参数有误!';
        }
        
        

        $fit_img_data = Db::table('observerimg')->where('at', $observeimg_at)->where('date', 'like', $day.'%')->order('time desc')->select();
        
        if ( $fit_img_data )
        {
            foreach ($fit_img_data as $k => $v)
            {
                $result[$k]['name'] = str_replace('fit', 'png', $v['name']); //图片名
                $result[$k]['dir'] =  $this->at_image_dir . $v['date'] . '/'; //图片所在的目录
            }

            return 'img#' . json_encode ($result);
        }else{
            return '未获取到观测图片';
        }
        
        
        
        //halt($this->file_path . $this->at_image_dir . $day);
        /*try{
            $res = scandir ($this->file_path . $this->at_image_dir . $day);
        }catch(\Exception $e){
            $err = '读取文件异常';
        }

        if ( $err === '' )  //读取图片正常
        {
            if ( $res !== false && count($res) > 2 )
            {
                unset ($res[0], $res[1]); //删除前2个数据
                foreach ( $res as $k => $v )
                {
                    $result[$k] = $v;  //将文件名存入数组中
                    $time[$k] = filemtime( $this->file_path . $this->at_image_dir . $day . '/' . $v );//获取文件最近修改日期
                }

                array_multisort($time, SORT_DESC, $result);//按时间降序排序

                return 'img#' . json_encode ($result) . '#' . $this->at_image_dir . $day . '/';
            }else{
                return '未获取到观测图片';
            }
        }else{//读取图片异常
            return '未获取到观测图片';
        }*/
    }//get_image 结束

}