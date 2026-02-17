<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$path = $_GET["path"];
$file = $_GET["file"];
$full = $path . '/' . $file;

if (file_exists($full)) {
    unlink($full);
    echo "Deleted successfully. <a href='dashboard.php'>Back</a>";
} else {
    echo "File not found. <a href='dashboard.php'>Back</a>";
}
?>
