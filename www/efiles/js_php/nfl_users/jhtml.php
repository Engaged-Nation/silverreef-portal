<?php
set_time_limit(0);

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

$allowed = array('24.234.137.217', '172.18.0.1');

if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $remoteIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
    $remoteIp = $_SERVER["REMOTE_ADDR"];
}


if(!in_array($remoteIp, $allowed)){
    die('What does the fox say?');
}

$path = str_replace(array($_SERVER['DOCUMENT_ROOT'], basename(__FILE__)), '', __FILE__);
?>
<!DOCTYPE html>
<html class="no-js" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr">
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>
        "use strict";

        var start, x, t, counter;
        x = 0;
        t = 50;
        counter = 0;

        function getScript(x, t, counter){
            $.ajax({
                type: 'GET',
                dataType: 'json',
                data: {'x': x, 't': t, 'count': counter},
                url: '<?php echo $path.'cal.php'; ?>?week=<?php echo $_GET['week']; ?>&year=<?php echo $_GET['year']; ?>',
                success: function(r){
                    if(r['next'] && r['pgl']){
                        $('#response').append('<div>' + r['next'] + r['pgl'] + '</div>');

                        getScript(r['x'], r['t'], r['counter']);
                    }
                    else{
                        $('#response').append('stopped.');
                    }
                },
            });
        }
        $(document).ready(function(){
            $('#start').click(function(){
                $(this).unbind('click');
                getScript(x, t, counter);
            });
        });
    </script>
</head>
<body>
<a id="start">start...</a>
<div id="response"></div>
</body>
</html>