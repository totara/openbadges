<?php

require_once('config.php');
set_time_limit(0);

$sql = "SELECT pg_sleep(240)";
$DB->execute($sql);

echo 'Script completed';
?>

