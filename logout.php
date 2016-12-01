<?php session_start();
include('inc/config.php');
unset($_SESSION);
session_destroy();
header('Location: ' . $domain);
