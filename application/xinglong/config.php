<?php
//xinglong模块配置
return [
     'database'           => [
        // 数据库类型
        //'type'            => 'pgsql',    
        'type'            => 'mysql',
        
        // 服务器地址
        //'hostname'        => '192.168.160.100',
        'hostname'        => '127.0.0.1',
       
        // 数据库名
        //'database'        => 'ATCCSDB',   
        'database'        => 'xinglong',   
        
        // 数据库用户名
        //'username'        => 'atccs', 
        'username'        => 'root',

        // 数据库密码
        //'password'        => '123456', 
        'password'        => '',        

        // 数据库连接端口
        //'hostport'        => '5432',    
        'hostport'        => '3306',     

    ],
	
];