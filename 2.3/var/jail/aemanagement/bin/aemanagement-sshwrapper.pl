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
# Description:Andutteye management SSH-Server wrapper module. A part of andutteye management.
#
# $Id: aemanagement-sshwrapper.pl,v 1.13 2006/10/15 16:48:21 andutt Exp $
#
#
# The version parameter tells current version of the program
our $version="Andutteye Software Suite Management ssh validator. Version:1.0 Fixlevel:1.4 Latestfix:2005-02-13 (andutt)";
our $managementdir;
our $managementapi;
our $ae_databasemode;
our $ae_databasetype;
our $ae_databasesid;
our $ae_databaseusr;
our $ae_databasepwd;
our $sql;
our $dbh;
our $sth;
our @ARGV;
our $SSH_ORIGINAL_COMMAND=$ENV{SSH_ORIGINAL_COMMAND};
our $command;
our ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
our $date=sprintf("20%02d%02d%02d",$year%100,$mon+1,$mday);
our $time=sprintf("%02d:%02d:%02d",$hour,$min,$sec);

use strict;
use warnings;

if(!defined($ENV{ANDUTTEYEMANAGEMENT_REPOSITORY})) {
      print "** ERROR Andutteye management repository location parameter isnt set. Check documentation for more info.\n";
      exit 1;
} else {
      require("$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/config/aemanagement-config.conf");
}
open("SERVERLOG",">>$managementdir/log-server/aemanagement-sshwrapper.log")
	or die "** ERROR Failed to open ssh-wrapper log for writing error:$!\n";
# Splitting incoming ssh request.
my @SSHSPLIT=split(" ",$SSH_ORIGINAL_COMMAND);

if($SSH_ORIGINAL_COMMAND =~/^gatherconfig\ $SSHSPLIT[1]$/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=gatherconfig] $managementapi -gatherconfig=$tmparray[1]\n";
	system("$managementapi -gatherconfig=$tmparray[1]");
	
	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","gatherconfig","$managementapi -gatherconfig=$tmparray[1]");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^tellsyslog\ $SSHSPLIT[1]$/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=syslogconfig] $managementapi -getallowsyslog=$tmparray[1]\n";
	system("$managementapi -getallowsyslog=$tmparray[1]");
	
	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","syslogconfig","$managementapi -getallowsyslog=$tmparray[1]");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^gatherpackages\ $SSHSPLIT[1]/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=gatherpackages] $managementapi -gatherpackages=$tmparray[1] -md5sum=$tmparray[2] $tmparray[3]\n";
	system("$managementapi -gatherpackages=$tmparray[1] -md5sum=$tmparray[2] $tmparray[3]");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","gatherpackages","$managementapi -gatherpackages=$tmparray[1] -md5sum=$tmparray[2] $tmparray[3]");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^packagepermission\ $SSHSPLIT[1]$/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=packagepermission] $managementapi -getallowrpmupdate=$tmparray[1]\n";
	system("$managementapi -getallowrpmupdate=$tmparray[1]");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","packagepermission","$managementapi -getallowrpmupdate=$tmparray[1]");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^sendemail\ $SSHSPLIT[1]$/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=sendemail] $managementapi -sendnotificationemail=$tmparray[1]\n";
	system("$managementapi -sendnotificationemail=$tmparray[1]");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","sendemail","$managementapi -sendnotificationemail=$tmparray[1]");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^configpermission\ $SSHSPLIT[1]$/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=configpermission] $managementapi -getallowconfigupdate=$tmparray[1]\n";
	system("$managementapi -getallowconfigupdate=$tmparray[1]");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","configpermission","$managementapi -getallowconfigupdate=$tmparray[1]");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^reporttoae/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=AndutteyeSurveillane] $managementapi -reporttoaesurveillance=$tmparray[1],$tmparray[2]\n";
	system("$managementapi -reporttoaesurveillance=$tmparray[1],\"$tmparray[2]\"");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","AndutteyeSurveillane","$managementapi -reporttoaesurveillance=$tmparray[1],$tmparray[2]");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^checkstatus\ $SSHSPLIT[1]$/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=checkstatus] $managementapi -getstatus=$tmparray[1]\n";
	system("$managementapi -getstatus=$tmparray[1]");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","checkstatus","$managementapi -getstatus=$tmparray[1]");
		update_heartbeat("$tmparray[1]");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^appendlog/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=appendclientlog] $managementapi -appendlog=$tmparray[1]\n";
	system("$managementapi -appendlog=$tmparray[1]");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","appendlog","$managementapi -appendlog=$tmparray[1]");
		update_heartbeat("$tmparray[1]");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^scp -f out\//) {
	$command=$SSH_ORIGINAL_COMMAND;
	print SERVERLOG "[$date,$time type=scp -f command] $SSH_ORIGINAL_COMMAND\n";
	system("$command");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("Undefined","$date","$time","scp -f command","$SSH_ORIGINAL_COMMAND");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^scp -t/) {
	$command=$SSH_ORIGINAL_COMMAND;
	print SERVERLOG "[$date,$time type=scp -t command] $SSH_ORIGINAL_COMMAND\n";
	system("$command");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("Undefined","$date","$time","scp -t command","$SSH_ORIGINAL_COMMAND");
	}
}
elsif($SSH_ORIGINAL_COMMAND =~/^getfilesettings/) {
	my  @tmparray=split(" ",$SSH_ORIGINAL_COMMAND);
	print SERVERLOG "[$date,$time,$tmparray[1] type=getfilesettings] $managementapi -getfilesettings=$tmparray[1]\n";
	system("$managementapi -getfilesettings=$tmparray[1]");

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("$tmparray[1]","$date","$time","getfilesettings","$managementapi -getfilesettings=$tmparray[1]");
	}
} else {
	print SERVERLOG "[$date,$time type=FORBIDDEN COMMAND] $SSH_ORIGINAL_COMMAND\n";
	print SERVERLOG "[$date,$time type=AndutteyeSurveillance] $managementapi -reporttoaesurveillance=Andutteyemanagement-SSHWRAPPER-error,A-forbidden-access-command-has-been-attempted-to-andutteyemanagement-ssh-wrapper.See-serverlog-for-more-information,FATAL,test\n";
	system("$managementapi -reporttoaesurveillance=\"Andutteyemanagement SSHWRAPPER error\",\"A forbidden access command has been attempted to andutteyemanagement ssh wrapper.See serverlog for more information\",FATAL,See-logfile");
	print "** FORBIDDEN COMMAND, THIS IS A MONITORED SERVICE AND SYSTEM. GO AWAY!!\n";

	if($ae_databasemode eq "yes") {
		connect_to_database();
		execute_sql("Undefined","$date","$time","FORBIDDEN COMMAND","$SSH_ORIGINAL_COMMAND");
	}
	exit 1;
}
sub connect_to_database {
#
# Establishing connection to database.
#
require DBI;
$dbh = DBI->connect("dbi:$ae_databasetype:$ae_databasesid", $ae_databaseusr, $ae_databasepwd)
        or die("Failed to connect to database:$ae_databasesid err:$!");

# End of subfunction
}
sub update_heartbeat {
#
# Updating heartbeat tags
#
my @row;
my $parameter1=$_[0];

$sql="select count(seqnr) from andutteye_man_heartbeat where hostname = \"$parameter1\"";
$sth = $dbh->prepare("$sql");
$sth->execute or die "Failed :$!\n";
@row = $sth->fetchrow_array;

if($row[0] == 0) {
        $sql="insert into andutteye_man_heartbeat(hostname,dte,tme,status,type) values(\"$parameter1\",\"$date\",\"$time\",\"CHECKEDIN\",\"Linux\")";
        $sth = $dbh->prepare("$sql");
        $sth->execute or die "Failed :$!\n";
} else {
        $sql="update andutteye_man_heartbeat set dte='$date',tme='$time',type='Linux' where hostname = '$parameter1'";
        $sth = $dbh->prepare("$sql");
        $sth->execute or die "Failed :$!\n";
}
# End of subfunction
}
sub execute_sql {
#
# Logging to database
#
my @row;
my $parameter1=$_[0];
my $parameter2=$_[1];
my $parameter3=$_[2];
my $parameter4=$_[3];
my $parameter5=$_[4];

$sql="insert into andutteye_man_serverlog(hostname,dte,tme,aetype,log) values(\"$parameter1\"";
$sql.=",\"$parameter2\",\"$parameter3\",\"$parameter4\",\"$parameter5\")";
$sth = $dbh->prepare("$sql");
$sth->execute or die "Failed :$!\n";

# End of subfunction
}
