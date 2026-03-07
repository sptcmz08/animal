<?php
/**
 * PawHaven - Bootstrap / Init
 */
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Current page for nav highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
