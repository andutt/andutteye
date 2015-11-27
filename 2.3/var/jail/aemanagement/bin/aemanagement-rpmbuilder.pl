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
# Description:Andutteye management rpmbuilder and validator module. A part of andutteye management.
#
# $Id: aemanagement-rpmbuilder.pl,v 1.16 2006/10/15 16:48:21 andutt Exp $
#
# The version parameter tells current version of the program.
our $version="Andutteye Software Suite Management Rpm validator Version:2.2 Lastfix:2006-10-09 (andutt)";
our $managementdir;
our %base_list;
our %fixed_list;
our %current_list;
our $hostname;
our $rpmname;
our $rpmversion;
our $rpmrelease;
our $query=0;
our $debug;
our $md5sum;
our $listonly=0;
our @ARGV;

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
	if (/^-debug/) {
		my @tmpvalue=split("=", $_);
		$debug=$tmpvalue[1];
	}
	if (/^-md5sum/) {
		my @tmpvalue=split("=", $_);
		$md5sum=$tmpvalue[1];
	}
	if (/^-listonly/) {
		$listonly=1;
	}
	if (/^-query/) {
		$query=1;
	}
}
if (!defined($hostname)) {
	print "** ERROR didnt recive any hostname value, cant work on nothing\n";
	exit 1;
}
if (!defined($debug)) {
	$debug=0;
}
if (!defined($md5sum)) {
	$md5sum=0;
}
sub load_base_list_with_data {
#
#
#
my $bundlelist=`$managementdir/bin/aemanagement-api.pl -getbundles=$hostname`;
my @bundlelist=split(",", $bundlelist);
my $packagelist=`$managementdir/bin/aemanagement-api.pl -getpackages=$hostname`;
my @packagelist=split(",", $packagelist);
my $count=1;

for (@bundlelist) {
chomp;
 if($debug > 2) {
 	print "** Validating bundlelist:$_\n";
 }
 open("BUNDLE","<$managementdir/bundles/$_")
	or die "Failed to open bundlelist:$_ error:$!\n";
		
for(<BUNDLE>) {
	chomp;
    	if(/^(.*)\ (.*)\ (.*)$/) {
  	 	my ($name, $version, $release) = ($1, $2, $3);

	       if(($version ne  "0") && ($release ne "0")) {
		       if($debug > 3) {
		       		print "** Adding to [BUNDLE][FIXED HASH] Package:$name version:$version release:$release\n";
				if($debug > 4) {
					print "-- [FIXED HASH]->". keys( %fixed_list ) ." st.\n";
				}
		       }
		       $fixed_list{$name} = { version => $version, release => $release };
		} else {
			unless(exists ($base_list{$name})) {
				if($debug > 3) {
		       			print "-- Adding to [Bundle][BASE HASH] Package:$name version:$version release:$release\n";
					if($debug > 4) {
						print "-- [BASE HASH]->". keys( %base_list ) ." st.\n";
					}
				}
				$base_list{$name} = { version => $version, release => $release };
       			} else {
      				print "** ERROR Multiple rpm entries found specified for:$name\n";
				exit 1;
			}
		}
	} else {
		print "** ERROR Incorrect rpm syntax:$_\n";
		exit 1;
	} 
 $count++;
 }
}
if($debug > 1) {
	print "** Bundled [BASE HASH ]:". keys( %base_list ) ." st.\n";
	print "** Bundled [FIXED HASH]:". keys( %fixed_list ) ." st.\n";
	print "** Bundled packages    :$count st loaded\n";
}
$count=0;

for(@packagelist) {
	chomp;
	my $packagetype;	
	my $packageprefix;
	my $name1;
	my $version1;
	my $release1;

	if($debug > 2) {
		print "-- Adding from packagelist:$_\n";
	}
	if(/^\+/) {
		$packagetype="add";
		$packageprefix="+";
	}
	elsif(/^\-/) {
		$packagetype="remove";
		$packageprefix="-";
	} 
	elsif($_ eq "") {
		if($debug > 3) {
			print "** No specified packages found, empty variable\n";
		}
		return;
	}		
	else {
		print "** ERROR Invalid package specification syntax:$_\n";
		exit 1;
	}
	my @tmp=split("--",$_);
	chomp @tmp;
	my $pkgname=$tmp[0];
        substr($pkgname, 0, 1, "");

	if(!defined($tmp[0])) {
		print "** ERROR Invalid packagesyntax, name field incorrect package:$_\n";
		exit 1;
	}
	if(!defined($pkgname)) {
		print "** ERROR Invalid packagesyntax, name field incorrect package:$_\n";
		exit 1;
	}
	if(!defined($tmp[1])) {
		print "** ERROR Invalid packagesyntax, version field incorrect package:$_\n";
		exit 1;
	}
	if(!defined($tmp[2])) {
		print "** ERROR Invalid packagesyntax, release field incorrect package:$_\n";
		exit 1;
	}
	 if(($tmp[1] ne 0) && ($tmp[2] ne 0)) {
	       if($debug > 2) {	 
	       		print "** [Package][FIXED HASH] Package:$pkgname version:$tmp[1] release:$tmp[2]\n";
	       }
	       if($packagetype eq "remove") {
			if($debug > 2) {
				print "** [Package][FIXED HASH][REMOVE]Package:$pkgname version:$tmp[1] release:$tmp[2]\n";
			}
			next;
		}
	       $fixed_list{$pkgname} = { version => $tmp[1], release => $tmp[2] };
	} else {
		unless(exists $base_list{$pkgname}) {
			if($debug > 2) {
	       			print "-- [Package][BASE HASH] Package:$pkgname version:$tmp[1] release:$tmp[2]\n";
				if($debug > 4) {
					print "Size of hash base ". keys( %base_list ) .".\n";
				}
			}
	       		if($packagetype eq "remove") {
				if($debug > 2) {
					print "** [Package][FIXED HASH][REMOVE]Package:$pkgname version:$tmp[1] release:$tmp[2]\n";
				}
				next;
			}
				$base_list{$pkgname} = { version => $tmp[1], release => $tmp[2] };
		} else {
      				print "** ERROR Multiple rpm entries found specified for:$pkgname. Is already loaded in some included bundle.\n";
				exit 1;
		}
	}
$count++;
}
if($debug > 1) {
	print "** Packages [BASE HASH ]:". keys( %base_list ) ." st.\n";
	print "** Packages [FIXED HASH]:". keys( %fixed_list ) ." st.\n";
	print "** Packages packages    :$count st loaded\n";
}
# End of subfunction
}
sub validate_against_structures {
#
#
#
my @tmp;
my $count=0;
my $countnohit=0;
my $patchlevel;
my $patchlevelstatus;
my $hostpatchlevel=`$managementdir/bin/aemanagement-api.pl -getpatchlevel=$hostname`;
my $distribution=`$managementdir/bin/aemanagement-api.pl -getdistribution=$hostname`;
my $checkname;
my $checkversion;
my $checkrelease;
my $val;
my $name;
my $version;
my $release;
my $filename;
chomp $distribution;
chomp $hostpatchlevel;

for($patchlevel = 0;$patchlevel <= $hostpatchlevel;$patchlevel++) {
	if($debug > 2) {
		print "Patchlevel:$patchlevel of defined:$hostpatchlevel\n";
	}
	$patchlevelstatus=`$managementdir/bin/aemanagement-api.pl -getpatchlevelstatus=$distribution,$patchlevel`;
	chomp $patchlevelstatus;
	if($debug > 2) {
		print "Patchlevelstatus:$patchlevelstatus for distribution:$distribution and patchlevel:$patchlevel\n";
	}
	if("$patchlevelstatus" ne "open") {
		print "** Aborting package gathering stage since patchlevel:$patchlevel for distribution:$distribution isnt open. Status is:$patchlevelstatus.\n";
		print "** Maybe Andutteye administrators is administrating the patchlevels or performing other maintenence work. Verify this and rerun\n";
		exit 1;
	}
	  open("RPMINDEX","<$managementdir/packages/$distribution/$patchlevel/rpmindex")
	  or die "** ERROR Failed to open $managementdir/$distribution/$patchlevel/rpmindex error:$!\n";

		for(<RPMINDEX>) {
			chomp;
			($name, $version, $release, $filename) = split;

			if(exists ($base_list{$name})) {
				if($debug > 2) {
					print "-- [Rpmindex][BASE HASH][DELANDREPLACE] Package:$name version:$version release:$release location:$patchlevel\n";
					if($debug > 5) {
						print "[Rpmindex][BASE HASH]". keys( %base_list ) .".\n";
					}
				}
				delete $base_list{$name};
				$base_list{$name} = { version => $version, release => $release, 
				location => $patchlevel, filename => $filename };
				$count++;
			}
			elsif(exists ($fixed_list{$name})) {
				if($debug > 2) {
					print "-- [Rpmindex][FIXED HASH][DELANDREPLACE] Package:$name version:$version release:$release location:$patchlevel filename:$filename\n";
				}
				delete $fixed_list{$name};
				$fixed_list{$name} = { version => $version, release => $release, 
				location => $patchlevel, filename => $filename };
				$count++;
			} else {
				if($debug > 5) {
					print "-- [Rpmindex][BASE HASH][NOTHIT] Package:$name ver:$version rel:$release\n";
				}
				$countnohit++;
			}

		 }
close("RPMINDEX");
	if($debug > 1) {
		print "** Rpmindex plevel:$patchlevel [BASE HASH ]:". keys( %base_list ) ." st.\n";
		print "** Rpmindex plevel:$patchlevel [FIXED HASH]:". keys( %fixed_list ) ." st.\n";
		print "** Packages plevel:$patchlevel packages    :$count st loaded\n";
		print "** Packages plevel:$patchlevel packages    :$countnohit st not found or isnt choosen in specification\n";
	}
$count=0;
$countnohit=0;
}
# End of subfunction
}
sub loop_over_fixed_and_replace {
#
#
#
my $count=0;

for my $name (sort keys %fixed_list) {
   my $val = $fixed_list{$name};
   if( $debug > 2) {
   	print "-- [ReplaceBaseWithFixed] Package:$name version: $val->{version} $val->{release} Location:$val->{location}\n";
		if($debug > 4) {
			print "Size of hash base". keys( %base_list ) ." st.\n";
			print "Size of hash fixed". keys( %fixed_list ) ." st.\n";
		}
   }
   delete $base_list{$name};
   delete $fixed_list{$name};
   $base_list{$name} = { version => $val->{version}, release => $val->{release}, 
   location => $val->{location}, filename => $val->{filename} };
}
if($debug > 1) {
	print "** Replace base with fixed [BASE HASH ]:". keys( %base_list ) ." st.\n";
	print "** Replace base with fixed [FIXED HASH]:". keys( %fixed_list ) ." st.\n";
	print "** Fixed packages processed            :$count st replaced\n";
}
# End of subfunction
}
sub search_for_unfound {
#
#
#
for my $name (sort keys %base_list) {
   my $val = $base_list{$name};

   if( $debug > 2) {
   	print "-- [SearchForUnfound] Package:$name version: $val->{version} release:$val->{release} Location:$val->{location}\n";
   }
   if(exists ($val->{location})) {
	   if($debug > 2) {
	   	print "-- [SearchForUnfound] Package:$name location are set found in repository [OK]\n";
	   }
   } else {
	   print "** ERROR Choosen package:$name in bundle or packages doesnt exist in repository or isnt included in rpmindex.\n";
	   exit;
   }
}
# End of subfunction
}
sub validate_current_list {
#
#
#
my @currentlist=`$managementdir/bin/aemanagement-api.pl -getcurrentpackages=$hostname`;
my $currentname;
my $currentversion;
my $currentrelease;


for(@currentlist) {
	my @tmp=split;

	if(!defined($tmp[0])) {
		print "** ERROR Rpmlist currupt, name entry is not defined:$_\n";
		exit 1;
	} else {
		$currentname=$tmp[0];
	}
	if(!defined($tmp[1])) {
		print "** ERROR Rpmlist currupt, version entry is not defined:$_\n";
		exit 1;
	} else {
		$currentversion=$tmp[1];
	}
	if(!defined($tmp[2])) {
		print "** ERROR Rpmlist currupt, release entry is not defined:$_\n";
		exit 1;
	} else {
		$currentrelease=$tmp[2];
	}
	$current_list{$currentname} = { version => $currentversion, release => $currentrelease };
}
my $count=0;
my $countnohit=0;

for $currentname (sort keys %current_list) {
	my $currentval = $current_list{$currentname};
   	my $basval     = $base_list{$currentname};
	my $tmpname    = $basval->{name};

	if(!defined($currentval->{version})) {
		if($debug > 2) {
			print "** [CompareBaseCurrent] PackageCurrent:$currentname has an undefined version, Shall probably be deleted\n";
		}
		$countnohit++;
		next;
	}
	if(!defined($basval->{version})) {
		if($debug > 2) {
			print "** [CompareBaseCurrent] PackageBase:$currentname has an undefined version, Shall probably be deleted\n";
		}
		$countnohit++;
		next;
	}
	if ("$currentval->{version}" eq "$basval->{version}") {
       		if("$currentval->{release}" eq "$basval->{release}") {
			if($debug > 2 ) {
				print "-- [CompareBaseCurrent] BasePackage:$currentname version:$basval->{version} rel:$basval->{release} [MATCHDELETE]\n";
				if($debug > 4) {
					print "-- [CURRENT HASH]". keys( %current_list ) ." st.\n";
					print "-- [BASE HASH]". keys( %base_list ) ." st.\n";
				}
			}
			delete $current_list{$currentname};
			delete $base_list{$currentname};
			$count++;
		} else {
			if($debug > 2) {
				print "[CompareBaseCurrent] Package:$currentname\n";
				print "\tver:$currentval->{version} ver:$basval->{version}\n";
				print "\trel:$currentval->{release} rel:$basval->{release}\n";
			}
		$countnohit++;
		}
	}

}
if($debug > 1) {
	print "-- CompareBaseCurrent packages direct match and delete:$count st\n";
	print "-- CompareBaseCurrent packages NOT direct match       :$countnohit st\n";
	print "-- [CURRENT HASH]". keys( %current_list ) ." st.\n";
	print "-- [BASE HASH]". keys( %base_list ) ." st.\n";
}
my $name;
for $name (sort keys %base_list) {
   	 my $basval     = $base_list{$name};
   	 my $currentval = $current_list{$name};
	
	# Removing any dots and alphasign in version and release fields.
	#$basval->{release} = regex_version_and_release_fields("$basval->{release}");
	#$currentval->{release} = regex_version_and_release_fields("$currentval->{release}");

	if(($basval->{version} eq "0") && ($basval->{release} eq "0")) {
		if($debug > 2) {
			print "** [ValidateLastinBase] Package:$name has a version 0 and release 0, shall  be removed\n";
		}
		next;	
	}
	if(exists ($current_list{$name})) {
		if($debug > 2) {
			print "-- [ValidateLastInBase] Package:$name exists both in basehash and in currenthash, validating version and releases\n";
		}
		if(($basval->{version} eq $currentval->{version}) && ($basval->{release} eq $currentval->{release})) {
			if($debug > 2) {
				print "-- [ValidateLastInBase] Package:$name has the same version and release, [BUG-SHOULDNTHAPPEN]\n";
			}
			next;
		}
		if ($basval->{version} gt $currentval->{version}) { 
			if($debug > 2) {
				print "-- [ValidateLastInBase] Package:$name has a greater version defined in repository.[INSTALL-UPGRADE]\n";
			}
			next;
			if ($basval->{release} gt $currentval->{release}) { 
				if($debug > 2) {
					print "-- [ValidateLastInBase] Package:$name has greater release defined in repository.[INSTALL-UPGRADE]\n";
				}
				next;
			} else {
				if ($basval->{release} eq $currentval->{release}) { 
					if($debug > 2) {
						print "-- [ValidateLastInBase] Package:$name has the same release defined in repository.[NOACTION]\n";
					}
					delete $base_list{$name};
				} else {
					if($debug > 2) {
						print "-- [ValidateLastInBase] Package:$name has never release installed then defined in repository.[NEWERINSTALLED]\n";
					}
					create_action_log("NEWERINSTALLED:$name:$basval->{version}:$currentval->{version}:$basval->{release}:$currentval->{release}");
					delete $base_list{$name};
					delete $current_list{$name};
				}
			}
		} else {
			if ("$currentval->{version}" ge "$basval->{version}") { 
				if($debug > 2) {
					print "-- [ValidateLastInBase] Package:$name has the same version defined in repository.Checking release\n";
				}
				if ("$basval->{release}" gt "$currentval->{release}") { 
					if($debug > 2) {
						print "-- [ValidateLastInBase] Package:$name has greater release defined in repository.[INSTALL-UPGRADE]\n";
					}
					next;
				} else {
					if ($basval->{release} eq $currentval->{release}) { 
						if($debug > 2) {
							print "-- [ValidateLastInBase] Package:$name has the same release defined in repository.[NOACTION]\n";
						}
						delete $base_list{$name};
					} else {
						if($debug > 2) {
							print "-- [ValidateLastInBase] Package:$name has newer release installed then defined in repository.[NEWERINSTALLED]\n";
						}
						create_action_log("NEWERINSTALLED:$name:$basval->{version}:$currentval->{version}:$basval->{release}:$currentval->{release}");
						delete $base_list{$name};
						delete $current_list{$name};
					}
				}
			} else {
				if($debug > 2) {
					print "-- [ValidateLastInBase] Package:$name has never version installed then defined in repository.[NEVERINSTALLED]\n";
				}
				create_action_log("NEWERINSTALLED:$name:$basval->{version}:$currentval->{version}:$basval->{release}:$currentval->{release}");
				delete $base_list{$name};
				delete $current_list{$name};
			}
		}
	} else {
		if($debug > 2) {
			print "-- [ValidateLastInBase] Package:$name isnt installed on node, [INSTALL-UPGRADE]\n";
		}
	}
}
$count=0;
if( $debug > 1) {
	print "=" x 70;
	print "\n** PACKAGES MARKED FOR REMOVAL\n";
	print "=" x 70;
	print "\n";
}
for $currentname (sort keys %current_list) {
      my $currentval = $current_list{$currentname};
	unless(exists ($base_list{$currentname})) {
		if( $debug > 1) {
      			print "-- Packages to remove:$currentname ver:$currentval->{version} rel:$currentval->{release}\n";
		}
      		create_action_log("DELETE:$currentname:$currentval->{version}:$currentval->{release}");
      		$count++;
	} else {
		if($debug > 2) {
			print "-- Package:$currentname exists both in install and delete hash, remove it in delete hash. Will be upgraded\n";
		}
	}
}
if( $debug > 1) {
	print "** Packages to remove:$count\n";
	print "=" x 70;
	print "\n** PACKAGES MARKED FOR INSTALLATION/UPGRADE\n";
	print "=" x 70;
	print "\n";
}
$count=0;

# Retriving hosts distribution to be able to complete absolute path.
my $distribution=`$managementdir/bin/aemanagement-api.pl -getdistribution=$hostname`;
chomp $distribution;
my @tarbundle;
my $tarbundle;

for $currentname (sort keys %base_list) {
      my $currentval = $base_list{$currentname};
	if( $debug > 1) {
      		print "++ Packages to install/upgrade\n";
      		print "\tName:$currentname\n";
      		print "\tVersion:$currentval->{version} Release:$currentval->{release}\n";
      		print "\tLocation:$managementdir/packages/$distribution/$currentval->{location} Filename:$currentval->{filename}\n";
	}
      $count++;
      	if ( ! -f "$managementdir/packages/$distribution/$currentval->{location}/$currentval->{filename}") {
	      print "** ERROR Install/upgrade selected package doesnt exist in physical path\n";
	      print "** ERROR Package:$managementdir/packages/$distribution/$currentval->{location}/$currentval->{filename}\n";
	      print "** ERROR is missing\n";
	      exit 1;
      	} else {
		create_action_log("INSTALL:$currentname:$currentval->{version}:$currentval->{release}:$currentval->{filename}");
	
		if($listonly) {
			if($debug > 2) {
				print "\tListonly specified, will only create worklist, not copy packages($currentname).\n";
			}
		} else {
			if ( ! -d "$managementdir/out/$hostname-rpms" ) {
				print "** Creating rpm tempdirectory:$managementdir/out/$hostname-rpms\n";
				`mkdir -p $managementdir/out/$hostname-rpms`;
			}
			system("cp $managementdir/packages/$distribution/$currentval->{location}/$currentval->{filename} $managementdir/out/$hostname-rpms");
		}
	}
}
if( $debug > 1) {
	print "** Packages to install:$count\n";
	print "=" x 70;
	print "\n";
}
if($listonly) {
	if($debug > 2) {
		print "-- Listonly specified, will only create worklist, not bundle packages.\n";
	}
} else {
	if($count == 0) {
		if($debug > 2) {
			print "-- Didnt have any packages($count st) to install, will not try to bundle.\n";
		}
	} else {
		bundle_packages();
	}
}
# End of subfunction
}
sub perform_clean {
#
#
#
if($debug > 1) {
	print "-- Cleaning old files and logs\n";
}
if ( -f "$managementdir/out/$hostname-action.list" ) {
	if($debug > 1) {
		print "-- Removing old actionlist:$managementdir/out/$hostname-action.list\n";
	}
		system("rm -f $managementdir/out/$hostname-action.list");
		system("rm -f $managementdir/out/$hostname-rpms/*.rpm");
}
if ( -f "$managementdir/out/$hostname-packages.tar.gz") {
	if($debug > 1) {
		print "-- Removing old tarbundle:$managementdir/out/$hostname-packages.tar.gz\n";
	}
		system("rm -f $managementdir/out/$hostname-packages.tar.gz");
}

# End of subfunction
}
sub create_action_log {
#
#
#
my $log=$_[0];
open("ACTIONLOG",">>$managementdir/out/$hostname-action.list")
	or die "Failed to open actionlist:$managementdir/out/$hostname-action.list error:$!\n";
print ACTIONLOG "$log\n";
close("ACTIONLOG");

# End of subfunction
}
sub bundle_packages {
#
#
#
my $bundle;
my $path;
my $file;

if (! -d "$managementdir/out/$hostname-rpms") {
	`mkdir $managementdir/out/$hostname-rpms`;
}
chdir("$managementdir/out/$hostname-rpms")
	or die "** ERROR Failed to chdir to:$managementdir/out/$hostname-rpms error:$!\n";

my $ecode=system("tar -z -cvf $managementdir/out/$hostname-packages.tar.gz *.rpm > /dev/null 2>&1");
if ($ecode == 0 ) {
	if($debug > 2) {
		print "-- Packaging of tarbundle for $hostname finisched successfully\n";
	}
} else {
	print "** ERROR Failed to bundle:$managementdir/out/$hostname-packages.tar.gz exitcode:$ecode\n";
	exit $ecode;
}

# End of subfunction
}
sub verify_integrity {
#
#
#
my $listchecksum=`$managementdir/bin/aemanagement-api.pl -getcurrentpackagechecksum=$hostname`;
chomp $listchecksum;

if(!$listchecksum) {
	print "** ERROR Didnt recive a valid md5hash from management-api\n";
	exit 1;
} else {
	if(!$md5sum) {
		print "** ERRROR Didnt recive a valid md5hash from connected agent:$hostname\n";
		exit 1;
	} else {
		if ("$md5sum" eq "$listchecksum") {
			if($debug > 3) {
				print "-- Spesified md5hash:$md5sum matches:$listchecksum from agent\n";
			}
		} else {
			print "** ERROR md5hash integrity check failed for connected agent:$hostname\n";
			exit 1;
		}
	}
}
# End of subfunction
}
sub regex_version_and_release_fields {
#
#
#
my $verify=$_[0];
my @strip;

if(!defined($verify)) {
        print "** ERROR recived and empty data field to validate:$verify aborting\n";
        exit;
} else {
	if($debug > 2) {
        	print "-- [regex_version_and_release_fields] Recived datafield to verify:$verify\n";
	}
}
if($verify =~ m/[A-Z]/) {
	if($debug > 2) {
        	print "-- [regex_version_and_release_fields] Found [A-Z] letters in data field:$verify, will regex it away.\n";
	}
        $verify =~ tr/[A-Z]//d;
}
if($verify =~ m/[a-z]/) {
	if($debug > 2) {
        	print "-- [regex_version_and_release_fields] Found [a-z] letters in data field:$verify, will regex it away.\n";
	}
        $verify =~ tr/[a-z]//d;
        $verify =~ tr/\.//d;
}
if($verify =~ m/\./) {
	if($debug > 2) {
        	print "-- [regex_version_and_release_fields] Found [.] letters in data field:$verify, will regex it away.\n";
	}
        $verify =~ tr/\.//d;
}
if($debug > 2) {
	print "-- [regex_version_and_release_fields] Data field after regex:s is now containing:$verify\n";
}
return("$verify");

# End of subfunction
}

#
# Start of program
#
if($debug > 1) {
	print "\n$version\n";
}
perform_clean();
verify_integrity();
load_base_list_with_data();
validate_against_structures();
loop_over_fixed_and_replace();
search_for_unfound();
validate_current_list();
