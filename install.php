<?php
function set_writeable($file)
{if(is_writeable($file))
   echo"����ļ����У�$file ���� <strong>��д</strong><br>";
   else{
        echo"����ļ����У�$file ���� <strong>����д</strong><br>���ڸı�Ȩ�� ���� ";
        if(@chmod($file,0777))
		  echo"<strong>��д</strong><br>";
		  else
		  {echo"<strong>ʧ��,���ֶ����Ĵ��ļ�����Ȩ�ޣ�</strong><br>";
          exit;
          }
        }
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>R-Blog ��װ����</title>
<link href="templates/Default/css/style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#CCCCCC">
<table width="623" height="720" border="0" align="center" cellpadding="0" cellspacing="0" class="row1">
  <tr>
    <td width="621" height="433" valign="top" bgcolor="#f5f5f5" class="text000000">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr bgcolor="#666666">
          <td width="2%" height="41">&nbsp;</td>
          <td width="98%" class="title">R-Blog ��װ����</td>
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
	    {echo"<br>����Ա�͹���Ա���벻��Ϊ�ա�";
		 exit;
		}
if($_POST['dbname']=="")
	    {echo"<br>���ݿ����Ʋ���Ϊ�ա�";
		 exit;
		}
if(strlen($_POST['adminpass'])<6)
   {echo"<br>����Ա���벻������6λ��";
		 exit;
   }
if(!eregi("^[a-z0-9_]{3,}\$",$_POST['admin']))
   {echo"<br>����Ա���Ʋ�������3λ���ܰ�������ĸ�����֣��»���������ַ�";
		 exit;
   }
echo"<br>��ʼ��װ<br><br>";
 set_writeable("include/dbconfig.php");
 set_writeable("include/settings.php");
 set_writeable("include/scrollup.js");
 set_writeable("upload");
 echo"�������ݿ��ļ� ���� ";
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
		echo"<strong>�ɹ�</strong><br>";
      }
	 else
	   { echo"<strong>ʧ��</strong><br>";
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

INSERT INTO `".$db_prefix."about` VALUES('1', '�ǳ�', 'nickname', 'Ray', '����ǳ�', 'input');
INSERT INTO `".$db_prefix."about` VALUES('2', '�Ա�', 'sex', '��', '����Ա�', 'input');
INSERT INTO `".$db_prefix."about` VALUES('3', 'E-Mail', 'email', 'x7e@msn.com', '�����ʼ�', 'input');
INSERT INTO `".$db_prefix."about` VALUES('4', 'MSN', 'msn', 'x7e@msn.com', 'MSN Messenger', 'input');
INSERT INTO `".$db_prefix."about` VALUES('6', '������վ', 'web', 'http://rays.512j.com', '��ĸ�����վ', 'input');
INSERT INTO `".$db_prefix."about` VALUES('8', '��������', 'about', '��һ����,ʲô��û����...', '�򵥵Ľ����Լ�', 'input');
INSERT INTO `".$db_prefix."about` VALUES('7', '����', 'hobby', '��', '��İ���', 'textarea');
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

INSERT INTO `".$db_prefix."blog` VALUES('0', '��ӭʹ��R-Blog', '[B]R-Blog v1.5.3[/B]\r\n�����д��Ray\r\n�ٷ���վ��http://Rays.512j.com\r\n������£�http://rays.512j.com/index.php?action=list&cid=7\r\nģ�����أ�http://rays.512j.com/index.php?action=list&cid=5\r\n\r\n[B]һЩ˵��[/B]��\r\n���汾Ϊ��Ѱ汾���û������������Ĵ����˳�ʽ���룬������ѧϰ�������������ߵ��Ͷ�����Ҫ���ҳ�Ų��ֵİ汾��Ϣ��', '8', '2004-09-19 14:06:01', '0');

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

INSERT INTO `".$db_prefix."cblog` VALUES('8', 'һЩ˵��', '200');

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

INSERT INTO `".$db_prefix."gb` VALUES('45', 'Ray', 'x7e@msn.com', '��ӭʹ��R-Blog :)', '2004-08-27 14:07:49', NULL, 'http://Rays.512j.com');

CREATE TABLE `".$db_prefix."link` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` char(200) default NULL,
  `url` char(200) default NULL,
  `de` char(200) default NULL,
  `visible` tinyint(3) unsigned default '1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."link` VALUES('8', 'Ray\'s Blog', 'http://rays.512j.com', 'R-Blog�ٷ���վ', '1');

CREATE TABLE `".$db_prefix."setting` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` char(200) default NULL,
  `title` char(200) default NULL,
  `value` char(200) default NULL,
  `description` char(200) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `".$db_prefix."setting` VALUES('1', 'title', '������', 'Ray\'s Blo\'g', '��ʾ��������������ϵ�����');
INSERT INTO `".$db_prefix."setting` VALUES('2', 'blogname', 'Blog����', 'Ray\'s Blog', 'Blog����');
INSERT INTO `".$db_prefix."setting` VALUES('3', 'description', 'Blog����', '���ߴ�ʦ����˵�������һ��ǧ�������Ĳ���ϵͳ�����ף�Ҫ�ı�һ���˵ı���ȴ���ѵöࡣ��   --- �����֮����', 'Blog�ļ򵥽���');
INSERT INTO `".$db_prefix."setting` VALUES('4', 'indexper', '��ҳ��ʾ��¼��', '7', '��ҳÿҳ��ʾ��¼��');
INSERT INTO `".$db_prefix."setting` VALUES('5', 'listper', '����ÿҳ��ʾ��¼��', '25', '����ÿҳ��ʾ��¼��');
INSERT INTO `".$db_prefix."setting` VALUES('6', 'gbper', '���Ա�ÿҳ��ʾ��¼��', '7', '���Ա�ÿҳ��ʾ��¼��');
INSERT INTO `".$db_prefix."setting` VALUES('7', 'addfeedbacktime', '��������ʱ����', '19', '���Է�ֹ���˹�ˮ,��λ��');
INSERT INTO `".$db_prefix."setting` VALUES('8', 'addtogbtime', '��������ʱ����', '19', '���Է�ֹ���˹�ˮ,��λ��');
INSERT INTO `".$db_prefix."setting` VALUES('9', 'templatename', 'ģ������', 't2', 'ģ���ļ�������(���ִ�Сд)');
INSERT INTO `".$db_prefix."setting` VALUES('10', 'blogger', '��������', 'Ray', 'Blog����������');
";

   $a_query = explode(";",$md);
   while (list(,$query) = each($a_query)) {
           $query = trim($query);
		   if ($query) {
               if (strstr($query,'CREATE TABLE')) {
                   ereg('CREATE TABLE ([^ ]*)',$query,$regs);
				   echo "���ڽ�����: ".$regs[1]." ���� ";
				   if(mysql_query($query))
				      echo"<strong>�ɹ�</strong><br>";
					  else
					  {echo"<strong>ʧ��</strong><br>
      "; exit;}} mysql_query($query); } }


	  ?><br>
      <br>
      ��װ�ɹ���ɣ�Ϊ�˰�ȫ��������ɾ��install.php�ļ�����
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
              һЩ˵��<br>
              <br>
              </strong>��ӭʹ��R-Blog 1.5.3<br>
              ���汾Ϊ��Ѱ汾���û������������Ĵ����˳�ʽ���룬������ѧϰ�������������ߵ��Ͷ�����Ҫ���ҳ�Ų��ֵİ汾��Ϣ��</p>
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
            �����ļ���Ȩ��<br>
            <br>
            </strong>��linuxϵͳ�����б���װ����ʱ������ftp�������ӵ�ftp�����������ļ���upload����������Ϊ��д(һ��777����)��<strong>
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
            ���ݿ�����<br>
            <br>
            </strong>
            <table width="73%" border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="19%" height="25" class="text000000">���ݿ��ַ��</td>
                  <td width="81%" class="text666666">
                    <input name="servername" type="text" id="servername" value="localhost">
                    * һ�㲻�����</td>
                </tr>
                <tr>
                  <td height="25" class="text000000">���ݿ����ƣ�</td>
                  <td class="text666666">
                    <input name="dbname" type="text" id="dbname">
                  </td>
                </tr>
                <tr>
                  <td height="25" class="text000000">���ݱ�ǰ׺��</td>
                  <td class="text666666">
                    <input name="db_prefix" type="text" id="db_prefix" value="ray_">
                    * һ�㲻�����</td>
                </tr>
                <tr>
                  <td height="25" class="text000000">�û�����</td>
                  <td class="text666666">
                    <input name="dbusername" type="text" id="dbusername"></td>
                </tr>
                <tr>
                  <td height="25" class="text000000">���룺</td>
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
              ���ù���Ա<br>
              <br>
              </strong>
              <table width="94%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="14%" height="25" class="text000000">����Ա��</td>
                  <td width="86%" class="text666666">
                    <input name="admin" type="text" id="admin">
                    * ֻ�����Ǵ�Сд��ĸ�����֡��»���</td>
                </tr>
                <tr>
                  <td height="25" class="text000000">���룺</td>
                  <td class="text666666">
                    <input name="adminpass" type="password" id="adminpass">
                    * 6λ����</td>
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
          <td><input type="submit" name="Submit" value="����ˣ���ʼ��װ��">
              <input type="reset" name="Submit2" value="����"></td>
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