<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$targetDir = "BSc_BSS/2019-20/ICE/";
$targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);

if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
    echo "The file has been uploaded. <a href='dashboard.php'>Back</a>";
} else {
    echo "Sorry, there was an error uploading your file. <a href='dashboard.php'>Back</a>";
}
?>
