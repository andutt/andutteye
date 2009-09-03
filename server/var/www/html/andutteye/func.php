<?php
//
// Andutteye webinterface functions.
//
// $Id$
//
ob_start();

function page_header() {

require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if(!$authNamespace->andutteye_theme) {
	$authNamespace->andutteye_theme = "Phoenix";
}

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

	<head>
        	<title>Andutteye controlcenter</title>
        	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
        	<meta name="description" content="Andutteye is a powerful opensource systemmanagement ecosystem." xml:lang="en" />
        	<meta name="keywords" content="Monitoring, managment, syslog, changevents" />
        	<link rel="stylesheet" href="themes/' . $authNamespace->andutteye_theme . '/theme.css" type="text/css" media="screen" />

        	<script type="text/javascript" src="js/mootools-dropdownmenu.js"></script>
        	<script type="text/javascript" src="js/dropdownmenu.js"></script>
        	<script type="text/javascript" src="js/mootools.v1.2.js"></script>
        	<script type="text/javascript" src="js/tip.js"></script>
        	<script type="text/javascript" src="js/accordion.js"></script>

        	</style>
        	<script type="text/javascript">
                	new UvumiDropdown("dropdown-demo");
        	</script>
	</head>
';

// End of subfunction
}
function new_menu() {

verify_if_user_is_logged_in();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

// start Search Box
echo '
        <div class="SearchBox">
                <div>
                        <form method="get" action="">
                                <h3>SEARCH</h3>
                                <label><input tabindex="1" class="med" type="text" name="search" id="search" size="20" maxlength="255" value=""></label>
                        </form>
                </div>
        </div>
';
// end Search Box

echo '<div id="MainMenu">
		<ul id="dropdown-demo" class="dropdown">
			<li>
				<a href="index.php?main=enviroment_overview">Home</a>
			</li>
			<li>
				<a>Domains</a>
				<ul>';

				$sql = $db->query("select domain_name,domain_description from andutteye_domains order by domain_name asc");
                                while ($row = $sql->fetch()) {
                                        $domain_name = $row['domain_name'];
                                        $domain_description = $row['domain_description'];


                                        if(verify_role_object_permission($domain_name,'domain',1,'0','0')) {
						echo '<li>
						<a href="index.php?main=domain_overview&param1=' . $domain_name . '"/>
						<img src="themes/' . $authNamespace->andutteye_theme . '/domains_1.png" alt="" title="" /> ' . $domain_name . '</a>';
						echo '<ul>';
				
					$subsql = $db->query("select group_name,group_description from andutteye_groups where domain_name = '$domain_name' order by group_name asc");
                                		while ($row = $subsql->fetch()) {
                                        		$group_name = $row['group_name'];
                                        		$group_description = $row['group_description'];

                                        		if(verify_role_object_permission($group_name,'group',1,'0','0')) {
                                                		echo '<li><a href="index.php?main=group_overview&param1=' .$domain_name. '&param2=' . $group_name . '"/>
								<img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" /> ' . $group_name . '</a>';
								echo '<ul>';
					
								$ssubsql = $db->query("select system_name from andutteye_systems where domain_name = '$domain_name' and group_name = '$group_name' order by system_name asc");
                                				while ($row = $ssubsql->fetch()) {
                                        				$system_name = $row['system_name'];

                                        				if(verify_role_object_permission($system_name,'system',1,'0','0')) {
                                                				echo '<li><a href="index.php?main=system_overview&param1='.$system_name.'&param2='.$domain_name.'&param3='.$group_name.'"/>
										<img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" /> ' . $system_name . '</a>';
										echo '
										<ul>

                                                                                        <li><a href="index.php?main=monitoring_front&param1=' . $system_name . '"><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Monitoring</a></li>';

                                						 if(verify_role_object_permission($system_name,'system',2,'0','0')) {

                                        						echo '
                                                        					<li><a href="index.php?main=system_specification&param1=' .$system_name. '"><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Pkgmanagement</a></li>
                                                        					<li><a href="index.php?main=system_files&param1=' .$system_name. '"><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Filemanagement</a></li>
                                                        					<li><a href="index.php?main=system_configuration&param1='.$system_name.'"><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Configuration</a></li>';
                                						}

                                        						  echo '<li><a href="index.php?main=show_statistics&param1='.$system_name.'"><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Statistics</a></li>
                                                        				        <li><a href="index.php?main=show_software_profile&param1='.$system_name.'"><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Softwareinventory</a></li>
                                                        					<li><a href="index.php?main=show_system_snapshot&param1='.$system_name.'"><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Systemsnapshots</a></li>
                                                        					<li><a href="index.php?main=show_server_transactions&param1='.$system_name.'"><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Servertranses</a></li>
                                                        					<li><a href="index.php?main=change_events_database"><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Changeeventsdb</a></li>
                                                        					<li><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> Syslogdb</li>

										</ul>';

						 		     		echo '</li>';
									}
								}
					    			echo '</ul>';

						 echo '</li>';
							}
						}
					    echo '</ul>';
                                        }
				 echo '</li>';
                                }


// End of domain menu items.
echo '</ul>';
echo '
			<li>
				<a href="#sync">Actions</a>
				<ul>
					<li><a href="index.php?main=user_settings"> 
					   <img src="themes/' . $authNamespace->andutteye_theme . '/settings.png" alt="" title="" /> 
					Settings</a></li>
                			<li><a href="index.php?main=upload_documentation"> 
					   <img src="themes/' . $authNamespace->andutteye_theme . '/upload_1.png" alt="" title="" /> 
					Upload</a></li>
                			<li><a href="index.php?main=link_documentation"> 
					   <img src="themes/' . $authNamespace->andutteye_theme . '/link_1.png" alt="" title="" /> 
					Link</a></li>
                			<li><a href="index.php?main=change_events_database"> 
					   <img src="themes/' . $authNamespace->andutteye_theme . '/changeevent_1.png" alt="" title="" /> 
					Changeeventsdb</a></li>
				</ul>
			</li>
			<li>
				<a href="#users">Admin</a>
				<ul>
					<li>
				           <a href="index.php?main=create_domain">
					   <img src="themes/' . $authNamespace->andutteye_theme . '/domains_1.png" alt="" title="" /> Domain admin</a>
					</li>
					<li>
					   <a href="index.php?main=create_group">
					   <img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" /> Group admin</a>
					</li>
					<li>
					   <a href="index.php?main=create_system">
                                           <img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" /> System admin</a>
					</li>
					<li>
					   <a href="index.php?main=create_role">
                                           <img src="themes/' . $authNamespace->andutteye_theme . '/role_1.png" alt="" title="" /> Role admin</a>
					</li>
					<li>
					   <a href="index.php?main=create_user">
					   <img src="themes/' . $authNamespace->andutteye_theme . '/user_1.png" alt="" title="" /> User admin</a>
					</li>
					<li>
					   <a href="index.php?main=fileadmin">
					   <img src="themes/' . $authNamespace->andutteye_theme . '/file_1.png" alt="" title="" /> File admin</a>
					</li>
					<li>
					   <a href="">
					   <img src="themes/' . $authNamespace->andutteye_theme . '/package_1.png" alt="" title="" /> Package admin</a>
					</li>
					<li>
					   <a href="index.php?main=create_front">
                                           <img src="themes/' . $authNamespace->andutteye_theme . '/front_1.png" alt="" title="" /> Front admin</a>
					</li>
				</ul>
			</li>
			<li>
				<a href="logout.php">Logout</a>
			</li>
		</ul>
	</div>';

}

function verify_if_user_have_admin_prevs() {

require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if(!$authNamespace->andutteye_admin) {
	header("Location:login.php?status=No%20admin%20privileges");
	exit;
}

// End of subfunction
}

function verify_role_object_permission($param1,$param2,$param3,$param4,$param5) {
//
//param1 roleobject, the object being verified.
//param2 objecttype, domain, group or system.
//param3 permission asking for, 1 for read, 2 for read-write, 3 for read-write-delete
//param4 domain_name (for management files)
//param5 distribution (for management files)

require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param4 && $param4 != 0) {
	$sql = $db->query("select * from andutteye_rolepermissions where rolename = '$authNamespace->andutteye_role' and roleobject = '$param1' and objecttype = '$param2' and domain_name = '$param4' and distribution = '$param5'");
	$res = $sql->fetchObject();
} else {
	$sql = $db->query("select * from andutteye_rolepermissions where rolename = '$authNamespace->andutteye_role' and roleobject = '$param1' and objecttype = '$param2'");
	$res = $sql->fetchObject();
}

if($param3 <= $res->role_permission) {
        //print "Access granted asked permission:$param3 is lower or equal with set permission for role:$res->role_permission\n";
	return(1);

	if($param3 > $res->role_permission) {
        	//print "Access NOT granted, asked permission:$param3 is lower or equal with set permission for role:$res->role_permission\n";
		return(0);
	} else {
        	//print "Invalid role permission question found, asked permission:$param3 set permission for role:$res->role_permission\n";
		return(0);
	}
} else {
       	//print "Access NOT granted, asked permission:$param3 for $param1 type $param2 role($authNamespace->andutteye_role) has no permission set for this roleobject($res->role_permission).\n";
	return(0);
}

// End of subfunction
}

function verify_if_user_is_logged_in() {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';

$authNamespace = new Zend_Session_Namespace('Zend_Auth');
if(!$authNamespace->andutteye_username) {
	header("Location:login.php?status=NO_USERNAME_SPECIFIED");
	exit;
}
if(!$authNamespace->andutteye_password) {
	header("Location:login.php?status=NO_PASSWORD_SPECIFIED.");
	exit;
}
$authAdapter = new Zend_Auth_Adapter_DbTable($db, 'andutteye_users', 'andutteye_username', 'andutteye_password');

$authAdapter->setIdentity("$authNamespace->andutteye_username")->setCredential("$authNamespace->andutteye_password");

$result = $authAdapter->authenticate();

if (!$result->isValid()) {
	header("Location:login.php?status=USERNAME_NOT_CORRECTLY_AUTHENTICATED.");
	exit;
}

// End of subfunction
}


function user_settings($param1,$param2,$param3) {

verify_if_user_is_logged_in();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');


if($param1 == "changepassword") {
	$password  = sha1($password_slt . $param2);
	$sql = "update andutteye_users set andutteye_password = '$password' where andutteye_username = '$authNamespace->andutteye_username'";
	$db->query($sql);
}
elseif($param1 == "changetheme") {
	$sql = "update andutteye_users set andutteye_theme = '$param2' where andutteye_username = '$authNamespace->andutteye_username'";
	$db->query($sql);

} else {
	//Nothing
}

$sql = $db->query("select * from andutteye_users where andutteye_username = '$authNamespace->andutteye_username'");
$res = $sql->fetchObject();

echo '
<div id="content">
	<div class="section content">

		<h2 class="BigTitle"><span class="ColoredTxt">Settings</span> for user ' . $authNamespace->andutteye_username . '</h2>

<fieldset class="GroupField">
	<legend>Account information</legend>
		<div class="leftcol">
			<label>Username ' . $res->andutteye_username . '</label>
			<label>Userrole ' . $res->andutteye_role . '</label>
			<label>Number of times logged in ' . $res->nr_of_loggins . ' Last logged in ' . $res->last_loggedin . '</label>
		</div>
		<div class="rightcol">
			<label>Userdescription ' . $res->user_description . '</label>
			<label>Account created ' . $res->created_date . ' ' . $res->created_time . '</label>
			<label>Selected theme ' . $authNamespace->andutteye_theme . '.</label>
		</div>
</fieldset>

		<div class="leftcol">
<fieldset class="GroupField">
	<legend>Change <span class="ColoredTxt">Password</span></legend>
			<form method="get" action="index.php">
				<input type="hidden" name="main" value="user_settings">
				<input type="hidden" name="param1" value="changepassword">

				<label>After the password change you need to logout and login again to be able to use the new password. Your current session will be invalid as soon as the password change is completed.</label>
				<label>Password<br />
					<input type="password" name="param2" maxlength="255" value="" size="50">
				</label>
				<label><input class="button" type="submit" value="Submit"></label>
			</form>
</fieldset>
		</div>

		<div class="rightcol">
<fieldset class="GroupField">
	<legend>Change <span class="ColoredTxt">Theme</span></legend>
			<form method="get" action="index.php">
				<input type="hidden" name="main" value="user_settings">
				<input type="hidden" name="param1" value="changetheme">

				<label>After the theme change you need to logout and login again to commit the new theme. Your current theme will be used until you have logged out and logged in again.<br /><br /></label>

				<label>Theme<br />
				<select name="param2" style="WIDTH: 260px">
';


			$command = `ls themes`;
        		$themes  = explode ("\n", $command);
       
			foreach($themes as $i) {
             			if ($i != "") {
					echo "<option value='$i'>$i";
             			}
        		}

echo '
				</select>
				</label>
				<label><input class="button" type="submit" value="Submit"></label>
			</form>

		</div>
</fieldset>

	</div>
</div>
';

// End of subfunction
}



function enviroment_overview() {

require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();
require 'db.php';

echo '
<div id="content">
	<h2 class="BigTitle"><img src="themes/' . $authNamespace->andutteye_theme . '/overview_b.png" alt="" title="" /><span class="ColoredTxt">Andutteye system</span> status</h2>
';

                $sql = $db->query("select system_name from andutteye_systems order by system_name asc");
                while ($row = $sql->fetch()) {
                        $system_name = $row['system_name'];

                        $subsql = $db->query("select seqnr from andutteye_alarm where system_name = '$system_name' and status = 'OPEN'");
                        $alarm = $subsql->fetchAll();
                        $alarm = count($alarm);

                        if($alarm != "0") {
echo '
<div class="SecurityAlert">
	<span>
		<a href="index.php?main=monitoring_front&param1=' . $system_name . '">There are <b>' . $alarm . '</b> untreated security alerts</a>
	</span>
</div>
';
                        }

                        $subsql = $db->query("select seqnr from andutteye_choosenbundles where system_name = '$system_name' and specaction != 'N'");
                        $bundle = $subsql->fetchAll();
                        $bundle = count($bundle);

                        if($bundle != "0") {
echo '
								<label><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <a href="index.php?main=system_specification&param1=' . $system_name . '"><b>' . $bundle . '</b> pending management bundle changes on system ' . $system_name . '</a></label>
';
                        }

                        $subsql = $db->query("select seqnr from andutteye_choosenpackages where system_name = '$system_name' and specaction != 'N'");
                        $package = $subsql->fetchAll();
                        $package = count($package);

                        if($package != "0") {
echo '
								<label><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" />  <a href="index.php?main=system_specification&param1=' . $system_name . '"><b>' . $package . '</b> pending management package changes on system ' . $system_name . '</a></label>
';
                        }

                }

echo '
<fieldset class="GroupField">
	<legend>Andutteye services status</legend>
	<div class="leftcol">
	                <table>
                        	<th>Andutteye server</th></tr>';

                if(check_if_service_is_alive('andutteyedsrv')) {
                        echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/started.png" alt="" title="" /> Andutteye server (andutteyedsrv) is running.</td><tr>';
                } else {
                        echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/stopped.png" alt="" title="" /> Andutteye server (andutteyedsrv) is down.</td><tr>';
                }
               	
		echo '<th>Andutteye frontproxy relayer</th></tr>';

                if(check_if_service_is_alive('andutteyedfrt')) {
                        echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/started.png" alt="" title="" /> Andutteye frontproxy relayer (andutteyedfrt) is running.</td><tr>';
                } else {
                        echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/stopped.png" alt="" title="" /> Andutteye frontproxy relayer (andutteyedfrt) is down.</td><tr>';
                }

                echo "</table>
	</div>";

echo '<div class="rightcol">
	<table>
        	<th>Andutteye agent</th></tr>';

                if(check_if_service_is_alive('andutteyedagt')) {
                        echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/started.png" alt="" title="" /> Andutteye agent (andutteyedagt) is running.</td><tr>';
                } else {
                        echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/stopped.png" alt="" title="" /> Andutteye agent (andutteyedagt) is down.</td><tr>';
                }

echo '
	</table>
    </div>
</fieldset>

<fieldset class="GroupField">
	<legend>Andutteye controlcenter overview</legend>';
                echo '<div class="leftcol">';
		echo '<label>';


		include_once 'graph/php-ofc-library/open_flash_chart_object.php';
		open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-alarmstatus-data.php", false );

		echo '
		</label>
                </div>
                <div class="rightcol">
		<label>';

		include_once 'graph/php-ofc-library/open_flash_chart_object.php';
		open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-systemstypes-data.php", false );

		echo '
		</label>
                </div>

</fieldset>
';

$sql = $db->query("select distinct(domain_name) from andutteye_domains order by domain_name asc");
while ($row = $sql->fetch()) {
	$domain_name = $row['domain_name'];

	echo '
	<fieldset class="GroupField">
		<legend><img src="themes/' . $authNamespace->andutteye_theme . '/domains_1.png" alt="" title="" /> Domain '.$domain_name.'</legend>
		<table>
			<th>System</th>
			<th>Status</th>
			<th>Date</th>
			<th>Time</th>
		</tr>';

        $subsql = $db->query("select * from andutteye_systems where domain_name = '$domain_name'");
	while ($subrow = $subsql->fetch()) {
		$system_name = $subrow['system_name'];
                        
		$ssubsql = $db->query("select * from andutteye_serverlog where system_name = '$system_name' order by seqnr desc limit 0,1");
		$res = $ssubsql->fetchObject();

		$date = date("20ymd");

		echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" /> '.$system_name.'</td>';
		
		if($date == $res->created_date) {
			echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/started.png" alt="" title="" /> System is online</td>';
		} else {
			echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/stopped.png" alt="" title="" /> System is offline</td>';
		}
		echo "<td>$res->created_date</td>";
		echo "<td>$res->created_time</td></tr>";
	}


	echo '</table></fieldset>';
}

echo '
	</div>
</div>
';

// End of subfunction
}

function create_domain($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10) {

verify_if_user_is_logged_in();
verify_if_user_have_admin_prevs();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');


if($param1) {
	$date = date("20y-m-d");
	$time = date("H:m:s");

  	$sql   = $db->query("select seqnr from andutteye_domains where domain_name ='$param1'");
        $exists = $sql->fetchAll();
        $exists = count($exists);


	if($param1 != "" && $param2 != "" && $exists == 0) {
		$data = array(
    			'domain_name'      => "$param1",
    			'domain_description' => "$param2",
    			'domain_status'     => "active",
    			'created_by'        => "$authNamespace->andutteye_username",
    			'created_date'      => "$date",
    			'created_time'      => "$time"
		);
		$db->insert('andutteye_domains', $data);
	}
}

echo '
<div id="content">
	<div class="content">

<fieldset class="GroupField">
	<legend><img src="themes/' . $authNamespace->andutteye_theme . '/domain_b.png" alt="" title="" /><span class="BigTitle"><span class="ColoredTxt">Create</span> New Domain</span></legend>

		<form method="get" action="index.php">
			<input type="hidden" name="main" value="create_domain">

				<label for="domainname">Domainname:</label>
				<label><input tabindex="1" class="med" type="text" name="param1" id="param1" size="35" maxlength="255" value=""></label>
				<br />
				<label for="password"> Domain description:</label>
				<label><input tabindex="2" class="med" type="text" name="param2" id="param1" size="35" maxlength="255" value=""></label>
			<br />
		<input tabindex="2" style="cursor:pointer;" class="button" type="submit" alt="Click Button to Submit Form" value="Submit" title="Click  Button to Submit Form">
	</form>
   </fieldset>
</div>
';

echo '
<div class="content"">
<fieldset class="GroupField">
	<legend><span class="BigTitle"><span class="ColoredTxt">Change</span> Current Domains</span></legend>
	
	<table>
		<th>Domain</th>
		<th>Submit</th>
	</tr>';

	$sql = $db->query("select * from andutteye_domains order by domain_name asc");
        	while ($row = $sql->fetch()) {
                	$seqnr = $row['seqnr'];
                        $domain_name = $row['domain_name'];
                        $domain_description = $row['domain_description'];
		
			echo '
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/domains_1.png" alt="" title="" />
					<a href="#" class="Tips2" title="Domain:' . $domain_name . ' Description:' . $domain_description . '">' . $domain_name . '</a>
				</td>
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />
					<a href=index.php?main=remove_domain&param1=' . $seqnr . '" onclick="return confirm(\'Remove domain ' . $domain_name . '?\')">Remove</a>
				</td></tr>';
                 }
echo '
</table>
</fieldset>
</div>
';

// End of subfunction
}

function create_group($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10) {

verify_if_user_is_logged_in();
verify_if_user_have_admin_prevs();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');


if($param1) {
	$date = date("20y-m-d");
	$time = date("H:m:s");

   	$sql   = $db->query("select seqnr from andutteye_groups where group_name ='$param1' and domain_name = '$param3'");
        $exists = $sql->fetchAll();
        $exists = count($exists);

        if($param1 != "" && $param2 != "" && $exists == 0) {
                $data = array( 
                        'group_name'      => "$param1",
                        'group_description' => "$param2",
                        'domain_name'       => "$param3",
                        'group_status'      => "active",
                        'created_by'        => "$authNamespace->andutteye_username",
                        'created_date'      => "$date",
                        'created_time'      => "$time"
                );
                $db->insert('andutteye_groups', $data);
        }
}

echo '<div id="content">
		<div class="content">
			<fieldset class="GroupField">
				<legend><img src="themes/' . $authNamespace->andutteye_theme . '/group_b.png" alt="" title="" /><span class="BigTitle"><span class="ColoredTxt">Create</span> New Group</span></legend>

					<form method="get" action="index.php">
						<input type="hidden" name="main" value="create_group">

							<label for="groupname">Groupname:</label>
							<label><input tabindex="1" class="med" type="text" name="param1" id="param1" size="35" maxlength="255" value=""></label>
							<br />
							<label for="groupdescription"> Group description:</label>
							<label><input tabindex="2" class="med" type="text" name="param2" id="param2" size="35" maxlength="255" value=""></label>
							<br />
							<label for="joindomain"> Join to current domain:</label>

							<label>
								<select name="param3" style="WIDTH: 260px">
';
                       			
					$sql    = $db->query("select distinct(domain_name) from andutteye_domains order by domain_name asc");

                			while ($row = $sql->fetch()) {
                        			$domain_name = $row['domain_name'];
						echo '
						<option value="' . $domain_name . '"> ' . $domain_name . '
						';
                			}

		echo '
				</select>
			    </label>
			<br />
			<input tabindex="2" style="cursor:pointer;" class="button" type="submit" alt="Click Button to Submit Form" value="Submit" title="Click  Button to Submit Form">

		</form>

	</fieldset>
     </div>
<br />

<div class="content">
<fieldset class="GroupField">
	<legend><span class="BigTitle"><span class="ColoredTxt">Change</span> Current Groups</span></legend>

	<table>
		<th>Group</th>
		<th>Submit</th>
		</tr>';

			$sql = $db->query("select * from andutteye_groups order by group_name asc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $group_name = $row['group_name'];
                                $domain_name = $row['domain_name'];
                                $group_description = $row['group_description'];

				echo '
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" />
					<a href="#" class="Tips2" title="Domain:' . $domain_name . ' Group:' . $group_name . ' Description:' . $group_description . '">' . $group_name . '</a>
				</td>
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />
					<a href="index.php?main=remove_group&param1=' . $seqnr . '" onclick="return confirm(\'Remove group ' . $group_name . '?\')">Remove</a>
				</td>
				</tr>';
                        }


echo '
</table>
</fieldset>
	</div>
';

// End of subfunction
}

function create_system($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10) {

verify_if_user_is_logged_in();
verify_if_user_have_admin_prevs();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param1) {
        $date = date("20y-m-d");
        $time = date("H:m:s");
	$result = split("#",$param2);

        $sql   = $db->query("select seqnr from andutteye_systems where system_name ='$param1'");
        $exists = $sql->fetchAll();
        $exists = count($exists);

        if($param1 != "" && $param2 != "" && $exists == 0) {
                $data = array(
                        'system_name'     => "$param1",
                        'domain_name'     => "$result[0]",
                        'group_name'      => "$result[1]",
                        'system_description' => "$param3",
                        'system_type'      => "$param4",
                        'system_information' => "$param5",
                        'created_by'      => "$authNamespace->andutteye_username",
                        'created_date'     => "$date",
                        'created_time'     => "$time"
                );
                $db->insert('andutteye_systems', $data);
	}
}

echo '<div id="content">
      		<div class="content">
			<fieldset class="GroupField">
				<legend><img src="themes/' . $authNamespace->andutteye_theme . '/system_b.png" alt="" title="" /><span class="BigTitle"><span class="ColoredTxt">Create</span> New System</span></legend>
						<form method="get" action="index.php">
							<input type="hidden" name="main" value="create_system">

								<label for="systemname">Systemname:</label>
								<label><input type="text" name="param1" maxlength="255" size="35" value="" size="70"></label>
								<br />
								<label for="systemkey"> System description (keep it short, displayed in menu)</label>
								<label><input type="text" name="param3" maxlength="30" size="35" value="" size="70"></label>
								<br />
								<label for="systemkey"> System information (long information, displayed in system overview)</label>
								<label><input type="text" name="param5" maxlength="255" size="35" value="" size="70"></label>
								<br />
								<label for="systemkey"> System type:</label>
								<label>
									<select name="param4" style="WIDTH: 260px">
										<option value="Linux"> Linux
										<option value="Unix"> Unix
										<option value="Windows"> Windows
										<option value="MacosX"> MacosX
										<option value="Vmware"> Vmware
										<option value="Xen"> Xen
										<option value="Kvm"> Kvm
									</select>
								</label>
								<br />
								<label for="joindomain"> Join system to current domain and group:</label>

								<label>
									<select name="param2" style="WIDTH: 260px">
								';

                                        $sql    = $db->query("select domain_name, group_name from andutteye_groups order by domain_name asc");

                                        while ($row = $sql->fetch()) {
                                                $domain_name = $row['domain_name'];
                                                $group_name = $row['group_name'];
						echo '
						<option value="' . $domain_name . '#' . $group_name . '"> Domain ' . $domain_name . ' and group ' . $group_name . '
						';
                                        }

					echo '
									</select>
								</label>
							<br />
							<input class="button" type="submit" value="Submit">
						</form>
					    </fieldset>					
					</div>
<br />

<div class="content">
	<fieldset class="GroupField">
		<legend><span class="BigTitle"><span class="ColoredTxt">Change</span> Current Systems</span></legend>
		
			<table>
				<th>System</th>
				<th>Install/Reinstall</th>
				<th>Action</th>
				</tr>';

			$sql = $db->query("select * from andutteye_systems order by system_name asc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $system_name = $row['system_name'];
                                $domain_name = $row['domain_name'];
                                $group_name = $row['group_name'];
                                $system_description = $row['system_description'];

				echo '
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" />
				<a href="index.php?main=create_system&param1=' .$system_name. '&param2=modify" class="Tips2" title="System:' . $system_name . ' Description:' . $system_description . ' Domain:' . $domain_name . ' Group:' . $group_name . '">' . $system_name . '</a>
				</td>
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/install_system_1.png" alt="" title="" />
					<a href="index.php?main=install_new_system&param1=' .$system_name. '">Systemprovisioning</a>
				
				</td>
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />
				<a href="index.php?main=remove_system&param1=' . $system_name . '" onclick="return confirm(\'Remove system ' . $system_name . '? All saved information, documents, management information and configuration will be removed.\')">Remove</a>
				</td>
				</tr>
				';
                        }

echo '
</table>
</fieldset>
</div>
';

// End of subfunction
}

function domain_overview($param1) {

verify_if_user_is_logged_in();

if(!verify_role_object_permission($param1,'domain',1,'0','0')) {
	// Verify if domain is allowed to be read.
	header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20access%20domain");
	exit;
}

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select * from andutteye_domains where domain_name = '$param1'");
$res = $sql->fetchObject();

$sql   = $db->query("select seqnr from andutteye_groups where domain_name = '$param1'");
$nrg = $sql->fetchAll();
$nrg = count($nrg);

$sql   = $db->query("select seqnr from andutteye_systems where domain_name = '$param1'");
$nrs = $sql->fetchAll();
$nrs = count($nrs);

$sql   = $db->query("select seqnr from andutteye_uploads where domain_name = '$param1'");
$nrd = $sql->fetchAll();
$nrd = count($nrd);

echo '<div class="DivSpacer"></div>';
echo '<div id="content">
	<fieldset class="GroupField">
		<legend><img src="themes/' . $authNamespace->andutteye_theme . '/domain_b.png" alt="" title="" /><span class="BigTitle"><span class="ColoredTxt">Domain</span> information</span></legend>

                <div class="leftcol">
				<table>
                			<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/domains_1.png" alt="" title="" /> Domain description ' . $res->domain_description . '
					</td>
					</tr>
                			<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" /> '.$nrg.' child groups of this domain
					</td>
					</tr>
                			<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" /> '.$nrs.' child systems of this domain
					</td>
					</tr>
				</table>
		</div>
                <div class="rightcol">
				<table>
                			<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> Domain created by ' . $res->created_by . '
					</td>
					</tr>
                			<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> Domain created on date ' . $res->created_date . '
					</td>
					</tr>
                			<td><img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> Domain created on time ' . $res->created_time . '
					</td>
					</tr>
				</table>
		</div>
	</fieldset>

';
echo '<div class="DivSpacer"></div>';

echo '
<fieldset class="GroupField">
	<legend><span class="BigTitle"><span class="ColoredTxt">Domain</span> overview</span></legend>
		<div class="leftcol">
			<label>

';

		include_once 'graph/php-ofc-library/open_flash_chart_object.php';
		open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-domainsystems-data.php?domain=$param1", false );

echo '
			</label>
		</div>

		<div class="rightcol">
			<label>
';

		include_once 'graph/php-ofc-library/open_flash_chart_object.php';
		open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-domaingroups-data.php?domain=$param1", false );


echo '
			</label>
		</div>
</fieldset>
';

		$sql = $db->query("select system_name from andutteye_systems where domain_name = '$res->domain_name' order by domain_name asc");
                while ($row = $sql->fetch()) {
                        $system_name = $row['system_name'];

			$subsql = $db->query("select seqnr from andutteye_alarm where system_name = '$system_name' and status = 'OPEN'");
			$alarm = $subsql->fetchAll();
			$alarm = count($alarm);

			if($alarm != "0") {
echo '
<div class="SecurityAlert">
	<span>
		<a href="index.php?main=monitoring_front&param1=' . $system_name . '">There are <b>' . $alarm . '</b> untreated security alerts</a>
	</span>
</div>
';
			}

			$subsql = $db->query("select seqnr from andutteye_choosenbundles where system_name = '$system_name' and specaction != 'N'");
			$bundle = $subsql->fetchAll();
			$bundle = count($bundle);
			
			if($bundle != "0") {
				 echo '<table>
					<th>Message</th>
					</tr>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <a href="index.php?main=system_specification&param1=' . $system_name . '"><b>' . $bundle . '</b> pending management bundle changes on system ' . $system_name . '</a>
					</td>
					</tr>
					</table>';
			}

			$subsql = $db->query("select seqnr from andutteye_choosenpackages where system_name = '$system_name' and specaction != 'N'");
			$package = $subsql->fetchAll();
			$package = count($package);
			
			if($package != "0") {
				echo '<table>
			 		<th>Message</th>
					</tr>
                                        <td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <a href="index.php?main=system_specification&param1=' . $system_name . '"> <b>' . $package . '</b> pending management package changes on system ' . $system_name . '</a></td></tr></table>';
			}
			
                }

echo '<h3 class="BigTitle"><span class="ColoredTxt">Andutteye</span> Information</h3>
     	<h3 class="toggler">
		<img src="themes/' . $authNamespace->andutteye_theme . '/domain_gr.png" alt="" title="" />
			<span class="InfoTitle"> DOMAIN GROUPS (<span class="ColoredTxt">' . $nrg . '</span>)</span></h3>
       				<div class="element">
					<table>
						<th>Group</th>
						<th>Description</th>
						<th>Creator</th>
						</tr>';
		
		$sql    = $db->query("select * from andutteye_groups where domain_name = '$param1' order by group_name asc");
                while ($row = $sql->fetch()) {
                        $group_name = $row['group_name'];
                        $group_description = $row['group_description'];
                        $created_by = $row['created_by'];


			if(verify_role_object_permission($group_name,'group',1,'0','0')) {
					echo '
						<td>
							<img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" />
							<a href="index.php?main=group_overview&param1=' . $param1 . '&param2=' . $group_name . '"  class="Tips2" title="Domain ' . $param1 . ' Group ' . $group_name . '">' . $group_name . '</a>
						</td>
						<td>
							' . $group_description . '
						</td>
						<td>
							<img src="themes/' . $authNamespace->andutteye_theme . '/user_1.png" alt="" title="" />
							' . $created_by . '
						</td>

						</tr>
					';
			}
                }
echo '</table></div>';

echo'<h3 class="toggler">
	<img src="themes/' . $authNamespace->andutteye_theme . '/group_doc.png" alt="" title="" />
		<span class="InfoTitle"> DOMAIN SYSTEMS (<span class="ColoredTxt">' . $nrs . '</span>)</span></h3>
        	<div class="element">
				<table>
					<th>System</th>
					<th>Description</th>
					<th>Group</th>
					<th>Systemtype</th>
					<th>Creator</th>
					</tr>';
		
		$sql    = $db->query("select * from andutteye_systems where domain_name = '$param1' order by group_name asc, system_type asc");
                while ($row = $sql->fetch()) {
                        $system_name = $row['system_name'];
                        $group_name  = $row['group_name'];
                        $system_type = $row['system_type'];
                        $system_heartbeat = $row['system_heartbeat'];
                        $created_by = $row['created_by'];
                        $system_description = $row['system_description'];
			
			if(verify_role_object_permission($system_name,'system',1,'0','0')) {

				  echo '<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" />
					<a href="index.php?main=system_overview&param1=' . $system_name . '&param2=' . $param1 . '&param3=' . $group_name . '" class="Tips2" title="Member of domain ' . $param1 . ' and group ' . $group_name . '">' . $system_name . '</a>
				     	</td>	
				     	<td>
					'.$system_description.'
					</td>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" />
					'.$group_name.'
					</td>
					<td>
					'.$system_type.'
					</td>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/user_1.png" alt="" title="" />
					'.$created_by.'
					</td>
					</tr>
				';
			}
                }

echo '</table></div>';

echo '<h3 class="toggler">
      	<img src="themes/' . $authNamespace->andutteye_theme . '/sys_doc.png" alt="" title="" />
		<span class="InfoTitle"> DOMAIN DOCUMENTATION (<span class="ColoredTxt">' . $nrd . '</span>)</span></h3>
        		<div class="element">
		        	<table>
                                	<th>Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        </tr>';

		$sql    = $db->query("select * from andutteye_uploads where domain_name = '$param1' order by content_description asc");
                while ($row = $sql->fetch()) {
                        $seqnr			= $row['seqnr'];
                        $content_description	= $row['content_description'];
                        $content_name		= $row['content_name'];
                        $content_type		= $row['content_type'];
                        $content_size		= $row['content_size'];
                        $created_by		= $row['created_by'];
                        $created_date		= $row['created_date'];
                        $created_time		= $row['created_time'];
                        $upload_type		= $row['upload_type'];
                        $content		= $row['content'];
		
			if ($upload_type == "upload") {
				echo '
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/upload.png" alt="" title="" />
					<a href="download.php?seqnr=' . $seqnr . '" class="Tips2" title="File:' . $content_name . ' Contenttype:' . $content_type . ' Size:' . $content_size . ' kb Created by:' . $created_by . ' On:' . $created_date . ' ' . $created_time . '">' . $content_description . '</a>
					</td>
					<td>
					'.$content_type.'
					</td>
					<td>
					'.$content_size.' kb
					</td>
					</tr>
				';
			} else {
				echo '
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/link.png" alt="" title="" />
					<a href="'.$content.'" target="_new" class="Tips2" title="File:' . $content_name . ' Contenttype:' . $content_type . ' Size:' . $content_size . ' kb Created by:' . $created_by . ' On:' . $created_date . ' ' . $created_time . '">' . $content_description . '</a>
					</td>
					<td>
					'.$content_type.'
					</td>
					<td>
					-
					</td>
					</tr>
				';
			}

                }

echo '</table></div>';

echo '<h3 class="toggler">
          <img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" />
              <span class="InfoTitle"> DOMAIN COMMANDS</span></h3>
                     <div class="element">

                        <form method="post" action="index.php">
                                <input type="hidden" name="main" value="submit_domain_command">
                                <input type="hidden" name="param3" value="'.$param1.'">

                                <table>
                                        <th>Enable filemanagement</th>
                                        <th>Disable filemanagement</th>
                                        <th>Enable packagemanagement</th>
                                        <th>Disable packagemanagement</th>
                                        <th>Lock all monitors</th>
                                        <th>Unlock all monitors</th>
                                        </tr>

                        <td><input type="radio" name="param1" value="EnableFilemanagment"></td>
                        <td><input type="radio" name="param1" value="DisableFilemanagement"></td>
                        <td><input type="radio" name="param1" value="EnablePackagemanagement"></td>
                        <td><input type="radio" name="param1" value="DisablePackagemanagement"></td>
                        <td><input type="radio" name="param1" value="LockAllMonitors"></td>
                        <td><input type="radio" name="param1" value="UnlockAllMonitors"></td>
                </tr>
                <th colspan="2">System</th>
                <th>Group</th>
                <th>Packagemanagement</th>
                <th>Filemanagement</th>
                <th>Enforce</th>
                </tr>
';
$gsql  = $db->query("select * from andutteye_systems where domain_name = '$param1' order by group_name asc, system_name desc");

while ($row = $gsql->fetch()) {
        $seqnr  = $row['seqnr'];
        $system_name    = $row['system_name'];
        $group_name    = $row['group_name'];

	$filemanagement_status = get_current_filemanagement_status($system_name);
	$packagemanagement_status = get_current_packagemanagement_status($system_name);

	if($filemanagement_status == "Disabled") {
		$filemanagement_status_image = '<img src="themes/' . $authNamespace->andutteye_theme . '/stopped.png" alt="" title="" />';
	} else {
		$filemanagement_status_image = '<img src="themes/' . $authNamespace->andutteye_theme . '/started.png" alt="" title="" />';
	}
	if($packagemanagement_status == "Disabled") {
		$packagemanagement_status_image = '<img src="themes/' . $authNamespace->andutteye_theme . '/stopped.png" alt="" title="" />';
	} else {
		$packagemanagement_status_image = '<img src="themes/' . $authNamespace->andutteye_theme . '/started.png" alt="" title="" />';
	}

        echo '<td colspan="2"> <img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" /> '.$system_name.'</td>
	     <td> <img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" /> '.$group_name.'</td>
	     <td>' .$packagemanagement_status_image.' </td>
	     <td>' .$filemanagement_status_image.' </td>
             <td><input type="checkbox" name="param2[]" value='.$system_name.'></td>
              </tr>';
}

echo '
        <td colspan="6"><input class="button" type="submit" value="Submit"></td>
        </tr>
        </form></table>
   </div>
</div>
';

echo '</div>
';

// End of subfunction
}

function link_documentation($param1,$param2,$param3,$param4,$param5) {

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param1) {
        $date = date("20y-m-d");
        $time = date("H:m:s");
        $result = split("#",$param2);

        if($param1 != "" && $param2 != "") {
                $data = array(
                        'domain_name'	=> "$param3",
                        'group_name'	=> "$param4",
                        'system_name'	=> "$param5",
                        'content' 	=> "$param2",
                        'content_description'	=> "$param1",
                        'content_type'	=> "link/url",
                        'upload_type'	=> "link",
                        'created_by'	=> "$authNamespace->andutteye_username",
                        'created_date'	=> "$date",
                        'created_time'	=> "$time"
                );
                $db->insert('andutteye_uploads', $data);
        }
}

echo '<div id="content">
      	<div class="section content">

<fieldset class="GroupField">
	<legend><span class="BigTitle">Link to <span class="ColoredTxt">Documentation</span></span></legend>

					<form method="get" action="index.php">
						<input type="hidden" name="main" value="link_documentation">
							<div>
								<label for="password"> Specify document or link description:</label>
								<label><input type="text" name="param1" size="35" maxlength="255" value=""></label>
								<br />
								<label for="password"> Insert internet url:</label>
								<label><input tabindex="2" class="med" type="text" name="param2" id="param2" size="35" maxlength="255" value="http://"></label>
							</div>

							<label for="password"> Choose if upload should be connected to a domain:</label>
							<label>
								<select name="param3" style="WIDTH: 260px">
									<option value=""> Dont connect to any domain
';

					$sql   = $db->query("select distinct(domain_name) from andutteye_domains order by domain_name asc");
                                        while ($row = $sql->fetch()) {
                                                $domain_name = $row['domain_name'];
echo '
									<option value="' . $domain_name . '"> ' . $domain_name . '
';
                                        }

echo '
								</select>
							</label>
				
							<label for="password"> Choose if upload should be connected to a group:</label>
							<label>
								<select name="param4" style="WIDTH: 260px">
									<option value=""> Dont connect to any group
';

                                        $sql   = $db->query("select distinct(group_name) from andutteye_groups order by group_name asc");
                                        while ($row = $sql->fetch()) {
                                                $group_name = $row['group_name'];
echo '
									<option value="' . $group_name . '"> ' . $group_name . '
';
                                        }

echo '
								</select>
							</label>

							<label for="password"> Choose if upload should be connected to a system:</label>
							<label>
								<select name="param5" style="WIDTH: 260px">
									<option value=""> Dont connect to any system
';

                                        $sql   = $db->query("select distinct(system_name) from andutteye_systems order by system_name asc");
                                        while ($row = $sql->fetch()) {
                                                $system_name = $row['system_name'];
echo '
									<option value="' . $system_name . '"> ' . $system_name . '
';
                                        }

echo '
								</select>
							</label>
							<br />
							<input tabindex="2" style="cursor:pointer;" class="button" type="submit" alt="Click Button to Submit Form" value="Submit" title="Click  Button to Submit Form">

						</form>

</fieldset>

			</div>

		</div>
';

// End of subfunction
}



function upload_documentation($param1,$param2,$param3,$param4,$param5) {

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

echo '<div id="content">
		<div class="section content">

<fieldset class="GroupField">
	<legend><span class="BigTitle">Upload <span class="ColoredTxt">Files</span> and <span class="ColoredTxt">Documentation</span></span></legend>

				<form action="upload.php" method="post" enctype="multipart/form-data">

					<label>Specify document or link description</label>
					<label><input type="file" name="file" id="file"></label>
					<br />
					<label>Specify document or file description:</label>
					<label><input type="text" name="param1" size="35" maxlength="255" value=""></label>


					<label>Choose if upload should be connected to a domain</label>
					<label>
						<select name="param2" style="WIDTH: 260px">
							<option value=""> Dont connect to any domain
';

                                        $sql   = $db->query("select distinct(domain_name) from andutteye_domains order by domain_name asc");
                                        while ($row = $sql->fetch()) {
                                                $domain_name = $row['domain_name'];
echo '
							<option value="' . $domain_name . '"> ' . $domain_name . '
';
                                        }

echo '
						</select>
					</label>

					<label for="password"> Choose if upload should be connected to a group</label>
					<label>
						<select name="param3" style="WIDTH: 260px">
							<option value=""> Dont connect to any group
';

                                        $sql   = $db->query("select distinct(group_name) from andutteye_groups order by group_name asc");
                                        while ($row = $sql->fetch()) {
                                                $group_name = $row['group_name'];
echo '
							<option value="' . $group_name . '"> ' . $group_name . '
';
                                        }

echo '
						</select>
					</label>

					<label for="password"> Choose if upload should be connected to a system</label>
					<label>
						<select name="param4" style="WIDTH: 260px">
							<option value=""> Dont connect to any system
';

                                        $sql   = $db->query("select distinct(system_name) from andutteye_systems order by system_name asc");
                                        while ($row = $sql->fetch()) {
                                                $system_name = $row['system_name'];
echo '
							<option value="' . $system_name . '"> ' . $system_name . '
';
                                        }

echo '
						</select>
					</label>
					<br />
					<input tabindex="2" style="cursor:pointer;" class="button" type="submit" value="Submit">

				</form>

</fieldset>

		</div>
	</div>
';

// End of subfunction
}

function group_overview($param1,$param2) {

verify_if_user_is_logged_in();

if(!verify_role_object_permission($param2,'group',1,'0','0')) {
        // Verify if group is allowed to be read.
        header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20access%20group");
        exit;
}

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select * from andutteye_groups where domain_name = '$param1' and group_name = '$param2'");
$res = $sql->fetchObject();

$sql   = $db->query("select seqnr from andutteye_systems where domain_name = '$param1' and group_name = '$param2'");
$nrs = $sql->fetchAll();
$nrs = count($nrs);

$sql   = $db->query("select seqnr from andutteye_uploads where group_name = '$param2' and domain_name = '$param1'");
$nrd = $sql->fetchAll();
$nrd = count($nrd);

echo'<div class="DivSpacer"></div>';

echo '<div id="content">

	<fieldset class="GroupField">
        	<legend><img src="themes/' . $authNamespace->andutteye_theme . '/group_b.png" alt="" title="" /> <span class="BigTitle"><span class="ColoredTxt">Group</span> '.$param2.'</span></legend>
      		<div class="leftcol">

		<table>
			<td>
			<img src="themes/' . $authNamespace->andutteye_theme . '/domains_1.png" alt="" title="" /> Parent domain ' . $param1 . '
			</td>
			</tr>
			<td>
			<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> Group description ' . $res->group_description . '
			</td>
			</tr>
			<td>
			<img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" /> ' . $nrs . ' systems is members of this group
			</td>
			</tr>
		</table>
	</div>

	<div class="rightcol">
		<table>
			<td>
			<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> Group created by '.$res->created_by.'
			</td>
			</tr>
			<td>
			<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> Group created on date '.$res->created_date.'
			</td>
			</tr>
			<td>
			<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> Group created on time '.$res->created_time.'
			</td>
			</tr>
		</table>
	</div>
  </fieldset>

<div class="DivSpacer"></div>';

echo '<div>
	<fieldset class="GroupField">
		<legend><span class="BigTitle"><span class="ColoredTxt">Group</span> overview</span></legend>
				<div class="leftcol">
					<label>';

			include_once 'graph/php-ofc-library/open_flash_chart_object.php';
			open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-alarmstatus-data.php", false );

echo '
					</label>
				</div>
				<div class="rightcol">
					<label>
';

			include_once 'graph/php-ofc-library/open_flash_chart_object.php';
			open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-groupsystems-data.php?group=$res->group_name", false );


echo '
					</label>
				</div>

</fieldset>
';

                $sql = $db->query("select system_name from andutteye_systems where domain_name = '$res->domain_name' and group_name = '$res->group_name' order by system_name asc");
                while ($row = $sql->fetch()) {
                        $system_name = $row['system_name'];

                        $subsql = $db->query("select seqnr from andutteye_alarm where system_name = '$system_name' and status = 'OPEN'");
                        $alarm = $subsql->fetchAll();
                        $alarm = count($alarm);

                        if($alarm != "0") {
				echo '
				<div class="SecurityAlert">
					<span>
					<a href="index.php?main=monitoring_front&param1=' . $system_name . '">There are <b>' . $alarm . '</b> untreated security alerts</a>
					</span>
				</div>
				';
                        }

                        $subsql = $db->query("select seqnr from andutteye_choosenbundles where system_name = '$system_name' and specaction != 'N'");
                        $bundle = $subsql->fetchAll();
                        $bundle = count($bundle);

                        if($bundle != "0") {
				echo '
				<table>
				<th>Message</th>
				</tr>
				<td><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <a href="index.php?main=system_specification&param1=' . $system_name . '"><b>' . $bundle . '</b> pending management bundle changes on system ' . $system_name . '</a></td>
				</tr>
				</table>
				';
                        }

                        $subsql = $db->query("select seqnr from andutteye_choosenpackages where system_name = '$system_name' and specaction != 'N'");
                        $package = $subsql->fetchAll();
                        $package = count($package);

                        if($package != "0") {
				echo '
				<table>
                                <th>Message</th>
                                </tr>
				<td><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <b>' . $package . '</b> pending management package changes on system ' . $system_name . '</td>
				</tr>
				</table>
				';
                        }

                }

			echo '
			</div>
			<br />
			<h3 class="BigTitle"><span class="ColoredTxt">Andutteye</span> Information</h3>

			<h3 class="toggler">
			<img src="themes/' . $authNamespace->andutteye_theme . '/group_sys.png" alt="" title="" /><span class="InfoTitle"> GROUP SYSTEMS (<span class="ColoredTxt">' . $nrs . '</span>)</span></h3>

			<div class="element">
				<table>
					<th>System</th>
					<th>Description</th>
					<th>Group</th>
					<th>Systemtype</th>
					<th>Creator</th>
				</tr>
				';

                $sql    = $db->query("select * from andutteye_systems where domain_name = '$param1' and group_name = '$param2' order by group_name asc");

		 while ($row = $sql->fetch()) {
                        $system_name = $row['system_name'];
                        $group_name  = $row['group_name'];
                        $system_type = $row['system_type'];
                        $system_heartbeat = $row['system_heartbeat'];
                        $created_by = $row['created_by'];
                        $system_description = $row['system_description'];

			if(verify_role_object_permission($system_name,'system',1,'0','0')) {

				echo '
					<td>
					 <img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" />
					<a href="index.php?main=system_overview&param1=' . $system_name . '&param2=' . $param1 . '&param3=' . $group_name . '" class="Tips2" title="Member of domain ' . $param1 . ' and group ' . $group_name . '">' . $system_name . '</a>
					</td>
					<td>
					'.$system_description.'
					</td>
					<td>
					 <img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" />
					'.$group_name.'
					</td>
					<td>
					'.$system_type.'
					</td>
					<td>
					 <img src="themes/' . $authNamespace->andutteye_theme . '/user_1.png" alt="" title="" />
					'.$created_by.'
					</td>
					</tr>
				';
			}

                }

echo '</table></div>
	<h3 class="toggler">
		<img src="themes/' . $authNamespace->andutteye_theme . '/group_docu.png" alt="" title="" />
		<span class="InfoTitle"> GROUP DOCUMENTATION (<span class="ColoredTxt">' . $nrd . '</span>)</span></h3>
			<div class="element">
				<table>
					<th>Name</th>
					<th>Type</th>
					<th>Size</th>
					</tr>
		';

                $sql    = $db->query("select * from andutteye_uploads where group_name = '$param2' and domain_name = '$param1' order by content_description asc");

		 while ($row = $sql->fetch()) {
                        $seqnr                  = $row['seqnr'];
                        $content_description    = $row['content_description'];
                        $content_name           = $row['content_name'];
                        $content_type           = $row['content_type'];
                        $content_size           = $row['content_size'];
                        $created_by             = $row['created_by'];
                        $created_date           = $row['created_date'];
                        $created_time           = $row['created_time'];
                        $upload_type            = $row['upload_type'];
                        $content                = $row['content'];

                        if ($upload_type == "upload") {
				echo '
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/upload.png" alt="" title="" />
				<a href="download.php?seqnr=' . $seqnr . '" class="Tips2" title="File:' . $content_name . ' Contenttype:' . $content_type . ' Size:' . $content_size . ' kb Created by:' . $created_by . ' On:' . $created_date . ' ' . $created_time . '">' . $content_description . '</a>
				</td>
				<td>
				'.$content_type.'
				</td>
				<td>
				'.$content_size.' kb
				</td>
				</tr>
				';
                        } else {
				echo '
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/link.png" alt="" title="" />
				<a href="' . $content . '" class="Tips2" title="File:' . $content_name . ' Contenttype:' . $content_type . ' Size:' . $content_size . ' kb Created by:' . $created_by . ' On:' . $created_date . ' ' . $created_time . '">' . $content_description . '</a>
				</td>
				<td>
				'.$content_type.'
				</td>
				<td>
				-
				</td>
				</tr>
				';
                        }

                }

echo '
    </table>
  </div>';


echo '<h3 class="toggler">
          <img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" />
              <span class="InfoTitle"> GROUP COMMANDS</span></h3>
                     <div class="element">

			<form method="post" action="index.php">
				<input type="hidden" name="main" value="submit_group_command">
				<input type="hidden" name="param3" value="'.$param2.'">
				<input type="hidden" name="param4" value="'.$param1.'">

                                <table>
                                        <th>Enable filemanagement</th>
                                        <th>Disable filemanagement</th>
                                        <th>Enable packagemanagement</th>
                                        <th>Disable packagemanagement</th>
                                        <th>Lock all monitors</th>
                                        <th>Unlock all monitors</th>
                                        </tr>

			<td><input type="radio" name="param1" value="EnableFilemanagment"></td>
			<td><input type="radio" name="param1" value="DisableFilemanagement"></td>
			<td><input type="radio" name="param1" value="EnablePackagemanagement"></td>
			<td><input type="radio" name="param1" value="DisablePackagemanagement"></td>
			<td><input type="radio" name="param1" value="LockAllMonitors"></td>
			<td><input type="radio" name="param1" value="UnlockAllMonitors"></td>
		</tr>
		<th colspan="2">System</th>
                <th>Group</th>
                <th>Packagemanagement</th>
                <th>Filemanagement</th>
                <th>Enforce</th>
		</tr>
';
$gsql  = $db->query("select * from andutteye_systems where group_name ='$param2' and domain_name = '$param1' order by system_name desc");

while ($row = $gsql->fetch()) {
	$seqnr	= $row['seqnr'];
	$system_name	= $row['system_name'];
	$group_name	= $row['group_name'];

	$filemanagement_status = get_current_filemanagement_status($system_name);
        $packagemanagement_status = get_current_packagemanagement_status($system_name);

        if($filemanagement_status == "Disabled") {
                $filemanagement_status_image = '<img src="themes/' . $authNamespace->andutteye_theme . '/stopped.png" alt="" title="" />';
        } else {
                $filemanagement_status_image = '<img src="themes/' . $authNamespace->andutteye_theme . '/started.png" alt="" title="" />';
        }
        if($packagemanagement_status == "Disabled") {
                $packagemanagement_status_image = '<img src="themes/' . $authNamespace->andutteye_theme . '/stopped.png" alt="" title="" />';
        } else {
                $packagemanagement_status_image = '<img src="themes/' . $authNamespace->andutteye_theme . '/started.png" alt="" title="" />';
        }

        echo '<td colspan="2"> <img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" /> '.$system_name.'</td>
             <td> <img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" /> '.$group_name.'</td>
             <td>' .$packagemanagement_status_image.' </td>
             <td>' .$filemanagement_status_image.' </td>
	      <td colspan="2"><input type="checkbox" name="param2[]" value='.$system_name.'></td>
	      </tr>';
}

echo '
	<td colspan="6"><input class="button" type="submit" value="Submit"></td>
	</tr>
	</form></table>
   </div>
</div>
';

// End of subfunction
}

function system_overview($param1,$param2,$param3,$param4) {

verify_if_user_is_logged_in();

if(!verify_role_object_permission($param1,'system',1,'0','0')) {
        // Verify if systems is allowed to be read.
        header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20access%20this%20system");
        exit;
}

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select * from andutteye_systems where system_name = '$param1'");
$res = $sql->fetchObject();

$sql   = $db->query("select * from andutteye_uploads where group_name ='$res->group_name' or group_name = '$res->group_name' and domain_name = '$res->domain_name'");
$nrgd = $sql->fetchAll();
$nrgd = count($nrgd);

$sql   = $db->query("select * from andutteye_uploads where system_name = '$param1'");
$nrsd = $sql->fetchAll();
$nrsd = count($nrsd);

$subsql = $db->query("select seqnr from andutteye_alarm where system_name = '$param1' and status = 'OPEN' or status = 'ACK'");
$open = $subsql->fetchAll();
$open = count($open);

$subsql = $db->query("select seqnr from andutteye_choosenbundles where system_name = '$param1' and specaction != 'N'");
$bman = $subsql->fetchAll();
$bman = count($bman);

$subsql = $db->query("select seqnr from andutteye_choosenpackages where system_name = '$param1' and specaction != 'N'");
$pman = $subsql->fetchAll();
$pman = count($pman);

$trans="0";
if(is_dir("$Transfer_dir_location/$param1")) {
	$command   = `ls $Transfer_dir_location/$param1`;
        $files     = explode ("\n", $command);
       foreach($files as $i) {
             if ($i != "") {
			$trans++;
	     }
	}
}

echo '<div class="DivSpacer"></div>';
echo '<div id="content">
        <fieldset class="GroupField">
                <legend><img src="themes/' . $authNamespace->andutteye_theme . '/system_b.png" alt="" title="" /> <span class="BigTitle"><span class="ColoredTxt">System</span> '.$param1.'</span></legend>
                <div class="leftcol">
				<table>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/domains_1.png" alt="" title="" /> Parent domain  <a href="index.php?main=domain_overview&param1=' . $res->domain_name . '&param2=' . $res->group_name . '">'.$res->domain_name.'</a>
					</td>
					</tr>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" /> Parent group <a href="index.php?main=group_overview&param1=' . $res->domain_name . '&param2=' . $res->group_name . '">' . $res->group_name . '</a>
					</td>
					</tr>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" /> System type ' . $res->system_type . '
					</td>
					</tr>';

			if($pman != "0") {
				echo '<td>
					<blink><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <a href="index.php?main=system_specification&param1=' . $param1 . '"> <b>' . $pman . '</b> pending package management changes</a></blink>
				      </td>
				      </tr>';
			}
			if($trans != "0") {
					echo '
					<td><blink><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <b>' . $trans . '</b> new arrived transfer object</blink>
					</td>
					</tr>';
			}
			echo '</table>
				</div>
				<div class="rightcol">
					<table>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> System description ' . $res->system_description . '
					</td>
					</tr>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> System created by ' . $res->created_by . ' on date ' . $res->created_date . '
					</td>
					</tr>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> System created on time ' . $res->created_time . '
					</td>
					</tr>';
			
			if($bman != "0") {
					echo '
					<td>
					<blink><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" />
					<a href="index.php?main=system_specification&param1=' . $param1 . '"> <b>' . $bman . '</b> pending bundle management changes</a></blink>
					</td>
					</tr>';
			}

echo '</table></div></fieldset>';

if($open != "0") {       
	echo '<div class="SecurityAlert">
		<span>
		<a href="index.php?main=monitoring_front&param1=' . $param1 . '"><blink>There are <b>' . $open . '</b> untreated security alerts</blink></a>
		</span>
	      </div>';
}
echo '<div class="DivSpacer"></div>
	<div>
	<fieldset class="GroupField">
		<legend><span class="BigTitle"><span class="ColoredTxt">System</span> overview</span></legend>
			<div class="leftcol">
				<label>';

		include_once 'graph/php-ofc-library/open_flash_chart_object.php';
		open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-system-numberofprocs-data.php?system=$param1", false );

echo '
				</label>
			</div>

			<div class="rightcol">
				<label>';

		include_once 'graph/php-ofc-library/open_flash_chart_object.php';
		open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-system-cpuusage-data.php?system=$param1", false );

echo '
				</label>
			</div>
		</fieldset>
	</div>
</div>
';

echo '
<h3 class="BigTitle"><span class="ColoredTxt">Andutteye</span> Information</h3>
	<h3 class="toggler">
         <img src="themes/' . $authNamespace->andutteye_theme . '/group_docu.png" alt="" title="" /><span class="InfoTitle"> SYSTEM INFORMATION</h3>
            <div class="element">
			<table>
				<th>System information</th>
				</tr>
                                <td>
                                 <img src="themes/' . $authNamespace->andutteye_theme . '/edit.png" alt="" title="" />
                                        <a href="index.php?main=change_systeminformation&param1=' . $param1 . '">[Edit]</a> ' . $res->system_information . '
                                </td>
				</tr>
			</table>
            </div>

	<h3 class="toggler">
	 <img src="themes/' . $authNamespace->andutteye_theme . '/group_docu.png" alt="" title="" /><span class="InfoTitle"> GROUP DOCUMENTATION (<span class="ColoredTxt">' . $nrgd . '</span>)</span></h3>
	<div class="element">
			<table>
				<th>Name</th>
				<th>Type</th>
				<th>Size</th>
				<th>Created</th>
				<th>Remove</th>
				</tr>
		';

                $gsql  = $db->query("select * from andutteye_uploads where group_name ='$res->group_name' or group_name = '$res->group_name' and domain_name = '$res->domain_name' order by content_description asc");

		 while ($row = $gsql->fetch()) {
                        $seqnr                  = $row['seqnr'];
                        $content_description    = $row['content_description'];
                        $content_name           = $row['content_name'];
                        $content_type           = $row['content_type'];
                        $content_size           = $row['content_size'];
                        $created_by             = $row['created_by'];
                        $created_date           = $row['created_date'];
                        $created_time           = $row['created_time'];
                        $upload_type            = $row['upload_type'];
                        $content                = $row['content'];

                        if ($upload_type == "upload") {
				echo '
				<td>
				 <img src="themes/' . $authNamespace->andutteye_theme . '/upload.png" alt="" title="" /> <a href="download.php?seqnr=' . $seqnr . '" class="Tips2" title="File:' . $content_name . ' Contenttype:' . $content_type . ' Size:' . $content_size . ' kb Created by:' . $created_by . ' On:' . $created_date . ' ' . $created_time . '">' . $content_description . '</a>
				</td>
				<td>
				'.$content_type.'
				</td>
				<td>
				'.$content_size.' kb
				</td>
				<td>
				'.$created_date.'
				</td>
				';
			
				if(verify_role_object_permission($param3,'group',3,'0','0')) {
					echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" /> <a href="index.php?main=remove_documentation&param1=' . $seqnr . '&param2=' . $param1 . '" onclick="return confirm(\'Remove content ' . $content_description . '?\')">Remove</a></td></tr>';
				} else {
						
					echo '<td>-</td></tr>';
				}
                        } else {
				echo '
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme. '/link.png" alt="" title="" /> <a href="' . $content . '" class="Tips2" title="File:' . $content_name . ' Contenttype:' . $content_type . ' Size:' . $content_size . ' kb Created by:' . $created_by . ' On:' . $created_date . ' ' . $created_time . '">' . $content_description . '</a>
				</td>
				<td>
				'.$content_type.'
				</td>
				<td>
				-
				</td>
				<td>
				'.$created_date.'
				</td>';

				if(verify_role_object_permission($param3,'group',3)) {
					echo '<td><img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" /> <a href="index.php?main=remove_documentation&param1=' . $seqnr . '&param2=' . $param1 . '" onclick="return confirm(\'Remove content ' . $content_description . '?\')">Remove</a></td></tr>';
				} else {
					echo '<td>-</td></tr>';
				}
                        }
                }

echo '</table></div>';


echo '<h3 class="toggler">
	<img src="themes/' . $authNamespace->andutteye_theme . '/sys_doc.png" alt="" title="" /><span class="InfoTitle"> SYSTEM DOCUMENTATION (<span class="ColoredTxt">' . $nrsd . '</span>)</span></h3>
	<div class="element">
			<table>
				<th>Name</th>
				<th>Type</th>
				<th>Size</th>
				<th>Created</th>
				<th>Remove</th>
				</tr>
		';

                $gsql = $db->query("select * from andutteye_uploads where system_name = '$param1' order by content_description asc");

		 while ($row = $gsql->fetch()) {
                        $seqnr                  = $row['seqnr'];
                        $content_description    = $row['content_description'];
                        $content_name           = $row['content_name'];
                        $content_type           = $row['content_type'];
                        $content_size           = $row['content_size'];
                        $created_by             = $row['created_by'];
                        $created_date           = $row['created_date'];
                        $created_time           = $row['created_time'];
                        $upload_type            = $row['upload_type'];
                        $content                = $row['content'];

                        if ($upload_type == "upload") {
				echo '
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/upload.png" alt="" title="" /> <a href="download.php?seqnr=' . $seqnr . '" class="Tips2" title="File:' . $content_name . ' Contenttype:' . $content_type . ' Size:' . $content_size . ' kb Created by:' . $created_by . ' On:' . $created_date . ' ' . $created_time . '">' . $content_description . '</a>
				</td>
				<td>' . $content_type . '</td>
				<td>' . $content_size . ' kb</td>
				<td>' . $created_date . '</td>
				';

				if(verify_role_object_permission($param1,'system',3,'0','0')) {
					echo '<td>
						<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" /> <a href="index.php?main=remove_documentation&param1=' . $seqnr . '&param2=' . $param1 . '" onclick="return confirm(\'Remove content ' . $content_description . '?\')">Remove</a>
						</td></tr>';
				} else {
					echo '<td>-</td></tr>';
				}
                        } else {
				echo '
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/link.png" alt="" title="" /> <a href="' . $content . '" class="Tips2" title="File:' . $content_name . ' Contenttype:' . $content_type . ' Size:' . $content_size . ' kb Created by:' . $created_by . ' On:' . $created_date . ' ' . $created_time . '">' . $content_description . '</a></td>
				<td>' . $content_type . '</td>
				<td>-</td>
				<td>' . $created_date . '</td>
				';
				if(verify_role_object_permission($param1,'system',3)) {
					echo '<td>
						<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" /> <a href="index.php?main=remove_documentation&param1=' . $seqnr . '&param2=' . $param1 . '" onclick="return confirm(\'Remove content ' . $content_description . '?\')">Remove</a>
						</td></tr>';
				} else {
					echo '<td>-</td></tr>';
				}
                        }

                }
echo '</table></div>';

echo '<h3 class="toggler">
	<img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> SYSTEM TRANSFER FILES</span></h3>
	<div class="element">
			<table>
				<th>Transferobject</th>
				<th>Load</th>
				</tr>';

	if(is_dir("$Transfer_dir_location/$param1")) {
		$command   = `ls $Transfer_dir_location/$param1`;
		$files     = explode ("\n", $command);
        		foreach($files as $i) {
					if ($i != "") {
						$filename="$Transfer_dir_location/$param1/$i";

						echo '
						<form action="upload-transfer.php" method="post">
							<input type="hidden" name="file" value="' . $filename . '">
							<input type="hidden" name="system_name" value="' . $param1 . '">
							<td>
							<img src="themes/' . $authNamespace->andutteye_theme . '/transfer_f.png" alt="" title="" /> Incoming transfer file ' . $i . ' from system ' . $param1 . '
							</td>
							<td>
							<input class="button" type="submit" value="Load file connected to ' . $param1 . '">
							</td>
							</tr>
						</form>';
					}
                	}
	}
		
echo '</table></div>';

echo '<h3 class="toggler">
	<img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> SYSTEM SNAPSHOT</span></h3>
	<div class="element">
		<table>';

		// FIX
		//$sql = $db->select();
		//$sql->from('andutteye_snapshot', '*');
		//$select->where('system_name = ?', thundera);

		//$sql->order('seqnr');
		//$sql->limit(0, 1);
		//$res = $db->fetchAll($sql);

		$sql = $db->query("select * from andutteye_snapshot where system_name = '$param1' order by seqnr desc limit 0,1");
		$res = $sql->fetchObject();

		$formatted = preg_split("/;;;;/", "$res->users");
			echo '<th>User snapshot ' . $res->created_date . ' ' . $res->created_time . '</th></tr><td>';

		foreach($formatted as $i) {
				echo "$i </ br>";
		}
		echo '</td></tr>';

		$formatted = preg_split("/;;;;/", "$res->fs");

			echo '<th>Filesystem snapshot ' . $res->created_date . ' ' . $res->created_time . '</th></tr><td>';

		foreach($formatted as $i) {
        		echo "$i <br />";
		}
		echo '</td></tr>';

		$formatted = preg_split("/;;;;/", "$res->procs");

			echo '<th>Process snapshot ' . $res->created_date . ' ' . $res->created_time . '</th></tr><td>';

		foreach($formatted as $i) {
        		echo "$i <br />";
		}
		echo '</td></tr>';

		$formatted = preg_split("/;;;;/", "$res->net");

		echo '<th>Netactivity snapshot ' . $res->created_date . ' ' . $res->created_time . '</th></tr><td>';

		foreach($formatted as $i) {
        		echo "$i <br />";
		}
		echo '</td></tr>';

		$formatted = preg_split("/;;;;/", "$res->hardware");

		echo '<th>Hardware snapshot ' . $res->created_date . ' ' . $res->created_time . '</th></tr><td>';

		foreach($formatted as $i) {
        		echo "$i <br />";
		}
		echo '</td></tr>';

echo'</table></div>';

echo '<h3 class="toggler">
	<img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> ASSETMANAGEMENT INFORMATION</span></h3>
	<div class="element">
		<table>
		<th>Assetmanagement</th>
		<th>Result</th>
		</tr>';

		$sql    = $db->query("select distinct(assetmanagementname) from andutteye_assetmanagement where system_name = '$param1' order by assetmanagementname asc");
               	while ($row = $sql->fetch()) {
                        	$assetmanagementname   = $row['assetmanagementname'];

		     $subsql    = $db->query("select * from andutteye_assetmanagement where system_name = '$param1' and assetmanagementname = '$assetmanagementname' order by seqnr asc limit 0,1");
                	while ($row = $subsql->fetch()) {
                        	$assetmanagementresult = $row['assetmanagementresult'];
				echo '
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/revision_1.png" alt="" title="" /> <a href="index.php?main=assetmanagement_revision&param1=' . $param1 . '&param2=' . $assetmanagementname . '">[History]</a> ' . $assetmanagementname . '
					</td>
                			<td>' . $assetmanagementresult . '</td>
					</tr>
				';
			}
		}
		$sql = $db->query("select * from andutteye_snapshot where system_name = '$param1' order by seqnr desc limit 0,1");
		$res = $sql->fetchObject();

		$formatted = preg_split("/;;;;/", "$res->hardware");

		echo '
		<th colspan="2">Hardware pcibus profile</th>
		</tr>';

		foreach($formatted as $i) {
        		echo "<td colspan='2'>$i</td></tr>";
		}

echo '</table></div>';

echo '<h3 class="toggler">
	<img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> SOLUTIONS and CHANGEEVENTS</span></h3>
	<div class="element">
			<table>
				<th>Solution</th>
				<th>Severity</th>
				<th>Creator</th>
				</tr>';

                        $sql = $db->query("select * from andutteye_changeevent where system_name = '$param1'");
			while ($row = $sql->fetch()) {
                        	$system_name  = $row['system_name'];
                        	$description  = $row['description'];
                        	$information  = $row['information'];
                        	$solution     = $row['solution'];
                        	$workaround   = $row['workaround'];
                        	$information  = $row['information'];
                        	$severity     = $row['severity'];
                        	$created_date = $row['created_date'];
                        	$created_time = $row['created_time'];
                        	$created_by   = $row['created_by'];

				echo '
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/changeevent_1.png" alt="" title="" /> <a href="#" class="Tips2" title="Information:' . $information . ' Solution:' . $solution . ' Workaround:' . $workaround . ' Created:' . $created_date . ' ' . $created_time . ' by:' . $created_by . '">' . $description . '</a>
				</td>
				<td>' . $severity . '</td>
				<td>' . $created_by . '</td>
				</tr>
				';

                	}

echo '	</table>
      </div>
';
echo '<div class="DivSpacer"></div>';

// End of subfunction
}

function show_server_transactionlog($param1,$param2,$param3) {

verify_if_user_is_logged_in();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');


echo "
<div id='content'>
        <hr id='groupoverview'>
                <div class='section demo'>
                <h2>Server transactionlog</h2>

                <h3>System information</h3>
                <div class='leftcol'>
                       <label>Server adresses localhost</label>
                </div>
                <div class='rightcol'>
                       <label>Server ports 32000</label>
                </div>

                <div>
                <label>";

                include("/andutteye/graph/graph-serverlog.php");

                echo "
                </label>
                <br />
                ";


                echo "
                </div>
                <h2>Transactions</h2>";

                $sql    = $db->query("select * from andutteye_serverlog order by seqnr desc limit 100");

                while ($row = $sql->fetch()) {
                        $system_name = $row['system_name'];
                        $messagetype = $row['messagetype'];
                        $logentry    = $row['logentry'];
                        $created_date= $row['created_date'];
                        $created_time= $row['created_time'];

                	echo "
			<div class='table'>
			<div class='column'><label>$system_name</label></div>
                        <div class='column'><label>$created_date</label></div>
                        <div class='column'><label>$created_time</label></div>
                        <div class='column'><label>$messagetype</label></div>
                        <div class='column40'><label>$logentry</label></div>
			</div>
			";
                }
                echo "
                </div>
        </div>
</div>
";

// End of subfunction
}


function create_role($param1,$param2) {

verify_if_user_is_logged_in();
verify_if_user_have_admin_prevs();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param1) {
        $date = date("20y-m-d");
        $time = date("H:m:s");

        $sql   = $db->query("select seqnr from andutteye_roles where rolename ='$param1'");
        $exists = $sql->fetchAll();
        $exists = count($exists);

        if($param1 != "" && $param2 != "" && $exists == 0) {
                $data = array(
                        'rolename'       => "$param1",
                        'description'    => "$param2",
                        'created_by'     => "$authNamespace->andutteye_username",
                        'created_date'   => "$date",
                        'created_time'   => "$time"
                );
                $db->insert('andutteye_roles', $data);
        }
}

echo '<div id="content">
		<div class="content">
			<fieldset class="GroupField">
				<legend><img src="themes/' . $authNamespace->andutteye_theme . '/role_b.png" alt="" title="" /><span class="BigTitle"><span class="ColoredTxt">Create</span> New Role</span></legend>

				<form method="get" action="index.php">
					<input type="hidden" name="main" value="create_role">

						<label>Rolename</label>
						<label><input type="text" name="param1" size="35" maxlength="255" value=""></label>
						<label>Roledescription</label>
						<label><input type="text" name="param2" size="35" maxlength="255" value=""></label>

						<label><input type="submit" value="Submit"></label>
				</form>

</fieldset>

		</div>

		<br />

<div class="content">
	<fieldset class="GroupField">
		<legend><span class="BigTitle"><span class="ColoredTxt">Change</span> Current Roles</span></legend>
	
			<table>
				<th>Role</th>
				<th>Action</th>
				</tr>';

			$sql = $db->query("select * from andutteye_roles order by rolename asc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $rolename = $row['rolename'];
                                $description = $row['description'];

			echo '
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/role_1.png" alt="" title="" />
				<a href="index.php?main=change_role_permissions&param1=' . $rolename . '" class="Tips2" title="Rolename:' . $rolename . ' Description:' . $description . '">' . $rolename . '</a>
				</td>
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />
				<a href="index.php?main=remove_role&param1=' .$seqnr . '" onclick="return confirm(\'Remove role ' . $rolename . '?\')">Remove</a>
				</td>
				</tr>
			';

                        }



echo "
</table>
</fieldset>
</div>
";


// End of subfunction
}

function create_user($param1,$param2,$param3,$param4,$param5,$param6) {

verify_if_user_is_logged_in();
verify_if_user_have_admin_prevs();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');


if($param1) {
	$date = date("20y-m-d");
	$time = date("H:m:s");
	$password  = sha1($password_slt . $param2);

        $sql   = $db->query("select seqnr from andutteye_users where andutteye_username ='$param1'");
        $exists = $sql->fetchAll();
        $exists = count($exists);

	if($param1 != "" && $param2 != "" && $exists == 0) {
		$data = array(
    			'andutteye_username'    => "$param1",
    			'andutteye_password'	=> "$password",
    			'andutteye_role'     	=> "$param3",
    			'user_description'     	=> "$param4",
    			'andutteye_theme'     	=> "$param5",
    			'is_admin'     		=> "$param6",
			'nr_of_loggins'     	=> "0",
    			'created_by'        	=> "$authNamespace->andutteye_username",
    			'created_date'      	=> "$date",
    			'created_time'      	=> "$time"
		);
		$db->insert('andutteye_users', $data);
	}
}

echo '<div id="content">
		<div class="content">
			<fieldset class="GroupField">
				<legend><img src="themes/' . $authNamespace->andutteye_theme . '/user_b.png" alt="" title="" /><span class="BigTitle"><span class="ColoredTxt">Create</span> New User</span></legend>
				
				<form method="get" action="index.php">
					<input type="hidden" name="main" value="create_user">

						<label for="domainname">Username:</label>
						<label><input tabindex="1" class="med" type="text" name="param1" id="param1" size="35" maxlength="255" value=""></label>
						<label> Userdescription:</label>
						<label><input tabindex="2" class="med" type="text" name="param4" id="param4" size="35" maxlength="255" value=""></label>
						<label> Password:</label>
						<label><input type="password" name="param2" size="35" maxlength="255" value=""></label>

						<label>Select usertheme</label>
						<label>
							<select name="param5" style="WIDTH: 260px">
';

                			$command = `ls themes`;
                			$themes  = explode ("\n", $command);

                        		foreach($themes as $i) {
                                		if ($i != "") {
echo '
												<option value="' . $i . '">' . $i . '
';
                                		}
                        		}

echo '
							</select>
						</label>

						<label>Select userrole</label>
						<label>
							<select name="param3" style="WIDTH: 260px">
';

					$sql    = $db->query("select distinct(rolename) from andutteye_roles order by rolename asc");

                 			while ($row = $sql->fetch()) {
                        			$rolename = $row['rolename'];
echo '
								<option value="' . $rolename . '"> ' . $rolename . '
';
                  			}

echo '
							</select>
						</label>
						<label>Administrator privileges</label>
						<label><select name="param6" style="WIDTH: 260px">
							<option value="0"> No
							<option value="1"> Yes
							</select>
						</label>

						<label><input type="submit" value="Submit"></label>

				</form>

</fieldset>

		</div>

		<br />

<div class="content">
	<fieldset class="GroupField">
		<legend><span class="BigTitle"><span class="ColoredTxt">Change</span> Current Users</span></legend>

			<table>
				<th>User</th>
				<th>Action</th>
				</tr>';

			$sql    = $db->query("select * from andutteye_users order by andutteye_username asc");
                	while ($row = $sql->fetch()) {
                        	$seqnr = $row['seqnr'];
                        	$andutteye_username = $row['andutteye_username'];
                        	$user_description = $row['user_description'];
                        	$andutteye_theme = $row['andutteye_theme'];
                        	$andutteye_role = $row['andutteye_role'];

				echo '<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/user_1.png" alt="" title="" />
					<a href="#" class="Tips2" title="User:' . $andutteye_username . ' Description:' . $user_description . ' Theme:' . $andutteye_theme . ' Role:' . $andutteye_role . '">' . $andutteye_username . '</a>
					</td>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />
					<a href="index.php?main=remove_user&param1=' . $seqnr . '" onclick="return confirm(\'Remove user ' . $andutteye_username . '?\')">Remove</a>
					</td>
				</tr>
';
					
			}

echo '
</table>
</fieldset>
</div>
';

//End of subfunction
}

function show_software_profile($param1) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$count = $db->query("select seqnr from andutteye_software where system_name ='$param1' and status = 'CURRENT'");
$count = $count->fetchAll();
$count = count($count);

echo '<div id="content">
	<div class="section content">
		<h2 class="BigTitle"><span class="ColoredTxt">Software</span> Profile for ' . $param1 . '</h2>


<fieldset class="GroupField">
	<legend>Packages Installed</legend>
	<label>' . $count . ' packages currently installed</label>

</fieldset>

		<label>
			<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" />
			<a href="index.php?main=system_overview&param1=' . $param1 . '">&nbsp;Back to ' . $param1 . ' system overview</a>
		</label>

<div class="DivSpacer"></div>
	<fieldset class="GroupField">
		<h2 class="BigTitle"><span class="ColoredTxt">Package</span> View</h2>
		<table>
			<th>Package</th>
			<th>Version</th>
			<th>Release</th>
			<th>Archtype</th>
			<th>Status</th>
		</tr>
';

$sql    = $db->query("select * from andutteye_software where system_name = '$param1' order by aepackage asc, status desc");
                while ($row = $sql->fetch()) {
                        $seqnr       = $row['system_name'];
                        $system_name = $row['system_name'];
                        $aepackage   = $row['aepackage'];
                        $aeversion   = $row['aeversion'];
                        $aerelease   = $row['aerelease'];
                        $aearchtype  = $row['aearchtype'];
                        $packagetype = $row['packagetype'];
                        $status      = $row['status'];
                        $created_date = $row['created_date'];
                        $reported_date      = $row['reported_date'];

			$subsql = $db->query("select * from andutteye_bundles where aepackage = '$aepackage' and aearch = '$aearchtype'");
                        $res    = $subsql->fetchAll();


			echo '
				<td>
						<a href="#" class="Tips2" title="Package included in bundle:' . $res->bundle . ' Created date:' . $created_date . ' Last verified date:' . $reported_date . '">' . $aepackage . '</a>
					</td>
				<td>' . $aeversion . '</td>
				<td>' . $aerelease . '</td>
				<td>' . $aearchtype . '</td>
			';

				if($status == "DELETED") {
					echo '
					<td>
						<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />
					</td>
					';
				} else {
					echo '
					<td>' . $status . '</td>
					';
				}
		echo '</tr>';
                }

echo '
</table>
</fieldset>

		</div>
	</div>
';

// End of subfunction
}

function show_server_transactions($param1,$param2) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

echo '<div id="content">
	<h2 class="BigTitle">' . $param1 . ' Server <span class="ColoredTxt">Transactions</span></h2>
		<fieldset class="GroupField">
			<legend>Transactions</legend>';
echo '<label>';
	include_once 'graph/php-ofc-library/open_flash_chart_object.php';
	open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-serverlog-data.php?system=$param1", false );
echo '</label>
</fieldset>
';

echo '<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> Select other dates</span></h3>
	<div class="element">
		<table>
			<th>Transaction dates</th>
		</tr>';

                $sql = $db->query("select distinct created_date from andutteye_serverlog where system_name = '$param1' order by seqnr desc limit 0,100");
                while ($row = $sql->fetch()) {
                        $created_date = $row['created_date'];
			echo '
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" />&nbsp;
					<a href="index.php?main=show_server_transactions&param1=' . $param1 . '&param2=' . $created_date . '">' . $created_date . '</a>
				</td>
				</tr>
			';
                }

echo '</table>
    </div> 
     <h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> Server transactions('.$param2.')</span></h3>
	 <div class="element">';
echo '
<br>
<fieldset class="GroupField">
	<legend>Incoming agent requests to Andutteye services</legend>
	
	<table>
		<th>Time</th>
		<th>Date</th>
		<th>Msgtype</th>
		<th width="70%">Message</th>
		<th>System</th>
	</tr>';

if($param2) {
	$sql    = $db->query("select * from andutteye_serverlog where system_name = '$param1' and created_date = '$param2' order by seqnr desc limit 100");
} else {
	$sql    = $db->query("select * from andutteye_serverlog where system_name = '$param1' order by seqnr desc limit 100");
}
	while ($row = $sql->fetch()) {
                        $system_name = $row['system_name'];
                        $messagetype = $row['messagetype'];
                        $logentry    = $row['logentry'];
                        $created_date= $row['created_date'];
                        $created_time= $row['created_time'];

		echo '
		      <td>' . $created_time . '</td>
		      <td>' . $created_date . '</td>
		      <td>' . $messagetype . '</td>
		      <td>' . $logentry . '</td>
		      <td>' . $system_name . '</td>
		</tr>
		';
	}

echo '
	</table>
</fieldset>
</div>

<div class="ClearFloat"></div>
	<br />
	<label><img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" /><a href="index.php?main=system_overview&param1=' . $param1 . '">&nbsp;Back to ' . $param1 . ' system overview</a></label>
';
echo '
	</div>
</div>
';

// End of subfunction
}



function change_alarm($param1,$param2,$param3,$param4,$param5) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select * from andutteye_alarm where seqnr = '$param1'");
$res = $sql->fetchObject();

if($param2) {
	if($param4 == "this") {
		$sql = "update andutteye_alarm set status = '$param2' where seqnr = '$param1'";
        	$db->query($sql);
	}
	if($param4 == "system") {
		$sql = "update andutteye_alarm set status = '$param2' where system_name = '$param5'";
        	$db->query($sql);
	}
	if($param4 == "all") {
		$sql = "update andutteye_alarm set status = '$param2' where status != 'CLOSED'";
        	$db->query($sql);
	}

	if($param3 == "Yes") {
		header("Location:index.php?main=create_changeevent&param1=$param1");
		exit;
	} else {
		header("Location:index.php?main=monitoring_front&param1=$res->system_name");
		exit;
	}
}

echo '<div id="content">
	<div class="section content">
		<h2 class="BigTitle"><span class="ColoredTxt">Alarm Details</span> on Alarm ' . $param1 . '</h2>

<fieldset class="GroupField">
	<legend>Alarm Information</legend>
			<div class="leftcol">
				<label>System ' . $res->system_name . '</label>
				<label>Repeatcount ' . $res->repeatcount . '</label>
				<label>Severity ' . $res->severity . '</label>
				<label>Alarm last date registered ' . $res->lastdate . '</label>
			</div>
			<div class="rightcol">
				<label>Status ' . $res->status . '</label>
				<label>Alarm first date registered ' . $res->created_date . '</label>
				<label>Alarm first time registered ' . $res->created_time . '</label>
				<label>Alarm last time registered ' . $res->lasttime . '</label>
			</div>

			<div>
				<label>Shortinformation ' . $res->shortinformation . '</label>
				<label>Longinformation ' . $res->longinformation . '</label>
';
		
			include_once 'graph/php-ofc-library/open_flash_chart_object.php';
			open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-alarm-trend-data.php?system=$res->system_name&alarm=$res->shortinformation", false );

echo '
			</div>
</fieldset>

<fieldset class="GroupField">
	<legend>Monitorstatus information</legend>';

	$subsql = $db->query("select * from andutteye_monitor_status where monitortype = '$res->monitortype' and monitorname = '$res->monitor' and system_name = '$res->system_name' order by seqnr desc limit 0,5");
        while ($row = $subsql->fetch()) {
        	$system_name   = $row['system_name'];
                $monitorname   = $row['monitorname'];
                $monitortype   = $row['monitortype'];
                $monitormessage   = $row['monitormessage'];
                $monitorstatus   = $row['monitorstatus'];
                $created_date   = $row['created_date'];
                $created_time   = $row['created_time'];
                $number_ok   = $row['number_ok'];
                $lastdate_ok   = $row['lastdate_ok'];
                $lasttime_ok   = $row['lasttime_ok'];
                $number_notok   = $row['number_notok'];
                $lastdate_notok   = $row['lastdate_notok'];
                $lasttime_notok   = $row['lasttime_notok'];

               echo "<div class='table'>";
               echo "<div class='column'><label>$monitortype</label></div>";
               echo "<div class='column'><label>$monitorstatus</label></div>";
               echo "<div class='column40'><label><a href=''  class='Tips2' title='Monitor:$monitorname Type:$monitortype Status:$monitorstatus NrOk:$number_ok LastdateOk:$lastdate_ok LasttimeOk:$lasttime_ok NrNotOk:$number_notok LastdateNotOk:$lastdate_notok LasttimeNotOk:$lasttime_notok'>$monitormessage</a></label></div>";
                echo "<div class='column'><label>$created_date</label></div>";
                echo "<div class='column'><label>$created_time</label></div>";
         	echo "</div>";
         }

echo '
</fieldset>
<fieldset class="GroupField">
        <legend>Possible solutions</legend>';

	  $sql = $db->query("select * from andutteye_changeevent where shortinformation = '$res->shortinformation' and monitortype = '$res->monitortype' order by seqnr desc");
          while ($row = $sql->fetch()) {
          	$description            = $row['description'];
                $shortinformation       = $row['shortinformation'];
                $solution               = $row['solution'];
                $workaround             = $row['workaround'];
		
		echo '<label>
                      <img src="themes/' . $authNamespace->andutteye_theme . '/changeevent_1.png" alt="" title="" />
                      <a href="#" class="Tips2" title="Shortinformation:' . $shortinformation . ' Solution:' . $solution . ' Workaround:' . $workaround . '">' . $description . '</a>
                      </label>';
         }


echo '</fieldset>
<fieldset class="GroupField">
	<legend>Alarm Settings</legend>
			<form method="get" action="index.php">

				<div class="leftcol">
					<input type="hidden" name="main" value="change_alarm">
					<input type="hidden" name="param1" value="' . $param1 . '">
					<input type="hidden" name="param5" value="' . $res->system_name . '">


					<h3 class="InfoTitle">Status:</h3>
					<label><input type="radio" name="param2" value="ACK"> Acknowledge alarm</label>
					<label><input type="radio" checked="close" name="param2" value="FREEZE"> Freeze alarm</label>
					<label><input type="radio" name="param2" value="CLOSED"> Close alarm</label>
				</div>

				<div class="rightcol">

					<h3 class="InfoTitle">Action:</h3>
					<label><input type="radio" checked="This" name="param4" value="this"> This alarm</label>
					<label><input type="radio" name="param4" value="system"> All alarms for ' . $res->system_name . '</label>
					<label><input type="radio" name="param4" value="all"> All alarms on all systems</label>
				</div>

				<div class="ClearFloat"></div>
				<br />
				<h3 class="InfoTitle">Save solution on this alarm:</h3>
				<label><input type="radio" checked="save" name="param3" value="Yes"> Yes save solution</label>
				<label><input type="radio" name="param3" value="No"> No dont save solution</label>
				
				<br />
				<h3 class="InfoTitle">Submit changes:</h3>
				<label><input class="button" type="submit" " value="Submit"></label>

			</form>
	</fieldset>';
		
echo '
			<label>
				<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" />
				<a href="index.php?main=monitoring_front&param1=' . $res->system_name . '">&nbsp;Back to monitoring front</a>
			</label>
			
		</div>
	</div>
';

// End of subfunction
}



function system_configuration($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12,$param13,$param14,$param15,$param16,$param17,$param18,$param19,$param20,$param21,$param22,$param23,$param24,$param25) {


verify_if_user_is_logged_in();

if(!verify_role_object_permission($param1,'system',3)) {
        // Verify if system is allowed to be read, write and deleted.
        header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20access%20configuration%20for%20this%20system");
        exit;
}

require 'db.php';

require_once 'Zend/Session/Namespace.php';
$date = date("20y-m-d");
$time = date("H:m:s");
$authNamespace = new Zend_Session_Namespace('Zend_Auth');


if ($param2 == "override") {
	$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and seqnr = '$param3'");
	$res = $sql->fetchObject();

	if("$res->override" == "yes") {
		$sql = "update andutteye_monitor_configuration set override = 'no', created_date = '$date', created_time = '$time', created_by = '$authNamespace->andutteye_username' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}else {
		$sql = "update andutteye_monitor_configuration set override = 'yes', created_date = '$date', created_time = '$time', created_by = '$authNamespace->andutteye_username' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}

}
elseif($param2 == "delete") {
	$sql = "delete from andutteye_monitor_configuration where system_name = '$param1' and seqnr = '$param3'";
       	$db->query($sql);
}
elseif($param2 == "baseconfiguration") {
	$sql = "update andutteye_base_agentconfiguration set underchange = 'yes' where system_name = '$param1'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param3' where system_name = '$param1' and parametername = 'Server_listen_adress '";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param4' where system_name = '$param1' and parametername = 'Server_listen_port'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param5' where system_name = '$param1' and parametername = 'Enable_software_inventory'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param6' where system_name = '$param1' and parametername = 'Enable_package_update'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param7' where system_name = '$param1' and parametername = 'Enable_config_update'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param8' where system_name = '$param1' and parametername = 'Enable_ssl_encryption'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param9' where system_name = '$param1' and parametername = '$Use_ssl_server_key'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param10' where system_name = '$param1' and parametername = 'Use_hooks_directory'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param11' where system_name = '$param1' and parametername = 'Log_dir_location'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param12' where system_name = '$param1' and parametername = 'Cache_dir_location'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param13' where system_name = '$param1' and parametername = 'Api_dir_location'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param14' where system_name = '$param1' and parametername = 'Transfer_dir_location'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param15' where system_name = '$param1' and parametername = 'Bin_dir_location'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param16' where system_name = '$param1' and parametername = 'Use_mail_from_adress'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param17' where system_name = '$param1' and parametername = 'Use_smtp_server'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param18' where system_name = '$param1' and parametername = 'Use_mail_body'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param19' where system_name = '$param1' and parametername = 'Use_mail_subject'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param20' where system_name = '$param1' and parametername = 'Enable_daemon_mode'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param21' where system_name = '$param1' and parametername = 'Use_debug_level'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param22' where system_name = '$param1' and parametername = 'Loop_interval'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param23' where system_name = '$param1' and parametername = 'Enable_autoclose_alarms'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param24' where system_name = '$param1' and parametername = 'Enable_syslog_notification'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set parametervalue = '$param25' where system_name = '$param1' and parametername = 'Use_api'";
        $db->query($sql);

	$sql = "update andutteye_base_agentconfiguration set created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time' where system_name = '$param1'";
        $db->query($sql);

}
elseif($param2 == "saveconfig") {

	 $data = array(
                'system_name'      => "$param1",
                'parametername'    => "Install_New_Agent_Configuration",
                'parametervalue'   => "yes",
                'created_date'     => "$date",
                'created_time'     => "$time",
                'created_by'       => "$authNamespace->andutteye_username"
         );
         $db->insert('andutteye_base_agentconfiguration', $data);

	$sql = "update andutteye_base_agentconfiguration set underchange = 'no' where system_name = '$param1'";
        $db->query($sql);

	$sql = "update andutteye_monitor_configuration set underchange = 'no' where system_name = '$param1'";
        $db->query($sql);

}
elseif($param2 == "savemonitor") {

	if($param4 == "PS") {
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', status = '$param5', severity = '$param6', alarmlimit = '$param7', errorlimit = '$param8', message='$param9', schedule = '$param10', sendemail = '$param11', runprogram = '$param12', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
	if($param4 == "LA") {
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', status = '$param5', severity = '$param6', alarmlimit = '$param7', errorlimit = '$param8', message='$param9', schedule = '$param10', sendemail = '$param11', runprogram = '$param12', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time', monitorname = '$param13' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
	if($param4 == "MA") {
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', status = '$param5', severity = '$param6', alarmlimit = '$param7', errorlimit = '$param8', message='$param9', schedule = '$param10', sendemail = '$param11', runprogram = '$param12', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time', monitorname = '$param13' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
	if($param4 == "SA") {
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', status = '$param5', severity = '$param6', alarmlimit = '$param7', errorlimit = '$param8', message='$param9', schedule = '$param10', sendemail = '$param11', runprogram = '$param12', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time', monitorname = '$param13' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
	if($param4 == "FM") {
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', status = '$param5', severity = '$param6', alarmlimit = '$param7', errorlimit = '$param8', message='$param9', schedule = '$param10', sendemail = '$param11', runprogram = '$param12', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
	if($param4 == "EV") {
		if(!$param14) {
			$param14="0";
		}
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', status = '$param5', severity = '$param6', alarmlimit = '$param7', errorlimit = '$param8', message='$param9', schedule = '$param10', sendemail = '$param11', runprogram = '$param12', programargs = '$param13', exitstatus = '$param14', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
	if($param4 == "FT") {
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', status = '$param5', severity = '$param6', alarmlimit = '$param7', errorlimit = '$param8', message='$param9', schedule = '$param10', sendemail = '$param11', runprogram = '$param12', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time', searchpattern = '$param13' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
	if($param4 == "AM") {
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', monitorname = '$param5', program = '$param6', programargs = '$param7', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
	if($param4 == "ST") {
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', monitorname = '$param5', program = '$param6', programargs = '$param7', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
	if($param4 == "FS") {
		$sql = "update andutteye_monitor_configuration set underchange = 'yes', status = '$param5', alarmlimit = '$param7', errorlimit = '$param8', message='$param9', schedule = '$param10', sendemail = '$param11', runprogram = '$param12', created_by = '$authNamespace->andutteye_username', created_date = '$date', created_time = '$time', warninglimit = '$param13', criticallimit = '$param14', fatallimit = '$param15' where system_name = '$param1' and seqnr = '$param3'";
        	$db->query($sql);
	}
}
elseif($param2 == "settings") {

        $sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and seqnr = '$param3'");
        $res = $sql->fetchObject();

	if($res->monitortype == "EV") {
		echo "
                <div id='content'>
                        <div class='section content'>

			<fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Change $res->monitortype monitor $res->monitorname</span> on $param1 </span></legend>

                        ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='system_configuration'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>
                <input type='hidden' name='param3' value='$param3'>
                <input type='hidden' name='param4' value='$res->monitortype'>

                <label>Status  (Choosen now:$res->status)</label>

                <label>
                <select name='param5' style='WIDTH: 260px'>
                                <option value='up'> Up
                                <option value='down'> Down
                </select>
                </label>

                <label>Severity (Choosen now:$res->severity) </label>
                <label>
                <select name='param6' style='WIDTH: 260px'>
                                <option value='HARMLESS'> Harmless
                                <option value='WARNING'> Warning
                                <option value='CRITICAL'> Critical
                                <option value='FATAL'> Fatal
                </select>
                </label>
			
		<label>Alarmlimit  (Choosen now:$res->alarmlimit)</label>
                <label><select name='param7' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>

                <label>Errorlimit  (Choosen now:$res->errorlimit)</label>
                <label><select name='param8' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
                <label>Message</label>
                <label><input type='text' name='param9' maxlength='255' value='$res->message' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param10' maxlength='255' value='$res->schedule' size='70'></label>

                <h3>Recovery actions</h3>
                <label>Send email</label>
                <label><input type='text' name='param11' maxlength='255' value='$res->sendemail' size='70'></label>
                <label>Execute recovery program</label>
                <label><input type='text' name='param12' maxlength='255' value='$res->runprogram' size='70'></label>

		<h3>Monitor settings for $res->monitortype monitor</h3>

                <label>Arguments</label>
                <label><input type='text' name='param13' maxlength='255' value='$res->programargs' size='70'></label>

                <label>Ok exitstatus</label>
                <label><input type='text' name='param14' maxlength='255' value='$res->exitstatus' size='70'></label>

                <label><input class='button' type='submit' value='Submit'></label>
                </form>

                ";
                echo "</fieldset></div></div>";
        }
	if($res->monitortype == "PS" || $res->monitortype == "FM") {
	echo "
		<div id='content'>
                	<div class='section content'>

			<fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Change $res->monitortype monitor $res->monitorname</span> on $param1 </span></legend>

                        ";

		echo "
        	<form method='get' action='index.php'>
                <input type='hidden' name='main' value='system_configuration'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>
                <input type='hidden' name='param3' value='$param3'>
                <input type='hidden' name='param4' value='$res->monitortype'>

                <label>Status  (Choosen now:$res->status)</label>
		
		<label>
		<select name='param5' style='WIDTH: 260px'>
                                <option value='up'> Up
                                <option value='down'> Down
		</select>
		</label>

                <label>Severity (Choosen now:$res->severity) </label>
		<label>
		<select name='param6' style='WIDTH: 260px'>
                                <option value='HARMLESS'> Harmless
                                <option value='WARNING'> Warning
                                <option value='CRITICAL'> Critical
                                <option value='FATAL'> Fatal
		</select>
		</label>

                <label>Alarmlimit  (Choosen now:$res->alarmlimit)</label>
		<label><select name='param7' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
		</select>
		</label>
                
		<label>Errorlimit  (Choosen now:$res->errorlimit)</label>
		<label><select name='param8' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
		</select>
		</label>
	        <label>Message</label>
                <label><input type='text' name='param9' maxlength='255' value='$res->message' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param10' maxlength='255' value='$res->schedule' size='70'></label>

		<h3>Recovery actions</h3>
		<label>Send email</label>
                <label><input type='text' name='param11' maxlength='255' value='$res->sendemail' size='70'></label>
		<label>Execute recovery program</label>
                <label><input type='text' name='param12' maxlength='255' value='$res->runprogram' size='70'></label>
	 	<label><input class='button' type='submit' value='Submit'></label>
		</form>

		";
		echo "</fieldset></div></div>";
	}
	elseif($res->monitortype == "LA" || $res->monitortype == "MA" || $res->monitortype == "SA" || $res->monitortype == "PH") {

		echo "
                <div id='content'>
                        <div class='section content'>

			<fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Change $res->monitortype monitor $res->monitorname</span> on $param1 </span></legend>

                        ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='system_configuration'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>
                <input type='hidden' name='param3' value='$param3'>
                <input type='hidden' name='param4' value='$res->monitortype'>

                <label>Monitorvalue</label>
                <label><input type='text' name='param13' maxlength='255' value='$res->monitorname' size='70'></label>

                <label>Status  (Choosen now:$res->status)</label>

                <label>
                <select name='param5' style='WIDTH: 260px'>
                                <option value='up'> Up
                                <option value='down'> Down
                </select>
                </label>

                <label>Severity (Choosen now:$res->severity) </label>
                <label>
                <select name='param6' style='WIDTH: 260px'>
                                <option value='HARMLESS'> Harmless
                                <option value='WARNING'> Warning
                                <option value='CRITICAL'> Critical
                                <option value='FATAL'> Fatal
                </select>
                </label>

                <label>Alarmlimit  (Choosen now:$res->alarmlimit)</label>
                <label><select name='param7' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
	
		<label>Errorlimit  (Choosen now:$res->errorlimit)</label>
                <label><select name='param8' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
                <label>Message</label>
                <label><input type='text' name='param9' maxlength='255' value='$res->message' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param10' maxlength='255' value='$res->schedule' size='70'></label>

                <h3>Recovery actions</h3>
                <label>Send email</label>
                <label><input type='text' name='param11' maxlength='255' value='$res->sendemail' size='70'></label>
                <label>Execute recovery program</label>
                <label><input type='text' name='param12' maxlength='255' value='$res->runprogram' size='70'></label>
                <label><input class='button' type='submit' value='Submit'></label>
                </form>

                ";
                echo "</fieldset></div></div>";


	}
	 elseif($res->monitortype == "AM" || $res->monitortype == "ST") {

                echo "
                <div id='content'>
                        <div class='section content'>

			<fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Change $res->monitortype monitor $res->monitorname</span> on $param1 </span></legend>

                        ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='system_configuration'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>
                <input type='hidden' name='param3' value='$param3'>
                <input type='hidden' name='param4' value='$res->monitortype'>

                <label>Monitorvalue</label>
                <label><input type='text' name='param5' maxlength='255' value='$res->monitorname' size='70'></label>

                <label>Program</label>
                <label><input type='text' name='param6' maxlength='255' value='$res->program' size='70'></label>

                <label>Arguments</label>
                <label><input type='text' name='param7' maxlength='255' value='$res->programargs' size='70'></label>

	        <label><input class='button' type='submit' value='Submit'></label>
                </form>
                ";
                echo "</fieldset></div></div>";

	}
	elseif($res->monitortype == "FT") {

		 echo "
                <div id='content'>
                        <div class='section content'>

			<fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Change $res->monitortype monitor $res->monitorname</span> on $param1 </span></legend>

                        ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='system_configuration'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>
                <input type='hidden' name='param3' value='$param3'>
                <input type='hidden' name='param4' value='$res->monitortype'>

                <label>Status  (Choosen now:$res->status)</label>

                <label>
                <select name='param5' style='WIDTH: 260px'>
                                <option value='up'> Up
                                <option value='down'> Down
                </select>
                </label>

                <label>Severity (Choosen now:$res->severity) </label>
                <label>
                <select name='param6' style='WIDTH: 260px'>
                                <option value='HARMLESS'> Harmless
                                <option value='WARNING'> Warning
                                <option value='CRITICAL'> Critical
                                <option value='FATAL'> Fatal
                </select>
                </label>

                <label>Alarmlimit  (Choosen now:$res->alarmlimit)</label>
                <label><select name='param7' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>

		<label>Errorlimit  (Choosen now:$res->errorlimit)</label>
                <label><select name='param8' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
                <label>Message</label>
                <label><input type='text' name='param9' maxlength='255' value='$res->message' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param10' maxlength='255' value='$res->schedule' size='70'></label>

                <h3>Recovery actions</h3>
                <label>Send email</label>
                <label><input type='text' name='param11' maxlength='255' value='$res->sendemail' size='70'></label>
                <label>Execute recovery program</label>
                <label><input type='text' name='param12' maxlength='255' value='$res->runprogram' size='70'></label>

                <h3>Search for</h3>
                <label>Regular expression search pattern</label>
                <label><input type='text' name='param13' maxlength='255' value='$res->searchpattern' size='70'></label>

                <label><input class='button' type='submit' value='Submit'></label>
                </form>

                ";
                echo "</fieldset></div></div>";
	}
	elseif($res->monitortype == "FS") {

                 echo "
                <div id='content'>
                        <div class='section content'>

			<fieldset class='GroupField'>
                	<legend><span class='BigTitle'><span class='ColoredTxt'>Change $res->monitortype monitor $res->monitorname</span> on $param1 </span></legend>

			";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='system_configuration'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>
                <input type='hidden' name='param3' value='$param3'>
                <input type='hidden' name='param4' value='$res->monitortype'>

                <label>Status  (Choosen now:$res->status)</label>

                <label>
                <select name='param5' style='WIDTH: 260px'>
                                <option value='up'> Up
                                <option value='down'> Down
                </select>
                </label>

                <label>Alarmlimit  (Choosen now:$res->alarmlimit)</label>
                <label><select name='param7' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>

		<label>Errorlimit  (Choosen now:$res->errorlimit)</label>
                <label><select name='param8' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
                <label>Message</label>
                <label><input type='text' name='param9' maxlength='255' value='$res->message' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param10' maxlength='255' value='$res->schedule' size='70'></label>

                <h3>Recovery actions</h3>
                <label>Send email</label>
                <label><input type='text' name='param11' maxlength='255' value='$res->sendemail' size='70'></label>
                <label>Execute recovery program</label>
                <label><input type='text' name='param12' maxlength='255' value='$res->runprogram' size='70'></label>

                <h3>Filesystem limits</h3>

                <label>Warning limit (Choosen now:$res->warninglimit %)</label>
                <label><select name='param13' style='WIDTH: 260px'>
		";

		for ($i = 0; $i <= 100; $i++) {
			echo "<option value='$i'> $i%";
		}

		echo "</select></label>";

		echo "
		<label>Crtitical limit (Choosen now:$res->criticallimit %)</label>
                <label><select name='param14' style='WIDTH: 260px'>
                ";

                for ($i = 0; $i <= 100; $i++) {
                        echo "<option value='$i'> $i%";
                }
                echo "</select></label>";

		echo "
		<label>Fatal limit (Choosen now:$res->fatallimit %)</label>
                <label><select name='param15' style='WIDTH: 260px'>
                ";

                for ($i = 0; $i <= 100; $i++) {
                        echo "<option value='$i'> $i%";
                }
                echo "</select></label>";

		echo "
		<label><input class='button' type='submit' value='Submit'></label>
                </form>

                ";
                echo "</fieldset></div></div>";

	} else {
		//Nothing
	}

}
elseif($param2 == "disable") {
} else {
	//Nothing
}

echo "
<div id='content'>
    <div class='section content'>

		<fieldset class='GroupField'>
        	<legend><span class='BigTitle'><span class='ColoredTxt'>Andutteye configuration</span> for system  $param1 </span></legend>
		
		<h3>Monitortype graph</h3>";

		include_once 'graph/php-ofc-library/open_flash_chart_object.php';
		open_flash_chart_object( '100%', 350, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-monitortypes.php?system=$param1", false );

		$subsql = $db->query("select seqnr from andutteye_base_agentconfiguration where system_name = '$param1' and underchange = 'yes'");
                $base = $subsql->fetchAll();
                $base = count($base);

		$subsql = $db->query("select seqnr from andutteye_monitor_configuration where system_name = '$param1' and underchange = 'yes'");
                $mon = $subsql->fetchAll();
                $mon = count($mon);
		$total = ($base + $mon);

		if("$total" >  "0") {
		    	echo "<form method='get' action='index.php'>
                             <input type='hidden' name='main' value='system_configuration'>
                             <input type='hidden' name='param1' value='$param1'>
                             <input type='hidden' name='param2' value='saveconfig'>";

			echo "<h3>Pending agentchanges for system $param1</h3>";
			echo "<label><img src='themes/$authNamespace->andutteye_theme/alert.png' alt='' title='' /> New pending agent configuration changes that is needed to be saved on $param1. Press submit to save configuration and initiate a agent configuration regeneration on the system with your new settings included. You can still perform settings and monitor changes. The Andutteye agent will only fetch the changes after you press submit below.</label>
			<label><input class='button' type='submit' value='Submit'></label>
			</form>";
		}

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> Configuration information</span></h3>
	<div class="element">
';
 echo "
<label>Andutteye has a prebuildt monitor framework with defined parameters thar are easely changed by the administrator. The monitorframework can be altered and changed bothon the system on from the webinterface. On completed changes the Andutteye agent will notice the change and implement it on the system. All parameters must have a value, otherhise set it tono.</label>
</div>";

echo '
<!-- start Base configuration slider -->
                        <h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> Base configuration</span></h3>

                                <div class="element">
';

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Server_listen_adress'");
			$Server_listen_adress = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Server_listen_port'");
			$Server_listen_port = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Enable_daemon_mode'");
			$Enable_daemon_mode = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Enable_software_inventory'");
			$Enable_software_inventory = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Enable_package_update'");
			$Enable_package_update = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Enable_config_update'");
			$Enable_config_update = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Enable_ssl_encryption'");
			$Enable_ssl_encryption = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Use_ssl_server_key'");
			$Use_ssl_server_key = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Use_hooks_directory'");
			$Use_hooks_directory = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Cache_dir_location'");
			$Cache_dir_location = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Api_dir_location'");
			$Api_dir_location = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Log_dir_location'");
			$Log_dir_location = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Transfer_dir_location'");
			$Transfer_dir_location = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Bin_dir_location'");
			$Bin_dir_location = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Use_mail_subject'");
			$Use_mail_subject = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Use_smtp_server'");
			$Use_smtp_server = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Use_mail_body'");
			$Use_mail_body = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Use_mail_from_adress'");
			$Use_mail_from_adress = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Use_api'");
			$Use_api = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Use_debug_level'");
			$Use_debug_level = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Loop_interval'");
			$Loop_interval = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Enable_autoclose_alarms'");
			$Enable_autoclose_alarms = $sql->fetchObject();

			$sql = $db->query("select * from andutteye_base_agentconfiguration where system_name = '$param1' and parametername = 'Enable_syslog_notification'");
			$Enable_syslog_notification = $sql->fetchObject();

				echo "<label><img src='themes/$authNamespace->andutteye_theme/info.png' alt='' title='' /> Communication settings, specify server, port or multiple servers and ports. All settings must have a value, otherhise set it to no.</label>
				<br />
		
                        	<form method='get' action='index.php'>
                        	<input type='hidden' name='main' value='system_configuration'>
                        	<input type='hidden' name='param1' value='$param1'>
                        	<input type='hidden' name='param2' value='baseconfiguration'>

				<label>Server_listen_adress</label>
				<label><input type='text' name='param3' maxlength='255' value='$Server_listen_adress->parametervalue' size='70'></label>

				<label>Server_listen_port</label>
				<label><input type='text' name='param4' maxlength='255' value='$Server_listen_port->parametervalue' size='70'></label>
				<br />
				<label><img src='themes/$authNamespace->andutteye_theme/info.png' alt='' title='' /> Management settings, Software inventory must be enabled to be able to use management functionality.</label>
				<br />

				<label>Enable_software_inventory</label>";

       				if("$Enable_software_inventory->parametervalue" == "yes") {
                                        echo "
                                        <label>Yes<input type='radio' name='param5' value='yes' checked='this'></label>
                                        <label>No<input type='radio' name='param5' value='no'></label>
                                        ";
                                } else {
                                        echo "
                                        <label>Yes<input type='radio' name='param5' value='yes'></label>
                                        <label>No<input type='radio' name='param5' value='no' checked='this'></label>
                                        ";
                                }

				echo "
				<br />
				<label>Enable_package_update</label>";

				if("$Enable_package_update->parametervalue" == "yes") {
                                        echo "
                                        <label>Yes<input type='radio' name='param6' value='yes' checked='this'></label>
                                        <label>No<input type='radio' name='param6' value='no'></label>
                                        ";
                                } else {
                                        echo "
                                        <label>Yes<input type='radio' name='param6' value='yes'></label>
                                        <label>No<input type='radio' name='param6' value='no' checked='this'></label>
                                        ";
                                }

				echo"
				<br />
				<label>Enable_config_update</label>";

				 if("$Enable_config_update->parametervalue" == "yes") {
                                        echo "
                                        <label>Yes<input type='radio' name='param7' value='yes' checked='this'></label>
                                        <label>No<input type='radio' name='param7' value='no'></label>
                                        ";
                                } else {
                                        echo "
                                        <label>Yes<input type='radio' name='param7' value='yes'></label>
                                        <label>No<input type='radio' name='param7' value='no' checked='this'></label>
                                        ";
                                }

				echo "
				<br />
				<label><img src='themes/$authNamespace->andutteye_theme/info.png' alt='' title='' /> Encryption settings of Andutteye communications. Perl ssl packages must be installed on the systems for this to work.</label>
				<br />
				<label>Enable_ssl_encryption</label>";

				 if("$Enable_ssl_encryption->parametervalue" == "yes") {
                                        echo "
                                        <label>Yes<input type='radio' name='param8' value='yes' checked='this'></label>
                                        <label>No<input type='radio' name='param8' value='no'></label>
                                        ";
                                } else {
                                        echo "
                                        <label>Yes<input type='radio' name='param8' value='yes'></label>
                                        <label>No<input type='radio' name='param8' value='no' checked='this'></label>
                                        ";
                                }


				echo "
				<br />
				<label>Use_ssl_server_key</label>
				<label><input type='text' name='param9' maxlength='255' value='$Use_ssl_server_key->parametervalue' size='70'></label>

				<br />
                                <label><img src='themes/$authNamespace->andutteye_theme/info.png' alt='' title='' /> Default directories and logfile locations.</label>
                                <br />

                                <label>Use_hooks_directory</label>
                                <label><input type='text' name='param10' maxlength='255' value='$Use_hooks_directory->parametervalue' size='70'></label>

                                <label>Log_dir_location</label>
                                <label><input type='text' name='param11' maxlength='255' value='$Log_dir_location->parametervalue' size='70'></label>

                                <label>Cache_dir_location</label>
                                <label><input type='text' name='param12' maxlength='255' value='$Cache_dir_location->parametervalue' size='70'></label>

                                <label>Api_dir_location</label>
                                <label><input type='text' name='param13' maxlength='255' value='$Api_dir_location->parametervalue' size='70'></label>

                                <label>Transfer_dir_location</label>
                                <label><input type='text' name='param14' maxlength='255' value='$Transfer_dir_location->parametervalue' size='70'></label>

                                <label>Bin_dir_location</label>
                                <label><input type='text' name='param15' maxlength='255' value='$Bin_dir_location->parametervalue' size='70'></label>

				<br />
				<label><img src='themes/$authNamespace->andutteye_theme/info.png' alt='' title='' /> Email settings. Change the settings to change email formatting.</label>
				<br />

				<label>Use_mail_from_adress</label>
                                <label><input type='text' name='param16' maxlength='255' value='$Use_mail_from_adress->parametervalue' size='70'></label>

                                <label>Use_smtp_server</label>
                                <label><input type='text' name='param17' maxlength='255' value='$Use_smtp_server->parametervalue' size='70'></label>

                                <label>Use_mail_body</label>
                                <label><input type='text' name='param18' maxlength='255' value='$Use_mail_body->parametervalue' size='70'></label>

                                <label>Use_mail_subject</label>
                                <label><input type='text' name='param19' maxlength='255' value='$Use_mail_subject->parametervalue' size='70'></label>

				<br />
				<label><img src='themes/$authNamespace->andutteye_theme/info.png' alt='' title='' /> Andutteye agent behavior settings.</label>
				<br />

				<label>Enable_daemon_mode</label>";

                                if("$Enable_daemon_mode->parametervalue" == "yes") {
                                        echo "
                                        <label>Yes<input type='radio' name='param20' value='yes' checked='this'></label>
                                        <label>No<input type='radio' name='param20' value='no'></label>
                                        ";
                                } else {
                                        echo "
                                        <label>Yes<input type='radio' name='param20' value='yes'></label>
                                        <label>No<input type='radio' name='param20' value='no' checked='this'></label>
                                        ";
                                }

                                echo "
                                <br />

                                <label>Use_debug_level</label>
                                <label><input type='text' name='param21' maxlength='255' value='$Use_debug_level->parametervalue' size='70'></label>

                                <label>Loop_interval</label>
                                <label><input type='text' name='param22' maxlength='255' value='$Loop_interval->parametervalue' size='70'></label>

				<label>Enable_autoclose_alarms</label>";

				if("$Enable_autoclose_alarms->parametervalue" == "yes") {
                                        echo "
                                        <label>Yes<input type='radio' name='param23' value='yes' checked='this'></label>
                                        <label>No<input type='radio' name='param23' value='no'></label>
                                        ";
                                } else {
                                        echo "
                                        <label>Yes<input type='radio' name='param23' value='yes'></label>
                                        <label>No<input type='radio' name='param23' value='no' checked='this'></label>
                                        ";
                                }

				echo "
				<br />
                                <label>Enable_syslog_notification</label>";

				if("$Enable_syslog_notification->parametervalue" == "yes") {
                                	echo "
                                        <label>Yes<input type='radio' name='param24' value='yes' checked='this'></label>
                                        <label>No<input type='radio' name='param24' value='no'></label>
                                        ";
                                } else {
                                        echo "
                                        <label>Yes<input type='radio' name='param24' value='yes'></label>
                                        <label>No<input type='radio' name='param24' value='no' checked='this'></label>
                                        ";
                                }
				
				echo"
				<br />
                                <label>Use_api</label>
                                <label><input type='text' name='param25' maxlength='255' value='$Use_api->parametervalue' size='70'></label>

				<label><input class='button' type='submit' value='Save settings'></label>
				</form>

	  	   
		";
		echo "
		</div>";

echo '
<!-- start Process slider -->
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> PS Process monitor configuration</span></h3>
	<div class="element">
		<table>
			<th>Monitor name</th>
			<th>Status</th>
			<th>Overrideble</th>
			<th>Created</th>
			<th>Delete</th>
			<th>Disable</th>
			<th>Override</th>
			<th>Settings</th>
			<th>Change</th>
		</tr>
';

			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'PS' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                        	$seqnr = $row['seqnr'];
                        	$monitorname = $row['monitorname'];
                        	$monitorvalue = $row['monitorvalue'];
                        	$status = $row['status'];
                        	$schedule = $row['schedule'];
                        	$message = $row['message'];
                        	$sendemail = $row['sendemail'];
                        	$runprogram = $row['runprogram'];
                        	$override = $row['override'];
                        	$created_date = $row['created_date'];
                        	$created_time = $row['created_time'];
                        	$created_by = $row['created_by'];

					echo "
                        		<td>
					<img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Process monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a>
					</td>
                        		<td>$status</td>
                        		<td>$override</td>
                        		<td>$created_date</td>
                        		";

                        		echo "
                        		<form method='get' action='index.php'>
                        			<input type='hidden' name='main' value='system_configuration'>
                        			<input type='hidden' name='param1' value='$param1'>
                        			<input type='hidden' name='param3' value='$seqnr'>

				  		<td><input type='radio' name='param2' value='delete'></td>
				  		<td><input type='radio' name='param2' value='disable'></td>
                                		<td><input type='radio' name='param2' value='override'></td>
                                		<td><input type='radio' checked='yes' name='param2' value='settings'></td>
						<td><input class='button' type='submit' value='Change'></td>
					</tr>
					</form>
					";

                         }
			print "<td colspan='9'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /><a href='index.php?main=new_ps_fm_monitor&param1=$param1'> Create a new processmonitor.</a></td></tr>";

		echo "
		</table>
		</div>";

echo '
 <h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> FS Filesystem monitor configuration</span></h3>
	<div class="element">
		<table>
                        <th>Monitor name</th>
                        <th>Status</th>
                        <th>Overrideble</th>
                        <th>Created</th>
                        <th>Delete</th>
                        <th>Disable</th>
                        <th>Override</th>
                        <th>Settings</th>
                        <th>Change</th>
                </tr>

';


			 $sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'FS' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td>
					<img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Filesystem monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a>
					</td>
                                        <td>$status</td>
                                        <td>$override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
                                                <input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>
                                        <td><input class='button' type='submit' value='Change'></td>
                                        </td>
                                        </form>
					</tr>
                                        ";

                         }
			print "<td colspan='9'>
				<img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /><a href='index.php?main=new_fs_monitor&param1=$param1'> Create a new filesystemmonitor.</a></td>";

		echo "
		</tr>
		</table
		</div>";

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> FM Filemodification monitor configuration</span></h3>
	<div class="element">
		<table>
                        <th>Monitor name</th>
                        <th>Status</th>
                        <th>Overrideble</th>
                        <th>Created</th>
                        <th>Delete</th>
                        <th>Disable</th>
                        <th>Override</th>
                        <th>Settings</th>
                        <th>Change</th>
                </tr>
';



			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'FM' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td>
					<img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Filemodification monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a>
					</td>
                                        <td>$status</td>
                                        <td>$override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
						<input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>

                                        <td><input class='button' type='submit' value='Change'></td>
                                        </tr>
                                        </form>
                                        ";

                         }
			print "<td colspan='9'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /><a href='index.php?main=new_ps_fm_monitor&param1=$param1'> Create a new filemodification monitor.</a></td></tr>";


		echo "
		</table>
		</div>";

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> FT Filetrace pattern monitor configuration</span></h3>
	<div class="element">
		<table>
                        <th>Monitor name</th>
                        <th>Searchpattern</th>
                        <th>Overrideble</th>
                        <th>Created</th>
                        <th>Delete</th>
                        <th>Disable</th>
                        <th>Override</th>
                        <th>Settings</th>
                        <th>Change</th>
                </tr>
';


			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'FT' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $searchpattern = $row['searchpattern'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td>
					<img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Filetrace patternmatch monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a>
					</td>
                                        <td>$searchpattern</td>
                                        <td>$override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
						<input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>
                                        	<td><input class='button' type='submit' value='Change'></td>
                                        </tr>
                                        </form>
                                        ";

                         }
			print "<td colspan='9'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /><a href='index.php?main=new_ft_monitor&param1=$param1'> Create a new filepatternmatch monitor.</a></td></tr>";

		echo "
		</table>
		</div>";

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> EV Every execution monitor configuration</span></h3>
<div class="element">
	<table>
        	<th>Monitor name</th>
                <th>Status</th>
                <th>Overrideble</th>
                <th>Created</th>
                <th>Delete</th>
                <th>Disable</th>
                <th>Override</th>
                <th>Settings</th>
                <th>Change</th>
        </tr>

';


			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'EV' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td>
					<img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Every program monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a>
					</td>
                                        <td>$status</td>
                                        <td>$override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
						<input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>
                                        	<td><input class='button' type='submit' value='Change monitor settings'></td>
                                        </tr>
                                        </form>
                                        ";

                         }
			print "<td colspan='9̈́'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /> <a href='index.php?main=new_every_monitor&param1=$param1'>Create a new every execution monitor.</a></td></tr>";

		echo "
		</table>
		</div>";

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> PH Communication monitor configuration</span></h3>
	<div class="element">
		<table>
                        <th>Monitor name</th>
                        <th>Status</th>
                        <th>Overrideble</th>
                        <th>Created</th>
                        <th>Delete</th>
                        <th>Disable</th>
                        <th>Override</th>
                        <th>Settings</th>
                        <th>Change</th>
                </tr>
';

			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'PH' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td>
					<img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Communication monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a></td>
                                        <td>$status</td>
                                        <td>$override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
						<input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>
                                        	<td><input class='button' type='submit' value='Change'></td>
                                        </tr>
                                        </form>
                                        ";

                         }
			print "<td colspan='9'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /> <a href='index.php?main=new_la_sa_ma_ph_monitor&param1=$param1'>Create a new communication monitor.</a></td></tr>";

		echo "
		</table>
		</div>";

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> LA Loadaverege monitor configuration</span></h3>
	<div class="element">
		<table>
                        <th>Monitor name</th>
                        <th>Status</th>
                        <th>Overrideble</th>
                        <th>Created</th>
                        <th>Delete</th>
                        <th>Disable</th>
                        <th>Override</th>
                        <th>Settings</th>
                        <th>Change</th>
                </tr>
';

			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'LA' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td>
					<img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Load averege monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a></td>
                                        <td>$status</td>
                                        <td>$override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
						<input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>
                                        	<td><input class='button' type='submit' value='Change'></td>
                                        </tr>
                                        </form>
                                        ";

                         }
			print "<td colspan='9'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /> <a href='index.php?main=new_la_sa_ma_ph_monitor&param1=$param1'>Create a new loadaverege monitor.</a></td></tr>";

		echo "
		</table>
		</div>";

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> MA Memoryaverege monitor configuration</span></h3>
	<div class="element">
		<table>
                        <th>Monitor name</th>
                        <th>Status</th>
                        <th>Overrideble</th>
                        <th>Created</th>
                        <th>Delete</th>
                        <th>Disable</th>
                        <th>Override</th>
                        <th>Settings</th>
                        <th>Change</th>
                </tr>
';

			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'MA' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td>
					<img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Memory averege monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a></td>
                                        <td>$status</td>
                                        <td>$override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
						<input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>
                                        	<td><input class='button' type='submit' value='Change'></td>
                                        </tr>
                                        </form>
                                        ";

                         }
			print "<td colspan='9'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /> <a href='index.php?main=new_la_sa_ma_ph_monitor&param1=$param1'>Create a new memoryaverege monitor.</a></td></tr>";


		echo "
		</table>
		</div>";

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> SA Swapaverege monitor configuration</span></h3>
	<div class="element">
		<table>
                        <th>Monitor name</th>
                        <th>Status</th>
                        <th>Overrideble</th>
                        <th>Created</th>
                        <th>Delete</th>
                        <th>Disable</th>
                        <th>Override</th>
                        <th>Settings</th>
                        <th>Change</th>
                </tr>
';

			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'SA' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td><img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Swap averege monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a></label></div>
                                        <td>$status</td>
                                        <td>$override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
						<input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>
                                        	<td><input class='button' type='submit' value='Change'></td>
                                        </tr>
                                        </form>
                                        ";

                         }
			print "<td colspan='9'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /> <a href='index.php?main=new_la_sa_ma_ph_monitor&param1=$param1'>Create a new swapaverege monitor.</a></td></tr>";


		echo "
		</table>
		</div>";

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> AM Assetmanaegment monitor configuration</span></h3>
	<div class="element">
		<table>
                        <th>Monitor name</th>
                        <th>Status</th>
                        <th>Overrideble</th>
                        <th>Created</th>
                        <th>Delete</th>
                        <th>Disable</th>
                        <th>Override</th>
                        <th>Settings</th>
                        <th>Change</th>
                </tr>
';

			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'AM' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td><img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Assetmanagement monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a>
					</td>
                                        <td>$status</td>
                                        <td>Override $override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
						<input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>
                                        	<td><input class='button' type='submit' value='Change'></td>
                                        </tr>
                                        </form>
                                        ";

                         }
			print "<td colspan='9'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /><a href='index.php?main=new_am_st_monitor&param1=$param1'> Create a new assetmanagement monitor.</a></td></tr>";

		echo "
		</table>
		</div>";

echo '
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> ST Statistics monitor configuration</span></h3>
	<div class="element">
		<table>
                        <th>Monitor name</th>
                        <th>Status</th>
                        <th>Overrideble</th>
                        <th>Created</th>
                        <th>Delete</th>
                        <th>Disable</th>
                        <th>Override</th>
                        <th>Settings</th>
                        <th>Change</th>
                </tr>
';

			$sql = $db->query("select * from andutteye_monitor_configuration where system_name = '$param1' and monitortype = 'ST' order by seqnr desc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $monitorname = $row['monitorname'];
                                $monitorvalue = $row['monitorvalue'];
                                $status = $row['status'];
                                $schedule = $row['schedule'];
                                $message = $row['message'];
                                $sendemail = $row['sendemail'];
                                $runprogram = $row['runprogram'];
                                $override = $row['override'];
                                $created_date = $row['created_date'];
                                $created_time = $row['created_time'];
                                $created_by = $row['created_by'];

                                        echo "
                                        <td>
					<img src='themes/$authNamespace->andutteye_theme/actions.png' alt='' title='' /> <a href='#' class='Tips2' title='Statistics monitor, schedule:$schedule message:$message email:$sendemail runprogram:$runprogram Created by:$created_by on $created_date $created_time Override:$override'>$monitorname</a></td>
                                        <td>$status</td>
                                        <td>$override</td>
                                        <td>$created_date</td>
                                        ";

                                        echo "
                                        <form method='get' action='index.php'>
                                                <input type='hidden' name='main' value='system_configuration'>
                                                <input type='hidden' name='param1' value='$param1'>
                                                <input type='hidden' name='param3' value='$seqnr'>

                                                <td><input type='radio' name='param2' value='delete'></td>
                                                <td><input type='radio' name='param2' value='disable'></td>
                                                <td><input type='radio' name='param2' value='override'></td>
                                                <td><input type='radio' checked='yes' name='param2' value='settings'></td>
                                        </div>
                                        <td><input class='button' type='submit' value='Change'></td>
                                        </tr>
                                        </form>
                                        ";

                         }
			print "<td colspan='9'><img src='themes/$authNamespace->andutteye_theme/new_1.png' alt='' title='' /><a href='index.php?main=new_am_st_monitor&param1=$param1'> Create a new statistics monitor.</a></td></tr>";
	
	echo "</table></div>
	      <label><img src='themes/$authNamespace->andutteye_theme/back.png' alt='' title='' /><a href='index.php?main=system_overview&param1=$param1'>&nbsp;Back to $param1 system overview</a></label>
	</fieldset>
	</div>
</div>";

// end of subfunction
}



function show_system_snapshot($param1,$param2,$param3) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

echo '<div id="content">
	<h2 class="BigTitle">System <span class="ColoredTxt">Snapshot</span> ' . $param1 . '</h2>
		<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> Select other dates</span></h3>
        		<div class="element">
			<table>
				<th>Transaction dates</th>
			</tr>';

		$sql = $db->query("select distinct created_date, created_time from andutteye_snapshot where system_name = '$param1' order by seqnr desc limit 0,100");
		while ($row = $sql->fetch()) {
                	$created_date = $row['created_date'];
                	$created_time = $row['created_time'];
			echo '
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" />&nbsp;
					<a href="index.php?main=show_system_snapshot&param1=' . $param1 . '&param2=' . $created_date . '&param3=' . $created_time . '">' . $created_date . ' ' . $created_time . '</a>
				</td>
				</tr>
';
		}
		
echo '
	</table>
</div>
';
echo '<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle"> System snapshot</span></h3>
     	<div class="element">';

if($param2 && $param3) {
	$sql = $db->query("select * from andutteye_snapshot where system_name = '$param1' and created_date = '$param2' and created_time = '$param3' order by seqnr desc limit 0,1");
	$res = $sql->fetchObject();
} else {
	$sql = $db->query("select * from andutteye_snapshot where system_name = '$param1' order by seqnr desc limit 0,1");
	$res = $sql->fetchObject();
}

$formatted = preg_split("/;;;;/", "$res->users");

echo '
			<br />

<fieldset class="GroupField">
	<legend>User snapshot ' . $res->created_date . ' ' . $res->created_time . '</legend>
			<label>
';

foreach($formatted as $i) {
        echo "$i <br />";
}
echo '
			</label>
</fieldset>
';

$formatted = preg_split("/;;;;/", "$res->fs");

echo '
<fieldset class="GroupField">
	<legend>Filesystem snapshot ' . $res->created_date . ' ' . $res->created_time . '</legend>
			<label>
';

foreach($formatted as $i) {
        echo "$i <br />";
}
echo '
			</label>
</fieldset>
';

$formatted = preg_split("/;;;;/", "$res->procs");

echo '
<fieldset class="GroupField">
	<legend>Process snapshot ' . $res->created_date . ' ' . $res->created_time . '</legend>
			<label>
';

foreach($formatted as $i) {
        echo "$i <br />";
}
echo '
			</label>
</fieldset>
';

$formatted = preg_split("/;;;;/", "$res->net");

echo '
<fieldset class="GroupField">
	<legend>Netactivity snapshot ' . $res->created_date . ' ' . $res->created_time . '</legend>
			<label>
';

foreach($formatted as $i) {
        echo "$i <br />";
}
echo '
			</label>
</fieldset>
';

$formatted = preg_split("/;;;;/", "$res->hardware");

echo '
<fieldset class="GroupField">
	<legend>Hardware snapshot ' . $res->created_date . ' ' . $res->created_time . '</legend>
			<label>
';

foreach($formatted as $i) {
        echo "$i <br />";
}
echo '
			</label>
</fieldset>
</div>
';
echo '
			<br/ >
			<label>
				<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" />&nbsp;
				<a href="index.php?main=system_overview&param1=' . $param1 . '">&nbsp;Back to ' . $param1 . ' system overview</a>
			</label>

</div>
';

// End of subfunction
}


function create_new_changeevent($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9) {

require_once 'Zend/Session/Namespace.php';
require 'db.php';
verify_if_user_is_logged_in();

$date = date("20y-m-d");
$time = date("H:m:s");
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param1) {
	$sql = $db->query("select * from andutteye_changeevent where seqnr = '$param1'");
	$res = $sql->fetchObject();

}
if($param2) {
	if($param9 == "update") {
        	$sql = "update andutteye_changeevent set description='$param2', information='$param3', workaround='$param4', solution='$param5', severity='$param7' where seqnr = '$param1'";
        	$db->query($sql);
		header("Location:index.php?main=change_events_database");
		exit;
	} else {
	$data = array(
                        'system_name'      => "$param8",
                        'shortinformation' => "$res->shortinformation",
                        'description'      => "$param2",
                        'information'      => "$param3",
                        'workaround'       => "$param4",
                        'solution'         => "$param6",
                        'severity'         => "$param7",
                        'monitortype'      => "$res->monitortype",
                        'created_date'     => "$date",
                        'created_time'     => "$time",
                        'created_by'       => "$authNamespace->andutteye_username"
                );
                $db->insert('andutteye_changeevent', $data);
		header("Location:index.php?main=change_events_database");
		exit;
	}
}

echo '
	<div id="content">
		<div class="section content">

			<h2 class="BigTitle"><span class="ColoredTxt">Create a new</span> changevent solution</h2>


<fieldset class="GroupField">
	<legend>Solution Information</legend>

				<div>
					<form method="get" action="index.php">
						<input type="hidden" name="main" value="create_new_changeevent">
						<input type="hidden" name="param1" value="' . $param1 . '">
						<input type="hidden" name="param9" value="' . $param9 . '">

						<div class="leftcol">
							<h3 class="InfoTitle">Description</h3>
							<label><input type="text" name="param2" size="50" maxlength="255" value="' . $res->description . '"></label>

							<br />

							<h3 class="InfoTitle">Information</h3>
							<label><textarea cols="50" rows="10" name="param3">' . $res->information . '</textarea></label>

							<br />

							<h3 class="InfoTitle">Workaround</h3>
							<label><textarea cols="50" rows="10" name="param4">' . $res->workaround . '</textarea></label>
						</div>

						<div class="rightcol">

							<h3 class="InfoTitle">Author information</h3>
							<label><input type="text" name="param5" size="50" maxlength="255" value="User ' . $authNamespace->andutteye_username . ' Date ' . $date . ' Time ' . $time . '" readonly="true">
							</label>

							<br />

							<h3 class="InfoTitle">Solution</h3>
							<label><textarea cols="50" rows="10" name="param6">' . $res->solution . '</textarea></label>

							<br />

							<h3 class="InfoTitle">Severity:</h3>
							<label><input type="radio" name="param7" value="Harmless"> Harmless</label>
							<label><input type="radio" name="param7" value="Warning"> Warning</label>
							<label><input type="radio" name="param7" value="Critical"> Critical</label>
							<label><input type="radio" name="param7" value="Fatal"> Fatal</label>

						</div>

						<div class="ClearFloat"></div>

						<br />
						<h3 class="InfoTitle">Join to host:</h3>';

						echo '<label><select name="param8" style="WIDTH: 260px"> <option value="No specific system"> No specific system';
                                        	
						$sql    = $db->query("select distinct(system_name) from andutteye_systems order by system_name asc");
                                        	while ($row = $sql->fetch()) {
                                                	$system_name = $row['system_name'];
							echo "<option value='$system_name'> $system_name";
                                        	}

						echo '</select></label>';

						echo '
						<br />
						<h3 class="InfoTitle">Save solution:</h3>
						<label><input class="button" type="submit" value="Submit"></label>

					</form>
			
				<label><img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" /> <a href="index.php?main=change_events_database">&nbsp;Back to changeevents database</a></label>
				</div>

</fieldset>

		</div>
	</div>
';

// End of subfunction
}

function change_role_permissions($param1,$param2,$param3,$param4,$param5,$param6) {

require_once 'Zend/Session/Namespace.php';
require 'db.php';
verify_if_user_is_logged_in();

$date = date("20y-m-d");
$time = date("H:m:s");
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param2) {
	$sql = "delete from andutteye_rolepermissions where roleobject = '$param2' and rolename = '$param1' and objecttype = '$param4'";
	$db->query($sql);

        $data = array(
                        'roleobject'       => "$param2",
                        'rolename'         => "$param1",
                        'role_permission'  => "$param3",
                        'objecttype'       => "$param4",
                        'domain_name'      => "$param5",
                        'distribution'     => "$param6",
                        'created_date'     => "$date",
                        'created_time'     => "$time",
                        'created_by'       => "$authNamespace->andutteye_username"
                );
                $db->insert('andutteye_rolepermissions', $data);
                header("Location:index.php?main=change_role_permissions&param1=$param1");
                exit;
}

echo '
        <div id="content">
                <div class="section content">

                        <h2 class="BigTitle"><span class="ColoredTxt">Change role permissions</span> for role ' . $param1 . '</h2>

				 <fieldset class="GroupField">
                                        <legend>Set domain permissions for the role</legend>

						<table>
							<th>Permission</th>
							<th>Domain</th>
							<th>Submit</th>
							</tr>';

                                                 $sql = $db->query("select distinct(domain_name) from andutteye_domains order by domain_name asc");
                                                 while ($row = $sql->fetch()) {
                                                        $domain_name  = $row['domain_name'];

                                                        echo "
                                                        <form method='get' action='index.php'>
                                                        <input type='hidden' name='main' value='change_role_permissions'>
                                                        <input type='hidden' name='param1' value='$param1'>
                                                        <input type='hidden' name='param2' value='$domain_name'>
                                                        <input type='hidden' name='param4' value='domain'>";

                                                        $subsql = $db->query("select * from andutteye_rolepermissions where roleobject = '$domain_name' and rolename = '$param1' and objecttype = 'domain'");
                                                        $res = $subsql->fetchObject();

                                                        if(!$res->role_permission) {
                                                                $current="None (Hidden)";
                                                        }
                                                        elseif($res->role_permission == 1) {
                                                                $current="Read";
                                                        }
                                                        elseif($res->role_permission == 2) {
                                                                $current="Read-Write";
                                                        }
                                                        elseif($res->role_permission == 3) {
                                                                $current="Read-Write-Delete";
                                                        } else {
                                                                $current="No permission";
                                                        }

                                                        echo '
							     	<td>
                                                                <select name="param3" style="WIDTH: 200px">
                                                                        <option value="$res->role_permission">Set now: ' . $current . '
                                                                        <option value="0"> None (Hidden)
                                                                        <option value="1"> Read permission
                                                                        <option value="2"> Read-Write permission
                                                                        <option value="3"> Read-Write-Delete permission
                                                                </select>
                                                        	</td>
                                                        	<td>
								<img src="themes/' . $authNamespace->andutteye_theme . '/domains_1.png" alt="" title="" />&nbsp;
								<a href="#" class="Tips2" title="Permission set by:' . $res->created_by . ' Date:' . $res->created_date . ' Time:' . $res->created_time . '">' . $domain_name . '</a></td>
                                                        	<td><input class="button" type="submit" value="Set permission">
								</td>
                                                        </tr></form>';
                                                }

                                                echo '</table>
                                </fieldset>

				<fieldset class="GroupField">
                                        <legend>Set group permissions for the role</legend>

						<table>
							<th>Permission</th>
							<th>Group</th>
							<th>Submit</th>
							</tr>';

                                                 $sql = $db->query("select distinct(group_name) from andutteye_groups order by group_name asc");
                                                 while ($row = $sql->fetch()) {
                                                        $group_name  = $row['group_name'];

                                                        echo "
                                                        <form method='get' action='index.php'>
                                                        <input type='hidden' name='main' value='change_role_permissions'>
                                                        <input type='hidden' name='param1' value='$param1'>
                                                        <input type='hidden' name='param2' value='$group_name'>
                                                        <input type='hidden' name='param4' value='group'>";

                                                        $subsql = $db->query("select * from andutteye_rolepermissions where roleobject = '$group_name' and rolename = '$param1' and objecttype = 'group'");
                                                        $res = $subsql->fetchObject();

                                                        if(!$res->role_permission) {
                                                                $current="None (Hidden)";
                                                        }
                                                        elseif($res->role_permission == 1) {
                                                                $current="Read";
                                                        }
                                                        elseif($res->role_permission == 2) {
                                                                $current="Read-Write";
                                                        }
                                                        elseif($res->role_permission == 3) {
                                                                $current="Read-Write-Delete";
                                                        } else {
                                                                $current="No permission";
                                                        }

                                                        echo '
                                                        	<td>
                                                                <select name="param3" style="WIDTH: 200px">
                                                                        <option value="$res->role_permission">Set now: ' . $current . '
                                                                        <option value="0"> None (Hidden)
                                                                        <option value="1"> Read permission
                                                                        <option value="2"> Read-Write permission
                                                                        <option value="3"> Read-Write-Delete permission
                                                                </select>
                                                        	</td>
                                                        	<td>
							<img src="themes/' . $authNamespace->andutteye_theme . '/groups_2.png" alt="" title="" />&nbsp;
							<a href="#" class="Tips2" title="Permission set by:' . $res->created_by . ' Date:' . $res->created_date . ' Time:' . $res->created_time . '">' . $group_name . '</a>
								</td>
                                                        	<td>
								<input class="button" type="submit" value="Set permission">
								</td>
                                                        </tr></form>';
                                                }

                                                echo '</table>
                                </fieldset>

				<fieldset class="GroupField">
        				<legend>Set system permissions for the role</legend>

						<table>
							<th>Permission</th>
							<th>System</th>
							<th>Submit</th>
							</tr>';

						 $sql = $db->query("select distinct(system_name) from andutteye_systems order by system_name asc");
                				 while ($row = $sql->fetch()) {
                        				$system_name  = $row['system_name'];
						
							echo " 
							<form method='get' action='index.php'>
                                                	<input type='hidden' name='main' value='change_role_permissions'>
                                                	<input type='hidden' name='param1' value='$param1'>
                                                	<input type='hidden' name='param2' value='$system_name'>
                                                        <input type='hidden' name='param4' value='system'>";
                        
							$subsql = $db->query("select * from andutteye_rolepermissions where roleobject = '$system_name' and rolename = '$param1' and objecttype = 'system'");
							$res = $subsql->fetchObject();

							if(!$res->role_permission) {
								$current="None (Hidden)";
							}
							elseif($res->role_permission == 1) {
								$current="Read";
							}
							elseif($res->role_permission == 2) {
								$current="Read-Write";
							}
							elseif($res->role_permission == 3) {
								$current="Read-Write-Delete";
							} else {
								$current="No permission";
							}
					
							echo '
                                        		<td>
								<select name="param3" style="WIDTH: 200px">
                                                			<option value="$res->role_permission">Set now: ' . $current . '
                                                			<option value="0"> None (Hidden)
                                                			<option value="1"> Read permission
                                                			<option value="2"> Read-Write permission
                                                			<option value="3"> Read-Write-Delete permission
                                        			</select>
							</td>
                                        		<td>
							<img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" />&nbsp;
							<a href="#" class="Tips2" title="Permission set by:' . $res->created_by . ' Date:' . $res->created_date . ' Time:' . $res->created_time . '">' . $system_name . '</a></td>
                                        		<td>
							<input class="button" type="submit" value="Set permission">
							</td>
                                			</tr></form>';
                				}

						echo '</table>
				</fieldset>
				<fieldset class="GroupField">
                                        <legend>Set file permissions for the role</legend>

						<table>
							<th>Permission</th>
							<th>Domain</th>
							<th>Submit</th>
							</tr>';

                                                 $sql = $db->query("select distinct domain_name from andutteye_files");
                                                 while ($row = $sql->fetch()) {
                                                        $domain_name  = $row['domain_name'];

                                                        echo "
                                                        <form method='get' action='index.php'>
                                                        <input type='hidden' name='main' value='change_file_permissions'>
                                                        <input type='hidden' name='param1' value='$param1'>
                                                        <input type='hidden' name='param2' value='$domain_name'>
                                                        ";
                                                $subsql = $db->query("select distinct(distribution) from andutteye_files where domain_name = '$domain_name'");
                                                        $res = $subsql->fetchObject();

                                                echo '
                                                        <td>
                                                                <select name="param3" style="WIDTH: 200px">
                                                                        <option value="'.$res->distribution.'">' . $res->distribution . '
                                                                </select>
                                                        </td>
                                                        <td>
                                                        <img src="themes/' . $authNamespace->andutteye_theme . '/file_1.png" alt="" title="" />&nbsp;
                                                        <a href="#" class="Tips2" title="Domain:' .$domain_name.' Distribution:'.$res->distribution.'">' . $domain_name .'</a>
							</td>
                                                        <td>
							<input class="button" type="submit" value="Set permission">
							</td>
                                                        </tr></form>';
                                                }

                                                echo '</table>
                                </fieldset>

				<fieldset class="GroupField">
                                        <legend>Set package permissions for the role</legend>

						<table>
							<th>Permission</th>
							<th>Domain</th>
							<th>Submit</th>
							</tr>';

                                                 $sql = $db->query("select distinct domain_name from andutteye_packages");
                                                 while ($row = $sql->fetch()) {
                                                        $domain_name  = $row['domain_name'];

                                                        echo "
                                                        <form method='get' action='index.php'>
                                                        <input type='hidden' name='main' value='change_package_permissions'>
                                                        <input type='hidden' name='param1' value='$param1'>
                                                        <input type='hidden' name='param2' value='$domain_name'>
                                                        ";
						$subsql = $db->query("select distinct(distribution) from andutteye_packages where domain_name = '$domain_name'");
                                                        $res = $subsql->fetchObject();

                                                echo '
                                                        <td>
                                                                <select name="param3" style="WIDTH: 200px">
                                                                        <option value="'.$res->distribution.'">' . $res->distribution . '
                                                                </select>
                                                        </td>
                                                        <td>
							<img src="themes/' . $authNamespace->andutteye_theme . '/package_1.png" alt="" title="" />&nbsp;
							<a href="#" class="Tips2" title="Domain:' .$domain_name.' Distribution:'.$res->distribution.'">' . $domain_name .'</a></label>
							</td>

                                                        <td>
							<input class="button" type="submit" value="Set permission">
							</td>
                                                        </tr></form>';
                                                }

                                                echo '</table>
                                </fieldset>
		</div>
	</div>
';

// End of subfunction
}

function create_changeevent($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8) {

require_once 'Zend/Session/Namespace.php';
require 'db.php';
verify_if_user_is_logged_in();

$date = date("20y-m-d");
$time = date("H:m:s");
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param1) {
	$sql = $db->query("select * from andutteye_alarm where seqnr = '$param1'");
	$res = $sql->fetchObject();

}
if($param2) {
	$data = array(
                        'system_name'      => "$param8",
                        'shortinformation' => "$res->shortinformation",
                        'description'      => "$param2",
                        'information'      => "$param3",
                        'workaround'       => "$param4",
                        'solution'         => "$param6",
                        'severity'         => "$param7",
                        'monitortype'      => "$res->monitortype",
                        'created_date'     => "$date",
                        'created_time'     => "$time",
                        'created_by'       => "$authNamespace->andutteye_username"
                );
                $db->insert('andutteye_changeevent', $data);
		header("Location:index.php?main=monitoring_front&param1=$res->system_name");
		exit;
}

echo '
	<div id="content">
		<div class="section content">

			<h2 class="BigTitle"><span class="ColoredTxt">Create a Changeevent</span> Solution on ' . $param1 . '</h2>


<fieldset class="GroupField">
	<legend>Solution Information</legend>

				<div>
					<form method="get" action="index.php">
						<input type="hidden" name="main" value="create_changeevent">
						<input type="hidden" name="param1" value="' . $param1 . '">

						<div class="leftcol">
							<h3 class="InfoTitle">Description</h3>
							<label><input type="text" name="param2" size="50" maxlength="255" value="' . $res->shortinformation . '"></label>

							<br />

							<h3 class="InfoTitle">Information</h3>
							<label><textarea cols="50" rows="10" name="param3">' . $res->longinformation . '</textarea></label>

							<br />

							<h3 class="InfoTitle">Workaround</h3>
							<label><textarea cols="50" rows="10" name="param4"></textarea></label>
						</div>

						<div class="rightcol">

							<h3 class="InfoTitle">Author information</h3>
							<label><input type="text" name="param5" size="50" maxlength="255" value="User ' . $authNamespace->andutteye_username . ' Date ' . $date . ' Time ' . $time . '" readonly="true"></label>

							<br />

							<h3 class="InfoTitle">Solution</h3>
							<label><textarea cols="50" rows="10" name="param6"></textarea></label>

							<br />

							<h3 class="InfoTitle">Severity:</h3>
							<label><input type="radio" name="param7" value="Harmless"> Harmless</label>
							<label><input type="radio" name="param7" value="Warning"> Warning</label>
							<label><input type="radio" name="param7" value="Critical"> Critical</label>
							<label><input type="radio" name="param7" value="Fatal"> Fatal</label>

						</div>

						<div class="ClearFloat"></div>

						<br />
						<h3 class="InfoTitle">Join to host:</h3>
						<label><input type="radio" name="param8" value="' . $res->system_name . '"> ' . $res->system_name . '</label>
						<label><input type="radio" name="param8" value=""> No specific system</label>
						<br />
						<h3 class="InfoTitle">Save solution:</h3>
						<label><input class="button" type="submit" value="Submit"></label>

					</form>
				</div>

</fieldset>

		</div>
	</div>
';

// End of subfunction
}


function show_bundles($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select * from andutteye_specifications where system_name = '$param1' and revision = '$param3'");
$res = $sql->fetchObject();

$sql = $db->query("select * from andutteye_systems where system_name = '$param1'");
$dom = $sql->fetchObject();

if($param3 && $param4 ) {
        $sql = "update andutteye_choosenbundles set specaction = 'R' where system_name = '$param1' and specid = '$param3' and seqnr = '$param4'";
        $db->query($sql);

}

if($param5 && $param8) {

$date = date("20y-m-d");
$time = date("H:m:s");

$data = array(
        'system_name' => "$param1",
        'bundle'      => "$param5",
        'revision'    => "$param8",
        'specid'       => "$param3",
        'specaction'   => "A",
        'created_date' => "$date",
        'created_time' => "$time",
        'created_by'   => "$authNamespace->andutteye_username"
        );
        $db->insert('andutteye_choosenbundles', $data);
}

echo '<div id="content">
	<div class="section content">
		<h2 class="BigTitle">Choosen <span class="ColoredTxt">Software Bundles</span> for ' . $param1 . ' Revision ' . $param3 . '</h2>
			<div>
			
			<table>
				<th>Bundlename</th>
				<th>Package(s)</th>
				<th>Revision</th>
				<th>Date</th>
				<th>Delete</th>
				</tr>
			';

                $sql = $db->query("select * from andutteye_choosenbundles where system_name = '$param1' and specid = '$param3 ' and specaction = 'N' order by specaction asc");
                while ($row = $sql->fetch()) {
                        $seqnr  = $row['seqnr'];
                        $bundle  = $row['bundle'];
                        $revision    = $row['revision'];
                        $created_date    = $row['created_date'];
                        $created_time    = $row['created_time'];
                        $created_by    = $row['created_by'];

			$subsql = $db->query("select seqnr from andutteye_bundles where bundle = '$bundle' and revision = '$revision'");
                        $packages = $subsql->fetchAll();
                        $packages = count($packages);


			echo '<td><a href="#" class="Tips2" title="Bundle:' . $bundle . ' Revision:' . $revision . ' Created by:' . $created_by . '">' . $bundle . '</a></td>
			      <td>' . $packages . '</td>
			      <td>' . $revision . '</td>
			      <td>' . $created_date . '</td
			      <td><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> <a href="index.php?main=show_bundles&param1=' . $param1 . '&param3=' . $param3 . '&param4=' . $seqnr . '">Delete</a></td>
				</tr>';
                }
echo '
	</table>
	<br />
	</div>
';

echo '<fieldset class="GroupField">
	<legend>Pending bundle transactions</legend>
	
		<table>
			<th>Bundlename</th>
			<th>Package(s)</th>
			<th>Revision</th>
			<th>Date</th>
			<th>Status</th>
			</tr>
		';

                $sql = $db->query("select * from andutteye_choosenbundles where system_name = '$param1' and specid = '$param3' and specaction != 'N'");
                while ($row = $sql->fetch()) {
                        $seqnr  = $row['seqnr'];
                        $bundle  = $row['bundle'];
                        $revision    = $row['revision'];
                        $specaction    = $row['specaction'];
                        $created_date    = $row['created_date'];
                        $created_time    = $row['created_time'];
                        $created_by    = $row['created_by'];
			
			$subsql = $db->query("select seqnr from andutteye_bundles where bundle = '$bundle' and revision = '$revision'");
                        $packages = $subsql->fetchAll();
                        $packages = count($packages);

			echo '
				<td><a href="#" class="Tips2" title="Package:' . $aepackage . ' Version:' . $aeversion . ' Release:' . $aerelease . ' Arch:' . $aearchtype . ' Action:' . $aeaction . '">' . $bundle . '</a></td>
				<td>' . $packages . '</td>
				<td>' . $revision . '</td>
				<td>' . $created_date . '</td>
				<td><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <a href="#" class="Tips2" title="Bundle is in pending state until you save the specification. State A means add State R means remove."> Pending (' . $specaction . ')</a></td>
			</tr>
';
                }
echo '
</table>
</fieldset>

<fieldset class="GroupField">
	<legend>Search software bundles</legend>
			<form method="get" action="index.php">
				<input type="hidden" name="main" value="show_bundles">
				<input type="hidden" name="param1" value="' . $param1 . '">
				<input type="hidden" name="param3" value="' . $param3 . '">

				<label>
					<input type="text" name="param2" value="" size="70">
					<input class="button" type="submit" value="Search">
				</label>
			</form>
</fieldset>
';

echo '
<fieldset class="GroupField">
	<legend>Available software bundles in ' . $res->distribution . ' for domain ' . $dom->domain_name . '</legend>

	<table>
		<th>Bundle</th>
		<th>Package(s)</th>
		<th>Revision</th>
		<th>Distribution</th>
		<th>Add</th>
		<th>Submit</th>
		<th>Remove</th>
	</tr>';

                if($param2) {
                        $sql = $db->query("select distinct bundle, revision, created_date, created_time from andutteye_bundles where bundle like '%$param2%' and distribution = '$res->distribution' and domain_name = '$dom->domain_name' order by bundle asc limit 0,20")
;
                } else {
                        $sql = $db->query("select distinct bundle, revision, created_date, created_time from andutteye_bundles where distribution = '$res->distribution' and domain_name = '$dom->domain_name' order by bundle  asc limit 0,20");
                }
                while ($row = $sql->fetch()) {
                        $seqnr  = $row['seqnr'];
                        $bundle  = $row['bundle'];
                        $revision    = $row['revision'];
                        $created_date    = $row['created_date'];
                        $created_time    = $row['created_time'];

                        $subsql  = $db->query("select bundle from andutteye_choosenbundles where system_name = '$param1' and specid = '$param3' and specaction = 'N' and bundle = '$bundle' and revision = '$revision'");
                        $choosen = $subsql->fetchObject();

                        if($bundle == $choosen->bundle) {
                                continue;
                        }
			$subsql2 = $db->query("select seqnr from andutteye_bundles where bundle = '$bundle' and revision = '$revision' and domain_name = '$dom->domain_name'");
                        $packages = $subsql2->fetchAll();
                        $packages = count($packages);

echo '
			<form method="get" action="index.php">
				<input type="hidden" name="main" value="show_bundles">
				<input type="hidden" name="param1" value="' . $param1 . '">
				<input type="hidden" name="param3" value="' . $param3 . '">
				<input type="hidden" name="param5" value="' . $bundle . '">
				<input type="hidden" name="param8" value="' . $revision . '">


				<td><a href="#" class="Tips2" title="Bundle:' . $bundle . ' Revision:' . $revision . ' Distribution:' . $distribution . '"><b>' . $bundle . '</b></a></td>
				<td>' . $packages . '</td>
				<td>' . $revision . '</td>
				<td>' . $res->distribution . '</td>
				<td>Add<input type="radio" name="param9" value="add"></td>
				<td><input class="button" type="submit" value="Submit bundle change"></td>
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />
					<a href="index.php?main=remove_bundle&param1=' . $bundle . '&param2=' . $revision . '&param3=' . $res->distribution . '&param4=' . $dom->domain_name . '&param5=' . $param1 . '&param6=' . $param3 . '" onclick="return confirm(\'Remove bundle ' . $bundle . ' revision ' . $revision . ' for domain ' . $dom->domain_name . ' that contains ' . $packages . ' packages?\')">Remove</a>
				</td>
			</form>
			</tr>';
                }

echo '
</table>
</fieldset>

			<div class="DivSpacer"></div>
			<label>
				<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" />
				<a href="index.php?main=system_specification&param1=' . $param1 . '">&nbsp;Back to management profile</a>
			</label>

		</div>
	</div>
';

// End of subfunction
}

function change_file_permissions($param1,$param2,$param3,$param4,$param5,$param6) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$date = date("20y-m-d");
$time = date("H:m:s");

if($param5) {
        $sql = "delete from andutteye_rolepermissions where roleobject = '$param5' and rolename = '$param1' and objecttype = 'file' and domain_name = '$param2' and distribution = '$param3'";
        $db->query($sql);

        $data = array(
                        'roleobject'       => "$param5",
                        'rolename'         => "$param1",
                        'role_permission'  => "$param6",
                        'objecttype'       => "file",
                        'domain_name'      => "$param2",
                        'distribution'     => "$param3",
                        'created_date'     => "$date",
                        'created_time'     => "$time",
                        'created_by'       => "$authNamespace->andutteye_username"
                );
                $db->insert('andutteye_rolepermissions', $data);
                header("Location:index.php?main=change_file_permissions&param1=$param1&param2=$param2&param3=$param3&param4=$param5");
                exit;
}


echo '<div id="content">
        <div class="section content">
        <h2 class="BigTitle">File permissions for role <span class="ColoredTxt">'.$param1.'</span> Domain ' . $param2 . ' Distribution ' . $param3 . '</h2>
        <div>';
echo '
<fieldset class="GroupField">
        <legend>Available files</legend>
		<table>
			<th>Permission</th>
			<th>File</th>
			<th>Submit</th>
		</tr>';

$sql = $db->query("select filename,tagging from andutteye_files where revision = '1' order by filename asc");
 while ($row = $sql->fetch()) {
                $filename  = $row['filename'];
                $tagging  = $row['tagging'];
		$ressql = $db->query("select * from andutteye_files where domain_name = '$param2' and distribution = '$param3' and filename = '$filename' and tagging = '$tagging' and revision = '1'");
                $ressql = $ressql->fetchObject();

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='change_file_permissions'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='$param2'>
                <input type='hidden' name='param3' value='$param3'>
                <input type='hidden' name='param4' value='$param4'>
                <input type='hidden' name='param5' value='$ressql->directory/$filename$ressql->tagging'>";

                $subsql = $db->query("select * from andutteye_rolepermissions where roleobject = '$ressql->directory/$filename$ressql->tagging' and rolename = '$param1' and objecttype = 'file' and domain_name = '$param2' and distribution = '$param3'");
                $res = $subsql->fetchObject();

                if(!$res->role_permission) {
                        $current="None (Hidden)";
                }
                elseif($res->role_permission == 1) {
                        $current="Available (Full control)";
                } else {
                        $current="None (Hidden)";
                }

                echo '<td>
                         <select name="param6" style="WIDTH: 200px">
                         	<option value="$res->role_permission">Set now: ' . $current . '
                                <option value="0"> None (Hidden)
                                <option value="1"> Available (Full control)
                         </select>
                        </td>
                     <td>
                            <img src="themes/' . $authNamespace->andutteye_theme . '/file_1.png" alt="" title="" />&nbsp;
                             <a href="#" class="Tips2" title="Permission set by:' . $res->created_by . ' Date:' . $res->created_date . ' Time:' . $res->created_time . '">' .$ressql->directory.'/'.$ressql->filename.''.$ressql->tagging.'</a></td>
                            <td><input class="button" type="submit" value="Set permission"></label></td>
                </tr></form>';
	}


echo '</table></div></div>';


// End of function
}
function change_package_permissions($param1,$param2,$param3,$param4,$param5,$param6) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$date = date("20y-m-d");
$time = date("H:m:s");

if($param5) {
	$sql = "delete from andutteye_rolepermissions where roleobject = '$param5' and rolename = '$param1' and objecttype = 'package' and domain_name = '$param2' and distribution = '$param3'";
        $db->query($sql);

        $data = array(
                        'roleobject'       => "$param5",
                        'rolename'         => "$param1",
                        'role_permission'  => "$param6",
                        'objecttype'       => "package",
                        'domain_name'      => "$param2",
                        'distribution'     => "$param3",
                        'created_date'     => "$date",
                        'created_time'     => "$time",
                        'created_by'       => "$authNamespace->andutteye_username"
                );
                $db->insert('andutteye_rolepermissions', $data);
                header("Location:index.php?main=change_package_permissions&param1=$param1&param2=$param2&param3=$param3&param4=$param5");
                exit;
}

echo '<div id="content">
	<div class="section content">
	<h2 class="BigTitle">Add package permissions for role <span class="ColoredTxt">'.$param1.'</span> Domain ' . $param2 . ' Distribution ' . $param3 . '</h2>
        <div>
';


echo '
<fieldset class="GroupField">
        <legend>Search software packages</legend>
                        <form method="get" action="index.php">
                                <input type="hidden" name="main" value="change_package_permissions">
                                <input type="hidden" name="param1" value="' . $param1 . '">
                                <input type="hidden" name="param2" value="' . $param2 . '">
                                <input type="hidden" name="param3" value="' . $param3 . '">

                                <label>
                                        <input type="text" name="param4" value="" size="70">
                                        <input class="button" type="submit" value="Search">
                                </label>
                        </form>
</fieldset>
';
if($param4) {
	$sql = $db->query("select * from andutteye_packages where domain_name = '$param2' and distribution = '$param3' and aepackage like '%$param4%' order by aepackage asc limit 0,50");
} else {
	$sql = $db->query("select * from andutteye_packages where domain_name = '$param2' and distribution = '$param3' order by aepackage asc limit 0,50");
}
echo '
<fieldset class="GroupField">
        <legend>Software packages</legend>
		<table>
			<th>Permission</th>
			<th>Package</th>
			<th>Submit</th>
		</tr>';

	while ($row = $sql->fetch()) {
        	$aepackage  = $row['aepackage'];

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='change_package_permissions'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='$param2'>
                <input type='hidden' name='param3' value='$param3'>
                <input type='hidden' name='param4' value='$param4'>
                <input type='hidden' name='param5' value='$aepackage'>";

                $subsql = $db->query("select * from andutteye_rolepermissions where roleobject = '$aepackage' and rolename = '$param1' and objecttype = 'package' and domain_name = '$param2' and distribution = '$param3'");
                $res = $subsql->fetchObject();

                if(!$res->role_permission) {
                	$current="None (Hidden)";
                }
                elseif($res->role_permission == 1) {
                        $current="Available (Full control)";
                } else {
                        $current="None (Hidden)";
                }

                echo '<td>
                        	<select name="param6" style="WIDTH: 200px">
                                	<option value="$res->role_permission">Set now: ' . $current . '
                                        <option value="0"> None (Hidden)
                                        <option value="1"> Available (Full control)
                                </select>
                      </td>
                      <td>
                        	<img src="themes/' . $authNamespace->andutteye_theme . '/package_1.png" alt="" title="" />&nbsp;
                                <a href="#" class="Tips2" title="Permission set by:' . $res->created_by . ' Date:' . $res->created_date . ' Time:' . $res->created_time . '">' . $aepackage . '</a></td>
                      <td><input class="button" type="submit" value="Set permission"></td>
		</tr>
                </div></form>';
}


echo '</table></div></div>
';

// End of subfunction
}

function show_packages($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select * from andutteye_specifications where system_name = '$param1' and revision = '$param3'");
$res = $sql->fetchObject();

if($param3 && $param4 ) {
	$sql = "update andutteye_choosenpackages set specaction = 'R' where system_name = '$param1' and specid = '$param3' and seqnr = '$param4'";
        $db->query($sql);

}
if($param8 && $param9) { 

$date = date("20y-m-d");
$time = date("H:m:s");

if(!$param6) {
	$param6="0";
}
if(!$param7) {
	$param7="0";
}

$data = array(
        'system_name'  => "$param1",
		'aepackage'    => "$param5",
        'aeversion'    => "$param6",
        'aerelease'    => "$param7",
        'aearchtype'   => "$param8",
        'aeaction'     => "$param9",
        'specid'       => "$param3",
        'specaction'   => "A",
        'created_date' => "$date",
        'created_time' => "$time",
        'created_by'   => "$authNamespace->andutteye_username"
	);
	$db->insert('andutteye_choosenpackages', $data);
}

echo '<div id="content">
	<div class="section content">
		<h2 class="BigTitle">Choosen <span class="ColoredTxt">Individual Packages</span> for ' . $param1 . ' Revision ' . $param3 . '</h2>
			<div>
				<table>
					<th>Package</th>
					<th>Package(s)</th>
					<th>Revision</th>
					<th>Action</th>
					<th>Delete</th>
				</tr>
';

                $sql = $db->query("select * from andutteye_choosenpackages where system_name = '$param1' and specid = '$param3 ' and specaction = 'N' order by aeaction asc");
                while ($row = $sql->fetch()) { 
                        $seqnr  = $row['seqnr'];
                        $system_name  = $row['system_name'];
                        $aepackage    = $row['aepackage'];
                        $aeversion    = $row['aeversion'];
                        $aerelease    = $row['aerelease'];
                        $aearchtype   = $row['aearchtype'];
                        $aeaction     = $row['aeaction'];

			echo '
				<td>
				<a href="#" class="Tips2" title="Package:' . $aepackage . ' Version:' . $aeversion . '  Release:' . $aerelease . ' Arch:' . $aearchtype . ' Action:' . $aeaction . '">' . $aepackage . '</a></td>
				<td>'.$aeversion.'</td>
				<td>'. $aerelease.'</td>
				<td>'.$aeaction.'</td>
				<td><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> <a href="index.php?main=show_packages&param1=' . $param1 . '&param3=' . $param3 . '&param4=' . $seqnr . '">Delete</a></td>
				</tr>
';
                }
echo '</table></div>';

echo '
	<br />

<fieldset class="GroupField">
	<legend>Pending package transactions</legend>
		<table>
			<th>Package</th>
			<th>Version</th>
			<th>Release</th>
			<th>Action</th>
			<th>Status</th>
		</tr>';

		$sql = $db->query("select * from andutteye_choosenpackages where system_name = '$param1' and specid = '$param3' and specaction != 'N'");
                while ($row = $sql->fetch()) {
                        $seqnr  = $row['seqnr'];
                        $system_name  = $row['system_name'];
                        $aepackage    = $row['aepackage'];
                        $aeversion    = $row['aeversion'];
                        $aerelease    = $row['aerelease'];
                        $aearchtype   = $row['aearchtype'];
                        $aeaction     = $row['aeaction'];
                        $specaction   = $row['specaction'];

			echo '
				<td><a href="#" class="Tips2" title="Package:' . $aepackage . ' Version:' . $aeversion . ' Release:' . $aerelease . ' Arch:' . $aearchtype . ' Action:' . $aeaction . '">' . $aepackage . '</a></td>
				<td>'.$aeversion.'</td>
				<td>'.$aerelease.'</td>
				<td>'.$aeaction.'</td>
				<td><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <a href="#" class="Tips2" title="Package is in pending state until you save the specification. State A means add State R means remove.">Pending (' . $specaction . ')</a></td>
			</tr>
';
                }
echo '
</table>
</fieldset>
';

echo '
<fieldset class="GroupField">
	<legend>Search software packages</legend>
			<form method="get" action="index.php">
				<input type="hidden" name="main" value="show_packages">
				<input type="hidden" name="param1" value="' . $param1 . '">
				<input type="hidden" name="param3" value="' . $param3 . '">

				<label>
					<input type="text" name="param2" value="" size="70">
					<input class="button" type="submit" value="Search">
				</label>
			</form>
</fieldset>
';

echo '
<fieldset class="GroupField">
	<legend>Available software packages in ' . $res->distribution . '</legend>
		<table>
			<th>Package</th>
			<th>Arch</th>
			<th>Distribution</th>
			<th>Add</th>
			<th>Exclude</th>
			<th>Version</th>
			<th>Release</th>
			<th>Submit</th>
		</tr>';

		if($param2) {
                	$sql = $db->query("select aepackage,aeversion,aerelease,aearchtype,distribution from andutteye_packages where aepackage like '%$param2%' and distribution = '$res->distribution' order by aepackage asc limit 0,20");
		} else {
                	$sql = $db->query("select aepackage,aeversion,aerelease,aearchtype,distribution from andutteye_packages order by aepackage asc limit 0,20");
		}
                while ($row = $sql->fetch()) {
                        $aepackage  = $row['aepackage'];
                        $aeversion  = $row['aeversion'];
                        $aerelease  = $row['aerelease'];
                        $aearchtype  = $row['aearchtype'];
                        $distribution  = $row['distribution'];

		  	$subsql  = $db->query("select aepackage from andutteye_choosenpackages where system_name = '$param1' and specid = '$param3' and specaction = 'N' and aepackage = '$aepackage' and aearchtype = '$aearchtype'");
        		$choosen = $subsql->fetchObject();

			if($aepackage == $choosen->aepackage) {
				continue;
			}

			// Verifying package access, the user needs to have roleaccess or be andutteye admin.
			if((verify_role_object_permission($aepackage,'package',1,$res->domain_name,$res->distribution)) || ($authNamespace->andutteye_admin)) {

			echo '
			<form method="get" action="index.php">
				<input type="hidden" name="main" value="show_packages">
				<input type="hidden" name="param1" value="' . $param1 . '">
				<input type="hidden" name="param3" value="' . $param3 . '">
				<input type="hidden" name="param5" value="' . $aepackage . '">
				<input type="hidden" name="param8" value="' . $aearchtype . '">
			

				<td><a href="#" class="Tips2" title="Package:' . $aepackage . ' Version:' . $aeversion . ' Release:' . $aerelease . ' Archtype:' . $aearchtype . ' Distribution:' . $distribution . '"><b>' . $aepackage .'</b></a></td>
			        <td>' . $aearchtype . '</td>
				<td>' . $distribution . '</td>
				<td><input type="radio" name="param9" value="add"></td>
				<td><input type="radio" name="param9" value="exclude"></td>
				<td><select name="param6">
						<option value="0"> Auto version 0
						<option value="'.$aeversion.'"> ' . $aeversion . '
					</select>
				</td>
				<td><select name="param7">
					<option value="0"> Auto release 0
					<option value="'.$aerelease.'"> ' . $aerelease . '
				    </select>
				</td>
				<td><input class="button" type="submit" value="Submit"></td>
			</form>
			</tr>
			';
			// End of package access
			}
                }
echo '
</table>
</fieldset>
';
echo '
			<label><img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" /> <a href="index.php?main=system_specification&param1=' . $param1 . '">&nbsp;Back to management profile</a></label>

		</div>
	</div>
';

// End of subfunction
}



function change_events_database($param1,$param2,$param3,$param4,$param5) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select * from andutteye_changeevent order by seqnr desc limit 0,50");

echo '<div id="content">
		<div class="section content">
		<h2 class="BigTitle"><span class="ColoredTxt">Change</span> Events Database</h2>

<fieldset class="GroupField">
	<legend>Search for event</legend>
		<table>
			<th>Search</th>
			<th>Create new</th>
		</tr>
		<td><form method="get" action="index.php">
			<input type="hidden" name="main" value="change_events_database">
			<input type="text" name="param1" value="" size="70"> <input class="button" type="submit" value="Search">
		</td>
		</form>
	<td><img src="themes/$authNamespace->andutteye_theme/new_1.png" alt="" title="" /> <a href="index.php?main=create_new_changeevent"> Create a new changeevent.</a></td>
	</tr>
   </table>
</fieldset>
';

		if($param1) {
echo '
<fieldset class="GroupField">
	<legend>Search results</legend>
		<table>
			<th>System</th>
			<th>Date</th>
			<th>Time</th>
			<th>Event</th>
			<th>Severity</th>
			<th>Creator</th>
		</tr>
';

			$subsql = $db->query("select * from andutteye_changeevent where description like '%$param1%' or information like '%$param1%' or solution like '%$param1%' or workaround like '%$param1%' order by seqnr desc limit 0,50");

		 	while ($row = $subsql->fetch()) {
                        	$seqnr  = $row['seqnr'];
                        	$system_name  = $row['system_name'];
                        	$description  = $row['description'];
                        	$information  = $row['information'];
                        	$solution  = $row['solution'];
                        	$workaround  = $row['workaround'];
                       	 	$severity  = $row['severity'];
                        	$created_date  = $row['created_date'];
                        	$created_time  = $row['created_time'];
                        	$created_by    = $row['created_by'];

                        	if(!$system_name) {
                                	$system_name="No specific";
                        	}
			echo '
				<td>'.$system_name.'</td>
				<td>'.$created_date.'</td>
				<td>'.$created_time.'</td>
				<td><a href="index.php?main=create_new_changeevent&param1=' . $seqnr . '&param9=update" class="Tips2" title="Information:' . $information . ' Solution:' . $solution . ' Workaround:' . $workaround . ' Created:' . $created_date . ' ' . $created_time . ' by:' . $created_by . '">' . $description . '</a>
				</td>
				<td>'.$severity.'</td>
				<td>'.$created_by.'</td>
			</tr>
			';


                	}
echo '
</table>
</fieldset>
';
		}

echo '
			<br />
<fieldset class="GroupField">
	<legend>Changeevents</legend>
		<table>
			<th>System</th>
			<th>Date</th>
			<th>Time</th>
			<th>Event</th>
			<th>Severity</th>
			<th>Creator</th>
		</tr>
';

		while ($row = $sql->fetch()) {
			$seqnr  = $row['seqnr'];
			$system_name  = $row['system_name'];
			$description  = $row['description'];
			$information  = $row['information'];
			$solution  = $row['solution'];
			$workaround  = $row['workaround'];
			$severity  = $row['severity'];
			$created_date  = $row['created_date'];
			$created_time  = $row['created_time'];
			$created_by    = $row['created_by'];

			if(!$system_name) {
				$system_name="No specific";
			}

			echo '
				<td>'.$system_name.'</td>
				<td>'.$created_date.'</td>
				<td>'.$created_time.'</td>
				<td><a href="index.php?main=create_new_changeevent&param1=' . $seqnr . '&param9=update" class="Tips2" title="Information:' . $information . ' Solution:' . $solution . ' Workaround:' . $workaround . ' Created:' . $created_date . ' ' . $created_time . ' by:' . $created_by . '">' . $description . '</a>
				</td>
				<td>'.$severity.'</td>
				<td>'.$created_by.'</td>
			</tr>
			';

		}

echo '
</table>
</fieldset>
		</div>
	</div>
';

// End of subfunction
}



function system_specification($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12,$param13,$param14) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if(!$param2) {
	$sql = $db->query("select * from andutteye_specifications where system_name = '$param1' order by seqnr desc limit 0,1");
	$res = $sql->fetchObject();

        $sql   = $db->query("select seqnr from andutteye_choosenpackages where system_name ='$param1' and specid = '$res->revision'");
        $packages = $sql->fetchAll();
        $packages = count($packages);

        $sql   = $db->query("select seqnr from andutteye_choosenbundles where system_name ='$param1' and specid = '$res->revision'");
        $bundles = $sql->fetchAll();
        $bundles = count($bundles);

} else {
	$sql = $db->query("select * from andutteye_specifications where system_name = '$param1' and revision = '$param2'");
	$res = $sql->fetchObject();
		
	$sql   = $db->query("select seqnr from andutteye_choosenpackages where system_name ='$param1' and specid = '$param2'");
        $packages = $sql->fetchAll();
        $packages = count($packages);

	$sql   = $db->query("select seqnr from andutteye_choosenbundles where system_name ='$param1' and specid = '$param2'");
        $bundles = $sql->fetchAll();
        $bundles = count($bundles);
}

if($param12) {
	$tst = $db->query("select * from andutteye_specifications where system_name = '$param1' order by seqnr desc limit 0,1");
        $tst = $tst->fetchObject();

	$date = date("20y-m-d");
	$time = date("H:m:s");
	$next_revision=($tst->revision + 1);

if(!$param13 ) {
	$param13="0";
}
        $data = array(
       		'revision'       => "$next_revision",
                'system_name'    => "$param1",
                'packagetype'    => "$param5",
                'archtype'       => "$param7",
                'distribution'   => "$param8",
                'package_update' => "$param12",
                'config_update'  => "$param14",
                'patchlevel'     => "$param13",
                'created_date'   => "$date",
                'created_time'   => "$time",
                'created_by'     => "$authNamespace->andutteye_username"
        );
        $db->insert('andutteye_specifications', $data);

	$sql = $db->query("select * from andutteye_choosenbundles where system_name = '$param1' and specid = '$res->revision' and specaction = 'N'");
        while ($row = $sql->fetch()) {
        	$bundle = $row['bundle'];
        	$revision = $row['revision'];

		$data = array(
                	'bundle'       => "$bundle",
                	'system_name'  => "$param1",
                	'revision'     => "$revision",
                	'specid'       => "$next_revision",
                	'specaction'   => "N",
                	'created_date' => "$date",
                	'created_time' => "$time",
                	'created_by'   => "$authNamespace->andutteye_username"
        	);
        	$db->insert('andutteye_choosenbundles', $data);
        }
	$sql = $db->query("select * from andutteye_choosenbundles where system_name = '$param1' and specid = '$res->revision' and specaction  = 'A'");
        while ($row = $sql->fetch()) {
                $bundle = $row['bundle'];
                $revision = $row['revision'];

                $data = array(
                        'bundle'       => "$bundle",
                        'system_name'  => "$param1",
                        'revision'     => "$revision",
                        'specid'       => "$next_revision",
                	'specaction'   => "N",
                        'created_date' => "$date",
                        'created_time' => "$time",
                        'created_by'   => "$authNamespace->andutteye_username"
                );
                $db->insert('andutteye_choosenbundles', $data);
        }
	$sql = "update andutteye_choosenbundles set specaction = 'N' where system_name = '$param1' and specid = '$res->revision'";
        $db->query($sql);

	# Packages
	$sql = $db->query("select * from andutteye_choosenpackages where system_name = '$param1' and specid = '$res->revision' and specaction = 'N'");
        while ($row = $sql->fetch()) {
                $aepackage = $row['aepackage'];
                $aeversion = $row['aeversion'];
                $aerelease = $row['aerelease'];
                $aearchtype = $row['aearchtype'];
		$aeaction = $row['aeaction'];

                $data = array(
                        'aepackage'    => "$aepackage",
                        'system_name'  => "$param1",
                        'aeversion'     => "$aeversion",
                        'aerelease'     => "$aerelease",
                        'aearchtype'     => "$aearchtype",
                        'aeaction'     => "$aeaction",
                        'specid'       => "$next_revision",
                        'specaction'   => "N",
                        'created_date' => "$date",
                        'created_time' => "$time",
                        'created_by'   => "$authNamespace->andutteye_username"
                );
                $db->insert('andutteye_choosenpackages', $data);
        }
        $sql = $db->query("select * from andutteye_choosenpackages where system_name = '$param1' and specid = '$res->revision' and specaction  = 'A'");
        while ($row = $sql->fetch()) {
		$aepackage = $row['aepackage'];
                $aeversion = $row['aeversion'];
                $aerelease = $row['aerelease'];
                $aearchtype = $row['aearchtype'];
		$aeaction = $row['aeaction'];


                $data = array(
			'aepackage'    => "$aepackage",
                        'system_name'  => "$param1",
                        'aeversion'    => "$aeversion",
                        'aerelease'    => "$aerelease",
                        'aearchtype'   => "$aearchtype",
                        'aeaction'     => "$aeaction",
                        'specid'       => "$next_revision",
                        'specaction'   => "N",
                        'created_date' => "$date",
                        'created_time' => "$time",
                        'created_by'   => "$authNamespace->andutteye_username"
                );
                $db->insert('andutteye_choosenpackages', $data);
        }
        $sql = "update andutteye_choosenpackages set specaction = 'N' where system_name = '$param1' and specid = '$res->revision'";
        $db->query($sql);


	# Loading current view
        $sql = $db->query("select * from andutteye_specifications where system_name = '$param1' order by seqnr desc limit 0,1");
        $res = $sql->fetchObject();

        $sql   = $db->query("select seqnr from andutteye_choosenpackages where system_name ='$param1' and specid = '$res->revision' and specaction = 'N'");
        $packages = $sql->fetchAll();
        $packages = count($packages);

        $sql   = $db->query("select seqnr from andutteye_choosenbundles where system_name ='$param1' and specid = '$res->revision'");
        $bundles = $sql->fetchAll();
        $bundles = count($bundles);
}

$subsql = $db->query("select seqnr from andutteye_choosenbundles where system_name = '$param1' and specaction != 'N'");
$bundle = $subsql->fetchAll();
$bundle = count($bundle);

$subsql = $db->query("select seqnr from andutteye_choosenpackages where system_name = '$param1' and specaction != 'N'");
$package = $subsql->fetchAll();
$package = count($package);

echo '
	<div id="content">
		<div class="section content">

<fieldset class="GroupField">
	<legend><span class="BigTitle"><span class="ColoredTxt">System Specification</span> for ' . $param1 . '</span></legend>

			<form method="get" action="index.php">
				<div class="leftcol">
					<table>
					<th>System management settings</th>
					</tr>

					<input type="hidden" name="main" value="system_specification">
					<input type="hidden" name="param1" value="' . $param1 . '">
					<input type="hidden" name="param2" value="' . $param2 . '">
				
					<td>
					System ' . $res->system_name . '</td>
					</tr>
					<td>
					Package type</td>
					</tr>
					<td>
						<select name="param5" style="WIDTH: 260px">
							<option value="' . $res->packagetype . '"> ' . $res->packagetype . '
							<option value="rpm"> rpm
							<option value="apt"> apt
							<option value="andutteye"> andutteye
						</select>
					</td>
					</tr>
					<td>System patchlevel</td>
					</tr>
					<td>
						<select name="param13" style="WIDTH: 260px">';

        				$sql2 = $db->query("select distinct(patchlevelinfo) from andutteye_packages where distribution = '$res->distribution' and patchlevel = '$res->patchlevel' and patchlevelinfo is not NULL");
        				$res2 = $sql2->fetchObject();

				echo '<option value="' . $res->patchlevel . '"> ' . $res->patchlevel . ' (' . $res2->patchlevelinfo . ')';

                          $sql = $db->query("select distinct(patchlevel) from andutteye_packages where distribution = '$res->distribution' order by patchlevel asc");
                          while ($row = $sql->fetch()) {
                          	$patchlevel = $row['patchlevel'];

        				$sql2 = $db->query("select distinct(patchlevelinfo) from andutteye_packages where distribution = '$res->distribution' and patchlevel = '$patchlevel' and patchlevelinfo is not NULL");
        				$res2 = $sql2->fetchObject();
					echo '<option value="' . $patchlevel . '"> ' . $patchlevel . ' (' . $res2->patchlevelinfo . ')';
			}

echo '
						</select>
					</td>
					</tr>

					<th>Packagemanagement status</th>
					</tr>';

                        if("$res->package_update" == "Active") {
					echo '
					<td>
						<input type="radio" checked="This" name="param12" value="Active"> Active
					</td>
					</tr>
					<td>
						<input type="radio" name="param12" value="Disabled"> Disabled
					</td>
					</tr>
					';
                        } else {
					echo '
					<td>
						<input type="radio" name="param12" value="Active"> Active
					</td>
					</tr>
					<td>
						<input type="radio" checked="This" name="param12" value="Disabled"> Disabled
					</td>
					</tr>
					';
			}
				echo '
					<th>Filemanagement status</th>
					</tr>';

                        if("$res->config_update" == "Active") {
					echo '
					<td><input type="radio" checked="This" name="param14" value="Active"> Active</td>
					</tr>
					<td><input type="radio" name="param14" value="Disabled"> Disabled</td>
					</tr>
					';
                        } else {
					echo '
					<td><input type="radio" name="param14" value="Active"> Active</td>
					</tr>
					<td><input type="radio" checked="This" name="param14" value="Disabled"> Disabled</td>
					</tr>
					';
			}
			echo '
				<th>Systemprovisioning</th>
				</tr>';

			if("$res->revision" > "0") {
					echo '<td><a href="index.php?main=install_new_system&param1=' .$param1. '">
					<img src="themes/' . $authNamespace->andutteye_theme . '/install_system_1.png" alt="" title="" />
					Autoinstall system</a></td></tr>';
			}
			

				echo '
				</table>
				</div>

				<div class="rightcol">
					<table>
						<th>System management settings</th>
						</tr>	
					<td>
					Specification revision:' . $res->revision . ' Created by:' . $res->created_by . ' On:' . $res->created_date . ' ' . $res->created_time . 'i
					</td>
					</tr>
					<td>System archtype</td>
					</tr>
					<td>
						<select name="param7" style="WIDTH: 260px">
							<option value="' . $res->archtype . '"> ' . $res->archtype . '
							<option value="i386"> i386
							<option value="x86_64"> x86_64
							<option value="sparc"> sparc
							<option value="power"> power
						</select>
					</td>
					</tr>

					<td>System distribution</td>
					</tr>
					<td>
						<select name="param8" style="WIDTH: 260px">
							<option value="' . $res->distribution . '"> ' . $res->distribution . '
';

                          $sql = $db->query("select distinct(distribution) from andutteye_packages order by distribution asc");
                          while ($row = $sql->fetch()) {
                          	$distribution = $row['distribution'];
					echo '<option value="' . $distribution . '"> ' . $distribution . '';
			}

			echo'</select>
				</td>
				</tr>
				<th>Pending changes</th>
				</tr>';

                        if($bundle != "0") {
					echo '
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <b>' . $bundle . '</b> pending management bundle changes
					</td>
					</tr>
';
                        }
                        if($package != "0") {
					echo '
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> <b>' . $package . '</b> pending management package changes
					</td>
					</tr>';
                        }
                        
			echo "</table></div>";

echo '
				<h3 class="InfoTitle">Submit changes:</h3>
				<label><input class="button" type="submit" " value="Submit"></label>
';

echo '
			</form>

</fieldset>

			<br />

<fieldset class="GroupField">
	<legend><span class="BigTitle"><span class="ColoredTxt">Load other</span> specification revision</span></legend>
		<table>
			<th>Load older revision</th>
			</tr>

			<form method="get" action="index.php">
				<input type="hidden" name="main" value="system_specification">
				<input type="hidden" name="param1" value="'.$param1.'">
				<td>
					<select name="param2" style="WIDTH: 260px">';

                                 $sql = $db->query("select distinct(revision) from andutteye_specifications where system_name = '$param1' order by seqnr desc");
                                 while ($row = $sql->fetch()) {
                                        $revision = $row['revision'];
					echo '<option value="' . $revision . '"> ' . $revision . '';
                                 }
				echo '
					</select>
					<input class="button" type="submit" value="Load">
				</td>
			</form>
</tr>
</table>
</fieldset>
';

echo '<h3 class="BigTitle"><span class="ColoredTxt">Andutteye</span> Information</h3>

<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle">&nbsp;Individual package selection (<span class="ColoredTxt">' . $packages . '</span>)</span></h3>
        <div class="element">
                <table>
                        <th>Package</th>
                        <th>Version</th>
                        <th>Release</th>
                        <th>Action</th>
                        <th>Change</th>
                        </tr>';


                        $sql = $db->query("select * from andutteye_choosenpackages where system_name = '$param1'  and specid = '$res->revision' and specaction = 'N' order by aeaction asc");
                        while ($row = $sql->fetch()) {
                                $system_name  = $row['system_name'];
                                $aepackage    = $row['aepackage'];
                                $aeversion    = $row['aeversion'];
                                $aerelease    = $row['aerelease'];
                                $aearchtype   = $row['aearchtype'];
                                $aeaction     = $row['aeaction'];
                        	$created_date  = $row['created_date'];
                        	$created_time  = $row['created_time'];
                        	$created_by    = $row['created_by'];

				echo '
					<td>
					<a href="#" class="Tips2" title="Package:' . $aepackage . ' Version:' . $aeversion . ' Release:' . $aerelease . ' Archtype:' . $aearchtype . ' Aeaction:' . $aeaction . ' Created:' . $created_date . ' ' . $created_time . ' by:' . $created_by . '">' . $aepackage . '</a>
					</td>
					<td>' . $aeversion . '</td>
					<td>' . $aerelease . '</td>
					<td>' . $aeaction . '</td>
					<td><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> <a href="index.php?main=show_packages&param1=' . $param1 . '&param3=' . $res->revision . '">Change</a></td>
					</tr>
';
                        }
			if($packages == 0) {
				if(!$res->revision) {
					print '
					<td colspan="5">
					<img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> Specify base configuration above and save an intial management revision to be able to choose software.</td>
					</tr>
					';
				} else {
					echo '
					<td colspan="5">
					<img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> <a href="index.php?main=show_packages&param1=' . $param1 . '&param3=' . $res->revision . '"> Add packages</a></td>
					</tr>
';
				}
			}


echo '
</table>
</div>
<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle">&nbsp;Choosen software bundles (<span class="ColoredTxt">' . $bundles . '</span>)</span></h3>
	<div class="element">
		<table>
			<th>Bundle</th>
			<th>Package(s)</th>
			<th>Revision</th>
			<th>Date</th>
			<th>Change</th>
			</tr>';


			$sql = $db->query("select * from andutteye_choosenbundles where system_name = '$param1' and specid = '$res->revision' order by bundle asc");
			while ($row = $sql->fetch()) {
                        	$system_name  = $row['system_name'];
                        	$bundle       = $row['bundle'];
                        	$revision     = $row['revision'];
                        	$created_date  = $row['created_date'];
                        	$created_time  = $row['created_time'];
                        	$created_by    = $row['created_by'];

                        	$subsql = $db->query("select seqnr from andutteye_bundles where bundle = '$bundle' and revision = '$revision'");
                        	$packages = $subsql->fetchAll();
                        	$packages = count($packages);

				echo '
					<td>
					<a href="#" class="Tips2" title="Bundle:' . $bundle . ' contains ' . $packages . ' packages Created:' . $created_date . ' ' . $created_time . ' by:' . $created_by . '">' . $bundle . '</a>
					</td>
					<td>' . $packages . '</td>
					<td>' . $revision . '</td>
					<td>' . $created_date . '</td>
					<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> <a href="index.php?main=show_bundles&param1=' . $param1 . '&param3=' . $res->revision. '">Change</a>
					</td>
					</tr>';
        		}
			if($bundles == 0) {
				if(!$res->revision) {
					echo '
						<td colspan="5">
						<img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" /> Specify base configuration above and save an intial management revision to be able to choose software.</td>
						</tr>';
				} else {
					echo '
						<td colspan="5">
						<img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> <a href="index.php?main=show_bundles&param1=' . $param1 . '&param3=' . $res->revision . '"> Add bundles</a>
						</td>
						</tr>';
				}
			}

echo '
	</table>
</div>

<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle">&nbsp;Patchlevel management</span></h3>
	<div class="element">
		<table>
			<th>Distribution</th>
			<th>Patchlevel</th>
			<th>Status</th>
			<th>Log</th>
			<th>By</th>
			</tr>';

			 $sql = $db->query("select * from andutteye_patchlevel where distribution = '$res->distribution' order by patchlevel desc");
                       	 while ($row = $sql->fetch()) {
                                $distribution  = $row['distribution'];
                                $patchlevel    = $row['patchlevel'];
                                $status        = $row['status'];
                                $log           = $row['log'];
                                $created_date  = $row['created_date'];
                                $created_time  = $row['created_time'];
                                $created_by    = $row['created_by'];

				echo '
					<td>' . $distribution . '</td>
					<td>' . $patchlevel . '</td>
					<td>' . $status . '</td>
					<td>' . $log . '</td>
					<td>' . $created_by . '</td>
					</tr>';

				echo '
					<form method="get" action="index.php">
						<input type="hidden" name="main" value="system_configuration">
						<input type="hidden" name="param1" value="' . $param1 . '">
						<input type="hidden" name="' . $seqnr . '" value="' . $param3 . '">
							<div class="table">
								<div class="column40"><label>Change patchlevel characteristics here</a></label></div>
								<div class="column"><label>Open patchlevel<input type="radio" name="param2" value="open"></label></div>
								<div class="column"><label>Lock patchlevel<input type="radio" name="param2" value="disable"></label></div>
								<div class="column"><label>' . $created_date . '</label></div>
								<div class="column"><label>' . $created_time . '</label></div>
							</div>
						<label><input class="button" type="submit" value="Change patchlevel status"></label>
						<br />
					</form>
';

			}


echo '
</table>
</div>

<h3 class="toggler"><img src="themes/' . $authNamespace->andutteye_theme . '/category.png" alt="" title="" /><span class="InfoTitle">&nbsp;System management logs</span></h3>
	<div class="element">
		<table>
			<th>Managementlog id</th>
			<th>Date</th>
		</tr>';

				
                        $sql = $db->query("select distinct runid,created_date from andutteye_managementlog where system_name = '$res->system_name' order by seqnr desc limit 0,20");
                        while ($row = $sql->fetch()) {
                               $runid  = $row['runid'];
                               $created_date  = $row['created_date'];
				
				echo '
				<td><img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /><a href="index.php?main=show_managementlog&param1=' . $param1 . '&param2=' . $runid . '">Management log RunId ' . $runid . '</a></td>
				<td>'.$created_date.'</td>
				</tr>
';
			}



echo '
</table>
</div>
			
<div class="DivSpacer"></div>
	<label><img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" /> <a href="index.php?main=system_overview&param1=' . $param1 . '">&nbsp;Back to ' . $param1 . ' system overview</a></label>

	</div>
</div>
';
// End of subfunction
}

function monitoring_front($param1,$param2,$param3) {

require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();
if(!verify_role_object_permission($param1,'system',2)) {
        header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20access%20monitoring_front");
        exit;
}

$subsql = $db->query("select seqnr from andutteye_alarm where system_name = '$param1' and status = 'OPEN' or status = 'ACK'");
$open = $subsql->fetchAll();
$open = count($open);

$subsql = $db->query("select seqnr from andutteye_alarm where system_name = '$param1' and status = 'FREEZE'");
$freeze = $subsql->fetchAll();
$freeze = count($freeze);

$subsql = $db->query("select seqnr from andutteye_alarm where system_name = '$param1' and status = 'CLOSED'");
$closed = $subsql->fetchAll();
$closed = count($closed);

echo '<div id="content">
		<p class="BigTitle">
			<span class="ColoredTxt">Monitoring Alarms</span> for ' . $param1 . '</span>
		</p>
';

echo '
<fieldset class="GroupField">
	<legend>Alarm trend for ' . $param1 . '</legend>';

	include_once 'graph/php-ofc-library/open_flash_chart_object.php';
	open_flash_chart_object( '100%', 250, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-system-alarm-data.php?system=$param1", false );

echo '
</fieldset>
';

echo '
<fieldset class="GroupField">
	<legend>Open alarms</legend>
	
	<table>
		<th>Alert</th>
		<th>Repeatcount</th>
		<th>Status</th>
		<th>Severity</th>
		<th>Message</th>
	</tr>
';

$sql    = $db->query("select * from andutteye_alarm where system_name = '$param1' and status = 'OPEN' or status = 'ACK' order by seqnr desc");
        while ($row = $sql->fetch()) {
                        $seqnr                  = $row['seqnr'];
                        $system_name            = $row['system_name'];
                        $shortinformation       = $row['shortinformation'];
                        $longinformation        = $row['longinformation'];
                        $status                 = $row['status'];
                        $severity               = $row['severity'];
                        $repeatcount            = $row['repeatcount'];
                        $created_date           = $row['created_date'];
                        $created_time           = $row['created_time'];
                        $last_date              = $row['lastdate'];
                        $last_time              = $row['lasttime'];

			echo '
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" />
				</td>
				<td>
					' . $repeatcount . '
				</td>
				<td>
					' . $status . '
				</td>
				<td>
					' . $severity . '
				</td>
				<td>
					<a href="index.php?main=change_alarm&param1=' . $seqnr . '" class="Tips2" title="System:' . $system_name . ' Repeatcounts:' . $repeatcount . ' Recieved:' . $created_date.$created_time . ' Lastrecived:' . $last_date.$last_time . ' Detailedinfo:' . $longinformation . '"> ' . $shortinformation . '</a>
				</td>
				</tr>
				';
        }

echo '
</table>
</fieldset>
';

	echo '<h3 class="BigTitle"><span class="ColoredTxt">Andutteye</span> Alarms</h3>';

	echo '<h3 class="toggler">
		<img src="themes/' . $authNamespace->andutteye_theme . '/group_docu.png" alt="" title="" /><span class="InfoTitle"> MONITOR STATUS </span>
		 </h3> <div class="element">

			<table>
				<th>Historytrend</th>
				<th>Type</th>
				<th>Message</th>
				<th>Date</th>
				<th>Time</th>
			</tr>';

			$sql = $db->query("select distinct(monitorname) from andutteye_monitor_status where system_name = '$param1' order by monitortype desc");
                        while ($row = $sql->fetch()) {
                                $monitorname   = $row['monitorname'];

                                $subsql = $db->query("select * from andutteye_monitor_status where monitorname = '$monitorname' and system_name = '$param1' order by seqnr desc limit 0,1");
                                        while ($row = $subsql->fetch()) {
                                                 $system_name   = $row['system_name'];
                                                 $monitorname   = $row['monitorname'];
                                                 $monitortype   = $row['monitortype'];
                                                 $monitormessage   = $row['monitormessage'];
                                                 $monitorstatus   = $row['monitorstatus'];
                                                 $created_date   = $row['created_date'];
                                                 $created_time   = $row['created_time'];
                                                 $number_ok   = $row['number_ok'];
                                                 $lastdate_ok   = $row['lastdate_ok'];
                                                 $lasttime_ok   = $row['lasttime_ok'];
                                                 $number_notok   = $row['number_notok'];
                                                 $lastdate_notok   = $row['lastdate_notok'];
                                                 $lasttime_notok   = $row['lasttime_notok'];

                                                echo "<td>
							<img src='themes/$authNamespace->andutteye_theme/revision_1.png' alt='' title='' /> <a href='index.php?main=show_monitor_status&param1=$param1&param2=$monitorname&param3=$monitortype'>[History]</a> $monitortype
						</td>";
                                                echo "<td>$monitorstatus</td>";
                                                echo "<td>
							<a href=''  class='Tips2' title='System:$system_name Monitor:$monitorname Type:$monitortype Status:$monitorstatus NrOk:$number_ok LastdateOk:$lastdate_ok LasttimeOk:$lasttime_ok NrNotOk:$number_notok LastdateNotOk:$lastdate_notok LasttimeNotOk:$lasttime_notok'>$monitormessage</a>
						     </td>";
                                                echo "<td>$created_date</td>";
                                                echo "<td>$created_time</td>";
                                                echo "</tr>";
                                        }
                        }

		echo "</table>";
		echo '</div>';

		echo '<h3 class="toggler">
			<img src="themes/' . $authNamespace->andutteye_theme . '/freezed_alarms.png" alt="" title="" /><span class="InfoTitle"> FREEZED ALARMS (<span class="ColoredTxt">' . $freeze .'</span>)</span>
			</h3>
			<div class="element">
			<table>
				<th>System</th>
				<th>Repeatcount</th>
				<th>Messagetype</th>
				<th>Severity</th>
				<th>Message</th>
			</tr>';

$sql    = $db->query("select * from andutteye_alarm where system_name = '$param1' and status = 'FREEZE' order by seqnr desc");
        while ($row = $sql->fetch()) {
			$seqnr = $row['seqnr'];
                        $system_name            = $row['system_name'];
                        $shortinformation       = $row['shortinformation'];
                        $longinformation        = $row['shortinformation'];
                        $status                 = $row['status'];
                        $severity               = $row['severity'];
                        $repeatcount            = $row['repeatcount'];
                        $created_date           = $row['created_date'];
                        $created_time           = $row['created_time'];
                        $last_date              = $row['lastdate'];
                        $last_time              = $row['lasttime'];

			echo '
				<td>' . $system_name . '</td>
				<td>' . $repeatcount . '</td>
				<td>' . $status . '</td>
				<td>' . $severity . '</td>
				<td>
				<a href="index.php?main=change_alarm&param1=' . $seqnr . '" class="Tips2" title="Repeatcounts:' . $repeatcount . ' Recieved:' . $created_date.$created_time . ' Lastrecived:' . $last_date.$last_time . ' Detailedinfo:' . $longinformation . '">' . $shortinformation . '</a>
				</td>
				</tr>
';
        }

echo '
			</table>
			</div>
';

echo '
	<h3 class="toggler">
		<img src="themes/' . $authNamespace->andutteye_theme . '/closed_alarms.png" alt="" title="" /><span class="InfoTitle"> CLOSED ALARMS (<span class="ColoredTxt">' . $closed . '</span>)</span>
			</h3>

			<div class="element">
			<table>
				<th>Historytrend</th>
				<th>Type</th>
				<th>Message</th>
				<th>Date</th>
				<th>Time</th>
			</tr>';

$sql    = $db->query("select * from andutteye_alarm where system_name = '$param1' and status = 'CLOSED' order by seqnr desc limit 0,100");
        while ($row = $sql->fetch()) {
                        $system_name            = $row['system_name'];
                        $shortinformation       = $row['shortinformation'];
                        $longinformation        = $row['shortinformation'];
                        $status                 = $row['status'];
                        $severity               = $row['severity'];
                        $repeatcount            = $row['repeatcount'];
                        $created_date           = $row['created_date'];
                        $created_time           = $row['created_time'];
                        $last_date              = $row['lastdate'];
                        $last_time              = $row['lasttime'];

			echo '
				<td>' . $system_name . '</td>
				<td>' . $repeatcount . '</td>
				<td>' . $status . '</td>
				<td>' . $severity . '</td>
				<td>' . $shortinformation . '</td>
				</tr>
';
        }


echo '		
</table>
</div>
';

echo '
			<div class="DivSpacer"></div>

			<label>
				<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" /><a href="index.php?main=system_overview&param1=' . $param1 . '">&nbsp;Back to ' . $param1 . ' system overview</a>
			</label>
		</div>
';

// End of subfunction
}



function show_managementlog($param1,$param2) {
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

echo "<div id='content'>
                <div class='section content'>

		<fieldset class='GroupField'>
        	<legend><span class='BigTitle'><span class='ColoredTxt'>Management log </span> for RunId $param2</span> on system $param1</legend>

		<table>
			<th>Date</th>
			<th>Status</th>
			<th>Message</th>
		</tr>";

		$sql = $db->query("select * from andutteye_managementlog where system_name = '$param1' and runid = '$param2' order by seqnr asc");
        	while ($row = $sql->fetch()) {
       			$runid  = $row['runid'];
                	$created_date  = $row['created_date'];
                	$created_time  = $row['created_time'];
                	$logentry  = $row['logentry'];
                	$messagetype  = $row['messagetype'];

		
                        echo "
                        <td>$created_date</td>
                        <td>$messagetype</td>
                        <td><a href='#' class='Tips2' title='Messagetype:$messagetype Date:$created_date Time:$created_time '>$logentry&nbsp;</a></td>
                        </tr>
                        ";
        }

echo "</table>
 <label><img src='themes/$authNamespace->andutteye_theme/back.png' alt='' title='' /><a href='index.php?main=system_specification&param1=$param1'>&nbsp;Back to $param1 system overview</a></label>
	</fieldset>
     </div>
</div>
";





// End of subfunction
}

function system_files($param1,$param2) {
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if(!verify_role_object_permission($param1,'system',3)) {
        // Verify if domain is allowed to be read.
        header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20access%20systemfiles");
        exit;
}

$sql = $db->query("select * from andutteye_systems where system_name = '$param1'");
$res = $sql->fetchObject();

$man = $db->query("select * from andutteye_specifications where system_name = '$param1' order by revision desc limit 0,1");
$man = $man->fetchObject();

echo '
	<div id="content">
		<div class="section content">
			<h2 class="BigTitle"><span class="ColoredTxt">Managment Files</span> for System ' . $res->system_name . ' Distribution ' . $man->distribution . ' Domain ' . $res->domain_name . '</h2>
';

echo '
<fieldset class="GroupField">
	<legend>Select directory</legend>

	<table>
		<th>Directory</th>
		<th>Select</th>
		</tr>';
			$sql = $db->query("select distinct directory from andutteye_files where distribution = '$man->distribution' and domain_name = '$res->domain_name'");
			while ($row = $sql->fetch()) {
				$dir = $row['directory'];

			echo '<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/folder_1.png" alt="" title="" />&nbsp;' . $dir . '
			      </td>
			      <td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" /> <a href="index.php?main=system_files&param1=' . $param1 . '&param2=' . $dir . '">Show files</a>
				</td>
			</tr>
';
		}

echo "</table>";

		if($param2) {
			echo '
			<h3 class="InfoTitle">&nbsp;&nbsp;&nbsp;Files under directory ' . $param2 . '</h3>

				<table>
					<th>File</th>
					<th>Select</th>
				</tr>';

			$sql = $db->query("select distinct directory, filename, tagging from andutteye_files where distribution = '$man->distribution' and directory = '$param2' and domain_name = '$res->domain_name'");
			while ($row = $sql->fetch()) {
				$directory = $row['directory'];
       				$filename = $row['filename'];
       				$tagging = $row['tagging'];

			if(verify_role_object_permission("$directory/$filename$tagging",'file',1,$res->domain_name,$man->distribution)) {
				echo '
					<td>
						<img src="themes/' . $authNamespace->andutteye_theme . '/file_1.png" alt="" title="" />&nbsp;' . $directory . '/'.$filename.''.$tagging.'
					</td>
					<td>
						<img src="themes/' . $authNamespace->andutteye_theme . '/actions.png" alt="" title="" />
						<a href="index.php?main=open_file&param1=' . $filename . '&param2=' . $directory . '&param3=' . $man->distribution . '&param4=' . $tagging . '&param5=' . $param1 . '">Change file</a>
					</td>
				</tr>
				';
			}
		   }
		}
		
echo '
</table>
			<label>
				<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" />
				<a href="index.php?main=system_overview&param1=' . $param1 . '">&nbsp;Back to ' . $param1 . ' system overview</a>
			</label>
</fieldset>

		</div>
	</div>
';

// End of subfunction
}

function prepare_new_system($param1,$param2,$param3) {
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

$pxedir = $db->query("select parametervalue from andutteye_core_configuration where parametername = 'Management_pxe_directory_location'");
$pxedir = $pxedir->fetchObject();

$autodir = $db->query("select parametervalue from andutteye_core_configuration where parametername = 'Management_autoinstall_directory_location'");
$autodir = $autodir->fetchObject();

$sql = $db->query("select * from andutteye_specifications where system_name = '$param1' order by seqnr desc limit 0,1");
$spec = $sql->fetchObject();

if($param3) {
	$sql = $db->query("select * from andutteye_provisioning where system_name = '$param1' and revision = '$param3'");
	$res = $sql->fetchObject();
} else {
	$sql = $db->query("select * from andutteye_provisioning where system_name = '$param1' order by seqnr desc limit 0,1");
	$res = $sql->fetchObject();
}

if($param2 == "manual") {
	$macaddress="$param3";
} else {
	$macaddress="$param2";
}
if($param2 == "") {
	$macaddress="";
	$pxefilename="";
} else {
	$macaddressfixed = preg_replace ("/:/", "-", $macaddress);
	$pxefilename = "01-$macaddressfixed";
}

echo '<div id="content">
      	<div class="section content">
        	<h2 class="BigTitle">Install and reinstall configuration for <span class="ColoredTxt">' .$param1. '</span></h2>
';

echo '
<fieldset class="GroupField">
        <legend><span class="ColoredTxt">Load other</span> provisioning configuration revision</span></legend>
                <table>
                        <th>Load older revision</th>
                        </tr>

                        <form method="get" action="index.php">
                                <input type="hidden" name="main" value="prepare_new_system">
                                <input type="hidden" name="param1" value="'.$param1.'">
                                <input type="hidden" name="param2" value="'.$param2.'">
                                <td>
                                        <select name="param3" style="WIDTH: 260px">';

                                 $sql = $db->query("select distinct(revision) from andutteye_provisioning where system_name = '$param1' order by seqnr desc");
                                 while ($row = $sql->fetch()) {
                                        $revision = $row['revision'];
                                        echo '<option value="' . $revision . '"> ' . $revision . '';
                                 }
                                echo '
                                        </select>
                                        <input class="button" type="submit" value="Load">
                                </td>
                        </form>
</tr>
</table>
</fieldset>';


echo '<form method="post" action="index.php">
      	<input type="hidden" name="main" value="save_autoinstall">
        <input type="hidden" name="param1" value="' . $param1 . '">
        <input type="hidden" name="param4" value="' . $pxefilename . '">
        <input type="hidden" name="param5" value="' . $macaddress . '">

<fieldset class="GroupField">
        <legend><span class="ColoredTxt">Provisioning</span> fileoverview</span></legend>
                                <div class="leftcol">
                                        <label>Current loaded revision <b>' . $res->revision . '.0</b></label>
                                        <label>Created on ' . $res->created_date . ' ' . $res->created_time . '</label>';

				if (file_exists("$pxedir->parametervalue/$spec->distribution/$spec->archtype/$pxefilename")) {
					echo "<label>Pxe file $pxedir->parametervalue/$spec->distribution/$spec->archtype/$pxefilename (<b>OK</b>)</label>";
				} else {
					echo "<label>Pxe file $pxedir->parametervalue/$spec->distribution/$spec->archtype/$pxefilename (<b>Not present</b>)</label>";
				}
				if (file_exists("$autodir->parametervalue/$spec->distribution/$spec->archtype/$param1.auto")) {
					echo "<label>Autoinstall file $autodir->parametervalue/$spec->distribution/$spec->archtype/$param1.auto (<b>OK</b>)</label>";
				} else {
					echo "<label>Autoinstall file $autodir->parametervalue/$spec->distribution/$spec->archtype/$param1.auto (<b>Not present</b>)</label>";
				}

                                echo '</div>
                                <div class="rightcol">
                                        <label>Created by ' . $res->created_by . '</label>
                                        <label>Sequence number of last revision ' . $res->seqnr . '</label>
                                        <label>Mac address used:  ' . $macaddress . '</label>
                                        <label>PXE filename: ' . $pxefilename . '</label>
                                </div>
</fieldset>

<fieldset class="GroupField">
        <legend><span class="ColoredTxt">System</span> PXE configuration</span></legend>
                                <label><textarea cols="100" rows="10" name="param2">' . $res->pxefile . '</textarea></label>
</fieldset>
<fieldset class="GroupField">
        <legend><span class="ColoredTxt">Autoinstall</span> configuration</span></legend>
                                <label><textarea cols="100" rows="30" name="param3">' . $res->autoinstfile . '</textarea></label>
</fieldset>
<fieldset class="GroupField">
        <legend><span class="ColoredTxt">Ready</span> to install?</span></legend>
	<label>By pressing install the autoinstall configuration and PXE configuration will be saved and a install command will be sent to the Andutteye agent. The system will reboot and be installed according to your configurations and settings.</label>
	<label><input class="button" type="submit" value="Install system"></label>
</fieldset>';



// End of subfunction
}

function open_file($param1,$param2,$param3,$param4,$param5,$param6) {
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();


if($param6) {
	$sql = $db->query("select * from andutteye_files where filename = '$param1' and directory = '$param2' and distribution = '$param3' and tagging = '$param4' and revision = '$param6'");
	$res = $sql->fetchObject();

	$subsql = $db->query("select * from andutteye_files where filename = '$param1' and directory = '$param2' and distribution = '$param3' and tagging = '$param4' order by revision desc limit 0,1");
	$seq = $subsql->fetchObject();
} else {
	$sql = $db->query("select * from andutteye_files where filename = '$param1' and directory = '$param2' and distribution = '$param3' and tagging = '$param4' order by revision desc limit 0,1");
	$res = $sql->fetchObject();

	$subsql = $db->query("select * from andutteye_files where filename = '$param1' and directory = '$param2' and distribution = '$param3' and tagging = '$param4' order by revision desc limit 0,1");
	$seq = $subsql->fetchObject();
}

if(!verify_role_object_permission("$res->directory/$res->filename$res->tagging",'file',1,$res->domain_name,$res->distribution)) {
	header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20access%20that%20file");
	exit;
}

echo '<div id="content">
	<div class="section content">
		<h2 class="BigTitle">Review or <span class="ColoredTxt">Change File</span> ' . $res->directory . '/' . $res->filename. ' </h2>';

		echo '
			<form method="get" action="index.php">

				<input type="hidden" name="main" value="save_file">
				<input type="hidden" name="param7" value="' . $seq->seqnr . '">
				<input type="hidden" name="param8" value="' . $param5 . '">
				<input type="hidden" name="param10" value="' . $res->domain_name . '">
				<input type="hidden" name="param11" value="' . $res->filelocked . '">

<fieldset class="GroupField">
	<legend>File Overview</legend>
				<div class="leftcol">
					<table>
					<th>Fileinformation</th>
					</tr>
					<td>Current loaded revision <b>' . $res->revision . '.0</b></td>
					</tr>
					<td>Tagging ' . $res->tagging . '</td>
					</tr>
					<td>Domain '.$res->domain_name.' Distribution ' . $res->distribution . '</td>
					</tr>
					</table>
				</div>
				<div class="rightcol">
					<table>
					<th>Fileinformation</th>
					</tr>
					<td>Created by ' . $res->created_by . '</td>
					</tr>
					<td>Created on ' . $res->created_date . ' ' . $res->created_time . '</td>
					</tr>
					<td>Sequence number of last revision ' . $seq->seqnr . '</td>
					</tr>
					</table>
				</div>
</fieldset>

<fieldset class="GroupField">
	<legend>File content</legend>';
		
			// Verify if file is locked, if so we disable content text area.
			if($res->filelocked == "yes") {
				echo '<label><textarea cols="100" rows="30" name="param1" DISABLED>' . $res->content . '</textarea></label>';
			} else {
				echo '<label><textarea cols="100" rows="30" name="param1">' . $res->content . '</textarea></label>';
			}
				
echo '</fieldset>

<fieldset class="GroupField">
	<legend>File related</legend>
				<div class="leftcol">
					<table>
					<th>File owner</th>
					</tr>
					<td><input type="text" name="param2" maxlength="255" value="' . $res->perm_owner . '" size="50"></td>
					</tr>
					<th>File group</th>
					</tr>
					<td><input type="text" name="param3" maxlength="255" value="' . $res->perm_group . '" size="50"></td>
					</tr>
					<th>File permission</th>
					</tr>
					<td><input type="text" name="param4" maxlength="255" value="' . $res->perms . '" size="50"></td>
					</tr>
					</table>
				</div>
				<div class="rightcol">
					<table>
					<th>Pre hook execution command or program</th>
					</tr>
					<td><input type="text" name="param5" maxlength="255"  value="' . $res->prestep . '" size="50"></td>
					</tr>
					<th>Post hook execution command or program</th>
					</tr>
					<td><input type="text" name="param6" maxlength="255" value="' . $res->poststep . '" size="50"></td>
					</tr>
					<th>Specify file installation order</th>
					</tr>
					<td>
						<select name="param9" style="WIDTH: 260px">
							<option value="' . $res->fileindex . '"> ' . $res->fileindex . ' (Selected now)
							<option value="10"> 10 (Lowest priority, will be installed last)
							<option value="9"> 9
							<option value="8"> 8
							<option value="7"> 7
							<option value="6"> 6
							<option value="5"> 5
							<option value="4"> 4
							<option value="3"> 3
							<option value="2"> 2
							<option value="1"> 1 (Highest priority, will be installed first)
						</select>
					</td>
				</tr>
	</table>
</fieldset>

				<div class="DivSpacer"></div>

<fieldset class="GroupField">
	<legend>Submit and deploy a new revision of this file <span class="ColoredTxt">or</span> Remove this file and all revisions</legend>
				<div class="leftcol">
					<label><input class="button" type="submit" value="Submit"></label>
				</div>
				<div class="rightcol">
					<label>
						<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />&nbsp;
						<a href="index.php?main=remove_managementfile&param1=' . $seq->seqnr . '&param2=' . $param5 . '" onclick="return confirm(\'Remove file ' . $param2. '/' . $param1 . ' for distribution ' . $res->distribution . ' and domain ' . $res->domain_name . ' and all saved revisions? This operation can not be undone.\')">Remove</a>
					</label>
				</div>
</fieldset>

			</form>

<fieldset class="GroupField">
	<legend>Load old file revision</legend>
		<table>
			<th>Load older revision</th>
			</tr>

			<form method="get" action="index.php">
				<input type="hidden" name="main" value="open_file">
				<input type="hidden" name="param1" value="' . $param1 . '">
				<input type="hidden" name="param2" value="' . $param2 . '">
				<input type="hidden" name="param3" value="' . $param3 . '">
				<input type="hidden" name="param4" value="' . $param4 . '">
				<input type="hidden" name="param5" value="' . $param5 . '">

				<td>
					<select name="param6" style="WIDTH: 260px">';

                                           $sql = $db->query("select revision from andutteye_files where filename = '$param1' and directory = '$param2' and distribution = '$param3' and tagging = '$param4' order by revision desc");
                                            while ($row = $sql->fetch()) {
                                                    $revision = $row['revision'];
						     echo '<option value="' . $revision . '">&nbsp;' . $revision . '.0';
                                            }

					echo '
					</select>
					<input class="button" type="submit" value="Load">
				</td>
			</form>
	</table>
</fieldset>

';

echo '<div class="DivSpacer"></div>
	<label>
	<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" /><a href="index.php?main=system_files&param1=' . $param5 . '">&nbsp;Back to ' . $param5 . ' files overview</a>
	</label>
    </div>
</div>
';

// End of subfunction
}


function save_file($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param1 != "" && $param2 != "" && $param3 != "") {

$sql = $db->query("select * from andutteye_files where seqnr = '$param7'");
$res = $sql->fetchObject();

	$next_revision=($res->revision + 1);
	$date = date("20y-m-d");
	$time = date("H:m:s");

	//Remove Control m.
	$pattern = "/(\cM)/";
	$replace = "";
	$param1 = preg_replace($pattern, $replace, "$param1");

                $data = array(
                        'filename'       => "$res->filename",
                        'directory'      => "$res->directory",
                        'tagging'        => "$res->tagging",
                        'distribution'   => "$res->distribution",
                        'revision'       => "$next_revision",
                        'content'        => "$param1",
                        'prestep'        => "$param5",
                        'poststep'       => "$param6",
                        'perm_owner'     => "$param2",
                        'perm_group'     => "$param3",
                        'perms'          => "$param4",
			'fileindex'      => "$param9",
			'domain_name'    => "$param10",
			'filelocked'     => "$param11",
                        'created_by'     => "$authNamespace->andutteye_username",
                        'created_date'   => "$date",
                        'created_time'   => "$time"
                );
                $db->insert('andutteye_files', $data);
		header("Location:index.php?main=system_files&param1=$param8&param2=$res->directory");
}

// End of subfunction
}
function save_autoinstall($param1,$param2,$param3,$param4,$param5) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param1 != "" && $param2 != "" && $param3 != "") {

$pxedir = $db->query("select parametervalue from andutteye_core_configuration where parametername = 'Management_pxe_directory_location'");
$pxedir = $pxedir->fetchObject();

$autodir = $db->query("select parametervalue from andutteye_core_configuration where parametername = 'Management_autoinstall_directory_location'");
$autodir = $autodir->fetchObject();

$sql = $db->query("select * from andutteye_specifications where system_name = '$param1' order by seqnr desc limit 0,1");
$spec = $sql->fetchObject();

$sql = $db->query("select * from andutteye_provisioning where system_name = '$param1' order by seqnr desc limit 0,1");
$res = $sql->fetchObject();

        $next_revision=($res->revision + 1);
        $date = date("20y-m-d");
        $time = date("H:m:s");

        //Remove Control m.
        $pattern = "/(\cM)/";
        $replace = "";
        $param2 = preg_replace($pattern, $replace, "$param2");
        $param3 = preg_replace($pattern, $replace, "$param3");

                $data = array(
                        'system_name'    => "$param1",
                        'revision'       => "$next_revision",
                        'pxefile'        => "$param2",
                        'autoinstfile'   => "$param3",
			'pxefilename'	 => "$param4",
			'macadress'      => "$param5",
                        'created_by'     => "$authNamespace->andutteye_username",
                        'created_date'   => "$date",
                        'created_time'   => "$time"
                );
                $db->insert('andutteye_provisioning', $data);

	// Write files to disk

	if(is_file("$pxedir->parametervalue/$spec->distribution/$spec->archtype")) {
		mkdir("$pxedir->parametervalue/$spec->distribution/$spec->archtype");
	}
	if(is_file("$autodir->parametervalue/$spec->distribution/$spec->archtype")) {
		mkdir("$autodir->parametervalue/$spec->distribution/$spec->archtype");
	}

	// Start with PXE file
	$myFile = "$pxedir->parametervalue/$spec->distribution/$spec->archtype/$param4";
	$fh = fopen($myFile, 'w') or die("Can't open file:$pxedir->parametervalue/$spec->distribution/$spec->archtype/$param4");
	fwrite($fh, $param2);
	fclose($fh);

	// Write autoinstall file
	$myFile = "$autodir->parametervalue/$spec->distribution/$spec->archtype/$param1.auto";
	$fh = fopen($myFile, 'w') or die("Can't open file:$autodir->parametervalue/$spec->distribution/$spec->archtype/$param1.auto");
	fwrite($fh, $param3);
	fclose($fh);

	$sql = "update andutteye_provisioning_checkin set status = 'install', system_name = '$param1' where macaddress = '$param5'";
	$db->query($sql);

        header("Location:index.php?main=prepare_new_system&param1=$param1&param2=$param5");
}

// End of subfunction
}
function remove_documentation($param1,$param2) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param1) {
	$sql = $db->query("select * from andutteye_uploads where seqnr = '$param1'");
	$res = $sql->fetchObject();
	
	//Validate som permissions.

	$sql = "delete from andutteye_uploads where seqnr = '$param1'";
	$db->query($sql);
}

header("Location:index.php?main=system_overview&param1=$param2");

// End of subfunction
}
function remove_user($param1) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param1) {
        $sql = $db->query("select * from andutteye_users where seqnr = '$param1'");
        $res = $sql->fetchObject();

        //Validate som permissions.

        $sql = "delete from andutteye_users where seqnr = '$param1'";
        $db->query($sql);
}

header("Location:index.php?main=create_user");

//End of subfunction
}
function remove_role($param1) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param1) {
        $sql = $db->query("select * from andutteye_roles where seqnr = '$param1'");
        $res = $sql->fetchObject();

        //Validate som permissions.

        $sql = "delete from andutteye_roles where seqnr = '$param1'";
        $db->query($sql);
}

header("Location:index.php?main=create_role");

//End of subfunction
}
function remove_front($param1) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param1) {
        $sql = $db->query("select * from andutteye_front_configuration where seqnr = '$param1'");
        $res = $sql->fetchObject();

        //Validate som permissions.

        $sql = "delete from andutteye_front_configuration where seqnr = '$param1'";
        $db->query($sql);
}

header("Location:index.php?main=create_front");

//End of subfunction
}
function remove_system($param1) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');
$tables = array('andutteye_systems',
                'andutteye_assetmanagement',
                'andutteye_changeevent',
                'andutteye_choosenbundles',
                'andutteye_choosenpackages',
                'andutteye_base_agentconfiguration',
                'andutteye_managementlog',
                'andutteye_monitor_configuration',
                'andutteye_provisioning',
                'andutteye_serverlog',
                'andutteye_snapshot',
                'andutteye_software',
                'andutteye_specifications',
                'andutteye_statistics',
                'andutteye_uploads');

verify_if_user_is_logged_in();

if(!$authNamespace->andutteye_admin) {

	if(verify_role_object_permission($param1,'system',3,'0','0')) {
		print "Yes you have read-write-delete on this system\n";
	} else {
		header("Location:index.php?main=create_system&status=You%20dont%20have%20permissions%20to%20do%20this.");
		exit;
	}
}
foreach ($tables as &$value) {
	print "Deleting $param1 information in table $value <br>";
        $sql = "delete from $value where system_name = '$param1'";
        $db->query($sql);
}
header("Location:index.php?main=create_system");

//End of subfunction
}
function remove_group($param1) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param1) {
        $sql = $db->query("select * from andutteye_groups where seqnr = '$param1'");
        $res = $sql->fetchObject();

        //Validate som permissions.

        $sql = "delete from andutteye_groups where seqnr = '$param1'";
        $db->query($sql);
}

header("Location:index.php?main=create_group");

//End of subfunction
}
function remove_domain($param1) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param1) {
        $sql = $db->query("select * from andutteye_domains where seqnr = '$param1'");
        $res = $sql->fetchObject();

        //Validate som permissions.

        $sql = "delete from andutteye_domains where seqnr = '$param1'";
        $db->query($sql);
}

header("Location:index.php?main=create_domain");

//End of subfunction
}



function change_systeminformation($param1,$param2,$param3) {

require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param2) {
	$system_information=addslashes($param2);
	$system_description=addslashes($param3);

	$pattern = '/\n/';
	$replacement = '<br>';
	$system_information=preg_replace($pattern, $replacement, $system_information);
	$system_description=preg_replace($pattern, $replacement, $system_description);


	$sql = "update andutteye_systems set system_information = '$system_information', system_description = '$system_description' where system_name = '$param1'";
	$db->query($sql);
}

$sql = $db->query("select * from andutteye_systems where system_name = '$param1'");
$res = $sql->fetchObject();

$system_information=stripslashes($res->system_information);
$system_description=stripslashes($res->system_description);

$pattern = '/<br>/';
$replacement = '';
$system_information=preg_replace($pattern, $replacement, $system_information);
$system_description=preg_replace($pattern, $replacement, $system_description);

echo '
	<div id="content">
		<div class="section content">

<fieldset class="GroupField">
	<legend><span  class="BigTitle"><span class="ColoredTxt">Change Sytem Information</span> for System ' . $param1 . '</span></legend>

			<div>

				<form method="get" action="index.php">
					<input type="hidden" name="main" value="change_systeminformation">
					<input type="hidden" name="param1" value="' . $param1 . '">

					<div class="leftcol">
						<h3 class="BigTitle">System information (Long system information)</h3>
						<label><textarea cols="50" rows="10" name="param2">' . $system_information . '</textarea></label>
					</div>
					<div class="rightcol">
						<h3 class="BigTitle">System description (Short system description)</h3>
						<label><textarea cols="50" rows="10" name="param3">' . $system_description . '</textarea></label>
					</div>


					<label><input class="button" type="submit" value="Submit"></label>
 				</form>

				<label>
					<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" />
					<a href="index.php?main=system_overview&param1=' . $param1 . '">&nbsp;Back to ' . $param1 . ' system overview</a>
				</label>

			</div>

</fieldset>

		</div>
	</div>
';

//End of subfunction
}



function assetmanagement_revision($param1,$param2) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

echo '<div id="content">
	<div class="section content">
		<h2 class="BigTitle"><span class="ColoredTxt">Asset Management Revision Trend</span> for System ' . $param1 . ' on ' . $param2 . '</h2>

<fieldset class="GroupField">
	<legend>Revision Management</legend>
		<table>
			<th>Assetmanagment monitor</th>
			<th>Assetmanagment value</th>
			<th>Date</th>
			<th>Time</th>
		</tr>';


		$sql = $db->query("select * from andutteye_assetmanagement where system_name = '$param1' and assetmanagementname = '$param2 'order by seqnr desc");
                while ($row = $sql->fetch()) {
                                $assetmanagementname   = $row['assetmanagementname'];
                                $assetmanagementresult   = $row['assetmanagementresult'];
                                $assetmanagementprog   = $row['assetmanagementprog'];
                                $assetmanagementargs   = $row['assetmanagementargs'];
                                $created_date   = $row['created_date'];
                                $created_time   = $row['created_time'];

				echo '<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/revision_1.png" alt="" title="" /> <a href="#"  class="Tips2" title="Date:' . $created_date . ' Time:' . $created_time . ' Program:' . $assetmanagementprog . ' Arguments:' . $assetmanagementargs . '">' . $assetmanagementname . '</a>
				      </td>
				      <td>' . $assetmanagementresult . '</td>
				      <td>' . $created_date . '</td>
				      <td>' . $created_time . '</td>
				</tr>';
                }


echo '
</table>
</fieldset>
	<div class="DivSpacer"></div>
	<label><img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" /><a href="index.php?main=system_overview&param1=' . $param1 . '">&nbsp;Back to ' . $param1 . ' system overview</a></label>
		</div>
	</div>
';




// End of subfunction
}
function remove_bundle($param1,$param2,$param3,$param4,$param5,$param6) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();


$sql = "delete from andutteye_bundles where bundle = '$param1' and revision = '$param2' and distribution = '$param3' and domain_name = '$param4'";
$db->query($sql);
header("Location:index.php?main=show_bundles&param1=$param5&param3=$param6");


// End of subfunction
}



function show_statistics($param1,$param2) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

echo '
	<div id="content">
		<div class="section content">

<fieldset class="GroupField">
	<legend><span class="BigTitle"><span class="ColoredTxt">Statistics</span> Repository for ' . $param1 . '</span></legend>

			<div>
';

		if(!$param2) {
                	$sql = $db->query("select distinct(systemstatisticsname) from andutteye_statistics where system_name = '$param1'");
                	while ($row = $sql->fetch()) {
                                $systemstatisticsname   = $row['systemstatisticsname'];
				
echo '
				<div class="leftcol">
					<label>
						<img src="themes/' . $authNamespace->andutteye_theme . '/statistics_1.png" alt="" title="" />&nbsp;&nbsp;&nbsp;
						<a href="index.php?main=show_statistics&param1=' . $param1 . '&param2=' . $systemstatisticsname . '">' . $systemstatisticsname . '</a>
					</label>
				</div>
				<div class="rightcol">
					<label>
						<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />&nbsp;&nbsp;&nbsp;
						<a href="index.php?main=remove_statistics&param1=' . $systemstatisticsname . '&param2=' . $param1 . '" onclick="return confirm(\'Remove all statistics data for ST monitor ' . $systemstatisticsname . '?\')">Remove</a>
					</label>
				</div>
';
			}
		
echo '
				<label>
					<img src="themes/' . $authNamespace->andutteye_theme . '/statistics_1.png" alt="" title="" />
					<a href="index.php?main=show_all_statistics&param1=' . $param1 . '"> Show all collected statistics for system ' . $param1 . '</a>
				</label>
				<label>
					<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" />
					<a href="index.php?main=system_overview&param1=' . $param1 . '">&nbsp;Back to ' . $param1 . ' system overview</a>
				</label>
';
		} else {
echo '
				<legend>Statistics trend for ' . $param2 . '</legend>
';
	
echo '
				<label>
';
	
			include_once 'graph/php-ofc-library/open_flash_chart_object.php';
			open_flash_chart_object( '100%', 450, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-statistics.php?system=$param1&statistics=$param2", false );

echo '
				</label>
';

echo '
				<label>Andutteye plots the output from your statistics monitor, it takes the values and places it in the axis from 1 to 5. Since there is not label tagging of the data that is sendt in to Andutteye yourself must know in which order your ST monitor places the data, label tagging will be supported in future versions of Andutteye.</label>

';
		}


echo '
</fieldset>

			<label>
				<img src="themes/' . $authNamespace->andutteye_theme . '/back.png" alt="" title="" />
				<a href="index.php?main=show_statistics&param1=' . $param1 . '">&nbsp;Back to statistics home</a>
			</label>

		</div>
	</div>
';

// End of subfunction
}



function show_monitor_status($param1,$param2,$param3) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

echo "<div id='content'>
                <div class='section content'>

		   <fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Monitorstatus</span> on $param1 </span> for $param2</legend>

			<table>
				<th>Monitortype</th>
				<th>Status</th>
				<th>Message</th>
				<th>Date</th>
				<th>Time</th>
				</tr>
                        ";

				$subsql = $db->query("select * from andutteye_monitor_status where monitorname = '$param2' and system_name = '$param1' and monitortype = '$param3' order by seqnr desc");
                        		while ($row = $subsql->fetch()) {
                               			 $system_name   = $row['system_name'];
                               			 $monitorname   = $row['monitorname'];
                               			 $monitortype   = $row['monitortype'];
                               			 $monitormessage   = $row['monitormessage'];
                               			 $monitorstatus   = $row['monitorstatus'];
                               			 $created_date   = $row['created_date'];
                               			 $created_time   = $row['created_time'];
                               			 $number_ok   = $row['number_ok'];
                               			 $lastdate_ok   = $row['lastdate_ok'];
                               			 $lasttime_ok   = $row['lasttime_ok'];
                               			 $number_notok   = $row['number_notok'];
                               			 $lastdate_notok   = $row['lastdate_notok'];
                               			 $lasttime_notok   = $row['lasttime_notok'];

                                		echo "<td>$monitortype</td>";
                                		echo "<td>$monitorstatus</td>";
                                		echo "<td><a href=''  class='Tips2' title='Monitor:$monitorname Type:$monitortype Status:$monitorstatus NrOk:$number_ok LastdateOk:$lastdate_ok LasttimeOk:$lasttime_ok NrNotOk:$number_notok LastdateNotOk:$lastdate_notok LasttimeNotOk:$lasttime_notok'>$monitormessage</a></td>";
                                		echo "<td>$created_date</td>";
                                		echo "<td>$created_time</td>";
                                		echo "</tr>";
					}
		
echo "</table>";
echo "<label><img src='themes/$authNamespace->andutteye_theme/back.png' alt='' title='' /><a href='index.php?main=monitoring_front&param1=$param1'>&nbsp;Back to $param1 monitoring front</a></label>";
echo "
                </div>
		</fieldset>
             </div>
        </div>
";

// End of subfunction
}
function show_all_statistics($param1) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

echo "<div id='content'>
                <div class='section content'>
                <hr>
                <h2>All collected statistics for system $param1</h2>
                <div>";

                        $sql = $db->query("select distinct(systemstatisticsname) from andutteye_statistics where system_name = '$param1'");
                        while ($row = $sql->fetch()) {
                                $systemstatisticsname   = $row['systemstatisticsname'];

				echo "<h3>Statistics trend $systemstatisticsname for $param1 </h3>";
                        	echo "<label>";

                        	include_once 'graph/php-ofc-library/open_flash_chart_object.php';
                        	open_flash_chart_object( '100%', 450, 'http://'. $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] ."/$graph_tp_dir/graph/graph-statistics.php?system=$param1&statistics=$systemstatisticsname", false );
                        echo "</label>";

                        }

                echo "<label><img src='themes/$authNamespace->andutteye_theme/back.png' alt='' title='' /><a href='index.php?main=show_statistics&param1=$param1'>&nbsp;Back to statistics home</a></label>";

echo "
                </div>
             </div>
        </div>
";

// End of subfunction
}
function new_every_monitor($param1) {

echo "
        <div id='content'>
                 <div class='section content'>

		  <fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Specify monitor settings</span> on $param1 </span></legend>

                        ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='new_every_monitor'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>
                <input type='hidden' name='param3' value='$param3'>
                <input type='hidden' name='param4' value='$res->monitortype'>

                <h3>Monitor settings for $res->monitortype monitor $res->monitorname</h3>
                <label>Status  (Choosen now:$res->status)</label>

                <label>
                <select name='param5' style='WIDTH: 260px'>
                                <option value='up'> Up
                                <option value='down'> Down
                </select>
                </label>

                <label>Severity (Choosen now:$res->severity) </label>
                <label>
                <select name='param6' style='WIDTH: 260px'>
                                <option value='HARMLESS'> Harmless
                                <option value='WARNING'> Warning
                                <option value='CRITICAL'> Critical
                                <option value='FATAL'> Fatal
                </select>
                </label>

                <label>Alarmlimit  (Choosen now:$res->alarmlimit)</label>
                <label><select name='param7' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
		<label>Errorlimit  (Choosen now:$res->errorlimit)</label>
                <label><select name='param8' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
                <label>Message</label>
                <label><input type='text' name='param9' maxlength='255' value='$res->message' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param10' maxlength='255' value='$res->schedule' size='70'></label>

                <h3>Recovery actions</h3>
                <label>Send email</label>
                <label><input type='text' name='param11' maxlength='255' value='$res->sendemail' size='70'></label>
                <label>Execute recovery program</label>
                <label><input type='text' name='param12' maxlength='255' value='$res->runprogram' size='70'></label>

                <h3>Monitor settings for $res->monitortype monitor</h3>

                <label>Arguments</label>
                <label><input type='text' name='param13' maxlength='255' value='$res->programargs' size='70'></label>

                <label>Ok exitstatus</label>
                <label><input type='text' name='param14' maxlength='255' value='$res->exitstatus' size='70'></label>

                <label><input class='button' type='submit' value='Submit'></label>
                </form>

                ";
                echo "</fieldset></div></div>";


// End of subfunction
}
function new_ps_fm_monitor($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12) {
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();

if($param2 == "savemonitor") {
	if(!$param6) {
		$param6="0";
	}
	if(!$param7) {
		$param7="0";
	}
	$date = date("20y-m-d");
	$time = date("H:m:s");

         $data = array(
                'system_name'      => "$param1",
                'underchange'      => "yes",
                'monitorname'      => "$param3",
                'status'           => "$param4",
                'severity'         => "$param5",
                'alarmlimit'       => "$param6",
                'errorlimit'       => "$param7",
                'message'          => "$param8",
                'schedule'         => "$param9",
                'sendemail'        => "$param10",
                'runprogram'       => "$param11",
                'monitortype'      => "$param12",
                'created_date'     => "$date",
                'created_time'     => "$time",
                'created_by'       => "$authNamespace->andutteye_username"
         );
         $db->insert('andutteye_monitor_configuration', $data);
	 header("Location:index.php?main=system_configuration&param1=$param1");
	 exit;
}

echo "
          <div id='content'>
           <div class='section content'>

		<fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Specify monitor settings</span> on $param1 </span></legend>

                        ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='new_ps_fm_monitor'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>

                <label>Specify process or file to monitor.</label>
                <label><input type='text' name='param3' maxlength='255' value='' size='70'></label>

                <label>Status</label>

                <label>
                <select name='param4' style='WIDTH: 260px'>
                                <option value='up'> Up
                                <option value='down'> Down
                </select>
                </label>

                <label>Severity</label>
                <label>
                <select name='param5' style='WIDTH: 260px'>
                                <option value='HARMLESS'> Harmless
                                <option value='WARNING'> Warning
                                <option value='CRITICAL'> Critical
                                <option value='FATAL'> Fatal
                </select>
                </label>

                <label>Alarmlimit</label>
                <label><select name='param6' style='WIDTH: 260px'>
                                <option value='10'> 10
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>

		<label>Errorlimit</label>
                <label><select name='param7' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
                <label>Message</label>
                <label><input type='text' name='param8' maxlength='255' value='' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param9' maxlength='255' value='' size='70'></label>

                <h3>Recovery actions</h3>
                <label>Send email</label>
                <label><input type='text' name='param10' maxlength='255' value='' size='70'></label>
                <label>Execute recovery program</label>
                <label><input type='text' name='param11' maxlength='255' value='' size='70'></label>
		
		<h3>What type of monitor to you want to create?</h3>
                <label><input type='radio' name='param12' value='PS' checked='yes'> PS Process monitor</label>
                <label><input type='radio' name='param12' value='FM'> FM Filemodification monitor</label>

                <label><input class='button' type='submit' value='Submit'></label>
                </form>

                ";
                echo "</fieldset></div></div>";


//End of subfunction
}
function new_la_sa_ma_ph_monitor($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12,$param13) {
verify_if_user_is_logged_in();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param2 == "savemonitor") {
        $date = date("20y-m-d");
        $time = date("H:m:s");

         $data = array(
                'system_name'      => "$param1",
                'underchange'      => "yes",
                'monitorname'      => "$param3",
                'monitortype'      => "FS",
                'status'           => "active",
                'alarmlimit'       => "$param5",
                'errorlimit'       => "$param6",
                'message'          => "$param7",
                'schedule'         => "$param8",
                'sendemail'        => "$param9",
                'runprogram'       => "$param10",
                'warninglimit'     => "$param11",
                'criticallimit'    => "$param12",
                'fatallimit'       => "$param13",
                'created_date'     => "$date",
                'created_time'     => "$time",
                'created_by'       => "$authNamespace->andutteye_username"
         );
         $db->insert('andutteye_monitor_configuration', $data);
         header("Location:index.php?main=system_configuration&param1=$param1");
         exit;
}

echo "
            <div id='content'>
              <div class='section content'>
               <hr>
               <h2>Create a new LA SA MA PH monitor on $param1</h2> ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='new_la_sa_ma_ph_monitor'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>

                <h3>Monitor settings</h3>
                <label>Monitoname</label>
                <label><input type='text' name='param3' maxlength='255' value='$res->monitorname' size='70'></label>

                <label>Monitorvalue</label>
                <label><input type='text' name='param4' maxlength='255' value='$res->monitorname' size='70'></label>

                <label>Status</label>

                <label>
                <select name='param5' style='WIDTH: 260px'>
                                <option value='up'> Up
                                <option value='down'> Down
                </select>
                </label>

                <label>Severity</label>
                <label>
                <select name='param6' style='WIDTH: 260px'>
                                <option value='HARMLESS'> Harmless
                                <option value='WARNING'> Warning
                                <option value='CRITICAL'> Critical
                                <option value='FATAL'> Fatal
                </select>
                </label>

                <label>Alarmlimit</label>
                <label><select name='param7' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
		 <label>Errorlimit</label>
                <label><select name='param8' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
                <label>Message</label>
                <label><input type='text' name='param9' maxlength='255' value='$res->message' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param10' maxlength='255' value='$res->schedule' size='70'></label>

                <h3>Recovery actions</h3>
                <label>Send email</label>
                <label><input type='text' name='param11' maxlength='255' value='$res->sendemail' size='70'></label>
                <label>Execute recovery program</label>
                <label><input type='text' name='param12' maxlength='255' value='$res->runprogram' size='70'></label>

                <h3>What type of monitor to you want to create?</h3>
		<label><input type='radio' name='param13' value='PH' checked='yes'> PH Communication monitor</label>
		<label><input type='radio' name='param13' value='LA'> LA Loadaverege monitor</label>
		<label><input type='radio' name='param13' value='MA'> MA Memoryaverege monitor</label>
		<label><input type='radio' name='param13' value='SA'> SA Swapaverge monitor</label>

                <label><input class='button' type='submit' value='Submit'></label>
                </form>

                ";
                echo "</div></div>";


//End of subfunction
}
function new_am_st_monitor($param1,$param2,$param3,$param4,$param5,$param6) {
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();


if($param2 == "savemonitor") {
        $date = date("20y-m-d");
        $time = date("H:m:s");

         $data = array(
                'system_name'      => "$param1",
                'underchange'      => "yes",
                'monitorname'      => "$param3",
                'program'          => "$param4",
                'programargs'      => "$param5",
                'monitortype'      => "$param6",
                'status'           => "active",
                'created_date'     => "$date",
                'created_time'     => "$time",
                'created_by'       => "$authNamespace->andutteye_username"
         );
         $db->insert('andutteye_monitor_configuration', $data);
         header("Location:index.php?main=system_configuration&param1=$param1");
         exit;
}

echo "
       <div id='content'>
         <div class='section content'>

		     <fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Specify monitor settings</span> on $param1 </span></legend>

                        ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='new_am_st_monitor'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>


                <h3>Specify monitor settings</h3>
                <label>Monitorname</label>
                <label><input type='text' name='param3' maxlength='255' value='' size='70'></label>

                <label>Program</label>
                <label><input type='text' name='param4' maxlength='255' value='$res->program' size='70'></label>

                <label>Arguments</label>
                <label><input type='text' name='param5' maxlength='255' value='$res->programargs' size='70'></label>

                <h3>What type of monitor to you want to create?</h3>
		<label><input type='radio' name='param6' value='AM' checked='yes'> AM Assetmanagement monitor</label>
		<label><input type='radio' name='param6' value='ST'> ST Statistics monitor</label>

                <label><input class='button' type='submit' value='Submit'></label>
                </form>
                ";
                echo "</fieldset></div></div>";


//End of subfunction
}
function new_ft_monitor($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12) {

verify_if_user_is_logged_in();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param2 == "savemonitor") {
        $date = date("20y-m-d");
        $time = date("H:m:s");

         $data = array(
                'system_name'      => "$param1",
                'underchange'      => "yes",
                'monitorname'      => "$param3",
                'monitortype'      => "FT",
                'status'           => "active",
                'severity'         => "$param5",
                'alarmlimit'       => "$param6",
                'errorlimit'       => "$param7",
                'message'          => "$param8",
                'schedule'         => "$param9",
                'sendemail'        => "$param10",
                'runprogram'       => "$param11",
                'searchpattern'    => "$param12",
                'created_date'     => "$date",
                'created_time'     => "$time",
                'created_by'       => "$authNamespace->andutteye_username"
         );
         $db->insert('andutteye_monitor_configuration', $data);
         header("Location:index.php?main=system_configuration&param1=$param1");
         exit;
}

echo "
               <div id='content'>
                        <div class='section content'>

			 <fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Specify monitor settings</span> on $param1 </span></legend>

                        ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='new_ft_monitor'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>

                <h3>Specify monitor settings</h3>
                <label>Monitorname</label>
                <label><input type='text' name='param3' maxlength='255' value='' size='70'></label>

                <label>Severity</label>
                <label>
                <select name='param5' style='WIDTH: 260px'>
                                <option value='HARMLESS'> Harmless
                                <option value='WARNING'> Warning
                                <option value='CRITICAL'> Critical
                                <option value='FATAL'> Fatal
                </select>
                </label>

                <label>Alarmlimit</label>
                <label><select name='param6' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
		<label>Errorlimit</label>
                <label><select name='param7' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
                <label>Message</label>
                <label><input type='text' name='param8' maxlength='255' value='' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param9' maxlength='255' value='' size='70'></label>

                <h3>Recovery actions</h3>
                <label>Send email</label>
                <label><input type='text' name='param10' maxlength='255' value='' size='70'></label>
                <label>Execute recovery program</label>
                <label><input type='text' name='param11' maxlength='255' value='' size='70'></label>

                <h3>Search for</h3>
                <label>Regular expression search pattern</label>
                <label><input type='text' name='param12' maxlength='255' value='' size='70'></label>

                <label><input class='button' type='submit' value='Submit'></label>
                </form>

                ";
                echo "</fieldset></div></div>";

//End of subfunction
}
function new_fs_monitor($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12,$param13,$param14) {

verify_if_user_is_logged_in();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param2 == "savemonitor") {
        $date = date("20y-m-d");
        $time = date("H:m:s");

         $data = array(
                'system_name'      => "$param1",
                'underchange'      => "yes",
                'monitorname'      => "$param3",
                'monitortype'      => "FS",
                'status'           => "active",
                'alarmlimit'       => "$param5",
                'errorlimit'       => "$param6",
                'message'          => "$param7",
                'schedule'         => "$param8",
                'sendemail'        => "$param9",
                'runprogram'       => "$param10",
                'warninglimit'     => "$param11",
                'criticallimit'    => "$param12",
                'fatallimit'       => "$param13",
                'created_date'     => "$date",
                'created_time'     => "$time",
                'created_by'       => "$authNamespace->andutteye_username"
         );
         $db->insert('andutteye_monitor_configuration', $data);
         header("Location:index.php?main=system_configuration&param1=$param1");
         exit;
}
echo "
                <div id='content'>
                        <div class='section content'>

			  <fieldset class='GroupField'>
                        <legend><span class='BigTitle'><span class='ColoredTxt'>Specify monitor settings</span> on $param1 </span></legend>

                        ";

                echo "
                <form method='get' action='index.php'>
                <input type='hidden' name='main' value='new_fs_monitor'>
                <input type='hidden' name='param1' value='$param1'>
                <input type='hidden' name='param2' value='savemonitor'>

                <h3>Monitor settings</h3>

                <label>Monitoprname</label>
                <label><input type='text' name='param3' maxlength='255' value='' size='70'></label>

                <label>Status</label>

                <label>
                <select name='param4' style='WIDTH: 260px'>
                                <option value='up'> Up
                                <option value='down'> Down
                </select>
                </label>

                <label>Alarmlimit</label>
                <label><select name='param5' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
		<label>Errorlimit</label>
                <label><select name='param6' style='WIDTH: 260px'>
                                <option value='0'> 0
                                <option value='1'> 1
                                <option value='2'> 2
                                <option value='3'> 3
                                <option value='4'> 4
                                <option value='5'> 5
                                <option value='6'> 6
                                <option value='7'> 7
                                <option value='8'> 8
                                <option value='9'> 9
                                <option value='10'> 10
                </select>
                </label>
                <label>Message</label>
                <label><input type='text' name='param7' maxlength='255' value='' size='70'></label>
                <label>Schedule</label>
                <label><input type='text' name='param8' maxlength='255' value='' size='70'></label>

                <h3>Recovery actions</h3>
                <label>Send email</label>
                <label><input type='text' name='param9' maxlength='255' value='' size='70'></label>
                <label>Execute recovery program</label>
                <label><input type='text' name='param10' maxlength='255' value='' size='70'></label>

                <h3>Filesystem limits</h3>

                <label>Warning limit</label>
                <label><select name='param11' style='WIDTH: 260px'>
                ";

                for ($i = 0; $i <= 100; $i++) {
                        echo "<option value='$i'> $i%";
                }

                echo "</select></label>";

                echo "
                <label>Crtitical limit</label>
                <label><select name='param12' style='WIDTH: 260px'>
                ";

                for ($i = 0; $i <= 100; $i++) {
                        echo "<option value='$i'> $i%";
                }
                echo "</select></label>";

                echo "
                <label>Fatal limit</label>
                <label><select name='param13' style='WIDTH: 260px'>
                ";

                for ($i = 0; $i <= 100; $i++) {
                        echo "<option value='$i'> $i%";
                }
                echo "</select></label>";

                echo "
                <label><input class='button' type='submit' value='Submit'></label>
                </form>

                ";
                echo "</fieldset></div></div>";

//End of subfunction
}
function remove_managementfile($param1,$param2) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

$sql = "delete from andutteye_files where seqnr = '$param1'";
$db->query($sql);

header("Location:index.php?main=system_files&param1=$param2");
exit;

//End of subfunction
}
function remove_statistics($param1,$param2) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

$sql = "delete from andutteye_statistics where system_name = '$param2' and systemstatisticsname = '$param1'";
$db->query($sql);

header("Location:index.php?main=show_statistics&param1=$param2");
exit;

//End of subfunction
}
function install_new_system($param1,$param2) {

verify_if_user_is_logged_in();
verify_if_user_have_admin_prevs();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select * from andutteye_systems where system_name = '$param1' order by system_name asc");
$res = $sql->fetchObject();

echo '<div id="content">
		<div class="section content">
			<fieldset class="GroupField">
			<legend><span class="BigTitle"><span class="ColoredTxt">Install new or reinstall</span> system</span></legend>
			
			<table>

				<form method="get" action="index.php">
					<input type="hidden" name="main" value="prepare_new_system">
					<input type="hidden" name="param1" value="'.$res->system_name.'">
					<th>Domain</th>
					<th>Group</th>
					<th>System</th>
					</tr>
					<td>'.$res->domain_name.'</td>
					<td>'.$res->group_name.'</td>
					<td>'.$res->system_name.'</td>
					</tr>

					<th colspan="3">Andutteye management status</th></tr>';

				        $sql    = $db->query("select seqnr from andutteye_specifications where system_name ='$param1'");
        				$exists = $sql->fetchAll();
        				$exists = count($exists);

					if($exists) {
						echo '<td colspan="3"><img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" />  <b>' .$exists. '</b> system specification(s) saved, ready to continue.</td>';
					} else {
						echo '<td colspan="3"><img src="themes/' . $authNamespace->andutteye_theme . '/alert.png" alt="" title="" />  <b>' .$exists. '</b> system specification saved, save a specification before trying to install the system.<br>';
						echo '<img src="themes/' . $authNamespace->andutteye_theme . '/info.png" alt="" title="" /> <a href="index.php?main=system_specification&param1=' .$param1. '">Create a system specification first by clicking this link.</a></td></tr>';
					}

			echo '
			</table>
			</fieldset>
		</div>
';

$sql = $db->query("select system_name,macadress from andutteye_provisioning where system_name = '$param1' order by seqnr desc limit 0,1");
$res = $sql->fetchObject();

if("$res->system_name") {

echo ' <fieldset class="GroupField">
                <legend><span class="BigTitle"><span class="ColoredTxt">System already</span> installed</span></legend>
		
		<table>
			<th>System</th>
			<th>Macadress</th>
			<th>Select</th>
		</tr>';
		echo '
			<td>'.$res->system_name.'</td>
			<td>'.$res->macadress.'</td>
			<td>
			<input type="radio" name="param2" value="' .$res->macadress. ' " CHECKED> Use current
			</td>
			</tr>';

}

echo '
</table>
</fieldset>';
echo '<div class="section content">
        <fieldset class="GroupField">
                <legend><span class="BigTitle"><span class="ColoredTxt">Use following</span> macadress</span></legend>
		
		<table>
			<th>Macadress</th>
			<th>Status</th>
			<th>Select</th>
			</tr>';

		echo '
			<td><input type="text" name="param3" maxlength="255" value="" size="25"></td>
			<td></td>
			<td>
				<input type="radio" name="param2" value="manual"> Manual
			</td>
			</tr>
		    ';

			$sql = $db->query("select * from andutteye_provisioning_checkin order by seqnr asc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $macaddress = $row['macaddress'];
                                $status = $row['status'];
                                $serialnumber = $row['serialnumber'];

			echo '
				<td>
					<img src="themes/' . $authNamespace->andutteye_theme . '/systems_2.png" alt="" title="" />
					'.$macaddress. '
				</td>
				<td>'.$status.'</td>
				<td>
					<input type="radio" name="param2" value="' .$macaddress. '"> Automatic
				</td>
				</tr>';

                        }
	echo '</table>';

if($exists) {
        echo '<label><input type="submit" value="Submit"></label>';
}
echo "
</form>
</fieldset>
</div>
</div>
";

}
function fileadmin($param1,$param2,$param3,$param4,$param5,$param6) {
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();
verify_if_user_have_admin_prevs();

echo '<div id="content">
	<div class="section content">
                     <legend><img src="themes/' . $authNamespace->andutteye_theme . '/file_b.png" alt="" title="" /><span class="BigTitle">Create<span class="ColoredTxt"> new file</span></span></legend>
			<form method="get" action="index.php">
				<input type="hidden" name="main" value="save_fileadmin">

			<fieldset class="GroupField">
	<legend>File content</legend>
';
		
echo '<label><textarea cols="100" rows="30" name="param1"></textarea></label>';
echo '
</fieldset>
<fieldset class="GroupField">
	<legend>File related</legend>
				<div class="leftcol">
					<table>
					<th>Directory (ex /etc/)</th>
					</tr>
					<td><input type="text" name="param2" maxlength="255" value="" size="50"></td>
					</tr>
					<th>Filename (ex passwd)</th>
					</tr>
					<td><input type="text" name="param3" maxlength="255" value="" size="50"></td>
					</tr>
					<th>File owner</th>
					</tr>
					<td><input type="text" name="param4" maxlength="255" value="" size="50"></td>
					</tr>
					<th>File group</th>
					</tr>
					<td><input type="text" name="param5" maxlength="255" value="" size="50"></td>
					</tr>
					<th>File permission</th>
					</tr>
					<td><input type="text" name="param6" maxlength="255" value="" size="50"></td>
					</tr>
					<th>Pre hook execution command or program</th>
                                        </tr>
                                        <td><input type="text" name="param9" maxlength="255"  value="" size="50"></td>
                                        </tr>
                                        <th>Post hook execution command or program</th>
                                        </tr>
                                        <td><input type="text" name="param10" maxlength="255" value="" size="50"></td>
                                        </tr>

					</table>
				</div>
				<div class="rightcol">
					<table>
					<th>Domain</th>
                                        </tr>
                                        <td><select name="param12" style="WIDTH: 260px">';

                                        $sql = $db->query("select distinct(domain_name) from andutteye_domains order by domain_name asc");
                                        while ($row = $sql->fetch()) {
                                                $domain_name = $row['domain_name'];
                                                echo '<option value="'.$domain_name.'"> '.$domain_name.'';
                                        }
                                        echo '</select></td>
                                        </tr>
					<th>Distribution</th>
					</tr>
					<td><select name="param7" style="WIDTH: 260px">';

        				$sql = $db->query("select distinct(distribution) from andutteye_packages order by distribution asc");
        				while ($row = $sql->fetch()) {
                				$distribution = $row['distribution'];
                				echo '<option value="'.$distribution.'"> '.$distribution.'';
        				}
        				echo '</select></td>
       					</tr>
					<th>File tagging</th>
					</tr>
					<td><select name="param8" style="WIDTH: 260px">';

        				$sql = $db->query("select domain_name,group_name,system_name from andutteye_systems order by system_name asc");
        				while ($row = $sql->fetch()) {
                				$domain_name = $row['domain_name'];
                				$group_name = $row['group_name'];
                				$system_name = $row['system_name'];

                				$man = $db->query("select * from andutteye_specifications where system_name = '$system_name' order by revision desc limit 0,1");
                				$man = $man->fetchObject();

                				echo '
                				<option value="">(Start '.$system_name.' tagging options)
                				<option value="--'.$domain_name.'"> --'.$domain_name.' (W)
                				<option value="--'.$domain_name.'--'.$man->patchlevel.'"> --'.$domain_name.'--'.$man->patchlevel.'
                				<option value="--'.$group_name.'"> --'.$group_name.'
                				<option value="--'.$group_name.'--'.$man->patchlevel.'"> --'.$group_name.'--'.$man->patchlevel.'
                				<option value="-- '.$domain_name.'--'.$group_name.'"> -- '.$domain_name.'--'.$group_name.'
                				<option value="-- '.$domain_name.'--'.$group_name.'--'.$man->patchlevel.'"> -- '.$domain_name.'--'.$group_name.'--'.$man->patchlevel.'
                				<option value="--'.$system_name.'"> --'.$system_name.'
                				<option value="--'.$system_name.'--'.$man->patchlevel.'"> --'.$system_name.'--'.$man->patchlevel.' (B)
                				<option value="">(End '.$system_name.' tagging options)
                				';
        				}
        				echo '</select></td>
       					</tr>
					<th>Specify file installation order</th>
					</tr>
					<td>
						<select name="param11" style="WIDTH: 260px">
							<option value="10"> 10 (Lowest priority, will be installed last)
							<option value="9"> 9
							<option value="8"> 8
							<option value="7"> 7
							<option value="6"> 6
							<option value="5"> 5
							<option value="4"> 4
							<option value="3"> 3
							<option value="2"> 2
							<option value="1"> 1 (Highest priority, will be installed first)
						</select>
					</td>
					</tr>

		</table>
</fieldset>

<fieldset class="GroupField">
	<legend>Submit <span class="ColoredTxt">to create a new filemanagement file</span></legend>
	<table>
		<th>Submit</th></tr>
			<td><input class="button" type="submit" value="Submit"></td>
			</tr>
	</table>
</fieldset>
';

    echo '</div>
';

// End of subfunction
}
function save_fileadmin($param1,$param2,$param3,$param4,$param5,$param6,$param7,$param8,$param9,$param10,$param11,$param12) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

verify_if_user_is_logged_in();
verify_if_user_have_admin_prevs();

if($param1 != "" && $param2 != "" && $param3 != "") {
        $date = date("20y-m-d");
        $time = date("H:m:s");

        //Remove Control m.
        $pattern = "/(\cM)/";
        $replace = "";
        $param1 = preg_replace($pattern, $replace, "$param1");

                $data = array(
                        'filename'       => "$param3",
                        'directory'      => "$param2",
                        'tagging'        => "$param8",
                        'distribution'   => "$param7",
                        'revision'       => "1",
                        'content'        => "$param1",
                        'prestep'        => "$param9",
                        'poststep'       => "$param10",
                        'perm_owner'     => "$param4",
                        'perm_group'     => "$param5",
                        'perms'          => "$param6",
                        'fileindex'      => "$param11",
                        'domain_name'    => "$param12",
                        'created_by'     => "$authNamespace->andutteye_username",
                        'created_date'   => "$date",
                        'created_time'   => "$time"
                );
                $db->insert('andutteye_files', $data);
                header("Location:index.php?main=fileadmin");
}

// End of subfunction
}
function check_if_service_is_alive($service) {

  $cmd = "ps -efl | grep $service | grep -v grep";

     // run the system command and assign output to a variable ($output)
     exec($cmd, $output, $result);

     // check the number of lines that were returned
     if(count($output) >= 1){
          // the process is still alive
          return true;
     }
     // the process is dead
     return false;

// End of subfunction
}
function submit_group_command($param1,$param2,$param3,$param4) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');
$date = date("20y-m-d");
$time = date("H:m:s");

verify_if_user_is_logged_in();

if(!verify_role_object_permission($param4,'domain',2,'0','0')) {
        // Verify if domain is allowed to be write.
        header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20write%20on%20domain");
        exit;
}
if(!verify_role_object_permission($param3,'group',2,'0','0')) {
        // Verify if domain is allowed to be write.
        header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20write%20on%20group");
        exit;
}

switch ($param1) {
    case 'EnableFilemanagment':
		while (list ($key,$system) = @each ($param2)) {
			$sql = $db->query("select revision from andutteye_specifications where system_name = '$system' order by revision desc limit 0,1");
			$res = $sql->fetchObject();

			$sql = "update andutteye_specifications set config_update = 'Active' where system_name = '$system' and revision = '$res->revision'";
			$db->query($sql);
		}
        break;
    case 'DisableFilemanagement':
		while (list ($key,$system) = @each ($param2)) {
			$sql = $db->query("select revision from andutteye_specifications where system_name = '$system' order by revision desc limit 0,1");
			$res = $sql->fetchObject();

			$sql = "update andutteye_specifications set config_update = 'Disabled' where system_name = '$system' and revision = '$res->revision'";
			$db->query($sql);
		}
        break;
    case 'EnablePackagemanagement':
		while (list ($key,$system) = @each ($param2)) {
			$sql = $db->query("select revision from andutteye_specifications where system_name = '$system' order by revision desc limit 0,1");
			$res = $sql->fetchObject();

			$sql = "update andutteye_specifications set package_update = 'Active' where system_name = '$system' and revision = '$res->revision'";
			$db->query($sql);
		}
        break;
    case 'DisablePackagemanagement':
		while (list ($key,$system) = @each ($param2)) {
			$sql = $db->query("select revision from andutteye_specifications where system_name = '$system' order by revision desc limit 0,1");
			$res = $sql->fetchObject();

			$sql = "update andutteye_specifications set package_update = 'Disabled' where system_name = '$system' and revision = '$res->revision'";
			$db->query($sql);
		}
        break;
    case 'LockAllMonitors':
	while (list ($key,$system) = @each ($param2)) {
		$sql = "update andutteye_monitor_configuration set override = 'no', created_date = '$date', created_time = '$time', created_by = '$authNamespace->andutteye_username' where system_name = '$system'";
		$db->query($sql);
	}
        break;
    case 'UnlockAllMonitors':
	while (list ($key,$system) = @each ($param2)) {
		$sql = "update andutteye_monitor_configuration set override = 'yes', created_date = '$date', created_time = '$time', created_by = '$authNamespace->andutteye_username' where system_name = '$system'";
		$db->query($sql);
	}
        break;
     default:
	//Invalid command, dont do anything
}
header("Location:index.php?main=group_overview&param1=$param4&param2=$param3&status=Command%20executed");
exit;


// End of subfunction
}
function submit_domain_command($param1,$param2,$param3) {
require 'db.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');
$date = date("20y-m-d");
$time = date("H:m:s");

verify_if_user_is_logged_in();

if(!verify_role_object_permission($param3,'domain',2,'0','0')) {
        // Verify if domain is allowed to be write.
        header("Location:index.php?main=enviroment_overview&status=Not%20allowed%20to%20write%20on%20domain");
        exit;
}

switch ($param1) {
    case 'EnableFilemanagment':
		while (list ($key,$system) = @each ($param2)) {
			$sql = $db->query("select revision from andutteye_specifications where system_name = '$system' order by revision desc limit 0,1");
			$res = $sql->fetchObject();

			$sql = "update andutteye_specifications set config_update = 'Active' where system_name = '$system' and revision = '$res->revision'";
			$db->query($sql);
		}
        break;
    case 'DisableFilemanagement':
		while (list ($key,$system) = @each ($param2)) {
			$sql = $db->query("select revision from andutteye_specifications where system_name = '$system' order by revision desc limit 0,1");
			$res = $sql->fetchObject();

			$sql = "update andutteye_specifications set config_update = 'Disabled' where system_name = '$system' and revision = '$res->revision'";
			$db->query($sql);
		}
        break;
    case 'EnablePackagemanagement':
		while (list ($key,$system) = @each ($param2)) {
			$sql = $db->query("select revision from andutteye_specifications where system_name = '$system' order by revision desc limit 0,1");
			$res = $sql->fetchObject();

			$sql = "update andutteye_specifications set package_update = 'Active' where system_name = '$system' and revision = '$res->revision'";
			$db->query($sql);
		}
        break;
    case 'DisablePackagemanagement':
		while (list ($key,$system) = @each ($param2)) {
			$sql = $db->query("select revision from andutteye_specifications where system_name = '$system' order by revision desc limit 0,1");
			$res = $sql->fetchObject();

			$sql = "update andutteye_specifications set package_update = 'Disabled' where system_name = '$system' and revision = '$res->revision'";
			$db->query($sql);
		}
        break;
    case 'LockAllMonitors':
	while (list ($key,$system) = @each ($param2)) {
		$sql = "update andutteye_monitor_configuration set override = 'no', created_date = '$date', created_time = '$time', created_by = '$authNamespace->andutteye_username' where system_name = '$system'";
		$db->query($sql);
	}
        break;
    case 'UnlockAllMonitors':
	while (list ($key,$system) = @each ($param2)) {
		$sql = "update andutteye_monitor_configuration set override = 'yes', created_date = '$date', created_time = '$time', created_by = '$authNamespace->andutteye_username' where system_name = '$system'";
		$db->query($sql);
	}
        break;
     default:
	//Invalid command, dont do anything
}
header("Location:index.php?main=domain_overview&param1=$param3&status=Command%20executed");
exit;


// End of subfunction
}
function create_front($param1,$param2,$param3,$param4) {

verify_if_user_is_logged_in();
verify_if_user_have_admin_prevs();

require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

if($param1) {
        $date = date("20y-m-d");
        $time = date("H:m:s");

        $sql   = $db->query("select seqnr from andutteye_front_configuration where system_name ='$param1'");
        $exists = $sql->fetchAll();
        $exists = count($exists);

        if($param1 != "" && $param2 != "" && $exists == 0) {
                $data = array(
                        'system_name'       => "$param1",
                        'system_address'    => "$param2",
                        'system_port'    => "$param3",
                        'front_description' => "$param4",
                        'created_by'     => "$authNamespace->andutteye_username",
                        'created_date'   => "$date",
                        'created_time'   => "$time"
                );
                $db->insert('andutteye_front_configuration', $data);
        }
}

echo '<div id="content">
		<div class="content">
			<fieldset class="GroupField">
				<legend><img src="themes/' . $authNamespace->andutteye_theme . '/front_b.png" alt="" title="" /><span class="BigTitle"><span class="ColoredTxt">Create</span> new front proxy</span></legend>

				<form method="get" action="index.php">
					<input type="hidden" name="main" value="create_front">

						<label>Frontname</label>
						<label><input type="text" name="param1" size="35" maxlength="255" value=""></label>
						<label>Contactadress (Ipaddress or dnsname)</label>
						<label><input type="text" name="param2" size="35" maxlength="255" value=""></label>
						<label>Contactport</label>
						<label><input type="text" name="param3" size="35" maxlength="255" value=""></label>
						<label>Description</label>
						<label><input type="text" name="param4" size="35" maxlength="255" value=""></label>

						<label><input type="submit" value="Submit"></label>
				</form>

</fieldset>

		</div>

		<br />

<div class="content">
	<fieldset class="GroupField">
		<legend><span class="BigTitle"><span class="ColoredTxt">Change</span> front proxies</span></legend>
	
			<table>
				<th>Frontname</th>
				<th>Contactaddress</th>
				<th>Contactport</th>
				<th>Action</th>
				</tr>';

			$sql = $db->query("select * from andutteye_front_configuration order by system_name asc");
                        while ($row = $sql->fetch()) {
                                $seqnr = $row['seqnr'];
                                $system_name = $row['system_name'];
                                $system_address = $row['system_address'];
                                $system_port = $row['system_port'];
                                $front_description = $row['front_description'];
                                $front_ver_name = $row['front_ver_name'];

			echo '
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/front_1.png" alt="" title="" />
				<a href="" class="Tips2" title="Frontname:' . $system_name . ' Description:' . $front_description . '">'.$system_name.'</a>
				</td>
				<td>
				'.$system_address.'
				</td>
				<td>
				'.$system_port.'
				</td>
				<td>
				<img src="themes/' . $authNamespace->andutteye_theme . '/delete_1.png" alt="" title="" />
				<a href="index.php?main=remove_front&param1=' .$seqnr . '" onclick="return confirm(\'Remove frontproxy ' . $system_name . '?\')">Remove</a>
				</td>
				</tr>
			';

                        }



echo "
</table>
</fieldset>
</div>
";


// End of subfunction
}
function get_last_system_specification_revision($system_name) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select revision from andutteye_specifications where system_name = '$system_name' order by revision desc limit 0,1");
$res = $sql->fetchObject();

if(!$res->revision) {
	return(0);
} else {
	return($res->revision);
}

// End of subfunction
}
function get_current_filemanagement_status($system_name) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select revision from andutteye_specifications where system_name = '$system_name' order by revision desc limit 0,1");
$res = $sql->fetchObject();

$sql = $db->query("select config_update from andutteye_specifications where system_name = '$system_name' and revision = '$res->revision'");
$res = $sql->fetchObject();

return($res->config_update);

// End of subfunction
}
function get_current_packagemanagement_status($system_name) {

verify_if_user_is_logged_in();
require 'db.php';
require_once 'Zend/Session/Namespace.php';
$authNamespace = new Zend_Session_Namespace('Zend_Auth');

$sql = $db->query("select revision from andutteye_specifications where system_name = '$system_name' order by revision desc limit 0,1");
$res = $sql->fetchObject();

$sql = $db->query("select package_update from andutteye_specifications where system_name = '$system_name' and revision = '$res->revision'");
$res = $sql->fetchObject();

return($res->package_update);

// End of subfunction
}

?>
