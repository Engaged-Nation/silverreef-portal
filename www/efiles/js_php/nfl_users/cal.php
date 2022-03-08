<?php
# extend script life time
set_time_limit(0);

# set timezone
date_default_timezone_set('America/Los_Angeles');

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

# include framework
include root . DS . 'lib/functions/global.php';

$allowed = array('24.234.137.217', '172.18.0.1');

if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $remoteIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
    $remoteIp = $_SERVER["REMOTE_ADDR"];
}

if(!in_array($remoteIp, $allowed)){
    die('What does the fox say?');
}

$pgl = microtime();
$pgl = explode(' ', $pgl);
$spgl = $pgl[1] + $pgl[0];

$x = intval($_GET['x']);
$t = intval($_GET['t']);
$counter = intval($_GET['count']);
$week = intval($_GET['week']);
$year = intval($_GET['year']);

$min = $x;
$max = $t;
$data = false;
$count = 0;

if(isset($_GET['week']) && isset($_GET['year']) && !empty($_GET['week']) && !empty($_GET['year'])){
    $a = 'SELECT id FROM #__nfl_picks_'.$year.' WHERE week = :week ORDER BY id ASC LIMIT '.$min.', '.$max;
    $b = array('week' => $week);
    $getUsers = $db->_query($dbc, 'searchAll', $a, $b);

    $countUsers = count($getUsers);

    if($getUsers && $countUsers > 0){
        for($i = 0; $i < $countUsers; $i++){
            $a = 'SELECT teamId, teamName FROM #__nfl_picks_data_'.$year.' WHERE pickId = :pickId';
            $b = array('pickId' => $getUsers[$i]->id);
            $getTeams = $db->_query($dbc, 'searchAll', $a, $b);

            if($getTeams){
                $totalScore = 0;
                for($i2 = 0; $i2 < count($getTeams); $i2++){
                    $a = 'SELECT winner FROM #__nfl_req_season_games_'.$year.' WHERE id = :id';
                    $b = array('id' => $getTeams[$i2]->teamId);
                    $getScore = $db->_query($dbc, 'searchOne', $a, $b);

                    if($getScore){
                        if(!empty($getScore->winner)){
                            if($getTeams[$i2]->teamName == $getScore->winner){
                                $totalScore += 1;
                            }
                        }
                    }
                }

                if($totalScore > 0){
                    # currently set to tmp table
                    $a = 'UPDATE #__nfl_picks_'.$year.' SET wscore = :wscore WHERE id = :id';
                    $b = array('id' => $getUsers[$i]->id, 'wscore' => $totalScore);
                    $update = $db->_query($dbc, 'update', $a, $b);

                    if($update){
                        $count++;
                    }
                    else{
                        # break loop - also in front end
                        $count = 0;
                        break;
                    }
                }
            }

            $counter++;
        }
    }
}

if($count > 0){
    $opt['next'] = $min.' / '.$count.' / '.$counter;
}

# clear data
unset($dbc);

$pgl = microtime();
$pgl = explode(' ', $pgl);
$pgl = $pgl[1] + $pgl[0];
$epgl = $pgl;
$pgl = round(($epgl - $spgl), 4);

$opt['pgl'] = ', '.$pgl;
$opt['x'] = $counter;
$opt['t'] = $max;
$opt['counter'] = $counter;
echo json_encode($opt);
