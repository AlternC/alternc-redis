PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
# Every 5 minutes, do redis actions
*/5 * * * *	   root		/usr/lib/alternc/update_redis.sh
