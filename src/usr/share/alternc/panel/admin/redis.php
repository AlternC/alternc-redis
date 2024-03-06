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

if(!$r=$redis->get_server()) {
  if ($quota->cancreate("redis")) {
    require_once("redis_add.php"); 
    exit();
  } else {
    require_once("main.php");
    exit();
  }
} else {
	?>
 <?php 
    echo $msg->msg_html_all();
    
?>

<table class="tedit">
        <tr>
    <th colspan="2" style='text-align:center;'><?php echo '<h1>'._("Redis server settings").'</h1>'; ?></th>
        </tr>

<tr><th><?php __("Redis server status"); ?></th><td class="lst1"><?php __("redis_status_".$r["redis_action"]); ?></td></tr>
<tr><th><?php __("Redis Socket Path"); ?></th><td class="lst2"><code><?php echo $r["path"]; ?></code></td></tr>
<tr><th><?php __("Max Memory (in MB)"); ?></th><td class="lst1"><?php echo $r["maxmemory"]; ?></td></tr>
<tr><th><?php __("Data Saved?"); ?></th><td class="lst2"><?php __("redis_save_".intval($r["save"])); ?></td></tr>

</table>

<p>
<?php

switch ( $r["redis_action"]) {
    case "OK":
    case "ERROR":
        echo "<a class=\"ina\" onclick=\"return confirm('"._("Please confirm that you want your Redis server stopped and deleted")."')\" href=\"redis_delete.php\">"._("Shutdown and delete")."</a>";
        break;
    case "DELETE":
        echo "<a class=\"ina\" href=\"redis_undelete.php\">"._("Cancel deletion")."</a>";
        break;
}
?>
                                 </p>
<?php

    }

include_once("foot.php");

