<?php
$db = new PDO('sqlite:' . realpath(__DIR__) . '/zftutorial.db');

$fh = fopen(__DIR__ . '/schema.sql', 'r');
while ($line = fread($fh, 4096)) {
    $db->exec($line);
}
fclose($fh);

$fh = fopen(__DIR__ . '/users.sql', 'r');
while ($line = fread($fh, 4096)) {
    $db->exec($line);
}
fclose($fh);