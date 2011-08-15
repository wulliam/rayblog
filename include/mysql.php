<?php
function connect($servername,$dbusername,$dbpass)
{ if(!$db=@mysql_connect($servername,$dbusername,$dbpass))
     halt("未能连接到数据库");
}

function selectdb($dbname)
{if(!mysql_select_db($dbname))
    halt("未能找到到数据库");
    mysql_query("SET NAMES utf8");
			mysql_query("SET CHARACTER SET utf8");
			mysql_query("SET COLLATION_CONNECTION='utf8_general_ci'");
}
function query($querystr)
{$res=mysql_query($querystr);
 if(!$res)
    halt("SQL语句无效,可能数据库被破坏，或输入了错误的URL".$querystr);
    return $res;
}
function halt($msg)
{echo $msg;
exit;


}
?>