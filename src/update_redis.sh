#!/usr/bin/php -q
<?php

/*
  This script look in the database wich redis server should be CREATED / DELETED
  it can be launched every 5 minutes (with a lock)
*/

if (posix_getuid()!=0) {
    echo "FATAL: this crontab MUST be launched as root, since it's creating / deleting files for Redis.\n";
    exit();
}

require("/usr/share/alternc/panel/class/config_nochk.php");
$admin->enabled=1;

$redis->cron_update();

