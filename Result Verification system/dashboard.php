<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$baseDir = __DIR__;
$mainDirs = ['BSc_BSS', 'MSc_MSS'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h2, h3 {
            color: #333;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        form {
            background: #ffffff;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            max-width: 600px;
        }
        input[type="text"], select, input[type="file"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin: 8px 0 16px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #218838;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        ul {
            list-style-type: none;
            padding-left: 0;
        }
        li {
            margin-bottom: 10px;
            background: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<h2>Welcome, <?= $_SESSION["user"]; ?> | <a href="logout.php">Logout</a></h2>

<h3>Select Program, Session & Department</h3>
<form method="post" enctype="multipart/form-data">
    Program:
    <select name="program" required>
        <option value="">--Select--</option>
        <?php foreach ($mainDirs as $dir): ?>
            <option value="<?= $dir ?>"><?= $dir ?></option>
        <?php endforeach; ?>
    </select><br>

    Session: <input type="text" name="session" placeholder="e.g. 2019-20" required><br>
    Department: <input type="text" name="department" placeholder="e.g. ICE" required><br>
    Upload Excel: <input type="file" name="fileToUpload" accept=".xlsx" required><br>
    <button type="submit" name="upload">Upload</button>
</form>

<?php
if (isset($_POST['upload'])) {
    $program = $_POST["program"];
    $session = $_POST["session"];
    $dept = $_POST["department"];

    $uploadPath = "$program/$session/$dept/";
    $fullPath = $baseDir . "/" . $uploadPath;

    if (!file_exists($fullPath)) {
        mkdir($fullPath, 0777, true); // Create nested folders
    }

    $targetFile = $fullPath . basename($_FILES["fileToUpload"]["name"]);
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
        echo "<p class='success'>File uploaded successfully to $uploadPath</p>";
    } else {
        echo "<p class='error'>Upload failed.</p>";
    }
}
?>

<hr>
<h3>Delete Existing Files</h3>
<form method="get">
    Program:
    <select name="program" required>
        <?php foreach ($mainDirs as $dir): ?>
            <option value="<?= $dir ?>"><?= $dir ?></option>
        <?php endforeach; ?>
    </select>
    Session: <input type="text" name="session" placeholder="e.g. 2019-20" required>
    Department: <input type="text" name="department" placeholder="e.g. ICE" required>
    <button type="submit">View Files</button>
</form>

<?php
if (isset($_GET['program'])) {
    $program = $_GET["program"];
    $session = $_GET["session"];
    $dept = $_GET["department"];
    $viewPath = "$program/$session/$dept/";

    $files = @scandir($viewPath);
    if ($files && count($files) > 2) {
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                echo "<li>$file 
                    <a href='$viewPath/$file' target='_blank'>[View]</a> 
                    <a href='delete.php?path=$viewPath&file=" . urlencode($file) . "' onclick=\"return confirm('Delete this file?')\">[Delete]</a>
                </li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>No files found in $viewPath</p>";
    }
}
?>
</body>
</html>
