<?php
/**
 * Created by PhpStorm.
 * User: kurisu
 * Date: 2017/11/16
 * Time: 1:26
 */
$start_time = microtime( true );//运行时间开始计时
require 'vendor/autoload.php';

use \CAPTCHA_Reader\Tools\MultipleTestsRepository;

//TODO 这里补dir
$multipleTest = new MultipleTestsRepository();
$result=$multipleTest->startTest();
//var_dump( $result );
echo "<pre>";
print_r( $result );
//var_dump($multipleTest->dirTraverse('D:\code\DEMO\CAPTCHA_Idenify\new\imgs_test\\'));

$end_time = microtime( true );//计时停止
echo '执行时间为：' . ($end_time - $start_time) . ' s' . '<br/>';