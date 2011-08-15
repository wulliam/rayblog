<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: link.php
    作  者: Ray
    说  明: 后台链接管理
    版  本: 1.0
    日  期: 2004-8-27
    ***********************************************************************/
    include"function.php";
    if (islogin() == 0)
    pagelogin();
    $t = new Template("HTML");
    if (!isset($_GET['action']))
    {
        $t->set_file("link", "link_add.html");
        $t->set_var(array("h1" => "添加链接",
            "action" => "add"));
        $t->parse("out", "link");
        $t->p("out");
    }
    else if($_GET['action'] == "add")
    {   $_POST['name']=AddSlashes2($_POST['name']);
        $_POST['url']=AddSlashes2($_POST['url']);
        $_POST['de']=AddSlashes2($_POST['de']);
        query("insert into ".$db_prefix."link (name,url,de,visible) values('$_POST[name]','$_POST[url]','$_POST[de]','$_POST[visible]')");
        msg("链接添加成功", "link.php?action=edit");
    }
    else if($_GET['action'] == "edit")
    {
        $t->set_file("link_list", "link_edit.html");
        $t->set_block("link_list", "blinklist", "rowslinklist");
        $res = query("select * from ".$db_prefix."link order by id");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("name" => $row['name'],
                "id" => $row['id'],
                "visible" => $row['visible'] == 1?"显示": "隐藏"));
            @$t->parse("rowslinklist", "blinklist", true);
        }
        while ($row = mysql_fetch_array($res));
        $t->parse("out", "link_list");
        $t->p("out");
    }
    else if($_GET['action'] == "modify")
    {
        $res = query("select * from ".$db_prefix."link where id=\"$_GET[id]\"");
        $row = mysql_fetch_array($res);
        $t->set_file("link_edit", "link_add.html");
        $t->set_var(array("h1" => "编辑链接",
            "action" => "update&amp;id=$_GET[id]",
            "name" => $row['name'],
            "url" => $row['url'],
            "de" => $row['de']));
        $t->parse("out", "link_edit");
        $t->p("out");
    }
    else if($_GET['action'] == "update")
    {
        $_POST['name']=AddSlashes2($_POST['name']);
        $_POST['url']=AddSlashes2($_POST['url']);
        $_POST['de']=AddSlashes2($_POST['de']);
        query("update ".$db_prefix."link set name=\"$_POST[name]\", url=\"$_POST[url]\", de=\"$_POST[de]\", visible=\"$_POST[visible]\" where id=$_GET[id]");
        msg("链接修改成功", "link.php?action=edit");
    }
    else if($_GET['action'] == "del")
    {
        if (@$_GET['ok'] == 1)
        {
            query("delete from ".$db_prefix."link where id=$_GET[id]");
            msg("链接删除成功", "link.php?action=edit");
        }
        else
            sure("确定删除吗？", "link.php?action=del&id=$_GET[id]&ok=1", "link.php?action=edit");
    }
?>

