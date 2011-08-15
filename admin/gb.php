<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: gb.php
    作  者: Ray
    说  明: 后台留言管理
    版  本: 1.5
    日  期: 2004-09-04
    ***********************************************************************/
    include"function.php";
    include"../include/settings.php";
    if (islogin() == 0)
    pagelogin();
    $t = new Template("HTML");
    if (!isset($_GET['action']))
    {
        $t->set_file("gb_edit", "gb_edit.html");
        $t->set_file("pages", "page.html");
        $t->set_block("pages", "bpagejump", "rowspj");
        $t->set_block("gb_edit", "bgblist", "rowsgblist");
        if (empty($_GET['page']))
        $_GET['page'] = 1;
        $limt = @limit($_GET['page'], $setting['gbper']);
        $res = query("select id,gbname,gbtext,gbdate,gbreply from ".$db_prefix."gb order by gbdate DESC limit $limt,$setting[gbper]");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("gbname" => $row['gbname'],
                "gbtext" => ubb($row['gbtext']),
                "gbdate" => $row['gbdate'],
                "id" => $row['id'],
                "gbreply" => $row['gbreply'] == ""?".还未回复": "管理员回复：".$row['gbreply']));
            @$t->parse("rowsgblist", "bgblist", true);
        }
        while ($row = mysql_fetch_array($res));
        @printpage(getallpage2($setting['gbper'], ""), $_GET['page'], "gb.php?", $setting['gbper']);
        $t->parse("out", "gb_edit");
        $t->p("out");
    }
    else if($_GET['action'] == "reply")
    {
        $t->set_file("gb_reply", "gb_reply.html");
        $t->set_var("id", "$_GET[id]");
        $t->parse("out", "gb_reply");
        $t->p("out");
    }
    else if($_GET['action'] == "savereply")
    {
        $reply = AddSlashes2($_POST['reply']);
        query("update ".$db_prefix."gb set gbreply=\"$reply\" where id=\"$_GET[id]\"");
        msg("回复成功", "gb.php");
    }
    else if($_GET['action'] == "del")
    {
        if (@$_GET['ok'] == 1)
        {
            query("delete from ".$db_prefix."gb where id=$_GET[id]");
            msg("留言删除成功", "gb.php");
        }
        else
            sure("确定删除吗？", "gb.php?action=del&id=$_GET[id]&ok=1", "gb.php");
    }
    else if($_GET['action'] == "deletemore")
    {
        $t->set_file("gb_del", "gb_del.html");
        $t->set_file("pages", "page.html");
        $t->set_block("pages", "bpagejump", "rowspj");
        $t->set_block("gb_del", "bgbdelmorelist", "rowsgbdelmorelist");
        $c = 0;
        if (empty($_GET['page']))
        $_GET['page'] = 1;
        $limt = @limit($_GET['page'], $setting['gbper']);
        $res = query("select id,gbname,gbtext,gbdate,gbreply from ".$db_prefix."gb order by gbdate DESC limit $limt,$setting[gbper]");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("gbname" => $row['gbname'],
                "gbtext" => ubb($row['gbtext']),
                "gbdate" => $row['gbdate'],
                "id" => $row['id'],
                "c" => $c++,
                "gbreply" => $row['gbreply'] == ""?".还未回复": "管理员回复：".$row['gbreply']));
            @$t->parse("rowsgbdelmorelist", "bgbdelmorelist", true);
        }
        while ($row = mysql_fetch_array($res));
        @printpage(getallpage2($setting['gbper'], ""), $_GET['page'], "action=deletemore&", $setting['gbper']);
        $t->parse("out", "gb_del");
        $t->p("out");
    }
    else if($_GET['action'] == "dodelmore")
    {   if(!isset($_POST['delmore']))
            msg("未选择任何留言。");
        for($i = 0; $i <= $_GET['c']; $i++)
        {
            if (@$_POST['delmore'][$i] != "")
            {
                query("delete from ".$db_prefix."gb where id=".$_POST['delmore'][$i]);
            }

        }
        msg("留言批量删除成功", "gb.php?action=deletemore");
    }
?>

