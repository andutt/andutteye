#!/bin/sh
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
#    $Id: install.sh,v 1.3 2006/10/15 16:59:27 andutt Exp $
#
# The VERSION-parameter tells current version of this program
VERSION="Andutteye Webinterface databaseinstaller 3.0 (2008 www.thundera.se)"
#
#
# Checking that user are executing the program as root
USER=`id -nu`
if [ $USER != "root" ]
	then
	echo "ERROR You must execute the installation programs as root, since if this is a new installation only root have"
	echo "ERROR have permission to create a new database"
	exit 1
fi

check_variables() {
#
# This section is checking that every variable is set
#
USERNAME=$1
PASSWORD=$2
AEDIR=$3
DATABASE=$4

if [ -z $USERNAME ]
        then
        echo "ERROR Cant create tables and stuff on empty variables. No username specified, aborting"
        exit 1
fi
if [ -z $PASSWORD ]
        then
        echo "ERROR Cant create tables and stuff on empty variables. No password specified, aborting"
        exit 1
fi
if [ ! -d "$AEDIR" ]
        then
        echo "ERROR Andutteye directory:$AEDIR dont seem to exist, rerun program"
        exit 1
fi
if [ ! -d "$AEDIR/db" ]
        then
        echo "ERROR Andutteye directory:$AEDIR/db dont seem to exist, rerun program"
        exit 1
fi
}
upgrade_ae() {
#
# Upgrading database if needed
#
USERNAME=$1
PASSWORD=$2
AEDIR=$3
DATABASE=$4

if [ ! -f "/usr/bin/mysqladmin" ]
	        then
              mysqladmin --help> /dev/null 2>&1
        if [$? != 0 ]
           then
           echo "ERROR You dont seem to have /usr/bin/mysqladmin installed under that location and neither"
           echo "ERROR in your PATH-variable, investigate if you have it installed otherwhise install it"
        exit 1
       fi
fi

UPGRADEPROGS=`ls $AEDIR/install | grep upgrade`

echo 
echo "* Probing for availeble Andutteye upgrade programs"
echo 

for i in $UPGRADEPROGS
	do
	echo "Upgrade program:$i"
done

echo
echo "* Choose from which version you want to upgrade. If you have a very old release you have to upgrade the"
echo "* Andutteye-database in syncron order, for example if you are running version 1.19 and want to install 1.21"
echo "* you have to run first the program that upgrades from version 1.19 to 1.20 and the the program that upgrades"
echo "* from version 1.20 to 1.21."
echo 
echo "Which upgrade program do you want to execute, paste in the whole program name (Ex:upgrade_from_1.19_to_1.20.sql)"
read UPGRADEPROGRAM

if [ -z "$UPGRADEPROGRAM" ]
	then
	echo "ERROR You have to specify a upgradeprogram to be able to upgrade anything, aborting"
	exit 1
fi

echo "Trying to upgrade database with:$AEDIR/install/$UPGRADEPROGRAM"
echo

# Trying to upgrade...
mysql -A $DATABASE -u "$USERNAME" --password="$PASSWORD" < $AEDIR/install/$UPGRADEPROGRAM

if [ $? != 0 ]
	then
	echo "ERROR Failed to upgrade database by executing:mysqladmin -A $DATABASE < $AEDIR/install/$UPGRADEPROGRAM"
	echo "ERROR Verify linuxpermissions and rerun program"
	exit 1
else
	echo "***********************************************************************"
	echo
	echo "* Upgrade COMPLETED"
	echo
	echo "* Rerun the installation program if you need to execute more upgrade programs"
	echo "* A complete list of what have changed since last release are availeble at"
	echo "* http://www.andutteye.com. If you find any bugs"
	echo "* or if the installationprogram fails, please report them."
	echo
	echo "* Enjoy Andutteye."
	echo
fi
}
echo
echo "$VERSION"
echo
echo "*****************************************************************************"
echo "*"
echo "* Note: This installer onlys supports Mysql-databases for the moment, so"
echo "*       if you are planning to use anohter database or if this installer"
echo "*       should fail for some reason. There are a complete installation"
echo "*       manual under the documentation section on http://www.andutteye.com"
echo "*       on howto manually configure Andutteye."
echo "*"
echo "*****************************************************************************"
echo "*"
echo "* Also  If you find any bug or have issues with any Andutteye"
echo "*       software dont hessitate to report them."
echo "*"
echo "*****************************************************************************"
echo "*"
echo "*       Thank you for using Andutteye!"
echo "*"
echo "*****************************************************************************"
echo 
echo "Welcome to Andutteye webinterface database install wisard what do you want to do?? (install,upgrade or quit)"
echo "You can abort the program at any time by executing CTRL+C"
read ans

case $ans in

install|INSTALL)

create_db() {
#
# Trying to create AE-database
#
DATABASE=$1
MYSQLROOT_PWD=$2

if [ -z "$DATABASE" ]
	then
	echo "ERROR Cant create a database if i dont recive any databasename, aborting"
	exit 1
fi
if [ ! -f "/usr/bin/mysqladmin" ]
	then
		mysqladmin --help> /dev/null 2>&1
	if [$? != 0 ]
	   then
		echo "ERROR You dont seem to have /usr/bin/mysqladmin installed under that location and neither"
		echo "ERROR in your PATH-variable, investigate if you have it installed otherwhise install it"
		exit 1
	fi
fi
echo "Creating to create database called $DATABASE"

if [ -z "$MYSQLROOT_PWD" ]
	then
	mysqladmin create $DATABASE
else 
	mysqladmin create $DATABASE --user=root --password="$MYSQLROOT_PWD"
fi

if [ $? = 0 ]
	then
	echo "Andutteye Surveillance database $DATABASE created successfully"
else
	echo "ERROR I recived a non zero existstate from mysqladmin, aborting"
	exit 1
fi
}
create_tables() {
#
# Trying to create tables and grant user
#
#
USERNAME=$1
PASSWORD=$2
AEDIR=$3
DATABASE=$4
MYSQLROOT_PWD=$5

echo "Probing for tablespecifications under $AEDIR/db"
TABLESPECS=`ls $AEDIR/db`

for i in $TABLESPECS
    do
    echo "Creating AE-table on AE-db($DATABASE):$i"

    if [ -z "$MYSQLROOT_PWD" ]
    	then
        mysql -A $DATABASE < $AEDIR/db/$i
    else
        mysql -A $DATABASE -u root --password="$MYSQLROOT_PWD" < $AEDIR/db/$i
    fi
	
    if [ $? = 0 ]
	then	
	echo "Created OK"
    else
	echo "Something happend, aborting"
	exit 1
    fi
done

echo "Granting user $USERNAME to $DATABASE with password $PASSWORD"
    
if [ -z "$MYSQLROOT_PWD" ]
    	then
mysql -A $DATABASE <<EOF
grant all on $DATABASE.* to $USERNAME@localhost identified by '$PASSWORD';
EOF
else
mysql -A $DATABASE -u root --password="$MYSQLROOT_PWD" <<EOF
grant all on $DATABASE.* to $USERNAME@localhost identified by '$PASSWORD';
EOF
fi

if [ $? = 0 ]
	then	
	echo "Grant successfully changed"
    else
	echo "Something happend, aborting"
	exit 1
    fi
}
echo
echo "Will try to install and build Andutteye Surveillance nessasary features"
echo 
echo "# First i need some information"
echo "# 1:What do you want to call you database? (Suggestion:andutteye)"
read DBNAME
echo "# 2:What shall the database user be called? (Suggestion:andutteye)"
read DBUSERNAME
echo "# 3:What shall the database password be? (Suggestion:More then 8characher+specialsigns+numbers)"
read DBPASSWORD
echo "# 4:Where are the andutteye-structure located, specify direct path (If you havent changed:/var/www/html/andutteye)"
read ANDUTTEYEDIR
echo "# 5:Have you set a mysql-root password? if so specifiy it here otherwhise just press enter"
echo "# 5:This is the last question before the installation program will try to install"
read MYSQLROOT_PWD

# Checking that everything is set
check_variables $DBUSERNAME $DBPASSWORD $ANDUTTEYEDIR $DBNAME
echo "#"
echo "# Installer start the creation faces, please wait..."
echo "#"
# Calling on create_db function with argument databasename
create_db $DBNAME $MYSQLROOT_PWD

# Calling create tables and grant user function
create_tables $DBUSERNAME $DBPASSWORD $ANDUTTEYEDIR $DBNAME $MYSQLROOT_PWD

echo "****************************************************************************************************"
echo
echo "Installation is COMPLETE. You have now two things to do manually before Andutteye Surveillance can be started."
echo
echo "1:You have to change DBNAME,DBUSER,DBPASS in (AEDIR)/config/config.php to the ones you just set"
echo
echo "2:You have to change DBNAME,DBUSER,DBPASS in /opt/andutteye/etc/andutteye_server.conf to the ones you just set"
echo
echo "* Thats it, no you should be able to start the server listener from /etc/init.d which will load incoming"
echo "* messages from the clients. And also the syslog server from the same location if you want to use it."
echo
echo "* And use a browser to your webbserver to be able to login to the webbinterface. Remember also to create"
echo "* a valid webbinterface user. You can do that be logged in as admin. You can read exactly how to do this on "
echo "* http://www.andutteye.com under the documentation section. Installation and configuration of"
echo "* Andutteye-server and client."
echo
echo "****************************************************************************************************"
;;
upgrade|UPGRADE)
echo
echo
echo "Will try to upgrade Andutteye Surveillance to a newer version"
echo 
echo "# First i need some information"
echo "# 1:What do you call your andutteye database? (Most common:andutteye)"
read DBNAME
echo "# 2:What are the andutteye database user called? (Most common:andutteye)"
read DBUSERNAME
echo "# 3:What are the andutteye database user password? (Most common:somethinghard i hope :) )"
read DBPASSWORD
echo "# 4:Where are the andutteye-structure located, specify direct path (If you havent changed:/var/www/html/andutteye)"
read ANDUTTEYEDIR

# Checking that everything is set
check_variables $DBUSERNAME $DBPASSWORD $ANDUTTEYEDIR $DBNAME

# Trying to upgrade database
upgrade_ae $DBUSERNAME $DBPASSWORD $ANDUTTEYEDIR $DBNAME
;;
quit|q|QUIT)
echo
echo "Recived quit signal from user, Thank you and welcome back"
echo
exit 0
;;
*)
echo
echo "$VERSION"
echo
echo "ERROR, You have to specifiy either install or upgrade thats the only availble chooise"
echo
exit 1
;;
esac
