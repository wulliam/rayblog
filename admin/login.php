<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: login.php
    作  者: Ray
    说  明: 后台管理员登陆
    版  本: 1.0
    日  期: 2004-8-27
    ***********************************************************************/
    include"function.php";
    $t = new Template("HTML");
    if (@login($_POST['administratorname'], $_POST['administratorpass']) == 1)
    {
        msg("登录成功，请稍候...", "main.php");
    }
    else
        {
        pagelogin();
    }
?>


