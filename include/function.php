<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: function.php
    作  者: Ray
    说  明: 前台函数
    版  本: 1.0
    日  期: 2004-8-27
    ***********************************************************************/
    function addslashes2($str)
    {
        if (get_magic_quotes_gpc())
        ;
        else
            $str = addslashes($str);
        return $str;
    }
    function tablecount($table, $id, $where = "")
    {
        $res = query("select count($id) as sum from $table $where");
        $row = mysql_fetch_array($res);
        return $row['sum'];
    }
    function addcount($isvisited)
    {
        global $db_prefix;
        if ($isvisited == "TRUE")
        ;
        else
            {
            query("update ".$db_prefix."count set count=count+1 where id=1");
            setcookie("isvisited", "TRUE", time()+86400);
        }
    }
    function getcname($cid)
    {
        global $db_prefix;
        $res = query("select cname from ".$db_prefix."cblog where cid=\"$cid\"");
        if ($row = mysql_fetch_array($res))
        return $row['cname'];
        else
            message("你输入了错误的URL,拒绝访问！", "javascript:history.go(-1);");
    }
    function listc($cid)
    {
        global $t, $limt, $setting, $db_prefix;
        $cid = intval($cid);
        $res = query("select bid,title,date,view from ".$db_prefix."blog where cid=\"$cid\" order by date DESC limit $limt,$setting[listper]");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("blogtitle" => $row['title'],
                "datetime" => $row['date'],
                "bloger" => $setting['blogger'],
                "bid" => $row['bid'],
                "cname" => getcname($cid),
                "view" => $row['view'],
                "feedbackcount" => tablecount($db_prefix."blogpl", "id", "where bid=$row[bid]")
            )
            );
            @$t->parse("rowslist", "blist", true);
        }
        while ($row = mysql_fetch_array($res));
        else
            {
            $t->set_var(array(
            "cnum" => $cid,
                "cname" => getcname($cid)."(还没有日志，等你添加呢)" ));
            @$t->parse("rowslist", "blist", true);
        }
    }
    function getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    function addfeedback($feedbacktitle, $feedbackname, $feedbackmail, $feedbacktext, $bid, $remember)
    {
        global $db_prefix, $setting;
        if ($feedbacktitle == "" || $feedbackname == "" || $feedbacktext == "")
        message("没有填写完整", "javascript:history.go(-1);");
        else if(!eregi("^[0-9a-z_]{1,}@[0-9a-z\._]{2,}\$", $feedbackmail))
        message("错误的E-Mail地址", "javascript:history.go(-1);");
        else if(time()-$_COOKIE['time'] < $setting['addfeedbacktime'])
        message("为防止恶意攻击，发表评论间隔为$setting[addfeedbacktime]秒", "javascript:history.go(-1);");
        else
            {
            $cid = query("select cid from ".$db_prefix."blog where bid=$bid");
            $cid = mysql_fetch_array($cid);
            $feedbacktitle = addslashes2($feedbacktitle);
            $feedbackname = addslashes2($feedbackname);
            $feedbacktext = addslashes2($feedbacktext);
            query("insert into ".$db_prefix."blogpl (pl_email,pltitle,plname,pltext,pldate,bid,pl_cid) values ('$feedbackmail','$feedbacktitle','$feedbackname','$feedbacktext',now(),$bid,$cid[cid])");
            setcookie("time", time(), time()+31536000);
            if ($remember == 1)
            {
                setcookie("feedbackname", $feedbackname, time()+31536000);
                setcookie("feedbackmail", $feedbackmail, time()+31536000);
            }
            else
                {
                setcookie("feedbackname", $feedbackname, time());
                setcookie("feedbackmail", $feedbackmail, time());
            }
            message("发表评论成功", "index.php?action=show&bid=$bid#feedback");
        }
    }
    function message($msg, $gotourl)
    {
        global $t;
        $t->set_file("message", "message.html");
        $t->set_var(array("msg" => $msg, "gotourl" => $gotourl));
        $t->parse("out", "message");
        $t->p("out");
        exit;
    }
    function listgb()
    {
        global $t, $db_prefix, $limt, $setting;
        @$t->set_var(array("cgbname" => $_COOKIE['feedbackname'],
            "cgbmail" => $_COOKIE['feedbackmail'],
            "cgbsite" => $_COOKIE['gbsite'] ));
        $res = query("select * from ".$db_prefix."gb order by gbdate DESC limit $limt,$setting[gbper]");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("gbname" => $row['gbname'],
                "gbsite" => ubb($row['gbsite']),
                "gbdate" => $row['gbdate'],
                "gbtext" => ubb($row['gbtext']),
                "gbmail" => parseremail($row['gbmail'])
            ));
            if ($row['gbreply'] != "")
            $t->set_var("gbreply", "<font color=\"#0000FF\">.管理员回复：".ubb($row['gbreply'])."</font>");
            else
                $t->set_var("gbreply", ".管理员还未回复此条留言");
            @$t->parse("rowsgb", "bguestbook", true);
        }
        while ($row = mysql_fetch_array($res));
    }
    function addtogb($gbname, $gbmail, $gbsite, $gbtext, $remember)
    {
        global $db_prefix, $setting;
        if ($gbname == "" || $gbmail == "" || $gbtext == "")
        message("没有填写完整", "javascript:history.go(-1);");
        else if(!eregi("^[0-9a-z_]{1,}@[0-9a-z\._]{2,}\$", $gbmail))
        message("EMAIL地址不合法", "javascript:history.go(-1);");
        else if(time()-$_COOKIE['time'] < $setting['addtogbtime'])
        message("为防止恶意攻击，发表评论间隔为$setting[addtogbtime]秒", "javascript:history.go(-1);");
        else
            {
            $gbname = addslashes2($gbname);
            $gbmail = addslashes2($gbmail);
            $gbsite = addslashes2($gbsite);
            $gbtext = addslashes2($gbtext);
            query("insert into ".$db_prefix."gb (gbname,gbmail,gbtext,gbdate,gbsite) values('$gbname','$gbmail','$gbtext',now(),'$gbsite')");
            setcookie("time", time(), time()+60);
            if ($remember == 1)
            {
                setcookie("feedbackname", $gbname, time()+31536000);
                setcookie("feedbackmail", $gbmail, time()+31536000);
                $gbsite = str_replace("http://", "", $gbsite);
                setcookie("gbsite", $gbsite, time()+31536000);
            }
            else
                {
                setcookie("feedbackname", $gbname, time());
                setcookie("feedbackmail", $gbmail, time());
                setcookie("gbsite", $gbsite, time());
            }
            message("发表留言成功", "index.php?action=guestbook");
        }
    }
    function c_substr($str, $start = 0)
    {
        $ch = chr(127);
        $p = array("/[x81-xfe]([x81-xfe]|[x40-xfe])/", "/[x01-x77]/");
        $r = array("", "");
        if (func_num_args() > 2)
        $end = func_get_arg(2);
        else
            $end = strlen($str);
        if ($start < 0)
        $start  += $end;
        if ($start > 0)
        {
            $s = substr($str, 0, $start);
            if ($s[strlen($s)-1] > $ch)
            {
                $s = preg_replace($p, $r, $s);
                $start  += strlen($s);
            }
        }
        $s = substr($str, $start, $end);
        $end = strlen($s);
        if ($s[$end-1] > $ch)
        {
            $s = preg_replace($p, $r, $s);
            $end  += strlen($s);
        }
        return substr($str, $start, $end);
    }
    function ubb($str)
    {   $str = eregi_replace("<\?php","PHP代码：[code]<?php",$str);
        $str = eregi_replace("\?>","?>[/code]",$str);
        $str = eregi_replace("<%","ASP代码：[code]<%",$str);
        $str = eregi_replace("\%>","%>[/code]",$str);
        $str = htmlspecialchars($str);
        $str = nl2br($str);
        $str = eregi_replace("(http|ftp):\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?_%#&;\-]{2,}", "[url]\\0[/url]", $str);
        $str = eregi_replace("\[img\]\[url\]((http|ftp)[:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/url\]\[/img\]", "<img src=\"\\1\" border=\"1\" alt=\"\" />", $str);
        $str = eregi_replace("\[img right\]\[url\]((http|ftp)[:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/url\]\[/img\]", "<img src=\"\\1\" border=\"1\" align=\"right\" alt=\"\" />", $str);
        $str = eregi_replace("\[img left\]\[url\]((http|ftp)[:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/url\]\[/img\]", "<img src=\"\\1\" border=\"1\" align=\"left\" alt=\"\" />", $str);
        $str = eregi_replace("\[img\]([0-9a-zA-Z_\-\/]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/img\]", "<img src=\"\\1\" border=\"1\" alt=\"\" />", $str);
        $str = eregi_replace("\[img right\]([0-9a-zA-Z_\-\/]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/img\]", "<img src=\"\\1\" border=\"1\" align=\"right\" alt=\"\" />", $str);
        $str = eregi_replace("\[img left\]([0-9a-zA-Z_\-\/]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/img\]", "<img src=\"\\1\" border=\"1\" align=\"left\" alt=\"\" />", $str);
        $str = eregi_replace("\[url\]\[url\]", "[url]", $str);
        $str = eregi_replace("\[/url\]\[/url\]", "[/url]", $str);
        $str = eregi_replace("\[url\]((http|ftp)[:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&;_%#\-]{2,})\[/url\]", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $str);
        $str = eregi_replace("\[url=(<a href=\"http:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&;_%#\-]{2,}\" target=\"_blank\">)http:\/\/[0-9a-zA-Z_\-]{2,}\.[0-9a-zA-Z\/\.=?&;_%#\-]{2,}</a>\]([a-z0-9\x80-\xfe_\.\/]{1,})\[/url\]", "\\1\\2</a>", $str);
        $str = eregi_replace("\[B\]", "<strong>", $str);
        $str = eregi_replace("\[/B\]", "</strong>", $str);
        $str = eregi_replace("\[download=([0-9a-zA-Z_\-\/]{2,}\.[0-9a-zA-Z\/\.=?&_%#\-]{2,})\]([0-9a-zA-Z\/\.=?&_%#\-]{2,})\[/download\]", "<a href=\"\\1\" target=\"_blank\"><strong>点这里下载附件（\\2）</strong></a>", $str);
        $str = eregi_replace("\[description\]","",$str);
        $str = eregi_replace("\[code\]","<table class=\"code\"><tr><td>",$str);
        $str = eregi_replace("\[/code\]","</td></tr></table>",$str);
        return $str;
    }
    function parseremail($str)
    {
        $str = str_replace("@", " <b>AT</b> ", $str);
        $str = str_replace(".", " <b>DOT</b> ", $str);
        return $str;
    }
    function getallpage($per, $str)
    {
        global $cid, $db_prefix;
        $cou = query("select count(bid) as sum from ".$db_prefix."blog $str ");
        $cou = mysql_fetch_array($cou);
        $p = ceil($cou['sum']/$per);
        return $p;
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
            $strPerior = "<a href=\"index.php?".$v."page=$perior\">上一页</a>";
            $strNext = "";
        }
        if ($page < $allpage)
        {
            if ($page == 1)
            {
                $strNext = "<a href=\"index.php?".$v."page=$next\">下一页</a>";
                $strPerior = "";
            }
            else
                {
                $strPerior = "<a href=\"index.php?".$v."page=$perior\">上一页</a>";
                $strNext = "<a href=\"index.php?".$v."page=$next\">下一页</a>";
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
    function search($str)
    {
        global $t, $limt, $setting, $db_prefix;

        $res=query("select bid,title,date,view from ".$db_prefix."blog where title like '%$str%' or date like '%$str%' or text like '%$str%' order by date DESC limit $limt,$setting[listper]");
        $c=mysql_num_rows($res);
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("blogtitle" => $row['title'],
                "datetime" => $row['date'],
                "bloger" => $setting['blogger'],
                "bid" => $row['bid'],
                "cname" =>"以下日志包含关键字\"$str\"",
                "view" => $row['view'],
                "feedbackcount" => tablecount($db_prefix."blogpl", "id", "where bid=$row[bid]")
            )
            );
            @$t->parse("rowslist", "blist", true);
        }
        while ($row = mysql_fetch_array($res));
        else
            {
            $t->set_var(array(
                "cname" => "没有找到符合条件的日志" ));
            @$t->parse("rowslist", "blist", true);
        } return $c;
    }
    function rss2($blogname,$description,$blogger,$httphost,$self)
    {
        global $db_prefix;
        $bloglink2="http://".$httphost.$self;
        $bloglink=str_replace("index.php","",$bloglink2);
        header("content-type:text/xml");
        echo"<?xml version=\"1.0\" encoding=\"gb2312\" ?>
            <rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">
            <channel>
            <title>$blogname</title>
            <link>$bloglink</link>
            <description>$description</description>
            <language>zh-cn</language>";
        $sql = "select * from ".$db_prefix."blog order by date desc limit 0,15";
        $res = mysql_query($sql);
        if ($row = mysql_fetch_array($res))
            do
        {
            //$row['text'] = nl2br($row['text']);
            //$row['text'] = preg_replace("/((http|ftp):\/\/[[:alnum:][:punct:]]{2,}\.[[:alnum:]\-\.?&=-_\/]{2,})/i", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $row['text']);
            //$row['text']=ubb($row['text']);
            //$row['text'] = str_replace("&", "&amp;", $row['text']);
            $row['title'] = htmlspecialchars($row['title']);
            //$row['text'] = str_replace("<", "&lt;", $row['text']);
            @list($description,$text)=split("\[description\]",$row['text']);
            $description=ubb($description);
            $description = htmlspecialchars($description);
            if($text!="")
            {
             $description.="<br>……<br><a href=\"index.php?action=show&amp;bid=$row[bid]\">【阅读全文】</a>";
             $description = str_replace("<", "&lt;", $description);
            }
            list($date, $time) = split(" ", $row['date']);
            list($year, $mon, $day) = split("-", $date);
            list($hour, $min, $sec) = split(":", $time);
            $d = gmdate("D, d M Y H:i:s", mktime($hour, $min, $sec, $mon, $day, $year));
            $itemlink=$bloglink2."?action=show&amp;bid=".$row['bid'];
            $itemlink2=$itemlink."#feedback";
            echo"<item>
                <dc:creator>$blogger</dc:creator>
                <title>$row[title]</title>
                <link>$itemlink</link>
                <comments>$itemlink2</comments>
                <pubDate>$d GMT</pubDate>
                <description>$description</description>
                </item> ";
        }
        while ($row = mysql_fetch_array($res));
        echo"
            </channel>
            </rss> ";
        exit;
    }
?>