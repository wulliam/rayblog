<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: feedback.php
    作  者: Ray
    说  明: 后台评论管理
    版  本: 1.0
    日  期: 2004-8-27
    ***********************************************************************/
    include"function.php";
    if (islogin() == 0)
    pagelogin();
    $t = new Template("HTML");
    if (!isset($_GET['action']))
    {
        $t->set_file("feedback", "feedback.html");
        $t->set_block("feedback", "bfblist", "rowsfblist");
        $res = query("select id,pltext,plname,pl_email from ".$db_prefix."blogpl where pl_id=\"$_GET[bid]\"");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("pltext" => ubb($row['pltext']),
                "plname" => $row['plname'],
                "pl_email" => $row['pl_email'],
                "id" => $row['id'],
                "title" => ubb(getblogtitle($_GET['bid']))));
            @$t->parse("rowsfblist", "bfblist", true);
        }
        while ($row = mysql_fetch_array($res));
        else
            msg("这篇文章暂时还没有评论");
        $t->parse("out", "feedback");
        $t->p("out");
    }
    else if($_GET['action'] == "del")
    {
        if (@$_GET['ok'] == 1)
        {
            query("delete from ".$db_prefix."blogpl where id=$_GET[id]");
            msg("评论删除成功", "blog.php?action=edit");
        }
        else
            sure("确定删除吗？", "feedback.php?action=del&id=$_GET[id]&ok=1");
    }
?>


