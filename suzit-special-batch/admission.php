<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admission Form | Suzit's Special Batch</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    header {
      background: #1a73e8;
      color: white;
      text-align: center;
      padding: 20px 0;
    }
    nav {
      background: #0c47a1;
      text-align: center;
      padding: 10px 0;
    }
    nav a {
      color: white;
      text-decoration: none;
      margin: 0 20px;
      font-weight: bold;
    }
    .form-container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
      border: 1px solid #ccc;
      background: #f9f9f9;
      border-radius: 8px;
    }
    .form-container h2 {
      text-align: center;
      color: #1a73e8;
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    .photo-section {
      float: right;
      width: 200px;
      margin-left: 20px;
    }
    .photo-section label {
      text-align: center;
      display: block;
    }
    .clearfix::after {
      content: "";
      clear: both;
      display: table;
    }
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin: 15px 0;
    }
    .info-table th, .info-table td {
      padding: 10px;
      text-align: left;
      border: 1px solid #ddd;
    }
    .info-table th {
      background-color: #f2f2f2;
      font-weight: bold;
    }
    .info-table input {
      margin: 0;
      padding: 8px;
    }
    button {
      background: #1a73e8;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background: #0c47a1;
    }
    .course-options {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }
    .course-option {
      display: flex;
      align-items: center;
      background: white;
      padding: 10px 15px;
      border-radius: 25px;
      border: 2px solid #e0e0e0;
      cursor: pointer;
      transition: all 0.3s ease;
      min-width: 120px;
    }
    .course-option:hover {
      border-color: #1a73e8;
      background: #f8faff;
    }
    .course-option input[type="checkbox"] {
      margin-right: 8px;
      width: auto;
    }
    .course-option input[type="checkbox"]:checked ~ label {
      color: #1a73e8;
      font-weight: bold;
    }
    .course-option.selected {
      border-color: #1a73e8;
      background: #e3f2fd;
    }
    .course-option label {
      margin: 0;
      cursor: pointer;
      font-weight: 500;
    }
    .radio-group {
      display: flex; 
      gap: 20px; 
      margin-top: 10px;
      flex-wrap: wrap;
    }
    .radio-item {
      display: flex; 
      align-items: center;
    }
    .radio-item input[type="radio"] {
      width: auto; 
      margin-right: 8px;
    }
    .radio-item label {
      margin: 0; 
      cursor: pointer;
    }
    
    footer {
      background: #222;
      color: white;
      text-align: center;
      padding: 20px 0;
      margin-top: 40px;
    }
  </style>
</head>
<body>
  

<header>
  <div style="margin-top: 20px; text-align: right;">
                     <a class="btn-back-home" href="index.php">Back to Home Page</a>
                </div>
  <div class="login-container">
  <h1>Suzit's Special Batch</h1>
  <p>Admission Form</p>
</header>

<nav>
  <a href="index.php">Home</a>
    <a href="about.html">About Us</a>
    <a href="admission.php">Admission</a>
    <a href="result.php">Result</a>
    <a href="download/login.php">Downoad</a>
</nav>

<div class="form-container">
  <h2>Apply for Admission</h2>
  
  <form action="submit_admission.php" method="POST" enctype="multipart/form-data">
    
    <div class="course-selection">
      <h3 style="margin-top: 0; color: #1a73e8; text-align: center;">Select Your Course(s)</h3>
      <div class="course-options">
        <div class="course-option">
          <input type="checkbox" id="university" name="courses[]" value="University" onchange="toggleCourseSelection(this)" />
          <label for="university">University</label>
        </div>
        <div class="course-option">
          <input type="checkbox" id="gst_university" name="courses[]" value="GST-University" onchange="toggleCourseSelection(this)" />
          <label for="gst_university">GST-University</label>
        </div>
        <div class="course-option">
          <input type="checkbox" id="gst_agriculture" name="courses[]" value="GST-Agriculture" onchange="toggleCourseSelection(this)" />
          <label for="gst_agriculture">GST-Agriculture</label>
        </div>
        <div class="course-option">
          <input type="checkbox" id="medical_math" name="courses[]" value="Medical Special Math" onchange="toggleCourseSelection(this)" />
          <label for="medical_math">Medical Special Math</label>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label>Select Days</label>
      <div class="radio-group">
        <div class="radio-item">
          <input type="radio" id="days1" name="batch_days" value="Sat-Mon-Wed-Fri" required />
          <label for="days1">Saturday, Monday, Wednesday, Friday</label>
        </div>
        <div class="radio-item">
          <input type="radio" id="days2" name="batch_days" value="Sun-Tue-Thu-Fri" required />
          <label for="days2">Sunday, Tuesday, Thursday, Friday</label>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="batch_time">Batch Time (e.g., 9:00 AM - 11:00 AM)</label>
      <input type="text" id="batch_time" name="batch_time" placeholder="Enter your preferred batch time" required />
    </div>

    <div class="clearfix">
      <div class="form-group" style="width: calc(100% - 220px); float: left;">
        <label for="student_name">Student Name</label>
        <input type="text" id="student_name" name="student_name" required />
      </div>
      <div class="photo-section">
        <label for="photo">Upload Photo</label>
        <input type="file" id="photo" name="photo" accept="image/*" required />
      </div>
    </div>

    <div class="form-group">
      <label for="phone">Phone Number</label>
      <input type="tel" id="phone" name="phone" required />
    </div>

    <div class="form-group">
      <label for="father_name">Father's Name</label>
      <input type="text" id="father_name" name="father_name" required />
    </div>

    <div class="form-group">
      <label for="mother_name">Mother's Name</label>
      <input type="text" id="mother_name" name="mother_name" required />
    </div>

    <div class="form-group">
      <label for="guardian_name">Guardian's Name</label>
      <input type="text" id="guardian_name" name="guardian_name" required />
    </div>

    <div class="form-group">
      <label for="guardian_phone">Guardian's Phone Number</label>
      <input type="tel" id="guardian_phone" name="guardian_phone" required />
    </div>

    <div class="form-group">
      <label for="dob">Date of Birth</label>
      <input type="date" id="dob" name="dob" required />
    </div>

    <h3>Academic Information</h3>
    <table class="info-table">
      <thead>
        <tr>
          <th>Examination</th>
          <th>Institution Name</th>
          <th>Roll No</th>
          <th>Registration No</th>
          <th>Board</th>
          <th>Year</th>
          <th>GPA</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>SSC</strong></td>
          <td><input type="text" id="ssc_ins_name" name="ssc_ins_name" required /></td>
          <td><input type="text" id="ssc_roll" name="ssc_roll" required /></td>
          <td><input type="text" id="ssc_reg" name="ssc_reg" required /></td>
          <td><input type="text" id="ssc_board" name="ssc_board" required /></td>
          <td><input type="number" id="ssc_year" name="ssc_year" required /></td>
          <td><input type="text" id="ssc_gpa" name="ssc_gpa" required /></td>
        </tr>
        <tr>
          <td><strong>HSC</strong></td>
          <td><input type="text" id="hsc_ins_name" name="hsc_ins_name" required /></td>
          <td><input type="text" id="hsc_roll" name="hsc_roll" required /></td>
          <td><input type="text" id="hsc_reg" name="hsc_reg" required /></td>
          <td><input type="text" id="hsc_board" name="hsc_board" required /></td>
          <td><input type="number" id="hsc_year" name="hsc_year" required /></td>
          <td><input type="text" id="hsc_gpa" name="hsc_gpa" required /></td>
        </tr>
      </tbody>
    </table>

    <div class="form-group">
      <label for="pres_address">Present Address</label>
      <textarea id="pres_address" name="pres_address" rows="4" placeholder="Enter your present address" required></textarea>
    </div>
    
    <div class="form-group">
      <label for="per_address">Permanent Address</label>
      <textarea id="per_address" name="per_address" rows="4" placeholder="Enter your full address village, upozilla, zilla" required></textarea>
    </div>

    <div style="text-align:center;">
      <button type="submit">Submit</button>
    </div>
  </form>
</div>

<footer>
  <p>© 2025 Suzit's Special Batch. All rights reserved.</p>
</footer>

<script>
function toggleCourseSelection(checkbox) {
  const courseOption = checkbox.parentElement;
  if (checkbox.checked) {
    courseOption.classList.add('selected');
  } else {
    courseOption.classList.remove('selected');
  }
}
</script>

</body>
</html>