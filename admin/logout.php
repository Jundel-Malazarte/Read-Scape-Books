<?php
session_start();
session_unset(); // Unset session variables
session_destroy(); // Destroy the session
header("Location: ../admin/admin.php", true, 302);
exit();
