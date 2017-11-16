<?php
namespace app\xinglong\model;

use think\Model;

class At60config extends Model
{
	//protected $pk = 'pk_dev1';
	protected $table = 'at60config';
	
	protected $connection = [
		// 数据库类型
		'type' => 'pgsql',
		// 服务器地址
		'hostname' => '127.0.0.1',
		// 数据库名
		'database' => 'postgres',
		// 数据库用户名
		'username' => 'postgres',
		// 数据库密码
		'password' => '123456',
		// 数据库编码默认采用utf8
		'charset' => 'utf8',
		// 数据库调试模式
		'debug' => false,
		//端口
		'hostport'        => '5432',
	];
}