#!/usr/bin/perl
#
#    Copyright Andreas Utterberg Thundera (c) All rights Reserved 2016
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#
our $version = "Andutteye Realtime Ontrac progresstracking version:1.0 Fixlevel:1.2";
our $specifications="/var/jail/aemanagement/specifications";
our $serverlog="/var/jail/aemanagement/log-server/aemanagement-sshwrapper.log";
our $clientlog="/var/jail/aemanagement/log-client";
our $serverlog_line_start;
our $srv_lines;
our $domain;
our $ontracid;
our $maxloops="180";
our $sleeptime="20";
our @check;
our $Use_database_type;
our $Use_database_name;
our $Use_database_user;
our $Use_database_password;
our $config;
our $sql;
our $dbh;
our $sth;
our $seqnr;
our $ontrac;
our $ontrac_description;
our $ontrac_domain;
our $tracid;

our ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
our $date=sprintf("20%02d-%02d-%02d",$year%100,$mon+1,$mday);
our $time=sprintf("%02d:%02d:%02d",$hour,$min,$sec);

use DBI;
use strict;
use warnings;


sub parse_and_load_configuration {
my @tmp;
my $params="0";

      open("conf","<$config")
                or die "ERROR Failed to open configuration file for reading config:$config error:$!\n";
        for(<conf>) {
                chomp;
                if(/^#/) {
                        next;
                }
                if(/^$/) {
                        next;
                }
                if(/^Use_database_type/) {
                        @tmp=split("=","$_");
                        $Use_database_type="$tmp[1]";
                        $params++;
                }
                if(/^Use_database_name/) {
                        @tmp=split("=","$_");
                        $Use_database_name="$tmp[1]";
                        $params++;
                }
                if(/^Use_database_user/) {
                        @tmp=split("=","$_");
                        $Use_database_user="$tmp[1]";
                        $params++;
                }
                if(/^Use_database_password/) {
                        @tmp=split("=","$_");
                        $Use_database_password="$tmp[1]";
                        $params++;
                }
	}
	close("conf")
		or die $!;

# End of subfunction
}
# Parse of commandline arguments.
for(@ARGV) {
	if ( $_ =~/-config/ ) {
        	my @tmp=split("=","$_");
                $config="$tmp[1]";
        }
}
if(!defined($ARGV[0])) {
	program_info();
}
sub get_current_log_line {

open("SRVLOG","<$serverlog")
	or die $!;

for(<SRVLOG>) {
	$serverlog_line_start++;
}

close("SRVLOG")
	or die $!;

$serverlog_line_start++;

print "Serverlog trace mark after line:$serverlog_line_start\n";

# End of subfunction
}
sub loop_and_check_progress {

for (my $i=0; $i <= $maxloops; $i++) {

	print "[$i of $maxloops] Checking ontrac progress.\n";

	open("SRVLOG","<$serverlog")
        or die $!;

	for(<SRVLOG>) {
		chomp;
		my $srvlog_line = $_;
        	$srv_lines++;

		if ("$srv_lines" == "$serverlog_line_start") {
			print "[$i of $maxloops] Reached tracking mark of $srv_lines. \n";
		}
		if ("$srv_lines" >= "$serverlog_line_start") {

			for(@check) {
				if ("$srvlog_line" =~/-getstatus=$_/) {
					print "\t[->]$_ is now asking the Andutteye repository for a status check.\n";
					update_system_status("$_","$ontracid","Is now asking the Andutteye repository for a status check.","1");
				}
				if ("$srvlog_line" =~/-gatherconfig=$_/) {
					print "\t[->]$_ is downloading filecontent from the Andutteye repository.\n";
					update_system_status("$_","$ontracid","Is downloading filecontent from the Andutteye repository.","2");
				}
				if ("$srvlog_line" =~/scp(.*)-f(.*)out\/$_/) {
					print "\t[->]$_ Installation and compliance is now in progress (If any).\n";
					update_system_status("$_","$ontracid","Installation and compliance is now in progress (If any).","3");
				}
				if ("$srvlog_line" =~/-sendnotificationemail=$_/) {
					print "\t[->]$_ Completed. Progresslog uploaded and emailreciptients notified.\n";
					update_system_status("$_","$ontracid","Compliance completed. Progresslog uploaded and emailreciptients notified.","4");
				}
			}
		}
	}	

	close("SRVLOG")
        	or die $!;

$serverlog_line_start=$srv_lines;
$serverlog_line_start++;

print "[$i of $maxloops] Check of ontrac progress completed. New ontrac tracemark on line $serverlog_line_start.\n";
$srv_lines=0;
sleep($sleeptime);
}

print "Tracker off.\n";

# End of subfunction
}
sub update_system_status {

my $system = "$_[0]";
my $ontracid = "$_[1]";
my $status = "$_[2]";
my $aestep = "$_[3]";
my $systemlog;
my $filehandle;
my $filedata="None";

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime(time);
my $datesystemlog=sprintf("20%02d%02d%02d",$year%100,$mon+1,$mday);
$date=sprintf("20%02d-%02d-%02d",$year%100,$mon+1,$mday);
$time=sprintf("%02d:%02d:%02d",$hour,$min,$sec);

$dbh = DBI->connect("dbi:$Use_database_type:$Use_database_name", $Use_database_user, $Use_database_password) 
	or die ("ERROR Couldnt open database:$Use_database_name errormessage:$!");

$sql ="select aestep from andutteye_ontracprogress where system_name = '$system' and ontracid = '$ontracid'";
$sth = $dbh->prepare("$sql");
$sth->execute;
my @row = $sth->fetchrow_array;

if(!defined("$row[0]")) {
	$row[0]="0";
}

if("$row[0]" < "$aestep") {

	if("$aestep" == "4") {

		if ( -f "$clientlog/$system-$datesystemlog.log") {
			print "[$date $time] Loading $system log to database.\n";
			open($filehandle, "$clientlog/$system-$datesystemlog.log")
        			or die $!;
			read($filehandle, $filedata, -s $filehandle);
		}

	}

	$sql="update andutteye_ontracprogress set status = '$status', aestep = '$aestep', created_date = '$date', created_time = '$time', system_log = '$filedata' where system_name = '$system' and ontracid = '$ontracid'";
	$sth = $dbh->prepare("$sql");
	$sth->execute;
	undef $sth;
}

# End of subfunction
}
sub load_servers_in_domain {

my $loaded_servers="0";

chdir("$specifications") 
	or die $!;

for(<*>) {
	chomp;
	my $server=$_;
	
	open("SPEC","<$specifications/$_")
		or die $!;
	
	for(<SPEC>) {
		chomp;
		
		if("$_" =~/location:$domain/) {
			print "[$date $time] Adding $server for ontrac progress tracking.\n";
			push @check, "$server";
			$loaded_servers++;

			$dbh = DBI->connect("dbi:$Use_database_type:$Use_database_name", $Use_database_user, $Use_database_password) 
				or die ("ERROR Couldnt open database:$Use_database_name errormessage:$!");
                        $sql="insert into andutteye_ontracprogress(ontracid,domain,system_name,aestep,status,created_date,created_time,created_by) values('$ontracid','$domain','$server','1','Initializing.','$date','$time','ontrac progress tracker')";
                        $sth = $dbh->prepare("$sql");
                        $sth->execute;
                        undef $sth;
		}
	}

	close("SPEC")
		or die $!;
	
}
print "$loaded_servers systems with location $domain added for ontrac progress tracking.\n";

# End of subfunction
}

sub check_for_ontrac_progress_requests {

my $found="0";

print "[$date $time] Checking for ontrac progresstracking items.\n";

$dbh = DBI->connect("dbi:$Use_database_type:$Use_database_name", $Use_database_user, $Use_database_password) 
	or die ("ERROR Couldnt open database:$Use_database_name errormessage:$!");

$sql="select seqnr,ontrac,ontrac_description,ontrac_domain from andutteye_ontrac where ontrac_done = 'Yes' and ontrac_progress = 'Yes' and ontrac_date = '$date' limit 0,1";
$sth = $dbh->prepare("$sql");
$sth->execute or die "Failed :$!\n";

$sth->bind_columns(undef, \$seqnr, \$ontrac, \$ontrac_description, \$ontrac_domain);
while($sth->fetch()) {
	print "[$date $time] Found a ontrac for domain $ontrac_domain id $seqnr :$ontrac ($ontrac_description) to trac progress for.\n";
	print "[$date $time] Setting domain to $ontrac_domain and tracid to $seqnr\n";
	$domain="$ontrac_domain";
	$tracid="$seqnr";
	$ontracid="$seqnr";
	$found++;
}

if ($found) {
	print "[$date $time] Setting ontrac progresstracking to OK for id $ontracid\n";
	$sql="update andutteye_ontrac set ontrac_progress = 'OK' where seqnr = '$ontracid'";
	$sth = $dbh->prepare("$sql");
	$sth->execute;
} else {
	exit;
}

# End of subfunction
}

sub program_info {
print "\n";
print "$version\n";
print "\n";
print "Example $0 -config=/etc/andutteye/andutteyedsrv.conf\n";
print "\n";
print "-config         :Specifies server configuration to use.\n";
print "\n";
exit 1;
}
parse_and_load_configuration();
check_for_ontrac_progress_requests();
load_servers_in_domain();
get_current_log_line();
loop_and_check_progress();
