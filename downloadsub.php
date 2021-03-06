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
//made by putyn @tbdev
require_once (__DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'bittorrent.php');
require_once (INCL_DIR . 'phpzip.php');
dbconn();
loggedinorreturn();
$lang = array_merge(load_language('global'), load_language('subtitles'));

$action = (isset($_POST["action"]) ? htmlsafechars($_POST["action"]) : "");
if ($action == "download") {
    $id = isset($_POST["sid"]) ? (int) $_POST["sid"] : 0;
    if ($id == 0) stderr($lang['gl_error'], $lang['gl_not_a_valid_id']);
    else {
        $res = sql_query("SELECT id, name, filename FROM subtitles WHERE id=".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
        $arr = $res->fetch_assoc();
        $ext = (substr($arr["filename"], -3));
        $fileName = str_replace(array(
            " ",
            ".",
            "-"
        ) , "_", $arr["name"]) . '.' . $ext;
        $file = $TRINITY20['sub_up_dir'] . "/" . $arr["filename"];
        $fileContent = file_get_contents($file);
        $newFile = fopen("{$TRINITY20['sub_up_dir']}/$fileName", "w");
        @fwrite($newFile, $fileContent);
        @fclose($newFile);
        $file = array();
        $zip = new PHPZip();
        $file[] = "{$TRINITY20['sub_up_dir']}/$fileName";
        $fName = "{$TRINITY20['sub_up_dir']}/" . str_replace(array(
            " ",
            ".",
            "-"
        ) , "_", $arr["name"]) . ".zip";
        $zip->Zip($file, $fName);
        $zip->forceDownload($fName);
        @unlink($fName);
        @unlink("{$TRINITY20['sub_up_dir']}/$fileName");
        sql_query("UPDATE subtitles SET hits=hits+1 where id=".sqlesc($id));
    }
} else stderr($lang['gl_error'], $lang['gl_no_way']);
?>
