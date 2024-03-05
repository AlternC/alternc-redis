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

if($r=$redis->get_server() || !$quota->cancreate("redis")) {
    require_once("main.php");
    exit();
}

?>

    <h2><?php __("Redis server"); ?></h2>
<hr id="topbar"/>
<br />
 <?php 
    echo $msg->msg_html_all();
?>
        <p><?php __("Enter the settings for your Redis server. You'll get the socket path in return."); ?></p>

            <form method="POST" action="redis_doadd.php">       
<table class="tlist">
     <tr><th><?php __("Max Memory (in MB)"); ?></th><td class="lst1">	<input type="text" class="int" name="maxmemory" id="maxmemory" size="8" maxlength="8" value="<?php echo $quota->getquota("redis"); ?>" /> <small><?php sprintf(_("(max allowed %s MB)"),$quota->getquota("redis")); ?></small> </td></tr>
<tr><th><?php __("Save data on disk?"); ?></th><td class="lst2">
     <select name="save" id="save" class="int">
     <?php
     for($i=0;$i<=2;$i++) {
         echo "<option value=\"$i\"";
         echo ">"._("redis_save_".$i)."</option>";
     }
?>
     </select>
     </td></tr>
         <tr><td colspan="2">
     <input type="submit" class="inb ok" name="submit" value="<?php __("Start a Redis server"); ?>" />
     </td></tr>
</table>
</form>
<?php
                                                                                                             }
include_once("foot.php");


