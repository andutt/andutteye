#!/usr/bin/perl -w
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
# Description:Andutteye management database api
#
# $Id: aemanagement-dbapi.pl,v 1.15 2006/10/15 16:48:21 andutt Exp $
#
our $managementdir;
our $managementapi;
our $ae_databasemode;
our $ae_databasetype;
our $ae_databasesid;
our $ae_databaseusr;
our $ae_databasepwd;
our $filedistribution;
our $counter=0;
our $onlycheck=0;
our $debug=0;
our $sql;
our $dbh;
our $sth;
our $command;
our @ARGV;
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
	if(/^-onlycheck/) {
		print "Will only verify, -onlycheck is specified\n";
		$onlycheck=1;
	}
	if(/^-debug/) {
		print "Verbosemode, -debugspecified\n";
		$debug=1;
	}
	if(/^-filedistribution=/) {
		my @tmp=split("=",$_);
		$filedistribution=$tmp[1];
	}
}
for(@ARGV) {
	if(/^-sync-specifications-todb/) {
		sync_specifications_todb();
	}
	if(/^-sync-bundles-todb/) {
		sync_bundles_to_db();
	}
	if(/^-sync-files-todb/) {
		sync_files_to_db();
	}
}
if(!@ARGV) {
	program_info();
}
sub program_info {
#
#
#
print "\n";
print "$0\n";
print "\n";
print "-sync-specifications-todb \t:Syncs specifications to andutteye database\n";
print "-sync-bundles-todb 	\t:Syncs bundles to andutteye database\n";
print "-sync-files-todb 	\t:Syncs filesrepository to andutteye database\n";
print "\n";
print "Option arguments:\n";
print "\n";
print "-debug 		\t\t:Debugmode enabled.\n";
print "-onlycheck 		\t:Only verify dont do anything sharp.\n";
print "-filedistribution=<argument> \t:Only verify dont do anything sharp.\n";
print "\n";

#End of subfunction
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
sub execute_sql {
#
#
#
my $param0=$_[0];
my $param1=$_[1];
my $param2=$_[2];
my $param3=$_[3];
my $param4=$_[4];
my $param5=$_[5];
my $param6=$_[6];
my $param7=$_[7];
my $param8=$_[8];
my $param9=$_[9];
my $param10=$_[10];
my @row;

if($param1 eq "specification") {
	if($debug) {
		print "\t(execute_sql) Checking if specification exists in database\n";
	}
	$sql="select aeversion from $param0 where hostname = '$param4' order by seqnr desc limit 0,1";
	$sth = $dbh->prepare("$sql");
	$sth->execute or die "Failed :$!\n";
	@row = $sth->fetchrow_array;

	my $lastversion;
	if(!defined($row[0])) {
		$lastversion='1';
	} else {
		if($param5 == 0) {
			$lastversion=($row[0] + 1);
		} else {
			$lastversion=$row[0];
		}
	}
	if($debug) {
		print "\t(execute_sql) Lastversion lock:$lastversion\n";
	}
	$sql="select count(*) from $param0 where hostname = '$param4'";
	$sth = $dbh->prepare("$sql");
	$sth->execute or die "Failed :$!\n";
	@row = $sth->fetchrow_array;

	if($debug) {
		print "\t(execute_sql) Rowcount is:$row[0] Onlycheck:$onlycheck\n";
	}
	if($row[0] eq "0") {
		if($debug) {
			print "\t(execute_sql) Specification didnt exist, creating entry\n";
		}
		if($onlycheck == 0) {
			$sql="insert into $param0($param2,hostname,lastupdatedof,lastupdatedte,lastupdatedtme,aeversion)";
 			$sql.="values(\"$param3\",'$param4','$0 by user:$ENV{USER}','$date','$time','$lastversion')";
			$sth = $dbh->prepare("$sql");
			$sth->execute;
		}else{
			if($debug) {
				print "\t(execute_sql) Running checkmode, no sharp action will be performed\n";
			}
		}
	} else {
		if($debug) {
			print "\t(execute_sql) Specification existed, updating table but on a new version\n";
		}
		if($param5 == 0) {
			if($debug) {
				print "\t(execute_sql) Creating initial row with version:$lastversion\n";
			}
			$sql="insert into $param0($param2,hostname,lastupdatedof,lastupdatedte,lastupdatedtme,aeversion)";
 			$sql.="values(\"$param3\",'$param4','$0 by user:$ENV{USER}','$date','$time','$lastversion')";
			$sth = $dbh->prepare("$sql");
			$sth->execute;
		}
		if($debug) {
			print "\t(execute_sql) Updating $param0 set $param2=$param3 where hostname = $param4 and aeversion=$lastversion\n";
		}
		$sql="update $param0 set $param2='$param3' where hostname = '$param4' and aeversion='$lastversion'";
		$sth = $dbh->prepare("$sql");
		$sth->execute;

	}
# End if specification
}
if ($param1 eq "deletebundle") {
	$sql="delete from $param0 where bundle ='$param2'";
	$sth = $dbh->prepare("$sql");
	$sth->execute or die "Failed :$!\n";
	print "(execute_sql) All packages belonging to bundle:$param2 was deleted\n";
}
if ($param1 eq "bundles") {
	$sql="select count(*) from $param0 where bundle ='$param2' and aepackage = '$param3' and aeversion = '$param4' and aerelease = '$param5'";
	$sth = $dbh->prepare("$sql");
	$sth->execute or die "Failed :$!\n";
	@row = $sth->fetchrow_array;

	if($row[0] == 0 ) {
		if($debug) {
			print "(execute_sql) Bundle:$param2 package:$param3 version:$param4 release:$param5 isnt loaded, loading it.\n";
		}
		$sql="insert into $param0(bundle,aepackage,aeversion,aerelease) values(\"$param2\",\"$param3\",\"$param4\",\"$param5\")";
		$sth = $dbh->prepare("$sql");
		$sth->execute or die "Failed :$!\n";
	} else {
		if($debug) {
			print "(execute_sql) Bundle:$param2 package:$param3 version:$param4 release:$param5 is already loaded\n";
		}
	}
# End if bundles
}
if($param1 eq "files") {
	$sql="select count(*) from $param0 where filename = \"$param2\" and directory = \"$param3\"";
	$sql.=" and location = \"$param4\" and group1 = \"$param5\" and group2 = \"$param6\"";
	$sql.=" and group3 = \"$param7\" and group4 = \"$param8\"";
	$sth = $dbh->prepare("$sql");
	$sth->execute or die "Failed :$!\n";
	@row = $sth->fetchrow_array;

	if($row[0] == 0) {
		print "(execute_sql) File:$param3 is not loaded, loading it.\n";
		$sql="insert into $param0(filename,directory,location,group1,group2,group3,group4,aeversion,dte,tme,crtuser,distribution)";
		$sql.=" values(\"$param2\",\"$param3\",\"$param4\",\"$param5\",\"$param6\",\"$param7\",\"$param8\"";
		$sql.=",'1','$date','$time',\"$ENV{USER}\",'$filedistribution')";
		$sth = $dbh->prepare("$sql");
		$sth->execute or die "Failed :$!\n";
	} else {
		if($debug) {
			print "(execute_sql) File:$param2 is already loaded\n";
		}
	}
# End if files.
}
if(!$sth) {
	print"\t(execute_sql) Insert/update of database failed\n";
	exit;
} else {
	if($debug) {
		print "\t(execute_sql) Database (insert/update) completed\n";
	}
}
# End of subfunction
}
sub sync_specifications_todb {
#
# Syncs filespecifications to andutteye database.
#
my (	$distribution, 
	$patchlevel, 
	$group, 
	$where, 
	$status,
	$current,
	$command,
	$currentspec,
	$parametername,
	@parameter_name,
	$value
);
chdir("$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/specifications")
	or die "ERROR Failed to change directory to:$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/specifications Msg:$!\n";

for(<*>) {
	chomp;
	$currentspec=$_;
	if(/^example/) {
		next;
	}
	print "Hostspecification->$_ verifying/comitting parameters\n";
	open("spec","<$_")
		or die "ERROR Failed to open:$_ for reading Msg:$!\n";
		for(<spec>) {
			chomp;
			if($_ =~/^packagetype/) {
				$command="-getpackagetype";
			}
			if($_ =~/^distribution/) {
				$command="-getdistribution";
			}
			if($_ =~/^patchlevel/){
				$command="-getpatchlevel";
			}
			if($_ =~/^group/){
				$command="-getgroup";
			}
			if($_ =~/^location/) {
				$command="-getlocation";
			}
			if($_ =~/^archtype/) {
				$command="-getarchtype";
			}
			if($_ =~/^description/) {
				$command="-getdescription";
			}
			if($_ =~/^status/) {
				$command="-getstatus";
			}
			if($_ =~/^packages/) {
				$command="-getpackages";
			}
			if($_ =~/^exclude/) {
				$command="-getexclude";
			}
			if($_ =~/^bundles/) {
				$command="-getbundles";
			}
			if($_ =~/^allow-rpmupdate/) {
				$command="-getallowrpmupdate";
			}
			if($_ =~/^allow-configupdate/) {
				$command="-getallowconfigupdate";
			}
			if($_ =~/^allow-syslog/) {
				$command="-getallowsyslog";
			}
			if($_ =~/^email/) {
				$command="-getemail";
			}
			@parameter_name=split(":",$_);
			$parametername=$parameter_name[0];
			$current=`$managementapi $command=$currentspec`;
			$value=$parameter_name[1];
			chomp $current;

			if($_ =~/^allow-rpmupdate/) {
                                $parametername="allowrpmupdate";
                        }
                        if($_ =~/^allow-configupdate/) {
                                $parametername="allowconfigupdate";
                        }
                        if($_ =~/^allow-syslog/) {
                                $parametername="allowsyslog";
                        }
                        if($_ =~/^group/) {
                                $parametername="aegroup";
                        }
			my $showvalue;
			if(!defined($value)) {
				$showvalue="Nothing defined";
			} else {
				$showvalue=$value;
			}
			if(!defined($current)) {
				$current="Undefined";
			}
			if($debug) {
				print "\t(sync_specifications_todb) Parametername    :$parametername\n";
				print "\t(sync_specifications_todb) Fileparameter    :$showvalue\n";
				print "\t(sync_specifications_todb) Databaseparameter:$current\n";
				print "\t(sync_specifications_todb) Status           :";
			}
			if("$current" eq "$value") {
				if($debug) {
					print "(sync_specifications_todb) [OK]\n";
					print "\n";
				}
			} else {
				if($debug) {
					print "(sync_specifications_todb) [DIFFER] Updating database with new parameter\n";
				}
				connect_to_database();
				execute_sql("andutteye_man_specification","specification","$parametername","$value","$currentspec","$counter");
				$counter++;
			}
		}
	print "Hostspecification->$currentspec $counter st parameters changed\n";
	$counter=0;
}
# End of subfunction
}
sub sync_bundles_to_db {
#
# Syncs bundles to andutteye database.
#
my $counter=0;
my (	$bundle, 
	$package, 
	$version, 
	$release, 
	@bundle
);
chdir("$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/bundles")
	or die "ERROR Failed to change directory to:$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/bundles Msg:$!\n";
for(<*>) {
	chomp;
	$bundle=$_;
	open("bundle","<$_")
		or die "ERROR Failed to open:$_ for reading Msg:$!\n";
		connect_to_database();
		execute_sql("andutteye_man_bundles","deletebundle","$bundle");
		for(<bundle>) {
			chomp;
			@bundle=split(" ",$_);
			($package, $version, $release) = @bundle;
			if($debug) {
				print "(sync_bundles_to_db) Package:$package version:$version release:$release\n";
			}
			connect_to_database();
			execute_sql("andutteye_man_bundles","bundles","$bundle","$package","$version","$release");
		$counter++;
		}
	print "(sync_bundles_to_db) Bundle:$bundle,$counter packages verified\n";
	$counter=0;
	close("bundle");
}

# End of subfunction
}
sub sync_files_to_db {
#
# Syncs filerepository to andutteye database
#
my $counter=0;
my (	$filename, 
	$location, 
	@tmp,
	$group1, 
	$group2,
	$group3,
	$group4,
	$directory 
);
if(!defined($filedistribution)) {
	print "ERROR Distribution must be set to be able to locate correct filerepository\n";
	exit;
}
chdir("$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/files/$filedistribution")
	or die "ERROR Failed to change directory to:$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/files/$filedistribution Msg:$!\n";

print "(sync_files_to_db) Direct location will be tagged as:$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/files/$filedistribution\n";
my @directorys=`find . -type d`;
for(@directorys) {
	chomp;
	$directory=$_;
	if("$_" eq ".") {
		next;	
	}
	chdir("$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/files/$filedistribution")
		or die "ERROR Failed to change directory to:$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/files/$filedistribution Msg:$!\n";
	if($debug) {
		print "(sync_files_to_db) Found directory:$directory\n";
	}
	chdir("$directory")
		or die "ERROR Failed to change directory to:$directory Msg:$!\n";
		for(<*>) {
			chomp;
			if( -d $_) {
				next;
			}
			my @tmp=split("--",$_);
			if(defined($tmp[0])) {
				$filename=$tmp[0];
			} else {
				print "ERROR Didnt get any filename hit after split on:$_\n";
				exit;
			}	
			if(defined($tmp[1])) {
				$group1=$tmp[1];
			} else {
				print "ERROR Didnt get any group definition, a file must contain at least one filetagging:$_\n";
				exit;
			}
			if(defined($tmp[2])) {
				$group2=$tmp[2];
			} else {
				$group2="";
			}
			if(defined($tmp[3])) {
				$group3=$tmp[3];
			} else {
				$group3="";
			}
			if(defined($tmp[4])) {
				$group4=$tmp[4];
			} else {
				$group4="";
			}
		if($debug > 2) {
			print "(sync_files_to_db) Found file:$filename group1:$group1 group2:$group2 group3:$group3 group4:$group4\n";
		} else {
			print "(sync_files_to_db) Found file:$filename\n";
		}
		connect_to_database();
		execute_sql("andutteye_man_files","files","$filename","$directory","$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/files/$filedistribution",
			    "$group1","$group2","$group3","$group4");
	}
}

# End of subfunction
}
