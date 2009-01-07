<?php

require_once ("func.php");
require_once ("db.php");
verify_if_user_is_logged_in();
page_header();

if(empty($_GET['main'])){
	if(empty($_POST['main'])){
        	$main = "enviroment_overview";
	} else {
        	$main = $_POST['main'];
	}
}else{
        $main = $_GET['main'];
}
if(empty($_GET['param1'])){
        $param1 = "";
}else{
        $param1 = $_GET['param1'];
}
if(empty($_GET['param2'])){
        $param2 = "";
}else{
        $param2 = $_GET['param2'];
}
if(empty($_GET['param3'])){
        $param3 = "";
}else{
        $param3 = $_GET['param3'];
}
if(empty($_GET['param4'])){
        $param4 = "";
}else{
        $param4 = $_GET['param4'];
}
if(empty($_GET['param5'])){
        $param5 = "";
}else{
        $param5 = $_GET['param5'];
}
if(empty($_GET['param6'])){
        $param6 = "";
}else{
        $param6 = $_GET['param6'];
}
if(empty($_GET['param7'])){
        $param7 = "";
}else{
        $param7 = $_GET['param7'];
}
if(empty($_GET['param8'])){
        $param8 = "";
}else{
        $param8 = $_GET['param8'];
}
if(empty($_GET['param9'])){
        $param9 = "";
}else{
        $param9 = $_GET['param9'];
}
if(empty($_GET['param10'])){
        $param10 = "";
}else{
        $param10 = $_GET['param10'];
}
if(empty($_GET['param11'])){
        $param11 = "";
}else{
        $param11 = $_GET['param11'];
}
if(empty($_GET['param12'])){
        $param12 = "";
}else{
        $param12 = $_GET['param12'];
}
if(empty($_GET['param13'])){
        $param13 = "";
}else{
        $param13 = $_GET['param13'];
}
if(empty($_GET['param14'])){
        $param14 = "";
}else{
        $param14 = $_GET['param14'];
}
if(empty($_GET['param15'])){
        $param15 = "";
}else{
        $param15 = $_GET['param15'];
}
if(empty($_GET['param16'])){
        $param16 = "";
}else{
        $param16 = $_GET['param16'];
}
if(empty($_GET['param17'])){
        $param17 = "";
}else{
        $param17 = $_GET['param17'];
}
if(empty($_GET['param18'])){
        $param18 = "";
}else{
        $param18 = $_GET['param18'];
}
if(empty($_GET['param19'])){
        $param19 = "";
}else{
        $param19 = $_GET['param19'];
}
if(empty($_GET['param20'])){
        $param20 = "";
}else{
        $param20 = $_GET['param20'];
}
if(empty($_GET['param21'])){
        $param21 = "";
}else{
        $param21 = $_GET['param21'];
}
if(empty($_GET['param22'])){
        $param22 = "";
}else{
        $param22 = $_GET['param22'];
}
if(empty($_GET['param23'])){
        $param23 = "";
}else{
        $param23 = $_GET['param23'];
}
if(empty($_GET['param24'])){
        $param24 = "";
}else{
        $param24 = $_GET['param24'];
}
if(empty($_GET['param25'])){
        $param25 = "";
}else{
        $param25 = $_GET['param25'];
}
if(empty($_GET['param26'])){
        $param26 = "";
}else{
        $param26 = $_GET['param26'];
}
if(empty($_GET['search'])){
        $search = "";
}else{
        $search = $_GET['search'];
	$param1 = $search;
	$main   = "system_overview";
}


echo "
<body>
";


echo '
<div class="PageWraper">

<div class="PageHeader">
	<div class="MainLogo">
		<p>Version 3.0<br><span class="NormalTxt">Codename<span class="ColoredTxt"> Phoenix</span></span></p>
	</div>
</div>

	<div class="PageTitle">
		<div class="PageTitleTxt">Andutteye Controlcenter</div>
		<div class="Imagery"></div>
	</div>
';

//page_menu();
new_menu();
$main($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12,$param13,$param14,$param15,$param16,$param17,$param18,$param19,$param20,$param21,$param22,$param23,$param24,$param25,$param26);

echo '
<script type="text/javascript" src="js/bsn.AutoSuggest_c_2.0.js"></script>
<script type="text/javascript">
        var options = {
                script:"search.php?",
                varname:"search",
                json:true,
                callback: function (obj) { document.getElementById("seqnr").value = obj.id; }
        };
        var as_json = new AutoSuggest("search", options);
</script>

';

echo '
<div class="PageFooter">
	<div class="ClearFloat"></div>
	<p style="float:left; text-align:left; margin-left:25px;">
		Andutteye - Copyright &copy;2008 Thundera AB, all rights reserved<br>
		published under the GPL License
	</p>
	<p style="float:right; text-align:right; margin-right:25px;">
		For more information visit us at<br>
		<a href="http://www.thundera.se">www.thundera.se</a>
	</p>
	<div class="ClearFloat"></div>
</div>
';



echo '
</div>
';


echo '
</body>
</html>
';
?>
