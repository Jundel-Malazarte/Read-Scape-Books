<?php
session_start();
session_unset(); // Unset session variables
session_destroy(); // Destroy the session
header("Location: sign-in.php", true, 302);
exit();
?>