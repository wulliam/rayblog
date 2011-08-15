<?php
function set_writeable($file)
{if(is_writeable($file))
   echo"检测文件（夹）$file …… <strong>可写</strong><br>";
   else{
        echo"检测文件（夹）$file …… <strong>不可写</strong><br>正在改变权限 …… ";
        if(@chmod($file,0777))
		  echo"<strong>可写</strong><br>";
		  else
		  {echo"<strong>失败,请手动更改此文件访问权限！</strong><br>";
          exit;
          }
        }
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>R-Blog 安装程序</title>
<link href="templates/Default/css/style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#CCCCCC">
<table width="623" height="720" border="0" align="center" cellpadding="0" cellspacing="0" class="row1">
  <tr>
    <td width="621" height="433" valign="top" bgcolor="#f5f5f5" class="text000000">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr bgcolor="#666666">
          <td width="2%" height="41">&nbsp;</td>
          <td width="98%" class="title">R-Blog 安装程序</td>
        </tr>
        <tr bgcolor="#666666">
          <td height="19">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <?php

if(isset($_GET['action'])&&$_GET['action']=="install")
{?>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="text000000">
<tr>
<td>
<?php
if($_POST['admin']==""||$_POST['adminpass']=="")
	    {echo"<br>管理员和管理员密码不能为空。";
		 exit;
		}
if($_POST['dbname']=="")
	    {echo"<br>数据库名称不能为空。";
		 exit;
		}
if(strlen($_POST['adminpass'])<6)
   {echo"<br>管理员密码不能少于6位。";
		 exit;
   }
if(!eregi("^[a-z0-9_]{3,}\$",$_POST['admin']))
   {echo"<br>管理员名称不能少于3位或不能包含除字母，数字，下划线以外的字符";
		 exit;
   }
echo"<br>开始安装<br><br>";
 set_writeable("include/dbconfig.php");
 set_writeable("include/settings.php");
 set_writeable("include/scrollup.js");
 set_writeable("upload");
 echo"配置数据库文件 …… ";
 if(@$fp = fopen("include/dbconfig.php", "w"))
    {    $contents = "<?php\n";
        $contents  .= "\$servername=\"$_POST[servername]\";\n";
		$contents  .= "\$dbname=\"$_POST[dbname]\";\n";
		$contents  .= "\$dbusername=\"$_POST[dbusername]\";\n";
		$contents  .= "\$dbpass=\"$_POST[dbpass]\";\n";
		$contents  .= "\$db_prefix=\"$_POST[db_prefix]\";\n";
        $contents  .= "?>";
        fwrite($fp, $contents);
        fclose($fp);
		echo"<strong>成功</strong><br>";
      }
	 else
	   { echo"<strong>失败</strong><br>";
         exit;
       }
include"include/dbconfig.php";
     mysql_connect($servername,$dbusername,$dbpass);
      mysql_select_db($dbname);
$md="CREATE TABLE `".$db_prefix."about` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `title` varchar(200) default NULL,
  `name` varchar(200) default NULL,
  `value` text,
  `description` varchar(200) default NULL,
  `type` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."about` VALUES('1', '昵称', 'nickname', 'Ray', '你的昵称', 'input');
INSERT INTO `".$db_prefix."about` VALUES('2', '性别', 'sex', '男', '你的性别', 'input');
INSERT INTO `".$db_prefix."about` VALUES('3', 'E-Mail', 'email', 'x7e@msn.com', '电子邮件', 'input');
INSERT INTO `".$db_prefix."about` VALUES('4', 'MSN', 'msn', 'x7e@msn.com', 'MSN Messenger', 'input');
INSERT INTO `".$db_prefix."about` VALUES('6', '个人网站', 'web', 'http://rays.512j.com', '你的个人网站', 'input');
INSERT INTO `".$db_prefix."about` VALUES('8', '个人描述', 'about', '这家伙很懒,什么都没留下...', '简单的介绍自己', 'input');
INSERT INTO `".$db_prefix."about` VALUES('7', '爱好', 'hobby', '玩', '你的爱好', 'textarea');
INSERT INTO `".$db_prefix."about` VALUES('5', 'QQ', 'qq', '14899515', 'QQ', 'input');

CREATE TABLE `".$db_prefix."admin` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `admin_name` char(200) default NULL,
  `admin_pass` char(200) default NULL,
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."admin` VALUES('1', '".$_POST['admin']."', '".md5($_POST['adminpass'])."');

CREATE TABLE `".$db_prefix."blog` (
  `bid` int(9) unsigned NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `text` text,
  `cid` tinyint(3) unsigned default NULL,
  `date` datetime default NULL,
  `view` int(9) unsigned default NULL,
  UNIQUE KEY `NewIndex` (`bid`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."blog` VALUES('0', '欢迎使用R-Blog', '[B]R-Blog v1.5.3[/B]\r\n程序编写：Ray\r\n官方网站：http://Rays.512j.com\r\n程序更新：http://rays.512j.com/index.php?action=list&cid=7\r\n模版下载：http://rays.512j.com/index.php?action=list&cid=5\r\n\r\n[B]一些说明[/B]：\r\n本版本为免费版本，用户可以无条件的传播此程式代码，并从中学习，但请尊重作者的劳动，不要清除页脚部分的版本信息。', '8', '2004-09-19 14:06:01', '0');

CREATE TABLE `".$db_prefix."blogpl` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `pltitle` varchar(255) default NULL,
  `pltext` text,
  `pldate` datetime default NULL,
  `plname` varchar(20) default NULL,
  `pl_id` int(8) unsigned default NULL,
  `pl_email` varchar(255) default NULL,
  `pl_cid` tinyint(3) unsigned NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM;

CREATE TABLE `".$db_prefix."cblog` (
  `cid` int(9) unsigned NOT NULL auto_increment,
  `cname` varchar(100) default NULL,
  `o` tinyint(3) unsigned default '200',
  UNIQUE KEY `cid` (`cid`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."cblog` VALUES('8', '一些说明', '200');

CREATE TABLE `".$db_prefix."count` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `count` int(10) unsigned default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."count` VALUES('1', '1');

CREATE TABLE `".$db_prefix."gb` (
  `id` int(9) unsigned NOT NULL auto_increment,
  `gbname` varchar(100) default NULL,
  `gbmail` varchar(200) default NULL,
  `gbtext` text,
  `gbdate` datetime default NULL,
  `gbreply` text,
  `gbsite` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."gb` VALUES('45', 'Ray', 'x7e@msn.com', '欢迎使用R-Blog :)', '2004-08-27 14:07:49', NULL, 'http://Rays.512j.com');

CREATE TABLE `".$db_prefix."link` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` char(200) default NULL,
  `url` char(200) default NULL,
  `de` char(200) default NULL,
  `visible` tinyint(3) unsigned default '1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."link` VALUES('8', 'Ray\'s Blog', 'http://rays.512j.com', 'R-Blog官方网站', '1');

CREATE TABLE `".$db_prefix."setting` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` char(200) default NULL,
  `title` char(200) default NULL,
  `value` char(200) default NULL,
  `description` char(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."setting` VALUES('1', 'title', '标题栏', 'Ray\'s Blo\'g', '显示在浏览器标题栏上的内容');
INSERT INTO `".$db_prefix."setting` VALUES('2', 'blogname', 'Blog名称', 'Ray\'s Blog', 'Blog名称');
INSERT INTO `".$db_prefix."setting` VALUES('3', 'description', 'Blog描述', '忍者大师如是说：“设计一个千百万程序的操作系统很容易，要改变一个人的本性却困难得多。”   --- 《编程之禅》', 'Blog的简单介绍');
INSERT INTO `".$db_prefix."setting` VALUES('4', 'indexper', '首页显示记录数', '7', '首页每页显示记录数');
INSERT INTO `".$db_prefix."setting` VALUES('5', 'listper', '分类每页显示记录数', '25', '分类每页显示记录数');
INSERT INTO `".$db_prefix."setting` VALUES('6', 'gbper', '留言本每页显示记录数', '7', '留言本每页显示记录数');
INSERT INTO `".$db_prefix."setting` VALUES('7', 'addfeedbacktime', '发表评论时间间隔', '19', '可以防止他人灌水,单位秒');
INSERT INTO `".$db_prefix."setting` VALUES('8', 'addtogbtime', '发表留言时间间隔', '19', '可以防止他人灌水,单位秒');
INSERT INTO `".$db_prefix."setting` VALUES('9', 'templatename', '模版名称', 't2', '模版文件夹名称(区分大小写)');
INSERT INTO `".$db_prefix."setting` VALUES('10', 'blogger', '作者名字', 'Ray', 'Blog的主人名字');
";

   $a_query = explode(";",$md);
   while (list(,$query) = each($a_query)) {
           $query = trim($query);
		   if ($query) {
               if (strstr($query,'CREATE TABLE')) {
                   ereg('CREATE TABLE ([^ ]*)',$query,$regs);
				   echo "正在建立表: ".$regs[1]." …… ";
				   if(mysql_query($query))
				      echo"<strong>成功</strong><br>";
					  else
					  {echo"<strong>失败</strong><br>
      "; exit;}} mysql_query($query); } }


	  ?><br>
      <br>
      安装成功完成！为了安全，请马上删除install.php文件！。
	  </td>
	  </tr>
	  </table>

	  <?php

	  } else { ?>
      <form name="form1" method="post" action="install.php?action=install">
        <table width="100%" height="559" border="0" cellpadding="0" cellspacing="0">
          <tr>
          <td width="13%" height="65" align="center"><font size="7"><strong>1</strong></font></td>
          <td width="85%" class="text000000"> <p><strong><br>
              一些说明<br>
              <br>
              </strong>欢迎使用R-Blog 1.5.3<br>
              本版本为免费版本，用户可以无条件的传播此程式代码，并从中学习，但请尊重作者的劳动，不要清除页脚部分的版本信息。</p>
            </td>
          <td width="2%" class="text000000">&nbsp;</td>
        </tr>
        <tr>
          <td height="25">&nbsp;</td>
          <td valign="top">&nbsp;</td>
          <td valign="top">&nbsp;</td>
        </tr>
        <tr>
          <td align="center"><strong><font size="7">2</font></strong></td>
          <td valign="top" class="text000000"><strong><br>
            设置文件夹权限<br>
            <br>
            </strong>在linux系统下运行本安装程序时，请用ftp工具连接到ftp服务器，将文件夹upload的属性设置为可写(一般777就行)。<strong>
            </strong></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center"><font size="7"><strong>3</strong></font></td>
          <td valign="top" class="text000000"><strong><br>
            数据库设置<br>
            <br>
            </strong>
            <table width="73%" border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="19%" height="25" class="text000000">数据库地址：</td>
                  <td width="81%" class="text666666">
                    <input name="servername" type="text" id="servername" value="localhost">
                    * 一般不需更改</td>
                </tr>
                <tr>
                  <td height="25" class="text000000">数据库名称：</td>
                  <td class="text666666">
                    <input name="dbname" type="text" id="dbname">
                  </td>
                </tr>
                <tr>
                  <td height="25" class="text000000">数据表前缀：</td>
                  <td class="text666666">
                    <input name="db_prefix" type="text" id="db_prefix" value="ray_">
                    * 一般不需更改</td>
                </tr>
                <tr>
                  <td height="25" class="text000000">用户名：</td>
                  <td class="text666666">
                    <input name="dbusername" type="text" id="dbusername"></td>
                </tr>
                <tr>
                  <td height="25" class="text000000">密码：</td>
                  <td class="text666666">
                    <input name="dbpass" type="password" id="dbpass"></td>
                </tr>
              </table>
            <strong> </strong></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center"><font size="7"><strong>4</strong></font></td>
            <td valign="top" class="text000000"><strong><br>
              设置管理员<br>
              <br>
              </strong>
              <table width="94%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="14%" height="25" class="text000000">管理员：</td>
                  <td width="86%" class="text666666">
                    <input name="admin" type="text" id="admin">
                    * 只允许是大小写字母、数字、下划线</td>
                </tr>
                <tr>
                  <td height="25" class="text000000">密码：</td>
                  <td class="text666666">
                    <input name="adminpass" type="password" id="adminpass">
                    * 6位以上</td>
                </tr>
              </table>
              <strong> </strong></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" name="Submit" value="填好了！开始安装！">
              <input type="reset" name="Submit2" value="重置"></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table></form>
<?php
}
?></td>
  </tr>
</table>
</body>
</html>