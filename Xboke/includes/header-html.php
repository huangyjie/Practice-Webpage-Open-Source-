<?php
require_once 'header.php';
header('Content-Type: text/html; charset=utf-8');
echo renderHeader($_GET['active'] ?? '');
?> 