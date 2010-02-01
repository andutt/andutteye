CREATE TABLE andutteye_domains (
  seqnr                 INT(11) NOT NULL auto_increment,
  domain_name           VARCHAR(255) DEFAULT NULL,
  domain_status         VARCHAR(20) DEFAULT NULL,
  domain_description    VARCHAR(255) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_groups (
  seqnr                 INT(11) NOT NULL auto_increment,
  domain_name           VARCHAR(255) DEFAULT NULL,
  group_name            VARCHAR(255) DEFAULT NULL,
  group_status          VARCHAR(20) DEFAULT NULL,
  group_description     VARCHAR(255) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_systems (
  seqnr                 INT(11) NOT NULL auto_increment,
  domain_name           VARCHAR(255) DEFAULT NULL,
  group_name            VARCHAR(255) DEFAULT NULL,
  system_name           VARCHAR(255) DEFAULT NULL,
  system_description    BLOB DEFAULT NULL,
  system_information    BLOB DEFAULT NULL,
  system_type           VARCHAR(255) DEFAULT NULL,
  system_heartbeat      VARCHAR(255) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_users (
  seqnr                 INT(11) NOT NULL auto_increment,
  andutteye_username    VARCHAR(255) DEFAULT NULL,
  andutteye_password    VARCHAR(255) DEFAULT NULL,
  andutteye_role        VARCHAR(255) DEFAULT NULL,
  andutteye_theme       VARCHAR(255) DEFAULT NULL,
  is_admin              VARCHAR(255) DEFAULT NULL,
  user_description      VARCHAR(255) DEFAULT NULL,
  last_loggedin         VARCHAR(255) DEFAULT NULL,
  nr_of_loggins         VARCHAR(255) DEFAULT NULL,
  created_by            VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_serverlog (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  messagetype           VARCHAR(255) DEFAULT NULL,
  logentry              BLOB DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_assetmanagement (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  assetmanagementname   VARCHAR(255) DEFAULT NULL,
  assetmanagementresult VARCHAR(255) DEFAULT NULL,
  assetmanagementprog   VARCHAR(255) DEFAULT NULL,
  assetmanagementargs   VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_software (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  aepackage             VARCHAR(255) DEFAULT NULL,
  aeversion             VARCHAR(255) DEFAULT NULL,
  aerelease             VARCHAR(255) DEFAULT NULL,
  aearchtype            VARCHAR(255) DEFAULT NULL,
  packagetype           VARCHAR(255) DEFAULT NULL,
  status                VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  reported_date         VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_snapshot (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  type                  VARCHAR(255) DEFAULT NULL,
  fs                    BLOB DEFAULT NULL,
  procs                 BLOB DEFAULT NULL,
  net                   BLOB DEFAULT NULL,
  hardware              BLOB DEFAULT NULL,
  users                 BLOB DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  reported_date         VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_alarm (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  shortinformation      VARCHAR(255) DEFAULT NULL,
  longinformation       BLOB DEFAULT NULL,
  repeatcount           VARCHAR(255) DEFAULT NULL,
  status                VARCHAR(255) DEFAULT NULL,
  severity              VARCHAR(255) DEFAULT NULL,
  monitor               VARCHAR(255) DEFAULT NULL,
  monitortype           VARCHAR(255) DEFAULT NULL,
  closedby              VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  reported_date         VARCHAR(100) DEFAULT NULL,
  lastdate              VARCHAR(100) DEFAULT NULL,
  lasttime              VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_statistics (
  seqnr 		INT(11) NOT NULL auto_increment,
  system_name 		VARCHAR(255) DEFAULT NULL,
  systemstatisticsname 	VARCHAR(255) DEFAULT NULL,
  systemstatisticsresult VARCHAR(255) DEFAULT NULL,
  systemstatisticsprog 	VARCHAR(255) DEFAULT NULL,
  systemstatisticsargs	VARCHAR(255) DEFAULT NULL,
  created_date 		VARCHAR(255) DEFAULT NULL,
  created_time 		VARCHAR(255) DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_uploads (
  seqnr                 INT(11) NOT NULL auto_increment,
  domain_name           VARCHAR(255) DEFAULT NULL,
  group_name            VARCHAR(255) DEFAULT NULL,
  system_name           VARCHAR(255) DEFAULT NULL,
  content               MEDIUMBLOB DEFAULT NULL,
  content_name   	VARCHAR(255) DEFAULT NULL,
  content_description   VARCHAR(255) DEFAULT NULL,
  content_type          VARCHAR(255) DEFAULT NULL,
  content_size          VARCHAR(255) DEFAULT NULL,
  upload_type           VARCHAR(255) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_changeevent (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  shortinformation     	VARCHAR(255) DEFAULT NULL,
  monitortype     	VARCHAR(255) DEFAULT NULL,
  description      	VARCHAR(255) DEFAULT NULL,
  information      	BLOB DEFAULT NULL,
  solution      	BLOB DEFAULT NULL,
  workaround      	BLOB DEFAULT NULL,
  external_link         VARCHAR(255) DEFAULT NULL,
  severity              VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_bundles (
  seqnr 		int(11) NOT NULL auto_increment,
  bundle 		VARCHAR(255) DEFAULT NULL,
  aepackage 		VARCHAR(255) DEFAULT NULL,
  aeversion 		VARCHAR(255) DEFAULT NULL,
  aerelease 		VARCHAR(255) DEFAULT NULL,
  aearch 		VARCHAR(255) DEFAULT NULL,
  distribution 		VARCHAR(255) DEFAULT NULL,
  domain_name           VARCHAR(255) DEFAULT NULL,
  revision 		VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_choosenbundles (
  seqnr                 int(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  bundle                VARCHAR(255) DEFAULT NULL,
  revision              VARCHAR(255) DEFAULT NULL,
  domain_name           VARCHAR(255) DEFAULT NULL,
  aeaction              VARCHAR(1) DEFAULT NULL,
  specid                NUMERIC(12) DEFAULT NULL,
  specaction            VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_choosenpackages (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  aepackage             VARCHAR(255) DEFAULT NULL,
  aeversion             VARCHAR(255) DEFAULT NULL,
  aerelease             VARCHAR(255) DEFAULT NULL,
  aearchtype            VARCHAR(255) DEFAULT NULL,
  domain_name 		VARCHAR(255) DEFAULT NULL,
  aeaction              VARCHAR(255) DEFAULT NULL,
  specid                NUMERIC(12) DEFAULT NULL,
  specaction            VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_specifications (
  seqnr 		int(11) NOT NULL auto_increment,
  system_name 		VARCHAR(255) DEFAULT NULL,
  revision 		VARCHAR(255) DEFAULT NULL,
  packagetype 		VARCHAR(255) DEFAULT NULL,
  distribution 		VARCHAR(255) DEFAULT NULL,
  patchlevel 		VARCHAR(255) DEFAULT NULL,
  aegroup 		VARCHAR(255) DEFAULT NULL,
  location 		VARCHAR(255) DEFAULT NULL,
  archtype 		VARCHAR(255) DEFAULT NULL,
  description 		blob DEFAULT NULL,
  status 		VARCHAR(255) DEFAULT NULL,
  package_update 	VARCHAR(255) DEFAULT NULL,
  config_update 	VARCHAR(255) DEFAULT NULL,
  syslog_update 	VARCHAR(255) DEFAULT NULL,
  pkgmanagementtype	VARCHAR(255) DEFAULT NULL,
  email 		blob DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_packages (
  seqnr 		int(11) NOT NULL auto_increment,
  aepackage 		VARCHAR(255) DEFAULT NULL,
  aeversion 		VARCHAR(255) DEFAULT NULL,
  aerelease 		VARCHAR(255) DEFAULT NULL,
  aearchtype 		VARCHAR(255) DEFAULT NULL,
  distribution 		VARCHAR(255) DEFAULT NULL,
  domain_name 		VARCHAR(255) DEFAULT NULL,
  packagetype 		VARCHAR(255) DEFAULT NULL,
  savemode 		VARCHAR(255) DEFAULT NULL,
  filename 		VARCHAR(255) DEFAULT NULL,
  location 		BLOB DEFAULT NULL,
  content 		LONGBLOB DEFAULT NULL,
  patchlevel 		VARCHAR(255) DEFAULT NULL,
  patchlevelinfo	VARCHAR(255) DEFAULT NULL,
  status 		VARCHAR(255) DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_packages_content (
  seqnr 		int(11) NOT NULL auto_increment,
  seqref		VARCHAR(255) DEFAULT NULL,
  domain_name 		VARCHAR(255) DEFAULT NULL,
  distribution 		VARCHAR(255) DEFAULT NULL,
  patchlevel 		VARCHAR(255) DEFAULT NULL,
  content 		LONGBLOB DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_packages_dependencies (
  seqnr 		int(11) NOT NULL auto_increment,
  seqref		VARCHAR(255) DEFAULT NULL,
  domain_name 		VARCHAR(255) DEFAULT NULL,
  distribution 		VARCHAR(255) DEFAULT NULL,
  patchlevel 		VARCHAR(255) DEFAULT NULL,
  aerequires            VARCHAR(255) DEFAULT NULL,
  aeprovides            VARCHAR(255) DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_patchlevel (
  seqnr 		int(11) NOT NULL auto_increment,
  distribution 		VARCHAR(255) DEFAULT NULL,
  patchlevel 		VARCHAR(255) DEFAULT NULL,
  status 		VARCHAR(255) DEFAULT NULL,
  log 			BLOB DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_monitor_configuration (
  seqnr 		int(11) NOT NULL auto_increment,
  system_name 		VARCHAR(255) DEFAULT NULL,
  monitorname 		VARCHAR(255) DEFAULT NULL,
  monitortype 		VARCHAR(255) DEFAULT NULL,
  monitorvalue 		VARCHAR(255) DEFAULT NULL,
  status 		VARCHAR(255) DEFAULT NULL,
  schedule 		VARCHAR(255) DEFAULT NULL,
  message 		VARCHAR(255) DEFAULT NULL,
  sendemail 		VARCHAR(255) DEFAULT NULL,
  runprogram		VARCHAR(255) DEFAULT NULL,
  severity		VARCHAR(255) DEFAULT NULL,
  alarmlimit		NUMERIC(5) DEFAULT NULL,
  errorlimit		NUMERIC(5) DEFAULT NULL,
  searchpattern		VARCHAR(255) DEFAULT NULL,
  program		VARCHAR(255) DEFAULT NULL,
  programargs		VARCHAR(255) DEFAULT NULL,
  warninglimit		NUMERIC(3) DEFAULT NULL,
  criticallimit		NUMERIC(3) DEFAULT NULL,
  fatallimit		NUMERIC(3) DEFAULT NULL,
  override		VARCHAR(255) DEFAULT NULL,
  underchange		VARCHAR(255) DEFAULT NULL,
  exitstatus		VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_managementlog (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  messagetype           VARCHAR(255) DEFAULT NULL,
  logentry              BLOB DEFAULT NULL,
  runid                 VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_roles (
  seqnr 		INT(11) NOT NULL auto_increment,
  rolename 		VARCHAR(255) DEFAULT NULL,
  system_name 		VARCHAR(255) DEFAULT NULL,
  description 		VARCHAR(255) DEFAULT NULL,
  permission 		VARCHAR(255) DEFAULT NULL,
  status 		VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_base_agentconfiguration (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name 		VARCHAR(255) DEFAULT NULL,
  parametername		VARCHAR(255) DEFAULT NULL,
  parametervalue	VARCHAR(255) DEFAULT NULL,
  override		VARCHAR(255) DEFAULT NULL,
  underchange		VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_files (
  seqnr 		INT(11) NOT NULL auto_increment,
  filename 		VARCHAR(255) DEFAULT NULL,
  directory		VARCHAR(255) DEFAULT NULL,
  location 		BLOB DEFAULT NULL,
  content 		LONGBLOB DEFAULT NULL,
  domain_name           VARCHAR(255) DEFAULT NULL,
  distribution 		VARCHAR(255) DEFAULT NULL,
  revision 		VARCHAR(255) DEFAULT NULL,
  fileindex 		VARCHAR(255) DEFAULT NULL,
  tagging 		BLOB DEFAULT NULL,
  prestep 		BLOB DEFAULT NULL,
  poststep 		BLOB DEFAULT NULL,
  perm_owner 		VARCHAR(255) DEFAULT NULL,
  perm_group 		VARCHAR(255) DEFAULT NULL,
  perms 		VARCHAR(255) DEFAULT NULL,
  filelocked 		VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  			PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_core_configuration (
  seqnr                 INT(11) NOT NULL auto_increment,
  parametername         VARCHAR(255) DEFAULT NULL,
  parametervalue        VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_monitor_status (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  monitorname           VARCHAR(255) DEFAULT NULL,
  monitortype           VARCHAR(255) DEFAULT NULL,
  monitorstatus         VARCHAR(255) DEFAULT NULL,
  monitormessage        BLOB DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  number_ok             VARCHAR(255) DEFAULT NULL,
  lastdate_ok           VARCHAR(255) DEFAULT NULL,
  lasttime_ok           VARCHAR(255) DEFAULT NULL,
  number_notok          VARCHAR(255) DEFAULT NULL,
  lastdate_notok        VARCHAR(255) DEFAULT NULL,
  lasttime_notok        VARCHAR(255) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_rolepermissions (
  seqnr                 INT(11) NOT NULL auto_increment,
  rolename		VARCHAR(255) DEFAULT NULL,
  roleobject		VARCHAR(255) DEFAULT NULL,
  objecttype		VARCHAR(255) DEFAULT NULL,
  role_permission	INT(1) DEFAULT NULL,
  domain_name		VARCHAR(255) DEFAULT NULL,
  distribution		VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_provisioning (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name		VARCHAR(255) DEFAULT NULL,
  macadress		VARCHAR(255) DEFAULT NULL,
  pxefilename           VARCHAR(255) DEFAULT NULL,
  serialnumber		VARCHAR(255) DEFAULT NULL,
  autoinstfile          LONGTEXT DEFAULT NULL,
  pxefile               BLOB DEFAULT NULL,
  revision		VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_provisioning_checkin (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name		VARCHAR(255) DEFAULT NULL,
  macaddress		VARCHAR(255) DEFAULT NULL,
  serialnumber		VARCHAR(255) DEFAULT NULL,
  status		VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
  lastcheckin		DATETIME     DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_front_configuration (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name		VARCHAR(255) DEFAULT NULL,
  system_address	VARCHAR(255) DEFAULT NULL,
  system_port		VARCHAR(255) DEFAULT NULL,
  front_description	VARCHAR(255) DEFAULT NULL,
  front_ver_system	VARCHAR(255) DEFAULT NULL,
  status		VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_managementobject_status (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  management_obj        VARCHAR(255) DEFAULT NULL,
  status                VARCHAR(255) DEFAULT NULL,
  management_log        VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
insert into andutteye_users(andutteye_username,andutteye_password,is_admin) values ('admin','d18ee3149a4b14914e5d07bd565fa6f87d7c7ab6','1');
insert into andutteye_core_configuration(parametername,parametervalue) values ('Management_top_directory_location','/var/cache/andutteye/management-repo');
insert into andutteye_core_configuration(parametername,parametervalue) values ('Management_pxe_directory_location','/var/cache/andutteye/pxe-profiles');
insert into andutteye_core_configuration(parametername,parametervalue) values ('Management_autoinstall_directory_location','/var/cache/andutteye/autoinstall-profiles');
