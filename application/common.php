<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// email 文件
use email\PHPMailer;  //引入extend中的邮件发送类文件
 /**
  * @param $sjr  收件人
  * @param $title 标题
  * @param $content 邮件内容
  *
  * @throws \phpmailer\phpmailerException
  */
 function sendEmail($sjr,$title,$content){
try {
         $mail = new PHPMailer(true);
         $mail->IsSMTP();       // 设定使用SMTP服务，SMTP简单邮件传输协议
         $mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
         $mail->SMTPAuth = true; //开启认证
         $mail->Port = 25;       // SMTP服务器的端口号
         $mail->Host = "smtp.163.com"; // SMTP 服务器
         $mail->Username = "ecshop3306@163.com";   //SMTP服务器用户名，邮箱号
         $mail->Password = "ec3306";          //SMTP服务器密码 授权码
         //$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could not execute: /var/qmail/bin/sendmail ”的错误提示
 
         $mail->AddReplyTo("cj372835766@163.com","这里输入回复邮件内容");//回复地址（收件人回复。发件人可以看到回复信息）  第一个参数是发件人邮箱，第二个为快捷回复的内容
         $mail->FromName = "合纵连横"; //发件人的名称
         $mail->From = "ecshop3306@163.com"; //发件人邮箱
 
         $to = $sjr;              //收件人地址
         $mail->AddAddress($to);
         $mail->Subject = $title; //邮件标题
         $mail->Body = $content;  //邮件内容
         $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略
         $mail->WordWrap = 80; // 设置每行字符串的长度
         //$mail->AddAttachment("f:/test.png"); //可以添加附件
         $mail->IsHTML(true);
 
         $mail->Send(); //发送邮件
         // echo "邮件发送成功";
         return 1;  //发送成功  输出1
     } catch (phpmailerException $e) {
         echo "邮件发送失败：".$e->errorMessage();
     }
 }
// 递归创建 分类 树形结构
function createTree($data,$parent_id=0,$level=1)
{
	//1 定义 一个容器（新数组);
	static  $new_arr = [];
	//2 遍历 数据一条条比对
	foreach ($data as $key => $value) {
		//3找 parent_id = 0 的id
		if ($value['parent_id'] == $parent_id) {
			//增加级别 字段
			$value['level'] = $level;
			//4 找到 之后放到新的数组里
		    $new_arr[] = $value;
		    //调用 程序自身递归找子集
		    createTree($data,$value['cid'],$level+1);
		}
	}
	return $new_arr;
}
// 递归创建 分类 呈次结构
function createTreeBySon($data,$parent_id=0)
{
    //1 定义 一个容器（新数组);
    $new_arr = [];
    //2 遍历 数据一条条比对
    foreach ($data as $key => $value) {
        //3找 parent_id = 0 的id
        if ($value['parent_id'] == $parent_id) {
           $new_arr[$key] = $value;
           $new_arr[$key]['son'] = createTreeBySon($data,$value['cid']);
        }
    }
    return $new_arr;
}