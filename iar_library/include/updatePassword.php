<?php
require_once 'const.php';
$db_conn = pg_connect(DB_CONN_STR) or die ("can't connect to db");
$result = pg_prepare($db_conn, "myqr", "update users set password = $1 where LOWER(first)=LOWER($2)");
$result = pg_execute(db_conn,"myqr",array(password_hash("athzmtmtyt",PASSWORD_DEFAULT),"Admin");
?>