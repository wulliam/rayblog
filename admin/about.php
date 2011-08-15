<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: about.php
    作  者: Ray
    说  明: 后台管理员设置
    版  本: 1.0
    日  期: 2004-8-27
    ***********************************************************************/
    include"function.php";
    if (islogin() == 0)
    pagelogin();
    $t = new Template("HTML");
    if (!isset($_GET['action']))
    {
        $t->set_file("about", "about.html");
        $t->set_block("about", "babout", "rowsabout");
        $res = query("select * from ".$db_prefix."about order by id");
        if ($row = mysql_fetch_array($res))
        do
        {
            if ($row['type'] == "input")
            $t->set_var("type", "<input  type=\"text\" name=\"about[".$row['name']."]\" size=\"35\" maxlength=\"50\" value=\"".$row['value']."\" >");
            else
                $t->set_var("type", "<textarea type=\"text\" name=\"about[".$row['name']."]\" cols=\"45\" rows=\"7\" >".$row['value']."</textarea>");
            $t->set_var(array("title" => $row['title'],
                "description" => $row['description'] ));
            @$t->parse("rowsabout", "babout", true);
        }
        while ($row = mysql_fetch_array($res));
        $t->parse("out", "about");
        $t->p("out");
    }
    else if($_GET['action'] == "update")
    {
        foreach ($_POST['about'] AS $name => $value)
        {
            query("update ".$db_prefix."about set value='".AddSlashes2(trim($value))."' WHERE name='".addslashes2($name)."'");
        }
        msg("更新成功", "about.php");
    }
    else if($_GET['action'] == "add")
    {
        $t->set_file("about_add", "about_add.html");
        $t->set_var(array("h1" => "添加管理员选项",
            "action" => "addabout" ));
        $t->parse("out", "about_add");
        $t->p("out");
    }
    else if($_GET['action'] == "addabout")
    {
        query("insert into ".$db_prefix."about (title,name,value,description,type) values ('".addslashes2($_POST['title'])."', '".addslashes2($_POST['name'])."', '".addslashes2($_POST['value'])."', '".addslashes2($_POST['description'])."', '".addslashes2($_POST['type'])."')");
        msg("更新成功", "about.php");
    }
    else if($_GET['action'] == "edit")
    {
        $t->set_file("about_edit", "about_edit.html");
        $t->set_block("about_edit", "baboutlist", "rowsaboutlist");
        $res = query("select title,id from ".$db_prefix."about order by id");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("title" => $row['title'],
                "id" => $row['id']));
            @$t->parse("rowsaboutlist", "baboutlist", true);
        }
        while ($row = mysql_fetch_array($res));
        $t->parse("out", "about_edit");
        $t->p("out");
    }
    else if($_GET['action'] == "del")
    {
        $_GET['id'] = intval($_GET['id']);
        query("delete from ".$db_prefix."about where id=\"$_GET[id]\"");
        msg("删除成功", "about.php?action=edit");
    }
    else if($_GET['action'] == "modify")
    {
        $_GET['id'] = intval($_GET['id']);
        $res = query("select * from ".$db_prefix."about where id=\"$_GET[id]\"");
        $row = mysql_fetch_array($res);
        $t->set_file("about_add", "about_add.html");
        $t->set_var(array("h1" => "编辑管理员选项",
            "action" => "mod&amp;id=$_GET[id]",
            "title" => $row['title'],
            "name" => $row['name'],
            "value" => $row['value'],
            "description" => $row['description'] ));
        $t->parse("out", "about_add");
        $t->p("out");
    }
    else if($_GET['action'] == "mod")
    {
        $_GET['id'] = intval($_GET['id']);
        query("update ".$db_prefix."about set title=\"$_POST[title]\" , name=\"$_POST[name]\" , value=\"$_POST[value]\" , description=\"$_POST[description]\" , type=\"$_POST[type]\" where id=$_GET[id]");
        msg("更新成功", "about.php?action=edit");
    }
    else if($_GET['action'] == "modpw")
    {
        $t->set_file("about_modpw", "about_modpw.html");
        $t->parse("out", "about_modpw");
        $t->p("out");
    }
    else if($_GET['action'] == "domodpw")
    {
        $password = md5($_POST['oldpassword']);
        $res = query("select admin_pass from ".$db_prefix."admin where id=\"1\"");
        $row = mysql_fetch_array($res);
        if ($row['admin_pass'] == $password)
        if ($_POST['newpassword'] == $_POST['newpassword2'])
        {
            query("update ".$db_prefix."admin set admin_pass=\"".md5($_POST['newpassword'])."\" where id=\"1\"");
            msg("密码修改成功", "main.php");
        }
        else
            msg("确认密码与新密码不一致");
        else
            msg("输入了错误的旧密码");
    }
?>


