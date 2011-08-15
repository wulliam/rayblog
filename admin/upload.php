<?php
 include"function.php";
    if (islogin() == 0)
    pagelogin();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>无标题文档</title>
<link href="HTML/style.css" rel="stylesheet" type="text/css">
</head>
<script language="javascript">
<!--
function addubb(ubb) {
if (ubb!=""){
parent.document.form1.text.value += ubb+" ";
parent.document.form1.text.focus();
}
}
//-->
</script>
<body bgcolor="#f5f5f5" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
if(isset($_FILES['picname']['tmp_name'])&&$_FILES['picname']['tmp_name']!="")
{
 list($n,$e)=split("\.",$_FILES['picname']['name']);
 if($e=="jpg"||$e=="jpeg"||$e=="gif"||$e=="png")
   {$filename="u".time();
     $folder="upload/img/";
     $ubbcode[0]="[IMG]";
	 $ubbcode[1]="[/IMG]";
   }
   else
   {$filename=$n;
   $folder="upload/software/";
     $ubbcode[0]="[download=";
	 $ubbcode[1]="]".$n.".".$e."[/download]";
   }
 $R=copy($_FILES['picname']['tmp_name'],"../".$folder.$filename.".".$e);

?><table width="98%" height="20" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="bodytext">已成功上传附件，点<a href='javascript:addubb("<?php echo $ubbcode['0'].$folder.$filename.".".$e.$ubbcode['1']; ?>")'>这里</a>添加到文章，或点<a href="upload.php">这里</a>继续上传</td>
  </tr>
</table><?php

//echo "已成功上传图片，点<a href='javascript:addubb(\"[IMG]$filename.jpg[/IMG]\")'>这里</a>添加到文章，或点<a href=\"upload.php\">这里</a>继续上传";
unlink($_FILES['picname']['tmp_name']);}
else
{
?>



<table width="98%" height="20" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><form action="upload.php" method="post" enctype="multipart/form-data" name="form2">
        <input name="picname" type="file" size="30" maxlength="500">
        <input type="submit" name="Submit" value="上传">
      </form></td>
  </tr>
</table>
<?Php
}
?>
</body>
</html>
