#!/usr/bin/perl
#
# Copyright (C) 2004-2010 Andreas Utterberg Thundera AB.
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# version 2 as published by the Free Software Foundation.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# For the complete GPL v2 license, visit http://www.fsf.org/licenses
#
# Description:Andutteye management program, a part of andutteye management.
#
# $Id: aemanagement.pl,v 1.19 2006/10/15 16:48:21 andutt Exp $
#
# Notes:The program requires that you generate a ssh key to use when communicating with
#       andutteyemanagement repository server. Read the Andutteye Software Suite Management
#       documentation for more information. 
#
# The managementhost parameter is specifiying host or ip that contain the aemanagement repository.our $managementhost="10.46.252.10";
our $managementhost="localhost";

# The managementport parameter is specifiying aerepository ssh-server port.
our $managementport="22";

# The managementaccount parameter is telling which user to connect as to repository.
our $managementaccount="ae";

# The managementkey parameter is specifying which ssh-key to use when initiate a connect.
our $managementkey="/root/.ssh/id_rsa";

# The enable_rpm_package_validation specifies if the agent shal validate the rpm database against the central
# rpm package repository. Valid chooises are (yes or no).
our $enable_rpm_package_validation="no";

# The enable_file_validation specifies if the agent shall validate local file(s) against the central
# file repository. Valid chooises are (yes or no).
our $enable_file_validation="yes";

# Set tightchecking to yes if you want to be paranoid and revalidate bundle
# transfered to make sure that wanted packages are included.
# NOTE: If set to yes it will take longer time to install packages.
our $tightchecking="no";

# The sshcommand parameter is telling which ssh binary and path to use.
our $sshcommand="/usr/bin/ssh -o BatchMode=yes -p $managementport";

# The scpcommand parameter is telling which scp binary and path to use.
our $scpcommand="/usr/bin/scp -o BatchMode=yes -P $managementport";

# The savedir parameter is telling which directory that shall be used for temporary extraction and more.
our $savedir="/var/aemanagement";

# The savedir parameter is telling which directory that shall be used for backup of changed configuration.
our $filesavedir="/var/aemanagement/saves";

# The aesurveillance_method parameter specifies if andutteye management agent shall report anomolies 
# to andutteye surveillace, by andutteye management repository server or from monitored host.
#
# Method:local will enable below andutteye parameters and the program will try to send alarms directly.
# Method:server will enable so the alarms are passed to aemanagement server and reported thru there.
# Method:NO will disable any notification to Andutteye Surveillance
our $aesurveillance_method="server";

# The aesurveillance_server parameter specifies which andutteye surveillance server to send alarms to. If used in localmode.
our $aesurveillance_server="localhost";

# The aesurveillance_port parameter specifies which andutteye surveillance port to send alarms to. If used in localmode
our $aesurveillance_port="32000";

# The aesurveillance_program specifies direct path to andutteye surveillances post utility. If used in localmode.
our $aesurveillance_program="/opt/andutteye/utils/postandutteye";

# The version parameter tells the programs version
our $version="Andutteye management for Linux Version:3.0 Fixlevel:1.0 Lastfix:2005-06-28 (andutt)";
#
# Unchangeble parameters below, dont change them.
#
our $thishost=`uname -n`;
our ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
our $date=sprintf("20%02d%02d%02d",$year%100,$mon+1,$mday);
our $time=sprintf("%02d:%02d:%02d",$hour,$min,$sec);
our $owner;
our $md5hash;
our $group;
our $filepermission;
our $fileprestep;
our $filepoststep;
our $tmpfilename;
our $debug=1;
our $allow_package_installation;
our $allow_config_installation;
our $tellsyslog;
our $exists;
our @ARGV;
our @rpminstall;
our @rpmdelete;
chomp $thishost;

use strict;
use warnings;
use File::Basename;
use Digest::MD5;

#################################################
# 
# SUBFUNCTIONS BEGIN
#
#################################################

sub create_nessasary {
#
# Create nessasary aemanagement structures.
#
if ( ! -d "$savedir/$thishost" ) {
	print "-- Creating nessasary tempdirectory:$savedir/$thishost\n";
	`mkdir -p $savedir/$thishost`;
}
if ( ! -d "$savedir/$thishost/files" ) {
	print "-- Creating nessasary tempdirectory:$savedir/$thishost/files\n";
	`mkdir -p $savedir/$thishost/files`;
}
if ( ! -d "$filesavedir" ) {
	print "-- Creating nessasary directory:$filesavedir\n";
	`mkdir -p $filesavedir`;
}

# End of subfunction
}
sub gather_files {
#
#
#
print "-- Asking repository=$managementhost to gather and bundle files belonging to me=$thishost\n";
my $ecode=system("$sshcommand -i $managementkey -l $managementaccount $managementhost \"gatherconfig $thishost\"");

if($ecode == 0 ) {
	print "-- Gatherconfig generated [OK]\n";
} else {
	print "** ERROR Failed to gather configuration files belonging to me\n";
	log_progress("** ERROR Failed to gather configuration files belonging to me");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Failed-to-gather-configuration-files-belonging-to-me",
	"WARNING");
	exit 1;
}
# End of subfunction
}
sub send_email {
#
#
#
if ( -f "$savedir/$thishost/$thishost-$date.log" ) {
	# Checking if the file contains any data.
	if ( -z "$savedir/$thishost/$thishost-$date.log") {
		print "## Worklog is 0kb big, will not try to notify administrators\n";
		return 0;
	}
	print "-- Sending worklog to administrators if any have been created\n";
	my $ecode=system("$sshcommand -i $managementkey -l $managementaccount $managementhost \"sendemail $thishost\"");

	if($ecode == 0) {
		print "-- Administrators have been notified by email\n";
	} else {
		print "** ERROR Failed to notify administrators by email\n";
		log_progress("** ERROR Failed to notify administrators by email, exitcode:$ecode");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-Failed-to-notify-administrators-by-email-exitcode:$ecode",
		"WARNING");
		exit 1;
	}
} else {
	print "-- Will not try to send any email. Found nothing to report.\n";
}

# End of subfunction
}
sub tellsyslog {
#
#
#
my $message=$_[0];

if($message eq "") {
	print "** ERROR Recived an empty syslogmessage to log, skipping this section\n";
} else {
	if($tellsyslog eq "yes") {
		my $ecode=system("logger -i -t Aemanagement \"$message\"");

		if($ecode == 0 ) {
			print "-- Local syslog notified OK\n";
		} else {
			print "** ERROR Failed to notify local syslog, errorcode:$ecode, continiue anyway\n";
			log_progress("** ERROR Failed to notify local syslog, errorcode:$ecode, continiue anyway");
			notify_andutteye_surveillance("Andutteyemanagement-agent-error",
			"**-ERROR-Failed-to-notify-local-syslog-exitcode:$ecode-Continiue-anyway.",
			"CRITICAL");
		}
	} else {
		print "Will not log this to localsyslog, tellsyslog are set to:$tellsyslog\n";
	}
}
# End of subfunction
}
sub check_if_allow_to_run {
#
#
#
print "-- Asking repository if im active and able to run\n";
my $allow_run=`$sshcommand -i $managementkey -l $managementaccount $managementhost \"checkstatus $thishost\"`;
chomp $allow_run;

if($allow_run ne "active") {
	print "## Andutteyemanagement repository says im not allowed to run, status are set to:$allow_run\n";
	exit 0;
} else {
	print "-- Repository is opened status:$allow_run\n";
}

# End of subfunction
}
sub get_allow_data {
#
#
#
print "-- Asking repository for package and config permissions\n";
$allow_package_installation=`$sshcommand -i $managementkey -l $managementaccount $managementhost \"packagepermission $thishost\"`;
$allow_config_installation=`$sshcommand -i $managementkey -l $managementaccount $managementhost \"configpermission $thishost\"`;
$tellsyslog=`$sshcommand -i $managementkey -l $managementaccount $managementhost \"tellsyslog $thishost\"`;
chomp $allow_package_installation;
chomp $allow_config_installation;
chomp $tellsyslog;

if(!defined($allow_package_installation)) {
	print "** ERROR Recived a empty package installation permission, should be yes or no\n";
	log_progress("** ERROR Recived a empty package installation permission, should be yes or no");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Recived-a-empty-package-installation permission-should-be-yes-or-no",
	"WARNING");
	exit 1;
} else {
	print "-- Package permission is set to:$allow_package_installation\n";
}
if(!defined($allow_config_installation)) {
	print "** ERROR Recived a empty config installation permission, should be yes or no\n";
	log_progress("** ERROR Recived a empty config installation permission, should be yes or no");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Recived-a-empty-config-installation-permission-should-be-yes-or-no",
	"WARNING");
	exit 1;
} else {
	print "-- Config permission is set to:$allow_config_installation\n";
}
if(!defined($tellsyslog)) {
	print "** ERROR Recived a empty tellsyslog parameter, should be yes or no\n";
	log_progress("** ERROR Recived a empty tellsyslog parameter, should be yes or no");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Recived-a-empty-tellsyslog-parameter-should-be-yes-or-no",
	"WARNING");
	exit 1;
} else {
	print "-- Tellsyslog is set to:$tellsyslog\n";
}

# End of subfunction
}
sub retrive_files {
#
#
#
print "-- Retriving filebundle:$managementaccount\@$managementhost:out/$thishost.tar.gz $savedir/$thishost/files\n";
my $ecode=system("$scpcommand -i $managementkey $managementaccount\@$managementhost:out/$thishost.tar.gz $savedir/$thishost/files > /dev/null 2>&1");

if($ecode == 0 ) {
	print "-- Files retrived [OK]\n";
} else {
	print "** ERROR Failed to retrive filebundle from management server errorcode:$ecode\n";
	log_progress("** ERROR Failed to retrive filebundle from management server errorcode:$ecode");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Failed-to-retrive-filebundle-from-management-server-errorcode:$ecode",
	"WARNING");
	exit 1;
}
# End of subfunction
}
sub unpack_files {
#
#
#
chdir("$savedir/$thishost/files") or die "Failed to change to directory:$savedir/$thishost/files error:$!\n";

if (! -f "$savedir/$thishost/files/$thishost.tar.gz" ) {
	print "** ERROR couldnt locate:$savedir/$thishost/files/$thishost.tar.gz, maybe the download went wrong\n";
	log_progress("** ERROR couldnt locate:$savedir/$thishost/files/$thishost.tar.gz, maybe the download went wrong");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-couldnt-locate:$savedir/$thishost/files/$thishost.tar.gz-maybe-the-download-went-wrong",
	"WARNING");
	exit 1;
}
print "-- Unpacking filebundle\n";
my $ecode = system("tar -zxvf $thishost.tar.gz > /dev/null 2>&1");

if ( $ecode == 0 ) {
	print "-- Filebundle unpacked OK\n";
} else {
	print "** ERROR Recived bad exitcall from unpacking files:$ecode\n";
	log_progress("** ERROR Recived bad exitcall from unpacking files:$ecode");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Recived-bad-exitcall-from-unpacking-files:$ecode",
	"WARNING");
	exit $ecode;
}

# End of subfunction
}
sub retrive_file_settings {
#
#
#
my $filetocheck=$_[0];


print "-- Trying to retrive filesettings for target:$filetocheck\n";
my $filesettings=`$sshcommand -i $managementkey -l $managementaccount $managementhost "getfilesettings $filetocheck"`;
chomp $filesettings;

print "-- Filesettings received:$filesettings\n";

my @filesettings=split(":", $filesettings);
$fileprestep=$filesettings[1];
$filepermission=$filesettings[2];
$filepoststep=$filesettings[3];

if(!defined($fileprestep)) {
	$fileprestep="None";
}
if ($fileprestep eq "") {
	$fileprestep="None";
}
if(!defined($filepoststep)) {
	$filepoststep="None";
}
if($filepoststep eq "") {
	$filepoststep="None";
}
if(!defined($filepermission)) {
	$filepermission="None";
}
if($filepermission eq "") {
	$filepermission="None";
}
print "-- Prestep=$fileprestep Poststep=$filepoststep Rawpermission=$filepermission\n";

	if ($filepermission ne  "None") { 

		my @fileperms=split("-", $filepermission);
		$owner=$fileperms[0];
		$group=$fileperms[1];
		$filepermission=$fileperms[2];

		if ($owner eq "") {
			print "** Error Didnt recive any owner permission, cant continiue\n";
			log_progress("** ERROR Didnt recive any owner permission, cant continiue");
			save_runlog();
			send_email();
			notify_andutteye_surveillance("Andutteyemanagement-agent-error",
			"**-ERROR-Didnt-recive-any-owner-permission-cant-continiue",
			"WARNING");
			exit 1;
		}
		if ($group eq "") {
			print "** Error Didnt recive any group permission, cant continiue\n";
			log_progress("** ERROR Didnt recive any group permission, cant continiue");
			save_runlog();
			send_email();
			notify_andutteye_surveillance("Andutteyemanagement-agent-error",
			"**-ERROR-Didnt-recive-any-group-permission-cant-continiue",
			"WARNING");
			exit 1;
		}
		if ($filepermission eq "") {
			print "** Error Didnt recive any filpermission, cant continiue\n";
			log_progress("** ERROR Didnt recive any filepermission, cant continiue");
			save_runlog();
			send_email();
			notify_andutteye_surveillance("Andutteyemanagement-agent-error",
			"**-ERROR-Didnt-recive-any-filepermission-cant-continiue",
			"WARNING");
			exit 1;
		}
		print "-- Owner:$owner Group:$group Filepermissions:$filepermission\n";
	} else {
		print "** Error didnt recive any raw file permission settings at all, cant continiue\n";
		log_progress("** Error didnt recive any raw file permission settings at all, cant continiue");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-didnt-recive-any-raw-file-permission-settings-at-all-cant-continiue",
		"WARNING");
		exit 1;
	}
# End of subfunction
}
sub log_progress {
#
#
#
my $logpost=$_[0];

open("LOG",">>$savedir/$thishost/$thishost-$date.log")
	or die "Failed to open logfile:$savedir/$thishost/$thishost-$date.log error:$!\n";
print LOG "$logpost\n";
close(LOG);

# End of subfunction
}
sub fix_permissions {
#
#
#
my $file=$_[0];

print "-- Setting correct permissions on:$file [owner=$owner group=$group permission:$filepermission]\n";
my $settingecode=system("chown $owner:$group $file;chmod $filepermission $file");

if($settingecode == 0 ) {
	print "-- Filesettings updated [OK]\n";
} else {
	print "** ERROR failed to modify filepermissions on the new file:$file\n";
	log_progress("** ERROR failed to modify filepermissions on the new file:$file");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-failed-to-modify-filepermissions-on-the-new-file:$file",
	"WARNING");
	exit 1;
}

# End of subfunction
}
sub execute_prestep {
#
#
#
my $fileprestep=$_[0];

if (! -f "$fileprestep") {
	print "** ERROR Specified prestep:$fileprestep doesnt exist.\n";
	log_progress("** ERROR Specified prestep:$fileprestep doesnt exist");
	tellsyslog("Specified prestep:$fileprestep doesnt exist");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Prestep-doesnt-exist",
	"WARNING");
}
print "-- Executing prestep:$fileprestep\n";
log_progress("-- Executing prestep:$fileprestep");
tellsyslog("Executing prestep:$fileprestep");
my $preexitcode=system("$fileprestep >> $savedir/$thishost/$thishost-$date.log");

	if ($preexitcode == 0 ) {
		print "-- Prestep ended successfully\n";
		log_progress("-- Prestep ended successfully");
		tellsyslog("Executed prestep:$fileprestep ok");
	} else {
		print "** ERROR Prestep ended with exitcode:$preexitcode\n";
		log_progress("** ERROR Prestep ended with exitcode:$preexitcode");
		tellsyslog("Executed prestep:$fileprestep failed with exitcode:$preexitcode");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-Prestep-ended-with-exitcode:$preexitcode",
		"WARNING");
		exit $preexitcode;
	}


# End of sufunction
}
sub execute_poststep {
#
#
#
my $filepoststep=$_[0];

print "-- Executing poststep:$filepoststep\n";
log_progress("-- Executing poststep:$filepoststep");
my $postexitcode=system("$filepoststep >> $savedir/$thishost/$thishost-$date.log");
	if ($postexitcode == 0 ) {
		print "-- Poststep ended successfully\n";
		log_progress("-- Poststep ended successfully");
		tellsyslog("Executed poststep:$filepoststep ended with exitcode:$postexitcode");
	} else {
		print "** ERROR Poststep ended with exitcode:$postexitcode\n";
		log_progress("** ERROR Poststep ended with exitcode:$postexitcode");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"** ERROR-Poststep-ended-with-exitcode:$postexitcode",
		"WARNING");
		exit $postexitcode;
	}


# End of subfunction
}
sub perform_clean {
#
#
#
print "-- Cleaning files, and old directorys\n";
if ( -d "$savedir" ) {
	print "-- Removing old filesstructure:$savedir/$thishost/files and file(s)\n";
	`rm -rf $savedir/$thishost/files`;
        `rm -rf $savedir/$thishost/$thishost-packages`;
        `rm -f $savedir/$thishost/*.log`;
        `rm -f $savedir/$thishost/*.gz`;
        `rm -f $savedir/$thishost/*.list`;

}
# End of subfunction
}
sub check_for_active_session {
#
#
#
my $nractive_sessions=`ps -efl | grep aemanagement.pl |grep -v grep | wc -l`;
chomp $nractive_sessions;

if($nractive_sessions > 1) {
	print "** INFORMATION An active andutteyemanagement session was discovered (count:$nractive_sessions st), will not continue\n";
	log_progress("** INFORMATION An active andutteyemanagement session was discovered (count:$nractive_sessions st), will not continue");
	save_runlog();
	send_email();
	exit 1;
} else {
	print "-- Found no other active andutteyemanagement process, continue processing\n";
}

# End of subfunction
}
sub save_runlog {
#
#
#
if ( -z "$savedir/$thishost/$thishost-$date.log") {
	print "## Worklog is 0kb big, will not try to save on repositoryserver\n";
	return 0;
}
if ( -f "$savedir/$thishost/$thishost-$date.log" ) {
	print "-- Saving runlog:$savedir/$thishost/$thishost-$date.log.upload on $managementhost\n";
	`$scpcommand -i $managementkey $savedir/$thishost/$thishost-$date.log $managementaccount\@$managementhost:log-client/$thishost-$date.log.upload`;
	 my $ecode=system("$sshcommand -i $managementkey -l $managementaccount $managementhost \"appendlog $thishost $thishost-$date.log.upload\"");
		if($ecode == 0) {
			print "-- $savedir/$thishost/$thishost-$date.log.upload uploaded and appended to todays log.\n";
		} else {
			print "** ERROR Failed to append:log-client/$thishost-$date.log.upload to todays worklog\n";
			log_progress("** ERROR Failed to append:log-client/$thishost-$date.log.upload to todays worklog");
			exit 1;
		}
} else {
	print "-- Dont have to save any log in repository. Didnt have anything to do\n";
}


# End of subfunction
}
sub generate_rpmlist {
#
#
#
print "-- Generating rpmpackage list for $thishost\n";
open("RPMLIST",">$savedir/$thishost/$thishost-rpmpackagelist.log")
	or die "Failed to open:$savedir/$thishost/$thishost-rpmpackagelist.log for writing error:$!\n";
my @rpms=`rpm -qa --qf %-30{NAME}\\\t%{VERSION}\\\t%{RELEASE}\\\\n`;

for(@rpms) {
	chomp;
	if($debug > 2) {
		print "-- Adding package:$_\n";
	}
	print RPMLIST "$_\n";
}

if( -f "$savedir/$thishost/$thishost-rpmpackagelist.log" ) {
	print "-- Rpmlist generated [OK]\n";
} else {
	print "** ERROR Failed to generate rpmpackage list, it didnt exist when i checked\n";
	log_progress("** ERROR Failed to generate rpmpackage list, it didnt exist when i checked");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"** ERROR-Failed-to-generate-rpmpackage-list-it-didnt-exist-when-i-checked",
	"WARNING");
	exit 1;
}
close("RPMLIST");
# End of subfunction
}
sub get_md5hash {
#
#
#
open("FILE", "< $savedir/$thishost/$thishost-rpmpackagelist.log") 
	or die "Failed to open rpmpackge list: $savedir/$thishost/$thishost-rpmpackagelist.log error:$!\n";

$md5hash=Digest::MD5->new->addfile(*FILE)->hexdigest;

print "-- Rpmpackage list md5hash:$md5hash\n";
close(FILE);

# End of subfunction
}
sub send_rpmlist {
#
#
#
my $ecode;

if ( ! -f "$savedir/$thishost/$thishost-rpmpackagelist.log" ) {
	print "** ERROR Couldnt locate any rpmpackagelist, as $savedir/$thishost/$thishost-rpmpackagelist.log\n";
	log_progress("** ERROR Couldnt locate any rpmpackagelist, as $savedir/$thishost/$thishost-rpmpackagelist.log");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Couldnt-locate-any-rpmpackagelist-as-$savedir/$thishost/$thishost-rpmpackagelist.log",
	"WARNING");
	exit 1;
} else {
	print "-- Sending rpmpackagelist to repository\n";
	$ecode=system("$scpcommand -i $managementkey $savedir/$thishost/$thishost-rpmpackagelist.log $managementaccount\@$managementhost:in > /dev/null 2>&1");
}

if ( $ecode == 0 ) {
	print "-- Rpmpackagelist saved [OK]\n";
} else {
	print "** ERROR Failed to save rpmpackagelist on repository, errorcode:$ecode\n";
	log_progress("** Failed to save rpmpackagelist on repository, errorcode:$ecode");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Failed-to-save-rpmpackagelist-on-repository-errorcode:$ecode",
	"WARNING");
	exit $ecode;
}
	
# End of subfunction
}
sub compare_files {
#
#
#
my $tmpdirname;
my $tmpfilename;
my $difflist;
my $imp_order;

if( !-f "$savedir/$thishost/files/$thishost.fileindex") {
	print "## ERROR Fileindex $savedir/$thishost/files/$thishost.fileindex not found.\n";
	exit;
}
	open("FILEINDEX","<$savedir/$thishost/files/$thishost.fileindex")
		or die "## ERROR Failed to open $savedir/$thishost/files/$thishost.fileindex for reading.\n";

for(1..20) {
	$imp_order="$_";
	
	open("FILEINDEX","<$savedir/$thishost/files/$thishost.fileindex")
		or die "## ERROR Failed to open $savedir/$thishost/files/$thishost.fileindex for reading.\n";

	for(<FILEINDEX>) {
		chomp;

		my @tmp=split(":","$_");
		my $findex_imp_order="$tmp[0]";
		my $findex_object="$tmp[1]";
		$tmpdirname=dirname($findex_object);
		$tmpfilename=basename($findex_object);

		if($findex_imp_order eq "") {
			print "## ERROR No implementation order is defined for:$findex_object. Aborting!\n";
			exit;
		}

		if("$imp_order" == "$findex_imp_order") {	
		     print "-- [$imp_order] Checking $findex_object ($tmpdirname|$tmpfilename)\n";
		
			if (! -f "$findex_object") {
				print "## Information file:$findex_object doesnt exist on the system, installing a new file\n";
	
				log_progress("** Warning file:$findex_object doesnt exist on the system, installing a new file");
                		tellsyslog("Warning file:$findex_object doesnt exist on the system, installing a new file");

                		if ( ! -d "/$tmpdirname" ) {
                        		print "## Warning directory that the file recides on dont exist, creating it\n";
                        		log_progress("** Warning directory that the file recides on dont exist, creating it");
                        		tellsyslog("Warning directory that the file recides on dont exist, creating it");
                        		print "-- Creating:$tmpdirname\n";
                        		`mkdir -p $tmpdirname`;
                        		retrive_file_settings("$tmpdirname");
                        		fix_permissions("$tmpdirname");
                		}

                		if("$allow_config_installation" eq "yes" ) {
                        		# Trying to retrive filesettings for the specific file.
                        		retrive_file_settings("$findex_object");

                        		# If fileprestep are set we are trying to execute the prestep.
                        		if($fileprestep ne "None") {
                                		execute_prestep("$fileprestep");
                        		}

                        		# Now we move the original file to place.
                        		print "-- Moving new file on place:$savedir/$thishost/files/$findex_object -> $tmpdirname/$tmpfilename\n";
                        		my $moveexitcode=system("mv -f $savedir/$thishost/files/$findex_object $tmpdirname");
                        		if ($moveexitcode == 0 ) {
                                		print "-- New file successfully installed\n";
                        		} else {
                                		print "** ERROR new fileinstallation failed with exitcode:$moveexitcode\n";
                                		log_progress("** ERROR new fileinsallation failed with exitcode:$moveexitcode");
                                		save_runlog();
                                		send_email();
                                		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
                                		"**-ERROR-new-fileinsallation-failed-with-exitcode:$moveexitcode",
                                		"CRITICAL");
                                		exit $moveexitcode;
                        		}
                        		fix_permissions("$findex_object");
                        		# If poststep are set we execute postprogram.
                        		if($filepoststep ne "None") {
                                		execute_poststep("$filepoststep");
                        		}
               	 		} else {
                        		print "-- Config permission are set to:$allow_config_installation will only print differences\n";
                        		log_progress("-- Config permission are set to:$allow_config_installation will only print differences");
                		}

			} else {
				$difflist=`diff $savedir/$thishost/files/$findex_object $findex_object`;
				chomp $difflist;
		
				if ($difflist eq "") {
					# Everything is fine, we will not do anything.
					print "-- Files are identical [OK]\n";
				} else {
					# If we find differences we begin our work.
					print "## Files differs [NOTOK]\n";

                        		log_progress("-- Found differences in file $findex_object");
                        		tellsyslog("Found differences in file $findex_object");
                        		log_progress("$difflist");

                   			if("$allow_config_installation" eq "yes" ) {
                        			# Copying original file as backup to a backupdirectory.
                        			my $copyfile_ecode=system("cp -f /$findex_object $filesavedir/$tmpfilename-$date");
                        
						if($copyfile_ecode == 0 ) {
                                			print "-- Original file saved as:$filesavedir/$tmpfilename-$date\n";
                        			} else {
                                			print "** Failed to safecopy original file, exitcode:$copyfile_ecode\n";
                                			log_progress("** ERROR Failed to safecopy original file, exitcode:$copyfile_ecode");
                                			save_runlog();
                                			send_email();
                                			notify_andutteye_surveillance("Andutteyemanagement-agent-error",
                                			"** ERROR-Failed-to-safecopy-original-file-exitcode:$copyfile_ecode",
                                			"CRITICAL");
                                			exit $copyfile_ecode;
                        			}
                        			# Trying to retrive filesettings for the specific file.
                        			retrive_file_settings("$findex_object");

                        			# If fileprestep are set we are trying to execute the prestep.
                        			if($fileprestep ne "None") {
                                			execute_prestep("$fileprestep");
                        			}
                        			# Now we move the original file to place.
                        			print "-- Moving new file on place:$savedir/$thishost/files/$findex_object -> $tmpdirname/$tmpfilename\n";
                        			my $moveexitcode=system("mv -f $savedir/$thishost/files/$findex_object $tmpdirname");
                        			if ($moveexitcode == 0 ) {
                                			print "-- New file successfully installed\n";
                                			log_progress("-- New file successfully installed");
                        			} else {
							 print "** ERROR new fileinstallation failed with exitcode:$moveexitcode\n";
                                			log_progress("** ERROR new fileinstallation failed with exitcode:$moveexitcode");
                                			save_runlog();
                                			send_email();
                                			notify_andutteye_surveillance("Andutteyemanagement-agent-error",
                                			"**-ERROR-new-fileinsallation-failed-with-exitcode:$moveexitcode",
                                			"CRITICAL");
                                			exit $moveexitcode;
                        			}
                        			fix_permissions("$findex_object");
                        			# If poststep are set we execute postprogram.
                        			if($filepoststep ne "None") {
                                			execute_poststep("$filepoststep");
                        			}
                   			} else {
                      				print "-- Config permission are set to:$allow_config_installation will only print differences\n";
                      				log_progress("-- Config permission are set to:$allow_config_installation will only print differences");
                   			}

				}
			}
		}

    	}
	close("FILEINDEX")
		or die "## ERROR Failed to close $savedir/$thishost/files/$thishost.fileindex after reading.\n";
}

# End of subfunction
}
sub gather_packages {
#
#
#
my $ecode;

if($allow_package_installation ne "yes") {
	print "-- Asking repository to gather any package changes to me=$thishost md5hash=$md5hash [Generate list only]\n";
	$ecode=system("$sshcommand -i $managementkey -l $managementaccount $managementhost \"gatherpackages $thishost $md5hash -listonly\" > $savedir/$thishost/$thishost-$date.log 2>&1");
} else {
	print "-- Asking repository to gather any package changes to me=$thishost md5hash=$md5hash\n";
	$ecode=system("$sshcommand -i $managementkey -l $managementaccount $managementhost \"gatherpackages $thishost $md5hash normal\" > $savedir/$thishost/$thishost-$date.log 2>&1");
}

if($ecode == 0 ) {
	print "-- Gatherpackages completed [OK]\n";
} else {
	print "** ERROR Failed to gather packages files belonging to me\n";
	log_progress("** ERROR Failed to gather packages files belonging to me");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Failed-to-gather-packages files-belonging-to-me",
	"WARNING");
	exit 1;
}

# End of subfunction
}
sub retrive_packages {
#
#
#
print "-- Retriving packages:$managementaccount\@$managementhost:out/$thishost-packages.tar.gz $savedir/$thishost\n";
`$scpcommand -i $managementkey $managementaccount\@$managementhost:out/$thishost*.* $savedir/$thishost > /dev/null 2>&1`;

if( -f "$savedir/$thishost/$thishost-packages.tar.gz") {
	print "-- Packages retrived [OK]\n";
} else {
	print "## Didnt retrive any packages, suppose i have nothing to do\n";
}

# End of subfunction
}
sub parse_rpmactionlist {
#
#
#
my $loop=0;
my $loop1=0;

if ( -f "$savedir/$thishost/$thishost-action.list" ) {
	print "-- Validating actionlist actions\n";
} else {
	print "-- Found no package work to do, actionlist is missing\n";
	return 0;
}
open("ACTIONLIST","<$savedir/$thishost/$thishost-action.list")
	or die "** ERROR Failed to open $savedir/$thishost/$thishost-action.list error:$!\n";

for(<ACTIONLIST>) {
	chomp;
	my @data=split(":",$_);
	
	if(!defined($data[0])) {
		print "** ERROR Syntax in actionlist is corrupt (field 0)\n";
		log_progress("** ERROR  Syntax in actionlist is corrupt (field 0)");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-Syntax-in-actionlist-is-corrupt-(field 0)",
		"WARNING");
		exit 1;
	}
	if(!defined($data[1])) {
		print "** ERROR Syntax in actionlist is corrupt (field 1)\n";
		log_progress("** ERROR  Syntax in actionlist is corrupt (field 1)");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-Syntax-in-actionlist-is-corrupt-(field1)",
		"WARNING");
		exit 1;
	}
	if(!defined($data[2])) {
		print "** ERROR Syntax in actionlist is corrupt (field 2)\n";
		log_progress("** ERROR  Syntax in actionlist is corrupt (field 2)");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-Syntax-in-actionlist-is-corrupt-(field2)",
		"WARNING");
		exit 1;
	}
	if(!defined($data[3])) {
		print "** ERROR Syntax in actionlist is corrupt (field 3)\n";
		log_progress("** ERROR  Syntax in actionlist is corrupt (field 3)");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-Syntax-in-actionlist-is-corrupt-(field3)",
		"WARNING");
		exit 1;
	}
	print "-- Action:$data[0] Package:$data[1] Version:$data[2] Release:$data[3]\n";
	if($data[0] eq "DELETE") {
		print "-- Adding package:$data[1] to delete array\n";
		log_progress("-- Adding package for removal");
		log_progress("--\tPackage:$data[1]");
		log_progress("--\tVersion:$data[2]");
	        log_progress("--\tRelease:$data[3]");
	        log_progress("---------------------------");
		push(@rpmdelete,$data[1]);
		$loop1++;
	}
	elsif($data[0] eq "NEWERINSTALLED") {
		print "-- Newer package:$data[1] is installed localy then defined in repository, keeping it.\n";
		log_progress("-- INFORMATION Newer package is installed then defined in repository. Manual interfearence needed.");
		log_progress("--\tPackage:$data[1]");
		log_progress("--\tRepository  Version :$data[2]");
	        log_progress("--\tRepository  Release :$data[4]");
	        log_progress("--\tInstalled   Version :$data[3]");
	        log_progress("--\tInstalled   Release :$data[5]");
	        log_progress("---------------------------");
	}
	elsif ($data[0] eq "INSTALL" ) {
		if(!defined($data[4])) {
			print "** ERROR Syntax in actionlist is corrupt (field 4)\n";
			log_progress("** ERROR  Syntax in actionlist is corrupt (field 4)");
			save_runlog();
			send_email();
			notify_andutteye_surveillance("Andutteyemanagement-agent-error",
			"**-ERROR-Syntax-in-actionlist-is-corrupt-(field4)",
			"WARNING");
			exit 1;
		}
		print "-- Adding package:$data[4] to install/upgrade array\n";
		log_progress("-- Adding package for installation");
		log_progress("--\tPackage:$data[1]");
		log_progress("--\tVersion:$data[2]");
	        log_progress("--\tRelease:$data[3]");
	        log_progress("---------------------------");

		if($allow_package_installation ne "yes") {
			print "-- Running in verifymode, will only print differences\n";
		} else {

		if (! -f "$savedir/$thishost/$thishost-packages.tar.gz" ) {
			print "** ERROR Package bundle from server is missing:$savedir/$thishost/$thishost-packages.tar.gz\n";
			log_progress("** ERROR  Package bundle from server is missing:$savedir/$thishost/$thishost-packages.tar.gz");
			save_runlog();
			send_email();
			notify_andutteye_surveillance("Andutteyemanagement-agent-error",
			"**-ERROR-Package-bundle-from-server-is-missing:$savedir/$thishost/$thishost-packages.tar.gz",
			"CRITICAL");
			exit 1;
		} else {
			if($tightchecking eq "yes") {
				$exists=system("tar -tzf $savedir/$thishost/$thishost-packages.tar.gz |grep $data[4] > /dev/null 2>&1");
			} else {
			 	$exists=0;
			}
			if ($exists == 0) {
				print "-- Package:$data[4] are accounted for\n";
			} else {
				print "** ERROR Required package:$data[4] are not included in bundle from server\n";
				log_progress("** ERROR Required package:$data[4] are not included in bundle from server");
				save_runlog();
				send_email();
				notify_andutteye_surveillance("Andutteyemanagement-agent-error",
				"** ERROR-Required-package:$data[4]-are-not-included-in-bundle-from-server",
				"CRITICAL");
				exit 1;
			}
		}
		push(@rpminstall,$data[4]);
		$loop++;
	}
	} else {
		print "** ERROR Recived an invalid action in actionlist, aborting\n";
		log_progress("** ERROR Recived an invalid action in actionlist, aborting");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-Recived-an-invalid-action-in-actionlist-aborting",
		"CRITICAL");
		exit 1;
	}


}
close("ACTIONLIST");
if($allow_package_installation ne "yes") {
	print "-- Running in verifymode, will only print differences. Leaving this subfunction\n";
}
if($loop1) {
	my $rpms_to_delete=join(" ",@rpmdelete);
	print "-- Joining all packages in delete-array:$rpms_to_delete\n";
	delete_rpms("$rpms_to_delete");
} else {
	print "-- Found no packages to delete\n";
}
if($loop) {
	my $rpms_to_install=join(" ",@rpminstall);
	print "-- Joining all packages in install-array:$rpms_to_install\n";

	if( ! -d "$savedir/$thishost/$thishost-packages") {
		print "-- Creating temporary extract directory:$savedir/$thishost/$thishost-packages\n";
		`mkdir -p $savedir/$thishost/$thishost-packages`;
	}
	print "-- Extracting tarbundle\n";
	system("tar -zxf $savedir/$thishost/$thishost-packages.tar.gz --directory=$savedir/$thishost/$thishost-packages > /dev/null 2>&1");
	install_rpms("$rpms_to_install");
} else {
	print "-- Found nothing to install or upgrade\n";
}

# End of subfunction
}
sub install_rpms {
#
#
#
if(!defined($_[0])) {
	print "** ERROR Recived a blank packagelist to install/upgrade. Should be impossible\n";
	log_progress("** ERROR Recived a blank packagelist to install/upgrade. Should be impossible");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Recived-a-blank-packagelist-to-install/upgrade.-Should-be-impossible",
	"CRITICAL");
	exit 1;
}
if ($allow_package_installation eq "yes" ) {
	print "-- Installing/Upgrading packages, please wait.....\n";
	my $ecode=system("cd $savedir/$thishost/$thishost-packages;rpm -Uvh $_[0] >> $savedir/$thishost/$thishost-$date.log 2>&1");

	if($ecode == 0) {
		print "-- Installation/Upgrade of packages completed successfully\n";
		tellsyslog("Installed-Upgraded package(s):$_[0]");
	} else {
		print "** ERROR Something went wrong during package installation, aborting\n";
		log_progress("** ERROR Something went wrong during package installation, aborting");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-Something-went-wrong-during-package-installation-aborting",
		"CRITICAL");
		exit 1;
	}
} else {
	print "-- Running in drymode, arent allowed to install/upgrade packages.\n";
	log_progress("** Running in drymode, arent allowed to install/upgrade packages.\n");
}


# End of subfunction
}
sub delete_rpms {
#
#
#

if(!defined($_[0])) {
	print "** ERROR Recived a blank packagelist to delete. Should be impossible\n";
	log_progress("** ERROR Recived a blank package to delete. Should be impossible");
	save_runlog();
	send_email();
	notify_andutteye_surveillance("Andutteyemanagement-agent-error",
	"**-ERROR-Recived-a-blank-package-to-delete.-Should-be-impossible",
	"CRITICAL");
	exit 1;
}
if ($allow_package_installation eq "yes" ) {
	print "-- Removing unwanted packages, please wait.....\n";
	my $ecode=system("rpm -e $_[0] >> $savedir/$thishost/$thishost-$date.log 2>&1");

	if($ecode == 0) {
		print "-- Removal of packages completed successfully\n";
		log_progress("-- Removal of packages completed successfully");
		tellsyslog("Removed package(s):$_[0]");
	} else {
		print "** ERROR Something went wrong during package removal, aborting\n";
		log_progress("** ERROR Something went wrong during package removal, aborting");
		save_runlog();
		send_email();
		notify_andutteye_surveillance("Andutteyemanagement-agent-error",
		"**-ERROR-Something-went-wrong-during-package-removal-aborting",
		"CRITICAL");
		exit 1;
	}
} else {
	print "** Running in drymode, arent allowed to remove any packages.\n";
	log_progress("** Running in drymode, arent allowed to remove any packages.\n");
}

# End of subfunction
}
sub notify_andutteye_surveillance {
#
#
#
my $smsg;
my $lmsg;
my $severity;

if (defined($_[0])) {
        $smsg=$_[0];
} else {
        print "** ERROR Shortinformation message isnt set\n";
	log_progress("** ERROR Shortinformation message isnt set");
	save_runlog();
	send_email();
        exit 1;
}
if (defined($_[1])) {
        $lmsg=$_[1];
} else {
        print "** ERROR Longinformation message isnt set\n";
	log_progress("** ERROR Longinformation message isnt set");
	save_runlog();
	send_email();
        exit 1;
}
if (defined($_[2])) {
        $severity=$_[2];
} else {
        print "** ERROR Severity isnt set\n";
	log_progress("** ERROR Severity isnt set");
	save_runlog();
	send_email();
        exit 1;
}
if (!defined($aesurveillance_server)) {
        print "** ERROR Cant notify Andutteye Surveillance, missing Andutteye server\n";
	log_progress("** ERROR Cant notify Andutteye Surveillance, missing Andutteye server");
	save_runlog();
	send_email();
        exit 1;
}
if (!defined($aesurveillance_port)) {
        print "** ERROR Cant notify Andutteye Surveillance, missing Andutteye port\n";
	log_progress("** ERROR Cant notify Andutteye Surveillance, missing Andutteye port");
	save_runlog();
	send_email();
        exit 1;
}
if (!defined($aesurveillance_program)) {
        print "** ERROR Cant notify Andutteye Surveillance, missing Andutteye program path\n";
	log_progress("** ERROR Cant notify Andutteye Surveillance, missing Andutteye program path");
	save_runlog();
	send_email();
        exit 1;
}
if($aesurveillance_method eq "server" ) {
  print "-- Will send alarm by Andutteye management server\n";
  print "-- Debug:$smsg,$lmsg,$severity\n";
  my $ecode=system("$sshcommand -i $managementkey -l $managementaccount $managementhost \"reporttoae $smsg,$lmsg,$severity $thishost\"");
  
  if($ecode == 0) {
  	print "-- Andutteye Surveillance communicated OK\n";
  } else {
	print "** ERROR Failed to send Andutteye surveillance alarm thru aemanagement server\n";
	exit 1;
 }
}
elsif($aesurveillance_method eq "local") {

	if ( ! -f $aesurveillance_program ) {
        	print "** ERROR Couldnt locate Andutteye Surveillance post utility:$aesurveillance_program\n";
		log_progress("** ERROR Couldnt locate Andutteye Surveillance post utility:$aesurveillance_program");
		save_runlog();
		send_email();
        	exit 1;
	}
	if ( ! -x $aesurveillance_program ) {
        	print "** ERROR Andutteye post utility isnt executeble\n";
		log_progress("** ERROR Andutteye post utility isnt executeble");
		save_runlog();
		send_email();
        	exit 1;
	}
	print "-- Trying to send alarm to AndutteyeSurveillance method:local server:$aesurveillance_server, port:$aesurveillance_port\n";
	my $ecode=system("$aesurveillance_program -send $aesurveillance_server $aesurveillance_port \"$smsg\" \"$lmsg\" $severity > /dev/null 2>&1");

	if ($ecode == 0 ) {
        	print "-- Alarm posted to Andutteye Surveillance\n";
	} else {
        	print "** ERROR Failed to post Andutteye Surveillance alarm\n";
		log_progress("** ERROR Failed to post Andutteye Surveillance alarm");
		save_runlog();
		send_email();
        	exit 1;
	}
}
elsif($aesurveillance_method eq "NO") {
	print "-- Andutteye Surveillance method are set to NO, will not trigger any alarm\n";
} else {
	print "** ERROR Invalid configuration syntax for aesurveillance_method. Only server and local are valid\n";
	log_progress("** ERROR Invalid configuration syntax for aesurveillance_method. Only server and local are valid");
	save_runlog();
	send_email();
	exit 1;
}

# End of subfunction
}
##########################################################
#
# START PROGRAM EXECUTION
#
##########################################################
print "\n$version\n\n";
check_for_active_session();
check_if_allow_to_run();
perform_clean();
create_nessasary();
get_allow_data();
#
# If enable_rpm_package_validation are set to yes we validate the rpmdatabase.
#
if ($enable_rpm_package_validation eq "yes") {
	generate_rpmlist();
	get_md5hash();
	send_rpmlist();
	gather_packages();
	retrive_packages();
	parse_rpmactionlist();
} else {
	print "** enable_rpm_package_validation are set to:$enable_rpm_package_validation. Will no validate rpm integrity.\n";
}
if ($enable_file_validation eq "yes") {
	gather_files();
	retrive_files();
	unpack_files();
	compare_files();
} else {
	print "** enable_file_validation are set to:$enable_file_validation. Will no validate file(s) integrity.\n";
}
save_runlog();
send_email();
