<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: class.php
    作  者: Ray
    说  明: 后台分类管理
    版  本: 1.0
    日  期: 2004-8-27
    ***********************************************************************/
    include"function.php";
    if (islogin() == 0)
    pagelogin();
    $t = new Template("HTML");
    if ($_GET['action'] == "add")
    {
        $t->set_file("class_add", "class_add.html");
        $t->set_var(array("h1" => "添加分类",
            "action" => "addclass",
            "o" => "200"));
        $t->parse("out", "class_add");
        $t->p("out");
    }
    else if($_GET['action'] == "addclass")
    {   $_POST['cname']=AddSlashes2($_POST['cname']);
        query("insert into ".$db_prefix."cblog (cname,o) values('$_POST[cname]','$_POST[o]')");
        msg("分类添加成功", "class.php?action=edit");
    }
    else if($_GET['action'] == "edit")
    {
        $t->set_file("class_edit", "class_edit.html");
        $t->set_block("class_edit", "bclasslist", "rowsclasslist");
        $res = query("select * from ".$db_prefix."cblog order by o");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("cname" => $row['cname'],
                "cid" => $row['cid'],
                "blogcount" => tablecount($db_prefix."blog", "bid", "where cid=$row[cid]")));
            @$t->parse("rowsclasslist", "bclasslist", true);
        }
        while ($row = mysql_fetch_array($res));
        $t->parse("out", "class_edit");
        $t->p("out");
    }
    else if($_GET['action'] == "modify")
    {
        $t->set_file("class_add", "class_add.html");
        $res = query("select * from ".$db_prefix."cblog where cid=$_GET[cid]");
        $row = mysql_fetch_array($res);
        $t->set_var(array("h1" => "编辑分类",
            "cname" => $row['cname'],
            "action" => "update&amp;cid=$_GET[cid]",
            "o" => $row['o']));
        $t->parse("out", "class_add");
        $t->p("out");
    }
    else if($_GET['action'] == "update")
    {   $_POST['cname']=AddSlashes2($_POST['cname']);
        query("update ".$db_prefix."cblog set cname=\"$_POST[cname]\", o=\"$_POST[o]\" where cid=$_GET[cid]");
        msg("分类修改成功", "class.php?action=edit");
    }
    else if($_GET['action'] == "del")
    {
        if (@$_GET['ok'] == 1)
        {
            query("delete from ".$db_prefix."blog where cid=$_GET[cid]");
            query("delete from ".$db_prefix."cblog where cid=$_GET[cid]");
            query("delete from ".$db_prefix."blogpl where pl_cid=$_GET[cid]");
            msg("分类删除成功", "class.php?action=edit");
        }
        else
            sure("此分类下的所有文章都将被删除，确定删除吗？", "class.php?action=del&cid=$_GET[cid]&ok=1", "class.php?action=edit");
    }
?>

