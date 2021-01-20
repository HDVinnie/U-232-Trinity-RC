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
$categorie = genrelist();
foreach ($categorie as $key => $value) $change[$value['id']] = array(
    'id' => $value['id'],
    'name' => $value['name'],
    'image' => $value['image']
);
if (($top10music_24 = $cache->get('top10_music_24_')) === false) {
    $tortime24music = $_SERVER['REQUEST_TIME'] - 86400;
    $res_music24 = sql_query("SELECT id, times_completed, seeders, poster, leechers, name, category from torrents WHERE last_action >= {$tortime24music}  AND category IN (".join(", ",$TRINITY20['music_cats']).") ORDER BY seeders + leechers DESC LIMIT {$TRINITY20['latest_torrents_limit']}") or sqlerr(__FILE__, __LINE__);
    while ($top10music24 = mysqli_fetch_assoc($res_music24)) 
		$top10music_24[] = $top10music24;
    $cache->set('top10_music_24_', $top10music_24);
}
    $HTMLOUT.= "<table class='stack'>
            <thead><tr>
            <th scope='col'><b>*</b></th>
            <th scope='col'><b>Top 10 torrents in 24 hours in Music</b></th>
			<th scope='col'><i class='fas fa-check'></i></th>
            <th scope='col'><i class='fas fa-arrow-up'></i></th>
            <th scope='col'><i class='fas fa-arrow-down'></i></th></tr></thead>";
	if ($top10music_24) {
		$counter = 1;
        foreach ($top10music_24 as $top10music_24w_arr) {
            $top10music_24w_arr['cat_name'] = htmlsafechars($change[$top10music_24w_arr['category']]['name']);
	    $top10music_24w_arr['cat_pic'] = htmlsafechars($change[$top10music_24w_arr['category']]['image']);
            $torrname = htmlsafechars($top10music_24w_arr['name']);
            if (strlen($torrname) > 50) 
				$torrname = substr($torrname, 0, 50) . "...";
            $HTMLOUT.= "
            <tbody><tr>
            <th scope='row'>". $counter++ ."</th>
            <td><a href=\"{$TRINITY20['baseurl']}/details.php?id=" . (int)$top10music_24w_arr['id'] . "&amp;hit=1\">{$torrname}</a></td>
			<td>" . (int)$top10music_24w_arr['times_completed'] . "</td>
          <td>" . (int)$top10music_24w_arr['seeders'] . "</td>
          <td>" . (int)$top10music_24w_arr['leechers'] . "</td>     
	 </tr></tbody>";
        }
    } else {
        //== If there are no torrents
        if (empty($top10music_24)) $HTMLOUT.= "<tbody><tr><td>{$lang['top5torrents_no_torrents']}</td></tr></tbody>";
    }
$HTMLOUT.= "</table>";
//==End	
// End Class
// End File