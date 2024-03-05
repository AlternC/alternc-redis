
Redis module for AlternC
========================

This package is an extension for [AlternC hosting software control panel](https://alternc.com) to manage Redis servers that uses Unix Socket, so that users can get their own redis server running without any security issue (since only this AlternC's user can access the redis via socket)

The servers are launched / stopped automatically using a systemd template service provided by this package.
We don't use the /lib/systemd/system/redis-server@.service provided by debian since it only allows you to start services launched by the redis user. 

The only parameters in Redis we currently manage are the max_memory in MB (the max memory can't be greater than the quota) and the save or not save setting: if save is 0 (the default), the data in this redis are never saved to disk. If save is 1, the data are saved once every hour if any keys has changed. If save is 2, the data are saved once every 10min if any keys has changed. 


Installation
============

This module depends on redis-server, which will be installed as a debian package during the install process. The redis-server service will be disabled once during alternc.install launch.

Once alternc-redis is installed, you should change the value from zero to non-zero for the "redis" quotas of an AlternC account. This will allow this account to use its own redis server with a maximum memory of the quotas, in MB. We advise you to use a quota of at least 128 (meaning 128MB of RAM available to this user).


If you want to use the redis-server on the usual port (6379), you should reenable it yourself with `systemctl enable --now redis-server` Although we advise you not to start that default redis-server since any alternc account could read and write any data on your redis server by default. 

