<?php
    /**************************************************************************
    Copyright (C), 2004, http://Rays.512j.com
    文件名: set.php
    作  者: Ray
    说  明: 后台常规设置
    版  本: 1.0
    日  期: 2004-8-27
    ***********************************************************************/
    include"function.php";
    if (islogin() == 0)
    pagelogin();
    if (@$_GET['action'] == "phpinfo")
    {
        phpinfo();
        exit;
    }
    $t = new Template("HTML");
    if (!isset($_GET['action']))
    {
        $t->set_file("set", "set.html");
        $t->set_block("set", "bsetting", "rowssetting");
        $res = query("select * from ".$db_prefix."setting");
        if ($row = mysql_fetch_array($res))
        {
            do
            {
                $t->set_var(array("title" => $row['title'],
                    "description" => $row['description'],
                    "name" => "setting[".$row['name']."]",
                    "value" => $row['value'] ));
                @$t->parse("rowssetting", "bsetting", true);
            }
            while ($row = mysql_fetch_array($res));
            $t->parse("out", "set");
            $t->p("out");
        }
    }
    if (@$_GET['action'] == "update")
    {
        foreach ($_POST['setting'] AS $name => $value)
        {
            query("UPDATE ".$db_prefix."setting SET value='".AddSlashes2(trim($value))."' WHERE name='".addslashes2($name)."'");
        }
        $fp = fopen("../include/settings.php", "w");
        $contents = "<?php\n\n\n";
        $res = query("SELECT * FROM ".$db_prefix."setting");
        while ($row = mysql_fetch_array($res))
        {
            $contents  .= "//$row[title] \n";
            $contents  .= "\$setting['$row[name]'] = \"".$row['value']."\";\n\n\n";
        }
        $contents  .= "?>";
        fwrite($fp, $contents);
        fclose($fp);
        msg("更新成功", "set.php");
    }
?>


