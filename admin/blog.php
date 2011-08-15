<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: blog.php
    作  者: Ray
    说  明: 后台文章管理
    版  本: 1.5
    日  期: 2004-09-04
    ***********************************************************************/
    include"function.php";
    if (islogin() == 0)
    pagelogin();
    $t = new Template("HTML");
    if ($_GET['action'] == "add")
    {
        $t->set_file("blog_add", "blog_add.html");
        $t->set_block("blog_add", "bclass", "rowsclass");
        $t->set_var(array("h1" => "添加文章",
            "action" => "addblog"));
        $res = query("select * from ".$db_prefix."cblog");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("v" => $row['cid'],
                "cn" => $row['cname']));
            @$t->parse("rowsclass", "bclass", true);
        }
        while ($row = mysql_fetch_array($res));
        $t->parse("out", "blog_add");
        $t->p("out");
    }
    else if($_GET['action'] == "addblog")
    {
        if ($_POST['title'] == "" || $_POST['text'] == "")
        msg("文章标题或内容不能为空", "javascript:history.go(-1);");
        if ($_POST['select'] == "-1")
        msg("请返回选择文章所属分类", "javascript:history.go(-1);");
        $title = AddSlashes2($_POST['title']);
        $text = AddSlashes2($_POST['text']);
        query("insert into ".$db_prefix."blog (title,cid,text,date,view) values('$title','$_POST[select]','$text',now(),'0')");
        msg("文章添加成功", "blog.php?action=add");
    }
    else if($_GET['action'] == "edit")
    {
        $t->set_file("blog_edit", "blog_edit.html");
        $t->set_block("blog_edit", "bclist", "rowsclist");
        $t->set_block("blog_edit", "bbloglist", "rowsbloglist");
        $t->set_file("pages", "page.html");
        $t->set_block("pages", "bpagejump", "rowspj");
        $res = query("select cid,cname from ".$db_prefix."cblog");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("cname" => $row['cname'],
                "cid" => $row['cid']));
            @$t->parse("rowsclist", "bclist", true);
        }
        while ($row = mysql_fetch_array($res));
        if (empty($_GET['page']))
        $_GET['page'] = 1;
        $limt = @limit($_GET['page'], 20);
        if (!isset($_GET['cid']) || $_GET['cid'] == "-1")
        {
            $_GET['cid'] = "-1";
            $res = query("select bid,title,date from ".$db_prefix."blog order by date DESC limit $limt,20");
            printpage(getallpage(20, ""), $_GET['page'], "blog.php?action=edit&cid=$_GET[cid]&", 20);
        }
        else
            {
            $res = query("select bid,title,date from ".$db_prefix."blog where cid=\"$_GET[cid]\" order by date DESC limit $limt,20");
            printpage(getallpage(20, "where cid=\"$_GET[cid]\""), $_GET['page'], "blog.php?action=edit&cid=$_GET[cid]&", 20);
        }
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("title" => $row['title'],
                "date" => $row['date'],
                "bid" => $row['bid'],
                "fb" => tablecount($db_prefix."blogpl", "id", "where bid=\"$row[bid]\"")
            ));
            @$t->parse("rowsbloglist", "bbloglist", true);
        }
        while ($row = mysql_fetch_array($res));
        else
            msg("此分类下没有文章");
        $t->set_var("cname2", $_GET['cid'] == "-1"?"全部":getcname($_GET['cid']));
        $t->parse("out", "blog_edit");
        $t->p("out");
    }
    else if($_GET['action'] == "modify")
    {
        $t->set_file("blog_add", "blog_add.html");
        $t->set_block("blog_add", "bclass", "rowsclass");
        $res = query("select bid,title,text,cid from ".$db_prefix."blog where bid=\"$_GET[bid]\"");
        $row = mysql_fetch_array($res);
        $t->set_var(array("h1" => "编辑文章",
            "action" => "updateblog&amp;bid=$_GET[bid]",
            "title" => ubb($row['title']),
            "text" => $row['text']));
        $res = query("select * from ".$db_prefix."cblog");
        if ($row2 = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("v" => $row2['cid'],
                "cn" => $row2['cname'],
                "select[$row[cid]]" => "selected"));
            @$t->parse("rowsclass", "bclass", true);
        }
        while ($row2 = mysql_fetch_array($res));
        $t->parse("out", "blog_add");
        $t->p("out");
    }
    else if($_GET['action'] == "updateblog")
    {
        $_POST['title'] = AddSlashes2($_POST['title']);
        $_POST['text'] = AddSlashes2($_POST['text']);
        query("update ".$db_prefix."blog set title=\"$_POST[title]\", text=\"$_POST[text]\", cid=\"$_POST[select]\" where bid=\"$_GET[bid]\"");
        msg("文章修改成功");
    }
    else if($_GET['action'] == "del")
    {
        if (@$_GET['ok'] == 1)
        {
            query("delete from ".$db_prefix."blog where bid=$_GET[bid]");
            query("delete from ".$db_prefix."blogpl where bid=$_GET[bid]");
            msg("文章删除成功", "blog.php?action=edit");
        }
        else
            sure("所有关于此文章的评论都将被删除，确定删除吗？", "blog.php?action=del&bid=$_GET[bid]&ok=1");
    }
    else if($_GET['action'] == "deletemore")
    {
        $t->set_file("blog_del", "blog_del.html");
        $t->set_block("blog_del", "bclist", "rowsclist");
        $t->set_block("blog_del", "bbloglist", "rowsbloglist");
        $t->set_file("pages", "page.html");
        $t->set_block("pages", "bpagejump", "rowspj");
        $c = 0;
        $res = query("select cid,cname from ".$db_prefix."cblog");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("cname" => $row['cname'],
                "cid" => $row['cid']));
            @$t->parse("rowsclist", "bclist", true);
        }
        while ($row = mysql_fetch_array($res));
        if (empty($_GET['page']))
        $_GET['page'] = 1;
        $limt = @limit($_GET['page'], 20);
        if (!isset($_GET['cid']) || $_GET['cid'] == "-1")
        {
            $_GET['cid'] = "-1";
            $res = query("select bid,title,date from ".$db_prefix."blog order by date DESC limit $limt,20");
            printpage(getallpage(20, ""), $_GET['page'], "blog.php?action=edit&cid=$_GET[cid]&", 20);
        }
        else
            {
            $res = query("select bid,title,date from ".$db_prefix."blog where cid=\"$_GET[cid]\" order by date DESC limit $limt,20");
            printpage(getallpage(20, "where cid=\"$_GET[cid]\""), $_GET['page'], "blog.php?action=edit&cid=$_GET[cid]&", 20);
        }
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("title" => $row['title'],
                "date" => $row['date'],
                "bid" => $row['bid'],
                "fb" => tablecount($db_prefix."blogpl", "id", "where bid=\"$row[bid]\""),
                "c" => $c++ ));
            @$t->parse("rowsbloglist", "bbloglist", true);
        }
        while ($row = mysql_fetch_array($res));
        else
            msg("此分类下没有文章");
        $t->set_var("cname2", $_GET['cid'] == "-1"?"全部":getcname($_GET['cid']));
        $t->parse("out", "blog_del");
        $t->p("out");
    }
    else if($_GET['action'] == "dodelmore")
    {   if(!isset($_POST['delmore']))
            msg("未选择任何文章。");
        for($i = 0; $i <= $_GET['c']; $i++)
        {
            if (@$_POST['delmore'][$i] != "")
            {
                query("delete from ".$db_prefix."blog where bid=".$_POST['delmore'][$i]);
                query("delete from ".$db_prefix."blogpl where bid=".$_POST['delmore'][$i]);
            }

        }
        msg("文章批量删除成功", "blog.php?action=deletemore");
    }
?>

