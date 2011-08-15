<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: news.php
    作  者: Ray
    说  明: 后台发布公告
    版  本: 1.5
    日  期: 2004-9-18
    ***********************************************************************/
    include"function.php";
    if (islogin() == 0)
    pagelogin();
    $t = new Template("HTML");
    if (@$_GET['action'] == "")
    {   $fd=fopen("../include/scrollup.js","r");
        $contents = fread($fd, filesize("../include/scrollup.js"));
        fclose($fd);
        $contents= str_replace("var marqueeContent=new Array();","",$contents);
        $contents = eregi_replace("marqueeContent\[[0-9]\]=","",$contents);
        $t->set_var("news",$contents);

        $t->set_file("news_edit","news_edit.html");
        $t->parse("out","news_edit");
        $t->p("out");

    }
    if (@$_GET['action'] == "edit")
    {
        $fd=fopen("../include/scrollup.js","w+");
        fwrite($fd,"var marqueeContent=new Array();\n");
       $text=split(";",stripslashes($_POST['news']));
        $i=0;
        while(isset($text[$i]))
        { if(trim($text[$i])!="")
          fwrite($fd,"marqueeContent[".$i."]=".trim($text[$i]).";\n");
          $i++;
        }

       fclose($fd);
       msg("发表公告成功");
    }




?>