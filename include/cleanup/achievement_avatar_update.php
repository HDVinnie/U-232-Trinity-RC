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
    set_time_limit(0);
    ignore_user_abort(1);
    // Updated Avatar Setter Achievement
    $res = sql_query("SELECT id, avatarset FROM usersachiev WHERE avatarset = '1' AND avatarach = '0'") or sqlerr(__FILE__, __LINE__);
    $msg_buffer = $usersachiev_buffer = $achievements_buffer = array();
    if (mysqli_num_rows($res) > 0) {
        $subject = sqlesc("New Achievement Earned!");
        $msg = sqlesc("Congratulations, you have just earned the [b]Avatar Setter[/b] achievement. :) [img]".$TRINITY20['baseurl']."/pic/achievements/piratesheep.png[/img]");
        while ($arr = mysqli_fetch_assoc($res)) {
            $dt = TIME_NOW;
            $points = rand(1, 3);
            $msgs_buffer[] = '(0,' . $arr['id'] . ',' . TIME_NOW . ', ' . sqlesc($msg) . ', ' . sqlesc($subject) . ')';
            $achievements_buffer[] = '(' . $arr['id'] . ', ' . TIME_NOW . ', \'Avatar Setter\', \'piratesheep.png\' , \'User has successfully set an avatar on profile settings.\')';
            $usersachiev_buffer[] = '(' . $arr['id'] . ',1, ' . $points . ')';
            $cache->delete('inbox_new_' . $arr['id']);
            $cache->delete('inbox_new_sb_' . $arr['id']);
            $cache->delete('user_achievement_points_' . $arr['id']);
        }
        $count = count($achievements_buffer);
        if ($count > 0) {
            sql_query("INSERT INTO messages (sender,receiver,added,msg,subject) VALUES " . implode(', ', $msgs_buffer)) or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO achievements (userid, date, achievement, icon, description) VALUES " . implode(', ', $achievements_buffer) . " ON DUPLICATE key UPDATE date=values(date),achievement=values(achievement),icon=values(icon),description=values(description)") or sqlerr(__FILE__, __LINE__);
            sql_query("INSERT INTO usersachiev (id, avatarach, achpoints) VALUES " . implode(', ', $usersachiev_buffer) . " ON DUPLICATE key UPDATE avatarach=values(avatarach), achpoints=achpoints+values(achpoints)") or sqlerr(__FILE__, __LINE__);
            if ($queries > 0) write_log("Achievements Cleanup: Achievements Avatar Setter Completed using $queries queries. Avatar Achievements awarded to - " . $count . " Member(s)");
        }
        unset($usersachiev_buffer, $achievement_buffer, $msgs_buffer, $count);
    }
    if (false !== mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        $data['clean_desc'] = mysqli_affected_rows($GLOBALS["___mysqli_ston"]) . " items updated";
    }
    if ($data['clean_log']) {
        cleanup_log($data);
    }
}

?>
