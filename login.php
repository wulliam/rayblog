<?php
session_start();
if (($_SESSION["wife"] == "judith") && ($_SESSION["date"] == date("Y-m-d")))
{
	$isWife=TRUE;
	$skipKey="Judith";
	$_SESSION["isWife"]=TRUE;
	$_SESSION["skipKey"]="Judith";
	header("Location: http://wulliam.senofo.com/note/index.php"); 
	exit;
}
?>
<style>
<!--
body
	{
	font:11px Arial, Verdana, Helvetica, sans-serif;
	color:#222222;
	margin:12px;
	padding:0;
	vertical-align:top;
  	background:#FFFFFF;
	}
-->
</style>
<?php


	Session_Register(wife);
    Session_Register(date);
	$_SESSION["wife"] =$_POST["name"];
	$_SESSION["date"] =$_POST["pass"];
	echo "<br>post wife".$_POST["name"];
	echo "<br>post date".$_POST["pass"];
	echo "<br>wife".$_SESSION["wife"];
	echo "<br>date".$_SESSION["date"];


?>
<form method="post" ="<?php echo $PHP_SELF;?>"> 
<input type=text size=20 name=name>name</input><br>
<input type=text size=20  name=pass>passs</input><br>
<input type="submit" value="Submit"><br>
</form>
<?php
if (!session_is_registered('count')) {
   session_register('count');
   $count = 1;
} else {
   $count++;
}
?>

<p>
Hello visitor, you have seen this page <?php echo $count; ?> times.
</p>

<p>
To continue, <?php echo strip_tags(SID); ?>">click
here</a>.
</p>
<?php


/*
function test()
{
 if($_SESSION["isWife"] == TRUE)
 {
	 echo "isWife";
	 //break;
 }
}

for ($i=0; $i < 10; $i++)
{
	echo $i;
	if ($i == 5)
	{
		test();
	}
}

echo $_SESSION["testT"];
echo $_SESSION["testF"];
if($_SESSION["testT"])
{
	echo "testT-T";
}

if($_SESSION["testF"])
{
	echo "testF-F";
}
*/

//$testT=TRUE;
//$testF=FALSE;
//Session_Register(testT);
//Session_Register(testF);

?>