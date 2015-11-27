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
# Description:Andutteye management API layer. A part of andutteye management.
# 
# $Id: aemanagement-api.pl,v 1.17 2006/10/15 16:48:21 andutt Exp $
#
# The version parameter tells current version of the program
our $version="Andutteye Software Suite Management API Version:1.0 Fixlevel:1.0 Lastfix:2004-12-01 (andutt)";

##############################################################
#
# Unchangeble parameters, dont change them.
#
##############################################################
our $managementdir;
our $aesurveillance_activate;
our $aesurveillance_server;
our $aesurveillance_port;
our $aesurveillance_program;
our $tellsyslog_activate;
our $ae_databasemode;
our $ae_databasetype;
our $ae_databasesid;
our $ae_databaseusr;
our $ae_databasepwd;
our $command;
our $action;
our $md5sum;
our $sql;
our $dbh;
our $sth;
our @row;
our @ARGV;

use strict;
use warnings;
use File::Basename;
use Digest::MD5;

if(!defined($ENV{ANDUTTEYEMANAGEMENT_REPOSITORY})) {
	print "** ERROR Andutteye management repository location parameter isnt set. Check documentation for more info.\n";
	exit 1;
} else {
	require("$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/config/aemanagement-config.conf");
}
for(@ARGV) {
	if(/^-md5sum/) {
		my @tmpsettings=split("=",$_);
		$md5sum=$tmpsettings[1];
	}
	if(/-getfilesettings/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getfilesettings";
	}
	if(/-gatherconfig/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="gatherconfig";
	}
	if(/-gatherpackages/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="gatherpackages";
	}
	if(/-getdistribution/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getdistribution";
	}
	if(/-getgroup/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getgroup";
	}
	if(/-getlocation/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getlocation";
	}
	if(/-getpatchlevel/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getpatchlevel";
	}
	if(/-getpatchlevelstatus/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="get_patchlevel_status";
	}
	if(/-getdescription/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getdescription";
	}
	if(/-getstatus/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getstatus";
	}
	if(/-getpackages/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getpackages";
	}
	if(/-getbundles/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getbundles";
	}
	if(/-getallowrpmupdate/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getallowrpmupdate";
	}
	if(/-getallowconfigupdate/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="allowconfigupdate";
	}
	if(/-getallowsyslog/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="allowsyslog";
	}
	if(/-getemail/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="email";
	}
	if(/-getarchtype/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="archtype";
	}
	if(/-genrpmindex/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="genrpmindex";
	}
	if(/-getexclude/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getexclude";
	}
	if(/-getpackagetype/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getpackagetype";
	}
	if(/-genbaselist/) {
		$action="genbaselist";
		$command="genbaselist";
	}
	if(/-getcurrentpackages/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getcurrentpackages";
	}
	if(/-getcurrentpackagechecksum/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="getcurrentpackagechecksum";
	}
	if(/-sendnotificationemail/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="sendnotificationemail";
	}
	if(/-appendlog/) {
		my @tmpsettings=split("=",$_);
		$command=$tmpsettings[1];
		$action="appendlog";
	}
	if(/-reporttoaesurveillance/) {
		my @tmpsettings=split("=",$_);
		my @tmpstring=split(",",$tmpsettings[1]);
		notify_andutteye_surveillance("$tmpstring[0]","$tmpstring[1]","$tmpstring[2]","$tmpstring[3]");
		exit;
	}
}
if(!$command) {
	program_info();
}
elsif($action eq "getfilesettings") {
	get_file_settings("$command");
}
elsif($action eq "sendnotificationemail") {
	sendnotificationemail("$command");
}
elsif($action eq "appendlog") {
	appendlog("$command");
}
elsif($action eq "gatherconfig") {
	gather_config("$command");
}
elsif($action eq "gatherpackages") {
	gatherpackages("$command");
}
elsif($action eq "getdistribution") {
	get_specification_value("distribution","$command");
}
elsif($action eq "getgroup") {
	get_specification_value("group","$command");
}
elsif($action eq "getlocation") {
	get_specification_value("location","$command");
}
elsif($action eq "getpatchlevel") {
	get_specification_value("patchlevel","$command");
}
elsif($action eq "getdescription") {
	get_specification_value("description","$command");
}
elsif($action eq "get_patchlevel_status") {
	get_patchlevel_status("$command");
}
elsif($action eq "getstatus") {
	get_specification_value("status","$command");
}
elsif($action eq "getpackages") {
	get_specification_value("packages","$command");
}
elsif($action eq "getexclude") {
	get_specification_value("exclude","$command");
}
elsif($action eq "getpackagetype") {
	get_specification_value("packagetype","$command");
}
elsif($action eq "getbundles") {
	get_specification_value("bundles","$command");
}
elsif($action eq "getallowrpmupdate") {
	get_specification_value("allow-rpmupdate","$command");
}
elsif($action eq "allowconfigupdate") {
	get_specification_value("allow-configupdate","$command");
}
elsif($action eq "allowsyslog") {
	get_specification_value("allow-syslog","$command");
}
elsif($action eq "email") {
	get_specification_value("email","$command");
}
elsif($action eq "archtype") {
	get_specification_value("archtype","$command");
}
elsif($action eq "genrpmindex") {
	genrpmindex("genrpmindex","$command");
}
elsif($action eq "genbaselist") {
	genbaselist("genbaselist");
}
elsif($action eq "getcurrentpackages") {
	getcurrentpackages("$command");
}
elsif($action eq "getcurrentpackagechecksum") {
	getcurrentpackagechecksum("$command");
}
sub getcurrentpackagechecksum {
#
#
#
my $hostname=$_[0];

open ("FILE","<$managementdir/in/$hostname-rpmpackagelist.log")
or die "Failed to open hostrpmlist:$managementdir/in/$hostname-rpmpackagelist.log, error:$!\n";

my $md5hash=Digest::MD5->new->addfile(*FILE)->hexdigest;
if (!$md5hash) {
	print "** ERROR Failed to gather md5hash for $managementdir/in/$hostname-rpmpackagelist.log\n";
	notify_andutteye_surveillance("Andutteyemanagement API error",
	"ERROR Failed to gather md5hash for $managementdir/in/$hostname-rpmpackagelist.log",
	"WARNING");
	exit 1;
} else {
	print "$md5hash\n";
}

# End of subfunction
}
sub getcurrentpackages {
#
#
#
my $hostname=$_[0];

open ("CURRENT","<$managementdir/in/$hostname-rpmpackagelist.log")
or die "Failed to open hostrpmlist:$managementdir/in/$hostname-rpmpackagelist.log, error:$!\n";

for(<CURRENT>) {
	print;
}
# End of subfunction
}
sub get_specification_value {
#
#
#
my $valuetoget=$_[0];
my $hostname=$_[1];

if ($ae_databasemode ne "yes") {
	open ("SPECIFICATION","<$managementdir/specifications/$hostname")
		or die "Failed to open hostspecification:$managementdir/specifications/$hostname, error:$!\n";
		for(<SPECIFICATION>) {
			chomp;
			if(/^#/) {
				next;
			}
			if(/^$valuetoget/) {
				my @tmparray=split(":", $_);
				if(!defined($tmparray[1])) {
				print "\n";
			} else {
				print "$tmparray[1]\n";
			}
			last;
		}
	}
} else {
	connect_to_database();

	# Translating forbidden databasesyntax	
	if($valuetoget eq "allow-rpmupdate") {
		$valuetoget="allowrpmupdate";
	}
	if($valuetoget eq "allow-configupdate") {
		$valuetoget="allowconfigupdate";
	}
	if($valuetoget eq "allow-syslog") {
		$valuetoget="allowsyslog";
	}
	if($valuetoget eq "group") {
		$valuetoget="aegroup";
	}
	if($valuetoget eq "bundles") {
		$valuetoget="bundles";
	}
	if($valuetoget eq "packages") {
		$valuetoget="packages";
	}
	$sql="select $valuetoget from andutteye_man_specification where hostname='$hostname' order by seqnr desc limit 0,1";
	$sth = $dbh->prepare("$sql");
	$sth->execute;
	@row = $sth->fetchrow_array;

	if(!defined($row[0])) {
		print "\n";
	} else {
		print "$row[0]\n";
	}
}
# End of subfunction
}
sub connect_to_database {
#
#
#
require DBI;
$dbh = DBI->connect("dbi:$ae_databasetype:$ae_databasesid", $ae_databaseusr, $ae_databasepwd) 
	or die("Failed to connect to database:$ae_databasesid err:$!");

# End of subfunction
}
sub appendlog {
#
#
#
my $ecode=system("$managementdir/bin/aemanagement-appendlog.pl -hostname=$command");

if ($ecode != 0 ) {
	print "** ERROR failed to append log to todays client log for hostname:$command, exitcode:$ecode\n";
	notify_andutteye_surveillance("Andutteyemanagement API error",
	"ERROR failed to append log to todays client log for hostname:$command, exitcode:$ecode",
        "WARNING");
	exit $ecode;
}

# End of subfunction
}
sub sendnotificationemail {
#
#
#
my $ecode=system("$managementdir/bin/aemanagement-email.pl -hostname=$command");

if ($ecode != 0 ) {
	print "** ERROR failed to execute send notificationemail for hostname:$command, exitcode:$ecode\n";
	notify_andutteye_surveillance("Andutteyemanagement API error",
	"ERROR failed to execute send notificationemail for hostname:$command, exitcode:$ecode",
        "WARNING");
	exit $ecode;
}

# End of subfunction
}
sub gather_config {
#
#
#
my $ecode=system("$managementdir/bin/aemanagement-gatherconfig.pl -hostname=$command -debug=2");

if ($ecode != 0 ) {
	print "** ERROR failed to execute gatherconfig for hostname:$command, exitcode:$ecode\n";
	notify_andutteye_surveillance("Andutteyemanagement API error",
	"ERROR failed to execute gatherconfig for hostname:$command, exitcode:$ecode",
	"WARNING");
	exit $ecode;
}

# End of subfunction
}
sub gatherpackages {
#
#
#
my $ecode=system("$managementdir/bin/aemanagement-rpmbuilder.pl -hostname=$command -md5sum=$md5sum");

if ($ecode != 0 ) {
	print "** ERROR failed to execute gatherrpmpackages for hostname:$command, exitcode:$ecode\n";
	notify_andutteye_surveillance("Andutteyemanagement API error",
	"ERROR failed to execute gatherrpmpackages for hostname:$command, exitcode:$ecode",
	"WARNING");
	exit $ecode;
}

# End of subfunction
}
sub genrpmindex {
#
#
#
my $ecode=system("$managementdir/bin/aemanagement-genindex.pl -directory=$command");

if ($ecode != 0 ) {
	print "** ERROR failed to generate index for directory$command, exitcode:$ecode\n";
	notify_andutteye_surveillance("Andutteyemanagement API error",
	"ERROR failed to generate index for directory$command, exitcode:$ecode",
	"WARNING");
	exit $ecode;
}

# End of subfunction
}
sub genbaselist {
#
#
#
my $ecode=system("$managementdir/bin/aemanagement-genindex.pl -generatebaselist");

if ($ecode != 0 ) {
	print "** ERROR failed to generate baselist based on current system exitcode:$ecode\n";
	notify_andutteye_surveillance("Andutteyemanagement API error",
	"ERROR failed to generate baselist based on current running system, exitcode:$ecode",
	"WARNING");
	exit $ecode;
}

# End of subfunction
}
sub get_patchlevel_status {
#
#
#
my @input=split(",", $_[0]);

if(!defined($input[0])) {
	$input[0]="0";
}
if(!defined($input[1])) {
	$input[1]="0";
}
if($ae_databasemode eq "yes") {
        connect_to_database();
        $sql="select status from andutteye_man_patchlevel where distribution = '$input[0]' and patchlevel = '$input[1]'";
        $sth = $dbh->prepare("$sql");
        $sth->execute;
        @row = $sth->fetchrow_array;

	if(!defined($row[0])) {
		print "\n";
	} else {
		print "$row[0]\n";	
	}
} else {
	if(-f "$managementdir/packages/$input[0]/$input[1]/LOCK") {
		print "locked\n";
	} else {
		print "open\n";
	}
}

# End of subfunction
}
sub get_file_settings {
#
#
#
my $prestep;
my $poststep;
my $perms;
my $filetofind=basename("$command");
my $dirtofind=dirname("$command");

if($ae_databasemode eq "yes") {
	connect_to_database();
	$sql="select prestep,poststep,perms from andutteye_man_files where filename='$filetofind' and directory = '.$dirtofind' order by seqnr desc limit 0,1";
	$sth = $dbh->prepare("$sql");
	$sth->execute;
	@row = $sth->fetchrow_array;

	if(!defined($row[0])) {
		$prestep="";
	} else {
		$prestep="$row[0]";
	}
	if(!defined($row[1])) {
		$poststep="";
	} else {
		$poststep="$row[1]";
	}
	if(!defined($row[2])) {
		$perms="";
	} else {
		$perms="$row[2]";
	}
	print "$command:$prestep:$perms:$poststep\n";
}else {
open ("FILESETTINGS","<$managementdir/config/aemanagement-filesettings.conf")
	or die "Failed to open filesettingsfile:$managementdir/config/aemangement-filesettings.conf, error:$!\n";
for(<FILESETTINGS>) {
	chomp;
	if(/^#/) {
		next;
	}
	if(/^$command/) {
		print "$_\n";
		last;
	}
}
# End if databasemod
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
my $sndhost;

if($aesurveillance_activate ne "YES" ) {
	print "** Andutteye Surveillance, its not activated. Parameter is set to:$aesurveillance_activate will not send alarm\n";
	return 0;
}
if (defined($_[0])) {
	$smsg=$_[0];
} else {
	print "** ERROR Shortinformation message isnt set\n";
	exit 1;
}
if (defined($_[1])) {
	$lmsg=$_[1];
} else {
	print "** ERROR Longinformation message isnt set\n";
	exit 1;
}
if (defined($_[2])) {
	$severity=$_[2];
} else {
	print "** ERROR Severity isnt set\n";
	exit 1;
}
if (defined($_[3])) {
	$sndhost=$_[3];
} else {
	$sndhost="Nohost-set";
}
if (!defined($aesurveillance_server)) {
	print "** ERROR Cant notify Andutteye Surveillance, missing Andutteye server\n";
	exit 1;
}
if (!defined($aesurveillance_port)) {
	print "** ERROR Cant notify Andutteye Surveillance, missing Andutteye port\n";
	exit 1;
}
if (!defined($aesurveillance_program)) {
	print "** ERROR Cant notify Andutteye Surveillance, missing Andutteye program path\n";
	exit 1;
}
if ( ! -f $aesurveillance_program ) {
	print "** ERROR [api] Couldnt locate Andutteye Surveillance post utility:$aesurveillance_program\n";
	exit 1;
}
if ( ! -x $aesurveillance_program ) {
	print "** ERROR Andutteye post utility isnt executeble\n";
	exit 1;
}
print "-- Trying to send alarm to AndutteyeSurveillance server:$aesurveillance_server, port:$aesurveillance_port\n";
my $ecode=system("$aesurveillance_program -send $aesurveillance_server $aesurveillance_port \"$smsg\" \"Sendinghost:$sndhost,$lmsg\" $severity > /dev/null 2>&1");

if ($ecode == 0 ) {
	print "-- Alarm posted to Andutteye Surveillance\n";
} else {
	print "** ERROR Failed to post Andutteye Surveillance alarm\n";
	exit 1;
}

# End of subfunction
}
sub program_info {
#
#
#
print "\n";
print "$version\n";
print "\n";
print "$0 -getfilesettings=spiderman\n";
print "\n";
print "-gatherconfig\t\t\t:Retrive andutteyemanagement files for monitored node.\n";
print "-gatherpackages\t\t\t:Retrive andutteyemanagement packages for monitored node.\n";
print "-genrpmindex\t\t\t:Generates rpmindex files for the specific directory\n";
print "-genbaselist\t\t\t:Generates base packagelist based on current running system\n";
print "-getfilesettings\t\t:Retrive andutteyemanagement settings for a monitored file.\n";
print "-getdistribution\t\t:Retrive defined distribution for node.\n";
print "-getpatchlevel\t\t\t:Retrive defined patchlevel for node.\n";
print "-getpatchlevelstatus\t\t:Retrive defined patchlevelstatus for specific patchlevel (DB mode only).\n";
print "-getdescription\t\t\t:Retrive defined nodedescription.\n";
print "-getgroup\t\t\t:Retrive defined group for node.\n";
print "-getarchtype\t\t\t:Retrive defined architechture type for the node.\n";
print "-getlocation\t\t\t:Retrive defined location for node.\n";
print "-getstatus\t\t\t:Retrive defined status for node.\n";
print "-getpackages\t\t\t:Retrive defined packages for node.\n";
print "-getexlude\t\t\t:Retrive defined packages for excludation for node.\n";
print "-getpackagetype\t\t\t:Retrive defined packagetype for node.\n";
print "-getbundles\t\t\t:Retrive defined bundlelist for node.\n";
print "-getallowrpmupdate\t\t:Retrive defined packageupdate permission for node.\n";
print "-getallowconfigupdate\t\t:Retrive defined configupdate permission for node.\n";
print "-getallosyslog\t\t\t:Retrive defined tellsyslog value for node.\n";
print "-getemail\t\t\t:Retrive emailreciptients for node.\n";
print "-getcurrentpackages\t\t:Show current packages for node.\n";
print "-getcurrentpackagechecksum\t:Show current md5sum for node packagelist.\n";
print "-appendlog\t\t\t:Append current uploaded log to todays clientlog.\n";
print "-sendnotificationemail\t\t:Send notfification email to reciptients on current log.\n";
print "-reporttoaesurveillance\t\t:Report information and anomolies to Andutteye Surveillance.\n";
print "\n";
}
