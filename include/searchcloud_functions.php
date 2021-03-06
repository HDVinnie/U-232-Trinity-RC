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
function searchcloud($limit = 50)
{
    global $cache, $TRINITY20;
    if (!($return = $cache->get('searchcloud'))) {
        $search_q = sql_query('SELECT searchedfor,howmuch FROM searchcloud ORDER BY id DESC ' . ($limit > 0 ? 'LIMIT ' . $limit : '')) or sqlerr(__FILE__, __LINE__);
        if ($search_q->num_rows) {
            $return = array();
            while ($search_a = $search_q->fetch_assoc()) $return[$search_a['searchedfor']] = $search_a['howmuch'];
            ksort($return);
            $cache->set('searchcloud', $return, 0);
            return $return;
        }
        return array();
    }
    ksort($return);
    return $return;
}
function searchcloud_insert($word)
{
    global $cache, $TRINITY20;
    $searchcloud = searchcloud();
    $ip = getip();
    $howmuch = isset($searchcloud[$word]) ? $searchcloud[$word] + 1 : 1;
    if (!count($searchcloud) || !isset($searchcloud[$word])) {
        $searchcloud[$word] = $howmuch;
        $cache->set('searchcloud', $searchcloud, 0);
    } else {
        $cache->update_row('searchcloud', [
            $word => $howmuch
        ], 0);
    }
    sql_query('INSERT INTO searchcloud(searchedfor,howmuch,ip) VALUES (' . sqlesc($word) . ',1,' . sqlesc($ip) . ') ON DUPLICATE KEY UPDATE howmuch=howmuch+1') or sqlerr(__FILE__, __LINE__);
}
function cloud()
{
    //min / max font sizes
    $small = 10;
    $big = 35;
    //get tag info from worker function
    $tags = searchcloud();
    //amounts
    if (isset($tags)) {
        if(!empty($tags)) $minimum_count = min(array_values($tags));
        if(!empty($tags)) $maximum_count = max(array_values($tags));
        $spread = $maximum_count - $minimum_count;
        if ($spread == 0) {
            $spread = 1;
        }
        $cloud_html = '';
        $cloud_tags = array();
        foreach ($tags as $tag => $count) {
            $size = $small + ($count - $minimum_count) * ($big - $small) / $spread;
            //set up colour array for font colours.
            $colour_array = array(
                'yellow',
                'green',
                'blue',
                'purple',
                'orange',
                '#0099FF'
            );
            //spew out some html malarky!
            $cloud_tags[] = '<a style="color:' . $colour_array[mt_rand(0, 5) ] . '; font-size: ' . floor($size) . 'px' . '" class="tag_cloud" href="browse.php?search=' . urlencode($tag) . '&amp;searchin=all&amp;incldead=1' . '" title="\'' . htmlsafechars($tag) . '\' returned a count of ' . $count . '">' . htmlsafechars(stripslashes($tag)) . '</a>';
        }
        $cloud_html = join("\n", $cloud_tags) . "\n";
        return $cloud_html;
    }
}
?>