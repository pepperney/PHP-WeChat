<?php
return array(
	//'配置项'=>'配置值'
       //(1) 测试用的服务号
       "APPID"          => "wxb8e38e0360a34680",
       "APPSECRET"      => "40f8fce447252a324e9bac84823d9ba0",
      
       //(2)系统日志配置
       "DLOG_DIR"       => "./log/",     //后台程序日志存放的目录
       "DLOG_LEVEL"     => array("debug","run","error","fatal"), //后台程序日志级别
       "LOG_FILE_SIZE"  => 1048576,

       //(3)数据库的配置
       "DB_TYPE"       => "mysqli",
       "DB_HOST"       => "127.0.0.1", //设置的为212的外网的IP地址
       "DB_NAME"       => "mywechat",
       "DB_USER"       => "root",
       "DB_PWD"        => "",
       "DB_PORT"       => "3306",
       "DB_PREFIX"     => "t_",
       "DB_CHARSET"    => "utf8mb4"

);
