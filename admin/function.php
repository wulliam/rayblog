<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: function.php
    作  者: Ray
    说  明: 后台函数
    版  本: 1.5
    日  期: 2004-9-10
    ***********************************************************************/
    include"../include/dbconfig.php";
    include"../include/mysql.php";
    include"../class/template.php";
    function login($username, $userpass)
    {
        global $db_prefix;
        $userpass = md5($userpass);
        $res = query("select admin_pass from ".$db_prefix."admin where admin_name=\"$username\"");
        $row = mysql_fetch_array($res);
        if ($userpass != "" && $row['admin_pass'] == $userpass)
        {
            setcookie("cadminuser", $username);
            setcookie("cadminpass", $userpass);
            return 1;
        }
        else
            return 0;
    }
    function logout()
    {
        global $t;
        setcookie("cadminuser", "", time());
        setcookie("cadminpass", "", time());
        msg("正在退出，请稍候...", "login.php");
    }
    function islogin()
    {
        global $db_prefix;
        if (isset($_COOKIE['cadminuser']))
        {
            $res = query("select admin_pass from ".$db_prefix."admin where admin_name=\"$_COOKIE[cadminuser]\"");
            $row = mysql_fetch_array($res);
            if ($_COOKIE['cadminpass'] != "" && $_COOKIE['cadminpass'] == $row["admin_pass"])
            return 1;
            else
                return 0;
        }
        else
            {
            return 0;
        }
    }
    function pagelogin()
    {
        $t = new Template("HTML");
        $t->set_file("login", "login.html");
        $t->parse("out", "login");
        $t->p("out");
        exit;
    }
    function tablecount($table, $id, $where = "")
    {
        $res = query("select count($id) as sum from $table $where");
        $row = mysql_fetch_array($res);
        return $row['sum'];
    }
    function AddSlashes2($str)
    {
        if (get_magic_quotes_gpc())
        ;
        else
            $str = AddSlashes($str);
        return $str;
    }
    function get_real_size($size)
    {
        $kb = 1024;
        // Kilobyte
        $mb = 1024 * $kb;
        // Megabyte
        $gb = 1024 * $mb;
        // Gigabyte
        $tb = 1024 * $gb;
        // Terabyte
        if ($size < $kb)
        {
            return $size." B";
        }
        else if($size < $mb)
        {
            return round($size/$kb, 2)." KB";
        }
        else if($size < $gb)
        {
            return round($size/$mb, 2)." MB";
        }
        else if($size < $tb)
        {
            return round($size/$gb, 2)." GB";
        }
        else
        {
            return round($size/$tb, 2)." TB";
        }
    }
    function ubb($str)
    {
        //onload=\"javascript:if(this.width>450)this.width=450\"
        $str = htmlspecialchars($str);
        $str = nl2br($str);
        $str = eregi_replace("(http|ftp):\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?_%#&;\-]{2,}", "[url]\\0[/url]", $str);
        $str = eregi_replace("\[img\]\[url\]((http|ftp)[:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/url\]\[/img\]", "<a href=\"\\1\" target=\"_blank\"><img src=\"\\1\" border=\"0\" class=\"img\"  alt=\"点这里在新的窗口中打开\"/></a>", $str);
        $str = eregi_replace("\[img right\]\[url\]((http|ftp)[:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/url\]\[/img\]", "<a href=\"\\1\" target=\"_blank\"><img src=\"\\1\" border=\"0\" align=\"right\" class=\"img\"  alt=\"点这里在新的窗口中打开\"/></a>", $str);
        $str = eregi_replace("\[img left\]\[url\]((http|ftp)[:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/url\]\[/img\]", "<a href=\"\\1\" target=\"_blank\"><img src=\"\\1\" border=\"0\" align=\"left\" class=\"img\"  alt=\"点这里在新的窗口中打开\"/></a>", $str);
        //$str=eregi_replace("\[/url\]\[/img\]",">",$str);
        $str = eregi_replace("\[img\]([0-9a-zA-Z_\-\/]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/img\]", "<a href=\"\\1\" target=\"_blank\"><img src=\"\\1\" border=\"0\" class=\"img\" alt=\"点这里在新的窗口中打开\"/></a>", $str);
        $str = eregi_replace("\[img right\]([0-9a-zA-Z_\-\/]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/img\]", "<a href=\"\\1\" target=\"_blank\"><img src=\"\\1\" border=\"0\" align=\"right\" class=\"img\" alt=\"点这里在新的窗口中打开\"/></a>", $str);
        $str = eregi_replace("\[img left\]([0-9a-zA-Z_\-\/]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/img\]", "<a href=\"\\1\" target=\"_blank\"><img src=\"\\1\" border=\"0\" align=\"left\" class=\"img\" alt=\"点这里在新的窗口中打开\"/></a>", $str);
        //$str=eregi_replace("\[/img\]",">",$str);
        $str = eregi_replace("\[url\]\[url\]", "[url]", $str);
        $str = eregi_replace("\[/url\]\[/url\]", "[/url]", $str);
        $str = eregi_replace("\[url\]((http|ftp)[:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&;_%#\-]{2,})\[/url\]", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $str);
        //$str=eregi_replace("\[url=(<a href=\".{1,}\">).{1,}</a>\](.{1,})\[/url\]","\\1\\2</a>",$str);
        //$str=eregi_replace("\[url=(<a href=\"http:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&;_%#\-]{2,}\" target=\"_blank\">)http:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&;_%#\-]{2,}</a>\]([a-z0-9]|[\x80-\xfe]{1,})\[/url\]","\\1\\2</a>",$str);
        $str = eregi_replace("\[url=(<a href=\"http:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&;_%#\-]{2,}\" target=\"_blank\">)http:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&;_%#\-]{2,}</a>\]([a-z0-9]{1,}|[\x80-\xfe]{1,})\[/url\]", "\\1\\2</a>", $str);
        return $str;
    }
    function getallpage2($per, $str)
    {
        global $db_prefix;
        $cou = query("select count(id) as sum from ".$db_prefix."gb $str");
        $cou = mysql_fetch_array($cou);
        $p = ceil($cou['sum']/$per);
        return $p;
    }
    function limit($page, $per)
    {
        if ($page == NULL)
        $page = 1;
        $lim = ($page-1) * $per;
        return $lim;
    }
    function printpage($allpage, $page, $v, $per)
    {
        global $strNext, $strPerior, $t;
        $v = htmlspecialchars($v);
        $strNext = "";
        $strPerior = "";
        if ($page == NULL)
        $page = 1;
        $next = $page+1;
        $perior = $page-1;
        // if($page==1)
        //  echo"<a href=\"index.php?action=guestbook&page=$next\">Next</a>";
        if ($page == $allpage && $page != 1)
        {
            $strPerior = "<a href=\"".$v."page=$perior\">上一页</a>";
            $strNext = "";
        }
        if ($page < $allpage)
        {
            if ($page == 1)
            {
                $strNext = "<a href=\"".$v."page=$next\">下一页</a>";
                $strPerior = "";
            }
            else
                {
                $strPerior = "<a href=\"".$v."page=$perior\">上一页</a>";
                $strNext = "<a href=\"".$v."page=$next\">下一页</a>";
            }
        }
        $t->set_var(array("strNext" => $strNext,
            "strPerior" => $strPerior,
            "cpage" => $_GET['page'],
            "allpage" => $allpage )
        );
        for($i = 1; $i <= $allpage; $i++)
        {
            if ($i == $_GET['page'])
            $sel = "selected";
            else
                $sel = "";
            $t->set_var(array("v" => $v,
                "i" => $i,
                "sel" => $sel )
            );
            @$t->parse("rowspj", "bpagejump", true);
        }
        $t->parse("putpage", "pages");
    }
    function msg($msg, $gotourl = "javascript:history.go(-1);")
    {
        global $t;
        $t->set_file("message", "msg.html");
        $t->set_var(array("msg" => $msg, "gotourl" => $gotourl));
        $t->parse("out", "message");
        $t->p("out");
        exit;
    }
    function sure($msg, $ok, $cannel = "javascript:history.go(-1);")
    {
        global $t;
        $t->set_file("sure", "sure.html");
        $t->set_var(array("msg" => $msg, "ok" => $ok, "cannel" => $cannel));
        $t->parse("out", "sure");
        $t->p("out");
        exit;
    }
    function getcname($cid)
    {
        global $db_prefix;
        $res = query("select cname from ".$db_prefix."cblog where cid=\"$cid\"");
        if ($row = mysql_fetch_array($res))
        return $row['cname'];
        else
            msg("你输入了错误的URL,拒绝访问！", "javascript:history.go(-1);");
    }
    function getallpage($per, $str)
    {
        global $cid, $db_prefix;
        $cou = query("select count(bid) as sum from ".$db_prefix."blog $str ");
        $cou = mysql_fetch_array($cou);
        $p = ceil($cou['sum']/$per);
        return $p;
    }
    function getblogtitle($bid)
    {
        global $db_prefix;
        $res = query("select title from ".$db_prefix."blog where bid=\"$bid\"");
        if ($row = mysql_fetch_array($res))
        return $row['title'];
        else
            msg("你输入了错误的URL,拒绝访问！", "javascript:history.go(-1);");
    }
    connect($servername, $dbusername, $dbpass);
    selectdb($dbname);
?>