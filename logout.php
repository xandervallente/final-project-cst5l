<?php
session_start();

// Destroy the session
session_unset();
session_destroy();

// Redirect back to login page
header("Location: /finalProj/index.php");
exit();
