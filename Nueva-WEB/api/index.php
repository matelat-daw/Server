<?php
// api/index.php

require_once 'config/database.php';
require_once 'routes/api.php';

header("Content-Type: application/json; charset=UTF-8");

// El router se ejecuta desde routes/api.php
?>