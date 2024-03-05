

CREATE TABLE IF NOT EXISTS `redis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `maxmemory` int(10) NOT NULL DEFAULT 128, -- maximum memory of this redis instance, in MB
  `save` int(10) NOT NULL DEFAULT 0, -- shall we save the data in this redis ? 0=no, 1=yes at least once every hour 2=yes at least once every 10min
  `redis_action` enum('OK','CREATE','DELETE', 'DELETING', 'REGENERATE') NOT NULL DEFAULT 'CREATE',
  `redis_result` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) DEFAULT CHARSET=utf8mb4 COMMENT='Redis server for users';


CREATE TABLE IF NOT EXISTS alternc_status (name VARCHAR(48) NOT NULL DEFAULT '',value LONGTEXT NOT NULL,PRIMARY KEY (name),KEY name (name) ) DEFAULT CHARSET=latin1;

INSERT IGNORE INTO alternc_status SET name='alternc-redis_version',value='1.0.sql';

