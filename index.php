<?php
if($_SESSION["isWife"] == TRUE)
{
  $skipKey = $_SESSION["skipKey"];
} else
{  
  $skipKey = "Judith";
  $_SESSION["skipKey"] = $skipKey;
}
/*
if ($_SESSION["isWife"] == TRUE)
{
  echo "true";
} else
{
  echo "fale";
}
*/
?>
<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: index.php
    作  者: Ray
    说  明: 前台首页
    版  本: 1.5.2
    日  期: 2004-10-20
    ***********************************************************************/
       file_exists("install.php")?die("请先用install.php安装，然后将install.php删除再访问。"):null;
    include"class/template.php";
    include"include/settings.php";
    include"include/function.php";
    include"include/mysql.php";
    include"include/dbconfig.php";
    connect($servername, $dbusername, $dbpass);
    selectdb($dbname);
    $time_start = getmicrotime();
    $t = new Template("templates/".$setting['templatename']);
    $t->set_file("index", "index.html");
    $t->set_file("show", "show.html");
    $t->set_file("pages", "page.html");
    @$t->set_block("index", "b", "rowsfenlei");
    @$t->set_block("index", "blink", "rowslink");
    @$t->set_block("index", "bnewblog", "rowsnewblog");
    @$t->set_block("index", "bnewfeedback", "rowsnewfeedback");
    @$t->set_block("show", "bshow", "rowsshow");
    @$t->set_block("pages", "bpagejump", "rowspj");
    @$_GET['page'] = intval($_GET['page']);
    //------------------------------------------------------------------
    //取得分类列表
    $res = query("select * from ".$db_prefix."cblog order by o");
    if ($row = mysql_fetch_array($res))
    do
    {
        //  if ("true" == filter($row['cname']) ) continue;
        //  filter show for judith
        if ($row['cname'] == $_SESSION["skipKey"])
        {
          if($_SESSION["isWife"])
          {

          } else
          {
            continue;
          }
        }
        $t->set_var(array("fenlei" => $row['cname'],
            "cid" => $row['cid'],
            "c" => tablecount($db_prefix."blog", "bid", "where cid=$row[cid]")
        )
        );
        @$t->parse("rowsfenlei", "b", true);
    }
    while ($row = mysql_fetch_array($res));
    //----------------------------------------------------------------
    //取得链接列表
    $res = query("select * from ".$db_prefix."link where visible=1");
    if ($row = mysql_fetch_array($res))
    do
    {
        $t->set_var(array("link" => $row['name'],
            "url" => $row['url'],
            "de" => $row['de'] )
        );
        @$t->parse("rowslink", "blink", true);
    }
    while ($row = mysql_fetch_array($res));
    $res = query("select title,bid from ".$db_prefix."blog order by date DESC limit 0,15");
    if ($row = mysql_fetch_array($res))
    do
    {
        @$t->set_var(array("newblog" => htmlspecialchars(c_substr($row['title'], 0, 12)."..."),
            "newblogbid" => $row['bid'],
            "blank" => htmlspecialchars($row['title'])
        )
        );
        @$t->parse("rowsnewblog", "bnewblog", true);
    }
    while ($row = mysql_fetch_array($res));
    //-----------------------------------------------------------------
    //取得最新评论列表
    $res = query("select cid, pltext,bid from ".$db_prefix."blogpl order by pldate DESC limit 0,10");
    if ($row = mysql_fetch_array($res))
    do
    {   
        // filter show for judith cid not cid
      $resType = query("select cname from ".$db_prefix."cblog where cid =".$row['cid']);
      if ($rowType = mysql_fetch_array($resType))
      {

        if ($rowType['cname'] == $_SESSION["skipKey"])
        {       

          if($_SESSION["isWife"])
          {

                                        } else
          {
                                          continue;
                                        }
        }
      }
        @$t->set_var(array("newfeedback" => htmlspecialchars(c_substr($row['pltext'], 0, 12)."..."),
            "bid" => $row['bid'],
            "blank2" => htmlspecialchars($row['pltext'])
        )
        );
        @$t->parse("rowsnewfeedback", "bnewfeedback", true);
    }
    while ($row = mysql_fetch_array($res));
    //-----------------------------------------------------------------
    $blogcount = tablecount($db_prefix."blog", "bid");
    $commentcount = tablecount($db_prefix."blogpl", "id");
    $res = query("select count from ".$db_prefix."count where id=1");
    $vcount = mysql_fetch_array($res);
    //-----------------------------------------------------------------
    if (@$_GET['action'] == "list")
    {
        $t->set_file("list", "list.html");
        @$t->set_block("list", "blist", "rowslist");
        if (empty($_GET['page']))
        $_GET['page'] = 1;
        $limt = @limit($_GET['page'], $setting['listper']);
        listc($_GET['cid']);
        $t->parse("putblog", "list");
        @printpage(getallpage($setting['listper'], "where cid=".intval($_GET[cid])), $_GET['page'], "action=list&cid=$_GET[cid]&", $setting['listper']);
    }
    else if(@$_GET['action'] == "")
    {
        @addcount($_COOKIE['isvisited']);
        if (empty($_GET['page']))
        $_GET['page'] = 1;
        $limt = @limit($_GET['page'], $setting['indexper']);
        @printpage(getallpage($setting['indexper'], ""), $_GET['page'], "", $setting['indexper']);
        $res = query("select * from ".$db_prefix."blog order by date DESC limit $limt,$setting[indexper]");    
        $d2 = "";
        if ($row = mysql_fetch_array($res))
        do
        {  
      // filter show for judith
      $resType = query("select cname from ".$db_prefix."cblog where cid =".$row['cid']);
      if ($rowType = mysql_fetch_array($resType))
      {
        if ($rowType['cname'] == $_SESSION["skipKey"])
        {
          if($_SESSION["isWife"])
          {

                                        } else
          {
                                          continue;
                                        }
        }
      }
            list($date, $time) = split(" ", $row['date']);
            list($year, $mon, $day) = split("-", $date);
            $d ="<img src=\"templates/t2/img/date_icon_blue.gif\" width=\"10\" height=\"10\" alt=\"\">&nbsp;". date("D,M d,Y", mktime(0, 0, 0, $mon, $day, $year));
            @list($description,$text)=split("\[description\]",$row['text']);
            if($text!="")
            {$row['text']=$description;
             $readall="<br>……<br><a href=\"index.php?action=show&amp;bid=$row[bid]\">【阅读全文】</a>";
            }
            else
             $readall="";
            $t->set_var(array("blogtitle" => htmlspecialchars($row['title']),
                "datetime" => $row['date'],
                "date" => $d == $d2?"": $d,
                "time" => $time,
                "bloger" => $setting['blogger'],
                "bid" => $row['bid'],
                "cid2"=>$row['cid'],
                "cname" => getcname($row['cid']),
                "view" => $row['view'],
                "blogtext" => ubb($row['text']).$readall,
                "feedbackcount" => tablecount($db_prefix."blogpl", "id", "where bid=$row[bid]")
            )
            );
            @$t->parse("rowsshow", "bshow", true);
            $d2 = $d;
        }
        while ($row = mysql_fetch_array($res));
        $t->parse("putblog", "show");
    }
    else if(@$_GET['action'] == "show")
    {
        do
        {
        $t->set_file("showblog", "showblog.html");
        @$t->set_block("showblog", "bshowfeedback", "rowssfb");
        $_GET['bid'] = intval($_GET['bid']);
        $res = query("select * from ".$db_prefix."blog where bid=\"$_GET[bid]\"");
        if ($row = mysql_fetch_array($res))
        {
            // filter show for judith
            $resType = query("select cname from ".$db_prefix."cblog where cid =".$row['cid']);
      if ($rowType = mysql_fetch_array($resType))
      {
        if ($rowType['cname'] == $_SESSION["skipKey"])
        {
          if($_SESSION["isWife"])
          {

           } else
          {
                                          continue;
                                        }
        }
      }
            list($date, $time) = split(" ", $row['date']);
            list($year, $mon, $day) = split("-", $date);
            $d ="<img src=\"templates/t2/img/date_icon_blue.gif\" width=\"10\" height=\"10\">&nbsp;". date("D,M d,Y", mktime(0, 0, 0, $mon, $day, $year));

            @$t->set_var(array("blogtitle" => htmlspecialchars($row['title']),
                "blogtext" => ubb($row['text']),
                "bloger" => $setting['blogger'],
                "datetime" => $time,
                "date"=>$d,
                "cname" => getcname($row['cid']),
                "view" => $row['view'],
                "cid2"=>$row['cid'],
                "feedbackcount" => tablecount($db_prefix."blogpl", "id", "where bid=$_GET[bid]"),
                "bid" => $row['bid'],
                "feedbackname" => $_COOKIE['feedbackname'],
                "feedbackmail" => $_COOKIE['feedbackmail'] )
            );
            query("update ".$db_prefix."blog set view=$row[view]+1 where bid=\"$_GET[bid]\"");
            $res = query("select * from ".$db_prefix."blogpl where bid=$_GET[bid]");
            if ($row = mysql_fetch_array($res))
            do
            {
                $t->set_var(array("blogfeedbacktitle" => htmlspecialchars($row['pltitle']),
                    "blogfeedbacktext" => ubb($row['pltext']),
                    "blogfeedbacktime" => $row['pldate'],
                    "blogfeedbacker" => $row['plname'] )
                );
                @$t->parse("rowssfb", "bshowfeedback", true);
            }
            while ($row = mysql_fetch_array($res));
            $t->parse("putblog", "showblog");
        }
        else
            message("你输入了错误的URL,拒绝访问！", "javascript:history.go(-1);");
        } while(0);
    }
    else if($_GET['action'] == "addfeedback")
    {
        @addfeedback($_POST['feedbacktitle'], $_POST['feedbackname'], $_POST['feedbackmail'], $_POST['feedbacktext'], $_GET['bid'], $_POST['remember']);
    }
    else if($_GET['action'] == "rss2")
    {
        rss2($setting['blogname'],$setting['description'],$setting['blogger'],$_SERVER["HTTP_HOST"],$_SERVER["PHP_SELF"]);
    }
    else if($_GET['action'] == "guestbook")
    {
        $t->set_file("guestbook", "guestbook.html");
        @$t->set_block("guestbook", "bguestbook", "rowsgb");
        if (empty($_GET['page']))
        $_GET['page'] = 1;
        $limt = @limit($_GET['page'], $setting['gbper']);
        listgb();
        @printpage(getallpage2($setting['gbper'], ""), $_GET['page'], "action=guestbook&", $setting['gbper']);
        $t->parse("putblog", "guestbook");
    }/**
    else if($_GET['action'] == "addtogb")
    {
        @addtogb($_POST['gbname'], $_POST['gbmail'], $_POST['gbsite'], $_POST['gbtext'], $_POST['remember']);
    }**/
    else if($_GET['action'] == "about")
    {
        $t->set_file("about", "about.html");
        $t->set_block("about", "babout", "rowsabout");
        $res = query("select title,value from ".$db_prefix."about order by id");
        if ($row = mysql_fetch_array($res))
        do
        {
            $t->set_var(array("atitle" => $row['title'],
                "value" => str_replace("@", "<b> AT </b>", ubb($row['value']))
            ));
            @$t->parse("rowsabout", "babout", true);
        }
        while ($row = mysql_fetch_array($res));
        $t->parse("putblog", "about");
    }
    else if($_GET['action'] == "search")
    {    $t->set_file("list","list.html");
         @$t->set_block("list", "blist", "rowslist");
        if(isset($_POST['str']))
        $str=addslashes2($_POST['str']);
        else
        $str=addslashes2($_GET['str']);
        if (empty($_GET['page']))
        $_GET['page'] = 1;
        $limt = @limit($_GET['page'], $setting['listper']);
        search($str);
        $t->parse("putblog", "list");
        @printpage(getallpage($setting['listper'],"where title like '%$str%' or date like '%$str%' or text like '%$str%'"),$_GET['page'], "action=search&str=$str&", $setting['listper']);
    }
    else
        {
        message("你输入了错误的URL,拒绝访问！", "javascript:history.go(-1);");
    }
    $time_end = getmicrotime();
    $time = $time_end - $time_start;
    $t->set_var(array("title" => $setting['title'],
        "blogname" => $setting['blogname'],
        "blogcount" => $blogcount,
        "commentcount" => $commentcount,
        "description" => $setting['description'],
        "putmicrotime" => substr($time, 0, 8),
        "ver" => "1.5.3",
        "visitedcount" => $vcount['count'] )
    );
    $t->parse("out", "index");
    $t->p("out");
    echo "<font color=\"#CCCCCC\">".$_SESSION["isWife"]."</font>";
	echo "<font color=\"#CCCCCC\">".$_SESSION["skipKey"]."</font>";
?>