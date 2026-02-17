<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll = $_POST["roll"];
    $session = $_POST["session"];
    $department = $_POST["department"];
    $degree = $_POST["degree"];
    $Registration_Number = $_POST["Registration_Number"];
    $Date_of_Birth = $_POST["Date_of_Birth"];
    $found = false;

    // Create file path - get department short name
    $deptShortNames = [
        "Computer Science and Engineering" => "CSE",
        "Electrical and Electronic Engineering" => "EEE",
        "Mathematics" => "MATH",
        "Business Administration" => "BBA",
        "Electrical, Electronic and Communication Engineering" => "EECE",
        "Information and Communication Engineering" => "ICE",
        "Physics" => "PHY",
        "Economics" => "ECON",
        "Geography and Environment" => "GEO",
        "Bangla" => "BAN",
        "Civil Engineering" => "CE",
        "Architecture" => "ARCH",
        "Pharmacy" => "PHARM",
        "Chemistry" => "CHEM",
        "Social Work" => "SW",
        "Statistics" => "STAT",
        "Urban and Regional Planning" => "URP",
        "English" => "ENG",
        "Public Administration" => "PA",
        "History" => "HIST",
        "Tourism and Hospitality Management" => "THM"
    ];
    
    $deptShort = $deptShortNames[$department] ?? $department;
    $sessionFileFormat = str_replace("-", "_", $session);
    $filePath = "$degree/$session/$deptShort/Result_{$deptShort}_{$sessionFileFormat}.xlsx";

    if (file_exists($filePath)) {
        // Simple Excel reader function without external libraries
        function readExcelFile($filePath) {
            $data = [];
            
            // Try to read as XML (Excel 2007+ format)
            $zip = new ZipArchive;
            if ($zip->open($filePath) === TRUE) {
                $xmlString = $zip->getFromName('xl/sharedStrings.xml');
                $xmlString2 = $zip->getFromName('xl/worksheets/sheet1.xml');
                $zip->close();
                
                // Parse shared strings
                $sharedStrings = [];
                if ($xmlString !== false) {
                    $xml = simplexml_load_string($xmlString);
                    if ($xml !== false) {
                        foreach ($xml->si as $val) {
                            $sharedStrings[] = (string)$val->t;
                        }
                    }
                }
                
                // Parse worksheet
                if ($xmlString2 !== false) {
                    $xml = simplexml_load_string($xmlString2);
                    if ($xml !== false) {
                        $rowIndex = 0;
                        foreach ($xml->sheetData->row as $row) {
                            $rowData = [];
                            $colIndex = 0;
                            foreach ($row->c as $cell) {
                                $value = '';
                                if (isset($cell->v)) {
                                    if (isset($cell['t']) && $cell['t'] == 's') {
                                        // Shared string reference
                                        $value = $sharedStrings[(int)$cell->v];
                                    } else {
                                        $value = (string)$cell->v;
                                    }
                                }
                                $rowData[$colIndex] = $value;
                                $colIndex++;
                            }
                            $data[$rowIndex] = $rowData;
                            $rowIndex++;
                        }
                    }
                }
            }
            return $data;
        }
        
        try {
            $excelData = readExcelFile($filePath);
            
            // Skip header row (index 0) and search for student
            for ($i = 1; $i < count($excelData); $i++) {
                $data = $excelData[$i];
                
                // Make sure we have enough columns
                if (count($data) > 17) {
                    $studentRoll = isset($data[8]) ? trim($data[8]) : '';
                    $studentReg = isset($data[7]) ? trim($data[7]) : '';
                    $studentDOB = isset($data[4]) ? trim($data[4]) : '';
                    
                    if (($studentRoll == $roll) && ($studentReg == $Registration_Number) && ($studentDOB == trim($Date_of_Birth))) {
                        $_SESSION['result'] = $data;
                        $_SESSION['session'] = $session;
                        $_SESSION['department'] = $department;
                        $_SESSION['degree'] = $degree;
                        $found = true;
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error reading Excel file. Please make sure the file format is correct.";
        }
    }

    if (!$found) {
        $_SESSION['error'] = "No student found with the provided information in $department ($session) for $degree program.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcript Verification System</title>
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            background-color: #f4f4f9; 
            margin: 0; 
            padding: 0; 
        }
        h1, h2 { 
            color: #333; 
            font-weight: bold; 
        }
        h1 { 
            text-align: center;
            margin: 10px 0;
            font-size: 30px;
        }
        h2 { 
            text-align: left; 
            margin-left: 15px; 
            font-size: 18px;
        }
        .login_button_one {
           position: fixed;
           top: 20px;
           right: 20px;
           width: 10px;
           height: 5px;
           border: none;
           border-radius: 5px;
           cursor: pointer;
           display: flex;
           align-items: center;
           justify-content: center;
           font-size: 10; /* টেক্সট হাইড করা হবে কারণ বাটন এত ছোট */
          }


        .container { 
            max-width: 800px; 
            margin: 20px auto; 
            padding: 20px; 
            background-color: white; 
            border-radius: 8px; 
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); 
        }
        .marquee { 
            color: red; 
            font-weight: bold; 
            margin-bottom: 20px; 
            text-align: center; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        table, th, td { 
            border: none; 
        }
        tr:nth-child(odd) { 
            background-color: #f9f9f9; 
        }
        tr:nth-child(even) { 
            background-color: #f1f1f1; 
        }
        td { 
            padding: 7px; 
            text-align: left; 
            height: 30px;
            vertical-align: middle;
        }
        td:first-child { 
            width: 200px; 
            font-weight: bold; 
        }
        td:nth-child(2) {
            width: 10px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 5px;
            height: 30px;
            box-sizing: border-box;
            font-family: "Times New Roman", serif;
            font-size: 15px;
        }
        button { 
            background-color: rgb(73, 75, 73); 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            margin-top: 20px; 
            display: block; 
            width: 20%; 
        }
        button:hover { 
            background-color: #45a049; 
        }
        .signature { 
            text-align: right; 
            margin-top: 10px; 
        }
        .signature img { 
            max-width: 100px; 
            height: auto; 
        }
        #printBtn { 
            background-color: #007BFF; 
            width: auto; 
        }
        #printBtn:hover { 
            background-color: #0056b3; 
        }
        .serial-number {
            font-size: 15px;
            font-weight: bold;
        }
        footer {
            background-color: #f2f2f2; 
            padding: 20px; 
            text-align: center; 
            font-size: 14px; 
            color: #555;
        }

        /* Print specific styles */
        @media print {
            body * { 
                visibility: hidden; 
            }
            #printable, #printable * { 
                visibility: visible; 
            }
            #printable { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%; 
                padding: 10mm; 
                box-sizing: border-box;
            }
            #printBtn { 
                display: none; 
            }
            
            @page {
                size: A4;
                margin: 10mm;
            }
            
            #printable h1 {
                margin: 5px 0;
                font-size: 30px;
            }
            #printable .header-logo img {
                width: 150px !important;
                height: auto !important;
            }
            #printable table {
                margin-top: 10px;
                font-size: 15px;
            }
            #printable td {
                padding: 2px 4px;
                line-height: 1.15;
                height: 25px;
            }
            #printable .signature {
                margin-top: 5px;
            }
            #printable .signature img {
                width: 70px !important;
            }
            #printable .signature p {
                margin: 2px 0;
                font-size: 15px;
            }
            .serial-number {
                position: absolute;
                top: 15mm;
                right: 15mm;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
       <button class="login_button_one" onclick="window.location.href='login.php'">
        <i class="fas fa-chart-line"></i> Login
       </button>

    <div class="container">
        <h1>Pabna University of Science and Technology</h1>
        <div style="text-align: center;">
            <img src="pust_logo.jpg" alt="PUST Logo" style="width: 150px; height: auto;">
        </div>
        <h1>Transcript Verification System</h1>
        <marquee class="marquee">
            Welcome to the Transcript of Academic Record Verification System. Please enter all the information correctly. After clicking the 'Find' button, the result will be displayed below.
        </marquee>

        <h2>Enter Student Information</h2>
        <form method="post">
            <table>
                <tr>
                    <td>Degree</td>
                    <td>:</td>
                    <td>
                        <select name="degree" id="degree" onchange="updateSessions()" required>
                            <option value="">Select Degree</option>
                            <option value="BSc_BSS">BSc/BSS</option>
                            <option value="MSc_MSS">MSc/MSS</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Session</td>
                    <td>:</td>
                    <td>
                        <select name="session" id="session" onchange="updateDepartments()" required>
                            <option value="">Select Session</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Department</td>
                    <td>:</td>
                    <td>
                        <select name="department" id="department" required>
                            <option value="">Select Department</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Roll Number</td>
                    <td>:</td>
                    <td><input type="text" name="roll" required></td>
                </tr>
                <tr>
                    <td>Registration Number</td>
                    <td>:</td>
                    <td><input type="text" name="Registration_Number" required></td>
                </tr>
                <tr>
                    <td>Date of Birth (e.g., January 3, 2000)</td>
                    <td>:</td>
                    <td><input type="text" name="Date_of_Birth" required></td>
                </tr>
            </table>

            <button type="submit">Find</button>
        </form>

        <?php
        if (isset($_SESSION['result'])) {
            $data = $_SESSION['result'];
            echo "<div id='printable'>";
            echo "<div style='position: relative;'>";
            echo "<div class='serial-number' style='position: absolute; top: 0; right: 0;'><strong>Sl. No. </strong> " . htmlspecialchars($data[0]) . "</div>";
            echo "<div class='header-logo' style='text-align: center;'>";
            echo "<img src='pust_logo.jpg' alt='PUST Logo' style='width: 150px; height: auto;'>";
            echo "</div>";
            echo "</div>";
            echo "<h1>Pabna University of Science and Technology</h1>";
            echo "<h1 style='text-decoration: underline;'>Transcript of Academic Record</h1>";
            
            echo "<table>";
            echo "<tr><td>Name of the Student</td><td>:</td><td>" . htmlspecialchars($data[1]) . "</td></tr>";
            echo "<tr><td>Father's Name</td><td>:</td><td>" . htmlspecialchars($data[2]) . "</td></tr>";
            echo "<tr><td>Mother's Name</td><td>:</td><td>" . htmlspecialchars($data[3]) . "</td></tr>";
            echo "<tr><td>Date of Birth</td><td>:</td><td>" . htmlspecialchars($data[4]) . "</td></tr>";
            echo "<tr><td>Department</td><td>:</td><td>" . htmlspecialchars($data[5]) . "</td></tr>";
            echo "<tr><td>Degree Awarded</td><td>:</td><td>" . htmlspecialchars($data[6]) . "</td></tr>";
            echo "<tr><td>Registration Number</td><td>:</td><td>" . htmlspecialchars($data[7]) . "</td></tr>";
            echo "<tr><td>Roll Number</td><td>:</td><td>" . htmlspecialchars($data[8]) . "</td></tr>";
            echo "<tr><td>Sessions Attended</td><td>:</td><td>" . htmlspecialchars($data[9]) . "</td></tr>";
            echo "<tr><td>Date of Completion</td><td>:</td><td>" . htmlspecialchars($data[10]) . "</td></tr>";
            echo "<tr><td>Total Number of Credits</td><td>:</td><td>" . htmlspecialchars($data[11]) . "</td></tr>";
            echo "<tr><td>Credits Completed</td><td>:</td><td>" . htmlspecialchars($data[12]) . "</td></tr>";
            echo "<tr><td>CGPA Earned</td><td>:</td><td>" . htmlspecialchars($data[13]) . "</td></tr>";
            echo "<tr><td>Highest CGPA in Class</td><td>:</td><td>" . htmlspecialchars($data[14]) . "</td></tr>";
            echo "<tr><td>Students in Class</td><td>:</td><td>" . htmlspecialchars($data[15]) . "</td></tr>";
            echo "<tr><td>Letter Grade Obtained</td><td>:</td><td>" . htmlspecialchars($data[16]) . "</td></tr>";
            echo "<tr><td>Medium of Instruction</td><td>:</td><td>" . htmlspecialchars($data[17]) . "</td></tr>";
            echo "</table>";

            echo "<div class='signature'>";
            echo "<img src='signature.png' alt='Signature'>";
            echo "<p>Controller of Examinations</p>";
            echo "<p>Pabna University of Science and Technology</p>";
            echo "<p>Pabna, Bangladesh</p>";
            echo "</div>";
            echo "</div>";

            echo "<button id='printBtn' onclick='printTranscript()'>Print Transcript</button>";

            unset($_SESSION['result']);
        } elseif (isset($_SESSION['error'])) {
            echo "<p style='color:red; text-align:center;'>" . htmlspecialchars($_SESSION['error']) . "</p>";
            unset($_SESSION['error']);
        }
        ?>
    </div>

    <footer>
        <p>&copy; 2025 Transcript of Academic Record Verification System. All rights reserved.</p>
        <p>Developed by Alamin</p>    
    </footer>

    <script>
        const sessions = [
            "2008-09", "2009-10", "2010-11", "2011-12", "2012-13", 
            "2013-14", "2014-15", "2015-16", "2016-17", "2017-18", 
            "2018-19", "2019-20", "2020-21"
        ];

        const departments = [
            "Computer Science and Engineering",
            "Electrical and Electronic Engineering",
            "Mathematics",
            "Business Administration",
            "Electrical, Electronic and Communication Engineering",
            "Information and Communication Engineering",
            "Physics",
            "Economics",
            "Geography and Environment",
            "Bangla",
            "Civil Engineering",
            "Architecture",
            "Pharmacy",
            "Chemistry",
            "Social Work",
            "Statistics",
            "Urban and Regional Planning",
            "English",
            "Public Administration",
            "History",
            "Tourism and Hospitality Management"
        ];

        const deptShortNames = {
            "Computer Science and Engineering": "CSE",
            "Electrical and Electronic Engineering": "EEE",
            "Mathematics": "MATH",
            "Business Administration": "BBA",
            "Electrical, Electronic and Communication Engineering": "EECE",
            "Information and Communication Engineering": "ICE",
            "Physics": "PHY",
            "Economics": "ECON",
            "Geography and Environment": "GEO",
            "Bangla": "BAN",
            "Civil Engineering": "CE",
            "Architecture": "ARCH",
            "Pharmacy": "PHARM",
            "Chemistry": "CHEM",
            "Social Work": "SW",
            "Statistics": "STAT",
            "Urban and Regional Planning": "URP",
            "English": "ENG",
            "Public Administration": "PAD",
            "History": "HIST",
            "Tourism and Hospitality Management": "THM"
        };

        const deptOptions = {
            "BSc_BSS": {},
            "MSc_MSS": {}
        };

        // Initialize department options for each session
        sessions.forEach(session => {
            deptOptions["BSc_BSS"][session] = [...departments];
            deptOptions["MSc_MSS"][session] = [...departments];
        });

        function updateSessions() {
            const degree = document.getElementById("degree").value;
            const sessionSelect = document.getElementById("session");
            const deptSelect = document.getElementById("department");
            
            sessionSelect.innerHTML = '<option value="">Select Session</option>';
            deptSelect.innerHTML = '<option value="">Select Department</option>';
            
            if (degree) {
                sessions.forEach(session => {
                    const option = document.createElement("option");
                    option.value = session;
                    option.text = session;
                    sessionSelect.appendChild(option);
                });
            }
        }

        function updateDepartments() {
            const degree = document.getElementById("degree").value;
            const session = document.getElementById("session").value;
            const deptSelect = document.getElementById("department");

            deptSelect.innerHTML = '<option value="">Select Department</option>';

            if (degree && session && deptOptions[degree] && deptOptions[degree][session]) {
                deptOptions[degree][session].forEach(function(dept) {
                    const option = document.createElement("option");
                    option.value = dept;
                    option.text = dept;
                    deptSelect.appendChild(option);
                });
            }
        }

        function printTranscript() {
            window.print();
        }
    </script>
</body>
</html>