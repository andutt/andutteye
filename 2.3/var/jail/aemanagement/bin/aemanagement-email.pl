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
# Description:Andutteye management email module, a part of andutteye management.
#
# $Id: aemanagement-email.pl,v 1.13 2006/10/15 16:48:21 andutt Exp $
#
our $managementdir;
our $emailprovider;
our $managementapi;
our $hostname;
our @reciptients;
our $reciptients;
our $body;
our ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
our $date=sprintf("20%02d%02d%02d",$year%100,$mon+1,$mday);
our $time=sprintf("%02d:%02d:%02d",$hour,$min,$sec);

use strict;
use warnings;
use Net::SMTP;

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
$reciptients=`$managementapi -getemail=$hostname`;
chomp $reciptients;
@reciptients=split(",",$reciptients);

if ( ! -f "$managementdir/log-client/$hostname-$date.log" ) {
	print "-- Didnt find any logfile for $hostname to send\n";
} else {

for(@reciptients) { 
    open("MAILFILE","<$managementdir/log-client/$hostname-$date.log") 
    or die "Failed to open mailfile:$managementdir/log-client/$hostname-$date.log for reading error:$!\n";
	my @body=<MAILFILE>;
        $body=join(" ",@body);
   	close(MAILFILE);
	my $smtp = Net::SMTP->new($emailprovider);

        if(!$smtp) {
	 	print "** ERROR Couldnt connect and interact with smtpserver:$emailprovider\n";
	 } else {
			print "-- Sending email to administrator:$_\n";
			$smtp->mail( "$_" ); 
			$smtp->to( "$_" );
			$smtp->data();
			$smtp->datasend("To: $_\n");
			$smtp->datasend("From: AndutteyeManagement\@$hostname\n");
	        	$smtp->datasend("Subject: AndutteyeManagement found differences on $hostname\n");
			$smtp->datasend("\n");
	        	$smtp->datasend("$body");
	        	$smtp->dataend();
	        	$smtp->quit;
	}
}
}
