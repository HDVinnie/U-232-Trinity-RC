<?php
/**
 * -------   U-232 Codename Trinity   ----------*
 * ---------------------------------------------*
 * --------  @authors U-232 Team  --------------*
 * ---------------------------------------------*
 * -----  @site https://u-232.duckdns.org/  ----*
 * ---------------------------------------------*
 * -----  @copyright 2020 U-232 Team  ----------*
 * ---------------------------------------------*
 * ------------  @version V6  ------------------*
 */
function docleanup($data)
{
    global $TRINITY20, $queries, $cache;
    set_time_limit(1200);
    ignore_user_abort(1);
    sql_query("UPDATE `freeslots` SET `addedup` = 0 WHERE `addedup` != 0 AND `addedup` < " . TIME_NOW) or sqlerr(__FILE__, __LINE__);
    sql_query("UPDATE `freeslots` SET `addedfree` = 0 WHERE `addedfree` != 0 AND `addedfree` < " . TIME_NOW) or sqlerr(__FILE__, __LINE__);
    sql_query("DELETE FROM `freeslots` WHERE `addedup` = 0 AND `addedfree` = 0") or sqlerr(__FILE__, __LINE__);
    if ($queries > 0) write_log("Freeslot Clean -------------------- Freeslot Clean Complete using $queries queries--------------------");
    if (false !== mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        $data['clean_desc'] = mysqli_affected_rows($GLOBALS["___mysqli_ston"]) . " items deleted/updated";
    }
    if ($data['clean_log']) {
        cleanup_log($data);
    }
}
?>
