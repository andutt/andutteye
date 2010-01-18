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

our @tables=('andutteye_alarm','andutteye_assetmanagement', 'andutteye_base_agentconfiguration', 'andutteye_bundles', 'andutteye_changeevent', 'andutteye_choosenbundles', 'andutteye_choosenpackages', 'andutteye_core_configuration', 'andutteye_domains', 'andutteye_files', 'andutteye_front_configuration', 'andutteye_groups', 'andutteye_managementlog', 'andutteye_monitor_configuration', 'andutteye_monitor_status', 'andutteye_packages', 'andutteye_packages_content', 'andutteye_packages_dependencies', 'andutteye_patchlevel', 'andutteye_provisioning', 'andutteye_provisioning_checkin', 'andutteye_rolepermissions', 'andutteye_roles', 'andutteye_serverlog', 'andutteye_snapshot', 'andutteye_software', 'andutteye_specifications', 'andutteye_statistics', 'andutteye_systems', 'andutteye_uploads', 'andutteye_users');
our $dbh;
our $sth;

use DBI;


$dbh = DBI->connect("dbi:$Use_database_type:$Use_database_name", $Use_database_user, $Use_database_password) or die 
("ERROR Couldnt open database:$Use_database_name errormessage:$!");                                                                               

for(@tables) {
	my $table = $_;
	print "Check table->$table";
	$sth = $dbh->prepare("check table $table");
	$sth->execute;

	my $row = $sth->fetchrow_array;
	print "\tstatus = $row\n";

}
