<?php

echo "<title>Counter Application</title>";
echo "Starting application...<br>";

// Reading Redis password from secret file
$password_file_path = "/credentials/password";
$password_file = fopen($password_file_path, "r") or die("Error! Unable to open Redis password secret file...<br>");
$redis_password = fgets($password_file);
$redis_password = trim(preg_replace('/\s+/', ' ', $redis_password));
fclose($password_file);

// Reading Redis hostname from env variable
$redis_host = getenv('REDIS_HOST');
if (!$redis_host) die("Error! Unable to get Redis hostname from env variable...<br>");


//Connecting to Redis
$redis_port = 6379;
$redis = new Redis();

try {
    $redis->connect($redis_host, $redis_port);
    $redis->auth($redis_password);
} catch (RedisException $ex) {
    die("Error! Unable to connect to Redis instance ('$redis_host' - '$redis_port' - '$redis_password')...<br>");
}

if (!$redis->ping()) die("Error! Unable to ping Redis instance...<br>");

echo "Counter service was successfully started...<br>";

// Initializing counter 
if (!$redis->exists('counter')) {
    $redis->set('counter', 1);
    $counter = 1;

// Incrementing counter
} else {
    $counter = $redis->get('counter');
    $counter += 1;
    $redis->set('counter', $counter);
}

// Get service instance hostname
$hostname = gethostname();

echo "----------------------------------------------<br>";
echo "Service usage counter: <b>$counter</b><br>";
echo "Current service instance: <b>$hostname</b><br>";
echo "----------------------------------------------<br>";

?> 
