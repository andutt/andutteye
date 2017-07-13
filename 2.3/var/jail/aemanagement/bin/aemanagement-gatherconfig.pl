#!/usr/bin/perl 
#
# Copyright (C) 2004-2017 Andreas Utterberg Thundera AB.
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
# Description:Andutteye management fileselector module. A part of andutteye management.
#
# $Id: aemanagement-gatherconfig.pl,v 1.14 2006/10/15 16:48:21 andutt Exp $
#
# The version tells current version of the program.
our $version = "Andutteye Software Suite Fileselector program. Version:2.0 Fixlevel:1.5 Lastfix:2005-06-28 (andutt)";
our $managementdir;
our $templatedir;
our $filedir;
our $savedir;
our @filetypes;
our @filestocheck;
our $dirname;
our $filename;
our $debug;
our $hostname;
our @ARGV;
our $count=0;
our $gatherfile_method;

use File::Basename;
use strict;
use warnings;

if(!defined($ENV{ANDUTTEYEMANAGEMENT_REPOSITORY})) {
	 print "** ERROR Andutteye management repository location parameter isnt set. Check documentation for more info.\n";
	 exit 1;
} else {
        require("$ENV{ANDUTTEYEMANAGEMENT_REPOSITORY}/config/aemanagement-config.conf");
}
for (@ARGV) {
	if(/-debug/) {
		my @tmpdebug=split("=", $_);
		$debug=$tmpdebug[1];
	}
	if(/-hostname/) {
		my @hostname=split("=", $_);
		$hostname=$hostname[1];
	}
}
#
# Validate and add default values incase of nothing.
#
if (!$debug) {
	$debug=1;
}
if(!$hostname) {
	print "** ERROR no hostname specified\n";
	exit 1;
}

#
# Push in running hostname in filetypes array.
#
my $group=`$managementdir/bin/aemanagement-api.pl -getgroup=$hostname`;
chomp $group;
my $location=`$managementdir/bin/aemanagement-api.pl -getlocation=$hostname`;
chomp $location;
my $patchlevel=`$managementdir/bin/aemanagement-api.pl -getpatchlevel=$hostname`;
chomp $patchlevel;
my $distribution=`$managementdir/bin/aemanagement-api.pl -getdistribution=$hostname`;
chomp $distribution;
my $archtype=`$managementdir/bin/aemanagement-api.pl -getarchtype=$hostname`;
chomp $archtype;

if (!defined($group)) {
	print "** ERROR Failed to get the correct group from management-api\n";
	exit 1;
}
if (!defined($location)) {
	print "** ERROR Failed to get the correct location from management-api\n";
	exit 1;
}
if (!defined($distribution)) {
	print "** ERROR Failed to get the correct distribution from management-api\n";
	exit 1;
}
if (!defined($archtype)) {
	print "** ERROR Failed to get the correct archtype from management-api\n";
	exit 1;
}
push(@filetypes, $group);
push(@filetypes, $location);
push(@filetypes, $archtype);
push(@filetypes, "$group--$location");
push(@filetypes, "$group--$archtype");
push(@filetypes, "$location--$archtype");
push(@filetypes, "$group--$location--$archtype");

if($debug > 3) {
	print "-- Pushing in running hostname:$hostname to valid filetypes to check\n";
}
push(@filetypes, $hostname);
#
# Begin looping and locate files containing filetypes.
#
perform_clean();
for(@filetypes) {
	my $filetype=$_;
		if($debug > 4) {
			print "-- Searching for file containing file type:$filetype for hostname:$hostname\n";
			print "-- Jumping to filerepository:$filedir/$distribution\n";
		}

	chdir("$filedir") or die "Failed to jump to filerepository:$filedir errormsg:$!\n";
	chdir("$distribution") or die "Failed to jump to filedistributionrepository:$distribution errormsg:$!\n";

	if("$gatherfile_method" ne "plainfilemode") {
		if($debug > 3) {
			print "-- Using directory method, using directory if it exists-->$filetype\n";
		}
		if(-d "$filedir/$distribution/$filetype") {
			print "-- Using directorymethod-->$filedir/$distribution/$filetype\n";
			chdir("$filedir/$distribution/$filetype") 
				or die "Failed to jump to filedistributionrepository:$filedir/$distribution/$filetype errormsg:$!\n";
		} else {
			if($debug > 3) {
				print "** Directory->$filedir/$distribution/$filetype doesnt exist\n";
			}
			next;
		}

	}	
	print "-- Searching for:*--[$filetype]\n";
	@filestocheck=`find . -type f -name "*--$filetype" -print`;
	#	
	# Running nested loop on found files containing filetypes.
	#
	for(@filestocheck) {
		chomp;
	        $dirname=dirname($_);	
	        $filename=basename($_);	
		my @tmpfilename=split("--",$filename);
		my $splittedfilename=$tmpfilename[0];	

		if($debug) {
			print "## Found a file:$_\n";
		}		
		if($debug > 4) {
			print "## Found file [dirname=$dirname, filename=$filename, complete=$_ splitted=$splittedfilename\n";
		}
		#
		# Creating nessasary directorys and copying files, the best match counts.
		#
		if (! -d "$savedir/$hostname") {
			   if($debug > 3) {
			   		print "-- Creating initial save/bundle directory:$savedir/$hostname\n";
			   }
			   `mkdir -p "$savedir/$hostname"`;
		}
		if( ! -d "$savedir/$hostname/$dirname" ) {
			   if($debug > 3) {
			   		print "-- Creating subdir:$savedir/$hostname/$dirname\n";
			    }
			   `mkdir -p $savedir/$hostname/$dirname`;
		} 
	        if($debug > 3) {
			print "## Copying file to bundle dir as:$savedir/$hostname/$dirname/$splittedfilename\n";
		}

		
		my $fixed_object=`echo "$dirname/$splittedfilename" | cut -c2-`;
		chomp $fixed_object;
		my $tmpfsettings=`$managementdir/bin/aemanagement-api.pl -getfilesettings=$fixed_object`;
		chomp $tmpfsettings;
		my @fsettings=split(":",$tmpfsettings);
		my $object="$fsettings[0]";
		my $pre="$fsettings[1]";
		my $perms="$fsettings[2]";
		my $post="$fsettings[3]";
		my $iorder="$fsettings[4]";

		if($debug > 3) {
			print "Object:$object Pre:$pre Perms:$perms Post:$post Iorder:$iorder\n";
			print "Writing index [$fixed_object] -> $savedir/$hostname/$hostname.fileindex\n";
		}
		`echo "$iorder:$fixed_object" >> $savedir/$hostname/$hostname.fileindex`;

		`cp $_  "$savedir/$hostname/$dirname/$splittedfilename"`;
		$count++;
	}

for(my $patch=0;$patchlevel >= $patch;$patch++) {
	
	if("$gatherfile_method" ne "plainfilemode") {
		if($debug > 3) {
			print "-- Using directory method, using directory if it exists-->$filetype--$patch\n";
		}
		if(-d "$filedir/$distribution/$filetype--$patch") {
			print "-- Using directorymethod-->$filedir/$distribution/$filetype--$patch\n";
			chdir("$filedir/$distribution/$filetype") 
				or die "Failed to jump to filedistributionrepository:$filedir/$distribution/$filetype--$patch errormsg:$!\n";
		} else {
			if($debug > 3) {
				print "** Directory->$filedir/$distribution/$filetype--$patch doesnt exist\n";
			}
			next;
		}

	}	
	print "-- Searching for:*--[$filetype--$patch] [Patchlevel:$patch]\n";
	@filestocheck=`find . -type f -name "*--$filetype--$patch" -print`;
	#	
	# Running nested loop on found files containing filetypes.
	#
	for(@filestocheck) {
		chomp;
	        $dirname=dirname($_);	
	        $filename=basename($_);	
		my @tmpfilename=split("--",$filename);
		my $splittedfilename=$tmpfilename[0];	

		if($debug) {
			print "## Found a file:$_\n";
		}		
		if($debug > 4) {
			print "## Found file [dirname=$dirname, filename=$filename, complete=$_ splitted=$splittedfilename\n";
		}
		#
		# Creating nessasary directorys and copying files, the best match counts.
		#
		if (! -d "$savedir/$hostname") {
			   if($debug > 3) {
			   		print "-- Creating initial save/bundle directory:$savedir/$hostname\n";
			   }
			   `mkdir -p "$savedir/$hostname"`;
		}
		if( ! -d "$savedir/$hostname/$dirname" ) {
			   if($debug > 3) {
			   		print "-- Creating subdir:$savedir/$hostname/$dirname\n";
			    }
			   `mkdir -p $savedir/$hostname/$dirname`;
		} 
	        if($debug > 3) {
			print "-- Copying file to bundle dir as:$savedir/$hostname/$dirname/$splittedfilename\n";
		}


		my $fixed_object=`echo "$dirname/$splittedfilename" | cut -c2-`;
		my $tmpfsettings=`$managementdir/bin/aemanagement-api.pl -getfilesettings=$fixed_object`;
		chomp $tmpfsettings;
		my @fsettings=split(":",$tmpfsettings);
		my $object="$fsettings[0]";
		my $pre="$fsettings[1]";
		my $perms="$fsettings[2]";
		my $post="$fsettings[3]";
		my $iorder="$fsettings[4]";

		if($debug > 3) {
			print "Object:$object Pre:$pre Perms:$perms Post:$post Iorder:$iorder\n";
			print "Writing index [$fixed_object] -> $savedir/$hostname/$hostname.fileindex\n";
		}
		`echo "$iorder:$fixed_object" >> $savedir/$hostname/$hostname.fileindex`;

		`cp $_  "$savedir/$hostname/$dirname/$splittedfilename"`;
		$count++;
	}
    }
}
sub bundle_software {
#
# This subfunction bundles the found software.
#
my $software=$_[0];

print "-- Will bundle all files residing under:$software\n";
chdir("$software") or die "Failed to jump to directory:$software and bundle files:$!\n";
`tar -z -cvf $savedir/$hostname.tar.gz *`;

if (! -f "$savedir/$hostname.tar.gz" ) {
	print "** ERROR Couldnt locate the bundled tarpackage, something must have wen wrong:$!\n";
	exit 1;
} else {
	print "-- Package bundled ok:$savedir/$hostname.tar.gz \n";
	print "-- [ $count st files found and validated ]\n";

}

# End of subfunction
}
sub perform_clean {
#
#
#
if ( -d "$savedir/$hostname" ) {
	print "-- Removing old filesave structure\n";
	`rm -rf $savedir/$hostname`;
}
if ( -f "$savedir/$hostname.tar.gz") {
	print "-- Removing old filebundle\n";
	`rm -f $savedir/$hostname.tar.gz`
}
# End of subfunction
}
bundle_software("$savedir/$hostname/");
