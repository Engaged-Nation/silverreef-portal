<?php
defined('_init') or die();

# set file
$filename = $enConfigClient['environment']['s3_uploads_url'].'/tmp-files/opt-email.csv';
$file = explode(PHP_EOL, file_get_contents($filename));

if(empty($file)){
    return;
}

# set total
$total = count($file);

for($i = 0; $i < $max; $i++){
    if(isset($file[$counter])){
        $parts = explode(',', $file[$counter]);

        $uid = trim($parts[0]);
        $optin = trim($parts[1]);

        # get their details
        $a = 'SELECT id FROM #__users WHERE id = :uid';
        $b = array('uid' => $uid);
        $chk = $db->_query($dbc, 'searchOne', $a, $b);

        if($chk){
            $a = 'UPDATE #__users SET optin_email = :optin_email WHERE id = :uid';
            $b = array('uid' => $uid, 'optin_email' => $optin);
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
        else{
            $opt['body'] .= $uid.' - uid not found';
            $opt['body'] .= '<br/>';
        }

    }

    #
    # no need to mod below
    #
    $data = $counter;
    $counter++;

    if($counter > $total){
        break;
    }
}
