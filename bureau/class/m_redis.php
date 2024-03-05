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
 Purpose of file: Manage Redis servers for users
 ----------------------------------------------------------------------
*/

class m_redis {
    
    /* ----------------------------------------------------------------- */
    /** 
     * Hook called by AlternC to tell which main menu element need adding for this module.
     */ 
    function hook_menu() {
        $obj = array(
            'title'       => _("Redis service"),
            'ico'         => 'images/redis.png',
            'link'        => 'redis.php',
            'pos'         => 70,
        ) ;

        return $obj;
    }

    /* this function is used to give gettext hints of strings that are dynamically used and need translation :) */
    private function _dynamic_translation() {
        [_("redis_status_OK"), _("redis_status_DELETE"), _("redis_status_DELETING"), _("redis_status_CREATE"), _("redis_status_REGENERATE")];
        [_("redis_save_0"), _("redis_save_1"), _("redis_save_2")];
    }

    
    /* ----------------------------------------------------------------- */
    /** Return the status of the currently launched server.
     * @return array a hash with all information, or false if no server exists
     */
    function get_server() {
        global $db,$cuid,$me;
        $db->query("SELECT * FROM redis WHERE uid='$cuid';");
        if (!$db->next_record()) return false;
        $r=$db->Record;
        $r["path"]="/run/redis-sock/".$me["login"].".sock";
        return $r;
    }

    
    /* ----------------------------------------------------------------- */
    /** Create the redis server for a user
     * @param $maxmemory integer the memory limit for this redis, in MB (between 16M & the allowed quota)
     * @param $save integer the save mode (0 / 1 / 2)
     * @return boolean TRUE if the redis has been marked for creation, or FALSE if an error occured
     */
    function create($maxmemory,$save) {
        global $db,$cuid,$msg,$quota;
        $msg->log("redis","create",$maxmemory." ".$save);

        $db->query("SELECT * FROM redis WHERE uid='$cuid';");
        if ($db->next_record()) { 
            $msg->raise("ERROR","redis",_("A redis server already exist for your AlternC account"));
            return false;
        }
        
        $db->query("INSERT INTO redis SET uid=$cuid, maxmemory=".$maxmemory.", save=".$save.", redis_action='CREATE';");
        
        return true;
    }



    /* ----------------------------------------------------------------- */
    /** Mark the redis server for deletion for a user
     * @param $uid integer which user to delete, use the current user if not specified
     * @return boolean TRUE if the redis has been marked for deletion, or FALSE if an error occured
     */
    function delete($uid=0) {
        global $db,$msg,$cuid,$quota;
        if (!$uid) $uid=$cuid;
        $msg->log("redis","delete",$uid);

        $db->query("SELECT * FROM redis WHERE uid='$cuid';");
        if (!$db->next_record()) { 
            $msg->raise("ERROR","redis",_("No redis server exists for your AlternC account"));
            return false;
        }
        
        $db->query("UPDATE redis SET redis_action='DELETE' WHERE uid=$cuid AND redis_action IN ('OK','CREATE');");
        
        return true;
    }


    /* ----------------------------------------------------------------- */
    /** Cancel the deletion of the redis server
     * @return boolean TRUE if the redis has been marked for undeletion, or FALSE if an error occured
     */
    function undelete() {
        global $db,$msg,$cuid,$quota;
        $msg->log("redis","undelete",$cuid);

        $db->query("SELECT * FROM redis WHERE uid='$cuid';");
        if (!$db->next_record()) { 
            $msg->raise("ERROR","redis",_("No redis server exists for your AlternC account"));
            return false;
        }
        
        $db->query("UPDATE redis SET redis_action='OK' WHERE uid=$cuid AND redis_action='DELETE';");
        
        return true;
    }


    /* ----------------------------------------------------------------- */
    /** This function is launched by a crontab 
     * and is in charge of effectively create or delete the pending redis servers
     * MUST be launched as root of course
     */
    private function cron_update() {
        global $db;
        putenv("PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin");

        $db->query("SELECT * FROM redis WHERE redis_action='CREATE';");
        while ($db->next_record()) {
            syslog(LOG_INFO,"Creating Redis server in AlternC for user ".$db->Record["uid"]);
            $out=[];
            exec("systemctl enable --now redis-sock@".$user."  2>&1",$out,$res);
            if ($res!=0) {
                syslog(LOG_ERR,"Can't start redis server. Output was\n".implode("\n",$out));
                $db->query("UPDATE redis SET redis_action='ERROR' WHERE id=".$db->Record["id"]);
            } else {
                syslog(LOG_INFO,"Redis started");
                $db->query("UPDATE redis SET redis_action='OK' WHERE id=".$db->Record["id"]);
            }            
        }

        $db->query("SELECT * FROM redis WHERE redis_action='DELETE';");
        while ($db->next_record()) {
            syslog(LOG_INFO,"Creating Redis server in AlternC for user ".$db->Record["uid"]);
            $out=[];
            exec("systemctl enable --now redis-sock@".$user."  2>&1",$out,$res);
            if ($res!=0) {
                syslog(LOG_ERR,"Can't start redis server. Output was\n".implode("\n",$out)); 
            } else {
                syslog(LOG_INFO,"Redis started"); 
            }            
            $db->query("DELETE FROM redis WHERE id=".$db->Record["id"]);
        }
        
    }

    

    /* ----------------------------------------------------------------- */
    /** Quota name
     */
    function hook_quota_names() {
        return array("redis"=>_("Redis Server"));
    }


    /* ----------------------------------------------------------------- */
    /** 
     * Hook function called when a user is deleted.
     * AlternC's standard function that delete a member
     * @access private
     */
    function alternc_del_member() {
        global $msg,$db;
        $msg->log("redis", "alternc_del_member");
        // if there is a server, delete it
        $db->query("SELECT * FROM redis WHERE uid='$cuid';");
        if ($db->next_record()) {
            $this->delete();
        }
        return true;
    }

    
    /* ----------------------------------------------------------------- */
    /** Returns the quota for the current account as an array
     * @return array an array with used (key 'u') and totally available (key 't') quota for the current account.
     * or FALSE if an error occured
     * @access private
     */ 
    function hook_quota_get() {
        global $msg,$cuid,$db;        
        $msg->log("redis","getquota");
        $q=Array("name"=>"redis", "description"=>_("Redis server"), "used"=>0);
        $db->query("SELECT SUM(maxmemory) AS cnt FROM redis WHERE uid='$cuid';");
        if ($db->next_record()) {
            $q['used']=($db->f("cnt")!=0);
        }
        return $q;
    }


} /* Class m_redis */

