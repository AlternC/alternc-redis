<?php
/*
 ----------------------------------------------------------------------
 AlternC - Web Hosting System
 Copyright (C) 2000-2022 by the AlternC Development Team.
 https://alternc.org/
 ----------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
 ----------------------------------------------------------------------
 Purpose of file: Manages the Redis server for a user 
 ----------------------------------------------------------------------
*/

/**
 * Validate and create the redis server
 * 
 * @copyright AlternC-Team 2024 https://alternc.com/
 */

require_once("../class/config.php");

$fields = array (
	"maxmemory"    		=> array ("post", "integer", 16),
	"save"    		=> array ("post", "save", 0),
);
getFields($fields);

if ($maxmemory < 16 || $maxmemory > $quota->getquota("redis"))
	$msg->raise("ERROR", "redis", _("Max Memory must be between 16MB and your maximum allowed. "));
	include("redis_add.php");
	exit();
}

// Attemp to create, exit if fail
if ($redis->create($maxmemory,$save)) {
	include ("redis.php");
	exit;
}

$msg->raise("ERROR", "redis", _("I was not able to create your redis server now. Please try later"));

include("main.php");


