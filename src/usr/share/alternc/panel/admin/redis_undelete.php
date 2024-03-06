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

require_once("../class/config.php");
include_once("head.php");

if (!$redis->get_server()) {
    require_once("main.php");
    exit();
}

// Attemp to delete, exit if fail
if ($redis->undelete()) {
	include ("redis.php");
	exit;
}

$msg->raise("ERROR", "redis", _("I was not able to cancel the deletion of your redis server now. It may be too late."));

include("main.php");
