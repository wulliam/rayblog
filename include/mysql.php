<?php
function connect($servername,$dbusername,$dbpass)
{ if(!$db=@mysql_connect($servername,$dbusername,$dbpass))
     halt("δ�����ӵ����ݿ�");
}

function selectdb($dbname)
{if(!mysql_select_db($dbname))
    halt("δ���ҵ������ݿ�");
    mysql_query("SET NAMES utf8");
			mysql_query("SET CHARACTER SET utf8");
			mysql_query("SET COLLATION_CONNECTION='utf8_general_ci'");
}
function query($querystr)
{$res=mysql_query($querystr);
 if(!$res)
    halt("SQL�����Ч,�������ݿⱻ�ƻ����������˴����URL".$querystr);
    return $res;
}
function halt($msg)
{echo $msg;
exit;


}
?>