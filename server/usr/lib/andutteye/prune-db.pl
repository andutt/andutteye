#!/usr/bin/perl
#
#    Copyright Andreas Utterberg Thundera (c) All rights Reserved 2008
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
our $Use_database_name ="<database name>";
our $Use_database_type ="<database type>";
our $Use_database_user ="<database user>";
our $Use_database_password ="<database password>";

our @tables=('andutteye_managementlog', 'andutteye_serverlog', 'andutteye_snapshot', 'andutteye_statistics');
our $dbh;
our $sth;

use DBI;
use POSIX;

sub GetDateStamp{
	my $time=time;
	return  strftime"%Y%m%d",localtime $time+60*60*(12-(localtime$time)- 200);
}

$dbh = DBI->connect("dbi:$Use_database_type:$Use_database_name", $Use_database_user, $Use_database_password) or die 
("ERROR Couldnt open database:$Use_database_name errormessage:$!");                                                                               

our $prune_after_date = GetDateStamp();

for(@tables) {
	my $table = $_;
	print "Pruning data older then $prune_after_date in table->$table.";
	$sth = $dbh->prepare("delete from $table where created_date < '$prune_after_date'");
	$sth->execute;
	print "\t [done]\n";
}
