<?php
require __DIR__ . '/../app/db.php';

$row = db()->query('SELECT COUNT(*) AS total, SUM(price) AS sum_price FROM services')->fetch();
var_export($row);
