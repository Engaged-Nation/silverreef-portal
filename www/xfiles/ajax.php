<?php
set_time_limit(0);

/*
$allowed = array('24.234.137.217', '127.0.0.1');
if(!in_array($_SERVER['REMOTE_ADDR'], $allowed)){
	die('What does the fox say?');
}
*/

# set headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate"); 
header("Pragma: no-cache");

$path = str_replace(array($_SERVER['DOCUMENT_ROOT'], basename(__FILE__)), '', __FILE__);
?>
<!DOCTYPE html>
<html class="no-js" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr">
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script>
		(function($){
			"use strict";
			
			var start, x, t, z;
			x = 0;
			t = 2000;
			z = 0;
			
			function getScript(x, t, z){
				$.ajax({
					type: 'GET',
					dataType: 'json',
					data: {'x': x, 't': t, 'z': z},
					url: '<?php echo $path.'ajax.body.php'; ?>',
					success: function(r){
						if(r['next'] && r['pgl']){
							$('#response').append('<div>' + r['body'] + '</div>');
							$('#response').append('<div>' + r['next'] + r['pgl'] + '</div>');

							getScript(r['x'], r['t'], r['z']);
						}
						else{
							$('#response').append('<div>' + r['body'] + '</div>');
							$('#response').append('stopped.');
						}
					},
				});
			}
			$(document).ready(function(){
				$('#start').click(function(){
					$(this).unbind('click');
					getScript(x, t, z);	
				});
			});
			
		}(jQuery));
		</script>
	</head>
	<body>
		<a id="start" style="cursor: pointer;">start...</a>
		<div id="response"></div>
	</body>
</html>