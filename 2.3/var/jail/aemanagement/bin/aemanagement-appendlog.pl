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
# Description:Andutteye management log appender, a part of andutteye management.
#
# License:See LICENSE file in included software or on the official website at www.andutteye.com
#
# $Id: aemanagement-appendlog.pl,v 1.5 2006/10/15 16:48:21 andutt Exp $
#
our $ae_databasemode;
our $ae_databasetype;
our $ae_databasesid;
our $ae_databaseusr;
our $ae_databasepwd;
our $sql;
our $dbh;
our $sth;
our $body;
our $managementdir;
our $managementapi;
our $hostname;
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
     if (/^-hostname/) {
            my @tmpvalue=split("=", $_);
            $hostname=$tmpvalue[1];
     }
}
if (!defined($hostname)) {
        print "** ERROR didnt recive any hostname value, cant work on nothing\n";
        exit 1;
}
if ( ! -f "$managementdir/log-client/$hostname-$date.log.upload" ) {
	print "** Didnt find any uploaded logfile for $hostname, skipping.\n";
} else {
	if("$ae_databasemode" eq "yes") {
		print "** Running in databasemode, loading logfile in database\n";
		connect_to_database();

        	open("uploadlog","<$managementdir/log-client/$hostname-$date.log.upload")
        		or die "Failed to open logfile:$managementdir/log-client/$hostname-$date.log.upload for reading error:$!\n";
        	my @body=<uploadlog>;
        	$body=join(" ",@body);

        	execute_sql("$hostname","$date","$time","$body");
        	close("uploadlog");
		print "** Logfile loaded.\n";
	}	
	open("CURRENTLOG",">>$managementdir/log-client/$hostname-$date.log")
		or die "** ERROR Failed to open:$managementdir/log-client/$hostname-$date.log errormsg:$!\n";
	open("UPLOADLOG","<$managementdir/log-client/$hostname-$date.log.upload")
		or die "** ERROR Failed to open:$managementdir/log-client/$hostname-$date.log.upload errormsg:$!\n";
	for(<UPLOADLOG>) {
		chomp;
		print CURRENTLOG "[Clientlog,$date,$time] $_\n";
	}
	close("CURRENTLOG");
	close("UPLOADLOG");
	unlink("$managementdir/log-client/$hostname-$date.log.upload")
		or die "** ERROR Failed to remove temporary upload log:$managementdir/log-client/$hostname-$date.log.upload errormsg:$!\n";	
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
sub execute_sql {
#
# Logging to database
#
my $parameter1=$_[0];
my $parameter2=$_[1];
my $parameter3=$_[2];
my $parameter4=$_[3];

$sql="insert into andutteye_man_clientlog(hostname,dte,tme,log) values(\"$parameter1\"";
$sql.=",\"$parameter2\",\"$parameter3\",\"$parameter4\")";
$sth = $dbh->prepare("$sql");
$sth->execute or die "Failed :$!\n";

# End of subfunction
}
