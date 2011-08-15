<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: main.php
    作  者: Ray
    说  明: 后台系统信息
    版  本: 1.5
    日  期: 2004-9-18
    ***********************************************************************/
    include"function.php";
    if (islogin() == 0)
    pagelogin();
    $t = new Template("HTML");
    if (@$_GET['action'] == "logout")
    {
        logout();
        exit;
    }
    $t->set_file("main", "main.html");
    $res = query("SHOW TABLE STATUS");
    while ($row = mysql_fetch_array($res))
    {
        @$datasize  += $row['Data_length'];
        @$indexsize  += $row['Index_length'];
    }
    $t->set_var(array("serveros" => PHP_OS,
        "phpver" => $_SERVER['SERVER_SOFTWARE'],
        "serveraddress" => $_SERVER["SERVER_ADDR"],
        "filesize" => ini_get("upload_max_filesize"),
        "formsize" => ini_get("post_max_size"),
        "globals" => get_cfg_var('register_globals')?"打开": "关闭",
        "blogcount" => tablecount($db_prefix."blog", "bid"),
        "feedbackcount" => tablecount($db_prefix."blogpl", "id"),
        "gbcount" => tablecount($db_prefix."gb", "id"),
        "ccount" => tablecount($db_prefix."cblog", "cid"),
        "dbsize" => get_real_size($datasize+$indexsize),
        "ver" => "1.5.3" ));
    $t->parse("out", "main");
    $t->p("out");
?>
