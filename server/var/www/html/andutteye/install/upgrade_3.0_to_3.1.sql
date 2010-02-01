CREATE TABLE andutteye_provisioning (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  macadress             VARCHAR(255) DEFAULT NULL,
  serialnumber          VARCHAR(255) DEFAULT NULL,
  autoinstfile          BLOB DEFAULT NULL,
  pxefile               BLOB DEFAULT NULL,
  revision              VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_provisioning_checkin (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  macaddress            VARCHAR(255) DEFAULT NULL,
  serialnumber          VARCHAR(255) DEFAULT NULL,
  status                VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
CREATE TABLE andutteye_front_configuration (
  seqnr                 INT(11) NOT NULL auto_increment,
  system_name           VARCHAR(255) DEFAULT NULL,
  system_address        VARCHAR(255) DEFAULT NULL,
  system_port           VARCHAR(255) DEFAULT NULL,
  front_description     VARCHAR(255) DEFAULT NULL,
  front_ver_system      VARCHAR(255) DEFAULT NULL,
  status                VARCHAR(255) DEFAULT NULL,
  created_date          VARCHAR(100) DEFAULT NULL,
  created_time          VARCHAR(100) DEFAULT NULL,
  created_by            VARCHAR(100) DEFAULT NULL,
                        PRIMARY KEY  (seqnr)
);
insert into andutteye_core_configuration(parametername,parametervalue) values ('Management_pxe_directory_location','/var/cache/andutteye/pxe-profiles');
insert into andutteye_core_configuration(parametername,parametervalue) values ('Management_autoinstall_directory_location','/var/cache/andutteye/autoinstall-profiles');
alter table andutteye_packages add column patchlevelinfo varchar(255) default null;
alter table andutteye_rolepermissions add column distribution varchar(255) default null;
alter table andutteye_rolepermissions add column domain_name varchar(255) default null;
alter table andutteye_files add column filelocked varchar(255) default null;
alter table andutteye_specifications add column pkgmanagementtype varchar(255) default null;
