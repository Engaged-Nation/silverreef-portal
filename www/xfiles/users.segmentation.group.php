<?php
defined('_init') or die();

# set file
$filename = 'https://s3.amazonaws.com/uploads.hipchat.com/83344/601097/l26j7VX6ViTQ1hb/MRC-Erin-Import-Resegment-Groups-12-12-2016.csv';
$file = explode(PHP_EOL, file_get_contents($filename));

if(empty($file)){
    return;
}

# set total
$total = count($file);

for($i = 0; $i < $max; $i++){
    if(isset($file[$counter])){
        $parts = explode(',', $file[$counter]);

        if(isset($parts[0]) && isset($parts[1])) {
            $uid = trim($parts[0]);
            $grp = trim($parts[1]);

            # get their details
            $a = 'SELECT id FROM #__users WHERE id = :uid';
            $b = array('uid' => $uid);
            $chk = $db->_query($dbc, 'searchOne', $a, $b);

            if($chk){
                $a = 'UPDATE #__users SET grp = :grp, grp_lastupd = :date WHERE cid = :cid AND id = :uid';
                $b = array('cid' => 1, 'uid' => $uid, 'grp' => $grp, 'date' => $date);
                $insert = $db->_query($dbc, 'insert', $a, $b);

                if($insert){
                    $opt['body'] .= $uid.' - completed';
                    $opt['body'] .= '<br/>';
                }
                else{
                    $opt['body'] .= $uid.' - incomplete';
                    $opt['body'] .= '<br/>';
                }
            }

        }
    }

    #
    # no need to mod below
    #
    $data = $counter;

    if($counter > $total){
        break;
    }

    $counter++;
}