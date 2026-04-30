<?php
include "config.php";

session_unset();
session_destroy();

/* Redirect to Home Page */
echo "<script>window.location='root.php';</script>";
exit();
?>
