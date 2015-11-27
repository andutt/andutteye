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
# Description:Andutteye management index filegenerator module. A part of andutteye management.
#
# $Id: aemanagement-genindex.pl,v 1.10 2006/10/15 16:48:21 andutt Exp $
#
# The version parameter tells current version of the program.
our $version="Andutteye Software Suite Index filegenerator Version:1.3 Fixlevel:1.0 Lastfix:2004-12-06 (andutt)";
our @ARGV;
our $directory;
our $baselist=0;
our $count;
our $dbh;
our $sth;
our $ae_databasemode;
our $ae_databasetype;
our $ae_databasesid;
our $ae_databaseusr;
our $ae_databasepwd;
our $distribution;
our $patchlevel;
our $sql;
our @row;
our $force=0;

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

for(@ARGV) {
	if(/-directory/) {
		my @tmparray=split("=",$_);
		$directory=$tmparray[1];
	}
	if(/-generatebaselist/) {
		$baselist=1;
	}
	if(/-distribution/) {
		my @tmparray=split("=",$_);
		$distribution=$tmparray[1];
	}
	if(/-patchlevel/) {
		my @tmparray=split("=",$_);
		$patchlevel=$tmparray[1];
	}
	if(/-forcerun/) {
		print "** FORCING RUN, UPDATING PATCHLEVEL EVEN THOUGH STATUS IS LOCKED\n";
		$force=1;
	}
}
if(!$directory) {
	if(!$baselist) {
		program_info();
	}
}
if($ae_databasemode eq "yes") {
	if(!defined($distribution)) {
		print "ERROR You must specify which distribution you are loading packageinformation for when databasemode is enabled\n";
		exit;
	}
	if(!defined($patchlevel)) {
		print "ERROR You must specify which patchlevel you are loading packageinformation for when databasemode is enabled\n";
		exit;
	}
}
sub connect_to_database {
#
# This subfunction connects to the database
#
require DBI;
$dbh = DBI->connect("dbi:$ae_databasetype:$ae_databasesid", $ae_databaseusr, $ae_databasepwd)
        or die("Failed to connect to database:$ae_databasesid err:$!");

# End of subfunction
}
sub load_to_database {
#
# Load indexinformation to database
#
my @string=split(" ",$_[0]);

if(!defined($string[0])) {
	print "ERROR No packagename passed on the database subfunction\n";
	exit;
}
if(!defined($string[1])) {
	print "ERROR No packageversion passed on the database subfunction\n";
	exit;
}
if(!defined($string[2])) {
	print "ERROR No packagerelease passed on the database subfunction\n";
	exit;
}

$sql="select count(seqnr) from andutteye_man_packages where aepackage = \"$string[0]\"";
$sql.="and aeversion = \"$string[1]\" and aerelease = \"$string[2]\" and patchlevel = '$patchlevel'";
$sql.="and distribution = '$distribution'";
$sth = $dbh->prepare("$sql");
$sth->execute;
@row = $sth->fetchrow_array;

if($row[0] == 0) {
	$sql="insert into andutteye_man_packages(aepackage,aeversion,aerelease,patchlevel,distribution,location,status";
	$sql.=",packagetype) values('$string[0]','$string[1]','$string[2]','$patchlevel','$distribution'";
	$sql.=",'packages/$distribution/$patchlevel','active','RPM')";
	$sth = $dbh->prepare("$sql");
	$sth->execute;
}
# End of subfunction
}
sub check_patchlevel_status {
#
# Checkin and setting patchlevel status
#
$sql="select count(seqnr) from andutteye_man_patchlevel where patchlevel = '$patchlevel' and distribution = '$distribution'";
$sth = $dbh->prepare("$sql");
$sth->execute;
@row = $sth->fetchrow_array;

if($row[0] == 0) {
	print "** Creating initial patchlevel information for patchlevel $patchlevel in database\n";
        $sql="insert into andutteye_man_patchlevel(patchlevel,status,dte,tme,changer,log,distribution)";
	$sql.=" values('$patchlevel','active','$date','$time','$0','Initial patchlevel creation','$distribution')";
        $sth = $dbh->prepare("$sql");
	$sth->execute;
} else {
	$sql="select status,dte,tme,changer from andutteye_man_patchlevel where patchlevel = '$patchlevel' and distribution = '$distribution'";
	$sth = $dbh->prepare("$sql");
	$sth->execute;
	@row = $sth->fetchrow_array;

	if("$row[0]" ne "open") {
		if(!$force) {
			print "** Patchlevel $patchlevel is locked by $row[3] on $row[1],$row[2]. Try again later or use -forcerun.\n";
			exit;
		} else {
			print "** Deleting patchlevel information for patchlevel $patchlevel\n";
			$sql="delete from andutteye_man_packages where patchlevel = '$patchlevel' and distribution = '$distribution'";
			$sth = $dbh->prepare("$sql");
 			$sth->execute;
		}
	} else {
		print "** Locking patchlevel $patchlevel until we are complete with our operation\n";
        	$sql="update andutteye_man_patchlevel set status='locked'";
		$sql.=",dte='$date',tme='$time',changer='$0',log='Updating patchlevel' where patchlevel = '$patchlevel' and distribution ='$distribution'";
        	$sth = $dbh->prepare("$sql");
		$sth->execute;
		
		print "** Deleting patchlevel information for patchlevel $patchlevel\n";
		$sql="delete from andutteye_man_packages where patchlevel = '$patchlevel' and distribution = '$distribution'";
		$sth = $dbh->prepare("$sql");
 		$sth->execute;
	}
}
# End of subfunction
}
sub unlock_patchlevel {
#
# Unlocking patchlevel
#
print "** Unlocking patchlevel $patchlevel after successfull operation\n";
$sql="update andutteye_man_patchlevel set status='open'";
$sql.=",dte='$date',tme='$time',changer='$0' where patchlevel = '$patchlevel' and distribution = '$distribution'";
$sth = $dbh->prepare("$sql");
$sth->execute;

# End of subfunction
}

sub program_info {
#
# This subfunction is showed on invalid specification, or none specified at all.
#
print "\n$version\n";
print "\n";
print "$0 -directory=/repository/packages/redhat-es3/2\n";
print "$0 -generatebaselist\n";
print "\n";
print "-directory=<directory> \t:Specify the directory to to generate a index file on.\n";
print "-generatebaselist \t:Will generate a baselist based on current running system.\n";
print "\n";
print "If Andutteyemanagement have databasemode enabled this arguments must be passed to the program\n";
print "\n";
print "-distribution \t:Specify for which distribution you are loading packageinformation\n";
print "-patchlevel \t:Specify for which patchlevel you are loading packageinformation\n";
print "-forcerun \t:If for some reason the patchlevel are locked and you are certain that no one else\n";
print "          \t are working with it -forcerun can be specified to enforce program operation\n";
print "\n";
exit 1;
# End of subfunction
}
sub generate_index {
#
# This subfunction jumps to the specified directory and begin to create index.
#
chdir("$directory")
	or die "Failed to switch directory to:$directory error:$!\n";
open("INDEX",">$directory/rpmindex")
	or die "Failed to open rpmindex file:$directory/rpmindex error:$!\n";

my @rpmpackages=`find . -name "*.rpm" -type f | cut -c3-900`;
my $information;
my $count=0;

if($ae_databasemode eq "yes") {
	print "** Datbasemode enabled, distribution:$distribution, patchlevel:$patchlevel\n";
	connect_to_database();
	check_patchlevel_status();
}
for(@rpmpackages) {
	chomp;
	print "-- [$count] [FILE] Generating aemanagement information for package:$_\n";
	$information=`rpm -qp --qf %-30{NAME}\\\t%{VERSION}\\\t%{RELEASE} $_`;
	print INDEX "$information $_\n";

	if($ae_databasemode eq "yes") {
		print "-- [$count] [DBAS] Generating aemanagement information for package:$_\n";
		connect_to_database();
		load_to_database("$information","$distribution");
	}
	$count++;
	
}
print "-- Generating phase completed:$count st package(s) loaded.\n";
close("INDEX");
if($ae_databasemode eq "yes") {
	unlock_patchlevel();
}

# End of subfunction
}
sub generate_baselist {
#
# Generate a baselist based on current running system.
#
my @rpms=`rpm -qa --qf %-1{NAME}\\\\n`;

for(@rpms) {
        chomp;
	print "$_ 0 0\n";
}

# End of subfunxtion
}
if($baselist) {
	generate_baselist();
} else {
	generate_index();
}
