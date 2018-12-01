<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;

/*此控制器 负责页面观测图像各功能*/
class Image extends Base
{
    protected $file_path = ROOT_PATH . 'public' . DS . 'fits-image';
    protected $at_image_dir = ''; //各望远镜普通格式观测图片目录
    protected $at_fits_image_dir = ''; //各望远镜fits格式观测图片目录

    public function get_image()
    {
        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

        $postData = input();
        if ( !isset($postData['at_aperture']) || empty($postData['at_aperture']) )
        {
            return '无效的请求!';
        }
        //halt($this->file_path);
        /*switch ($postData['at_aperture']) { //根据望远镜口径，给 $this->$at赋值
            case '50cm':
                //$this->at = 38;  break;
            case '60cm':
                //$this->at_image_dir = 37;
                //$this->at_fits_image_dir = 37;  break;
            case '80cm':
                //$this->at_image_dir = 37;
                //$this->at_fits_image_dir = 37;  break;
            case '85cm':
                //$this->at = 35;  break;
            case '100cm':
                //$this->at = 34;  break;
            case '126cm':
                //$this->at = 33;  break;
            case '216cm':
                //$this->at = 32;  break;
            default:
                return '提交的望远镜参数有误!';
        }*/

        $res = scandir ($this->file_path); /*后期要改为云量图片的处理异常来做 否则有bug*/

        if ( $res !== false && count($res) > 2 )
        {
            unset ($res[0], $res[1]); //删除前2个数据
            foreach ( $res as $v)
            {
                $result[] = $v;  //将文件名存入数组中
            }
            return 'img#' . json_encode ($result);
        }else{
            return '未获取到观测文件';
        }
    }//get_image 结束

}