#!/bin/sh
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
# Cron program for Andutteye Management (andutt)
#
# Description: If many nodes are connected to the repository it isnt so funny if every node try to 
#              connect to the repository at the same time. With this program they will connect randomly 
#	       with the max wait time of 900 seconds, 15 minutes.
#
# $Id: aemanagement.sh,v 1.4 2006/10/15 16:48:21 andutt Exp $
#
# The AEMANAGEMENT_PROG tells where the aemanagement program are located.
AEMANAGEMENT_PROGRAM=/opt/aemanagement/aemanagement.pl

if [ ! -f "$AEMANAGEMENT_PROGRAM" ]
      then
      echo "ERROR andutteyemanagement agent doesnt exist where specified or isnt a file:$AEMANAGEMENT_PROGRAM"
      exit 1
fi
if [ ! -x  "$AEMANAGEMENT_PROGRAM" ]
      then
      echo "ERROR andutteyemanagement agent isnt executeble:$AEMANAGEMENT_PROGRAM"
      exit 1
fi
RANDOM=`date '+%s'`
if [ -z  "$[($RANDOM % 900) + 1]" ]
      then
      sleep 100
else
      sleep $[($RANDOM % 900) + 1]
fi
$AEMANAGEMENT_PROGRAM > /dev/null 2>&1
