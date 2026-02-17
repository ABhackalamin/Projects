<?php
include 'auth.php';
// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "suzit-special-batch";

$conn = new mysqli($host, $user, $pass);

// Create database and select
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);

// Create tables
$conn->query("
    CREATE TABLE IF NOT EXISTS admission_info (
        id INT AUTO_INCREMENT PRIMARY KEY,
        roll_number VARCHAR(20) NOT NULL UNIQUE,
        student_name VARCHAR(100) NOT NULL,
        total_amount INT DEFAULT 10000,
        created_at DATE DEFAULT CURRENT_DATE
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS admission_fees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        roll VARCHAR(20) NOT NULL,
        name VARCHAR(100) NOT NULL,
        paid_amount INT DEFAULT 0,
        total_fee INT DEFAULT 10000,
        total_paid_so_far INT DEFAULT 0,
        due_amount INT DEFAULT 0,
        payment_date DATE DEFAULT CURRENT_DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS fee_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        total_fee INT DEFAULT 10000,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

// Initialize default fee if not exists
$fee_check = $conn->query("SELECT COUNT(*) as count FROM fee_settings");
$fee_row = $fee_check->fetch_assoc();
if ($fee_row['count'] == 0) {
    $conn->query("INSERT INTO fee_settings (total_fee) VALUES (10000)");
}

// Get current total course fee
$fee_result = $conn->query("SELECT total_fee FROM fee_settings ORDER BY id DESC LIMIT 1");
$current_fee = $fee_result->fetch_assoc();
$total_course_fee = $current_fee['total_fee'];

// Handle fee update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_fee'])) {
    $new_fee = (int)$_POST['new_total_fee'];
    $conn->query("UPDATE fee_settings SET total_fee = $new_fee WHERE id = (SELECT id FROM (SELECT id FROM fee_settings ORDER BY id DESC LIMIT 1) as temp)");
    $total_course_fee = $new_fee;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Function to calculate total paid and due for a student
function getStudentPaymentSummary($conn, $roll) {
    $result = $conn->query("SELECT SUM(paid_amount) as total_paid, MAX(total_fee) as course_fee FROM admission_fees WHERE roll = '$roll'");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_paid = $row['total_paid'] ?? 0;
        $course_fee = $row['course_fee'] ?? 0;
        $due = $course_fee - $total_paid;
        return ['total_paid' => $total_paid, 'course_fee' => $course_fee, 'due' => $due];
    }
    return ['total_paid' => 0, 'course_fee' => 0, 'due' => 0];
}

// Handle payment submission
$payment_success = false;
$receipt_data = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_payment'])) {
    $roll = $conn->real_escape_string($_POST['roll']);
    $name = $conn->real_escape_string($_POST['name']);
    $paid_now = (int)$_POST['paid'];
    $payment_date = isset($_POST['payment_date']) && !empty($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d');
    
    // Get previous payment summary
    $payment_summary = getStudentPaymentSummary($conn, $roll);
    $new_total_paid = $payment_summary['total_paid'] + $paid_now;
    $new_due = $total_course_fee - $new_total_paid;
    
    // Insert new payment record
    $conn->query("INSERT INTO admission_fees (roll, name, paid_amount, total_fee, total_paid_so_far, due_amount, payment_date) 
                  VALUES ('$roll', '$name', $paid_now, $total_course_fee, $new_total_paid, $new_due, '$payment_date')");
    
    $payment_success = true;
    $receipt_data = [
        'roll' => $roll,
        'name' => $name,
        'total_course_fee' => $total_course_fee,
        'paid_now' => $paid_now,
        'total_paid_so_far' => $new_total_paid,
        'due_amount' => $new_due,
        'payment_date' => $payment_date,
        'time' => date('H:i:s')
    ];
}

// Search for student info
$student_info = null;
$search_roll = '';
$student_payment_summary = null;
if (isset($_GET['roll_search']) && !empty($_GET['roll_search'])) {
    $search_roll = $conn->real_escape_string($_GET['roll_search']);
    $res = $conn->query("SELECT * FROM admission_info WHERE roll_number = '$search_roll'");
    if ($res && $res->num_rows > 0) {
        $student_info = $res->fetch_assoc();
        $student_payment_summary = getStudentPaymentSummary($conn, $search_roll);
    }
}

// Search payment history
$payment_history = [];
if (isset($_GET['history_search']) && !empty($_GET['history_search'])) {
    $history_roll = $conn->real_escape_string($_GET['history_search']);
    $res = $conn->query("SELECT * FROM admission_fees WHERE roll = '$history_roll' ORDER BY payment_date DESC, created_at DESC");
    while ($row = $res->fetch_assoc()) {
        $payment_history[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Fee Management</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .section h3 { margin-top: 0; color: #333; border-bottom: 2px solid #1a73e8; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #1a73e8; color: white; }
        .summary-box { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .summary-item { margin: 8px 0; font-size: 16px; }
        .summary-item strong { display: inline-block; width: 180px; }
        .due-amount { color: #d32f2f; font-weight: bold; }
        .paid-amount { color: #388e3c; font-weight: bold; }
        input[type=text], input[type=number], input[type=date] { padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: inline-block; width: 150px; font-weight: bold; }
        .btn { padding: 10px 20px; background: #1a73e8; color: white; border: none; cursor: pointer; border-radius: 4px; margin-right: 10px; }
        .btn:hover { background: #155ab6; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .receipt { background: #f8f9fa; border: 2px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .receipt h3 { text-align: center; color: #28a745; margin-bottom: 20px; }
        .receipt-info { margin: 10px 0; }
        .receipt-info strong { display: inline-block; width: 180px; }
        .signature-section { margin-top: 30px; text-align: right; }
        .alert { padding: 15px; margin: 10px 0; border-radius: 4px; }
        .alert-info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .alert-danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        
        /* Print Styles */
        /* Print Styles - Updated for smaller text */
@media print {
    body { 
        background: white; 
        margin: 0; 
        padding: 0; 
        font-size: 12px; /* Reduced from default 16px */
        line-height: 1.3; /* Tighter line spacing */
    }
    
    .container { 
        box-shadow: none; 
        border-radius: 0; 
        max-width: none; 
        margin: 0; 
        padding: 15px; /* Reduced padding */
    }
    /* Hide top navigation links during print */
    .top-links { display: none !important; }
    
    /* Hide everything except receipt when printing receipt */
    .print-receipt .section:not(.receipt-section),
    .print-receipt h1,
    .print-receipt .no-print { display: none !important; }
    
    /* Hide everything except history when printing history */
    .print-history .section:not(.history-section),
    .print-history h1,
    .print-history .no-print { display: none !important; }
    
    .receipt { 
        border: 2px solid #000; 
        background: white; 
        page-break-inside: avoid; 
        padding: 15px; /* Reduced padding */
        font-size: 12px; /* Smaller font for receipt */
    }
    
    .receipt h3 { 
        color: #000; 
        font-size: 18px; /* Smaller heading */
        margin-bottom: 15px;
    }
    
    .receipt-info {
        margin: 6px 0; /* Reduced margin */
        font-size: 12px; /* Smaller text */
    }
    
    .receipt-info strong {
        display: inline-block;
        width: 150px; /* Reduced width */
        font-size: 12px;
    }
    
    /* Print-specific styling for history */
    .history-print-header { 
        text-align: center; 
        margin-bottom: 20px; /* Reduced margin */
        border-bottom: 2px solid #000; 
        padding-bottom: 10px; /* Reduced padding */
        font-size: 14px; /* Smaller font */
    }
    
    .history-print-header h2 {
        font-size: 16px; /* Smaller heading */
        margin: 0 0 5px 0;
    }
    
    .history-print-header h3 {
        font-size: 14px; /* Smaller heading */
        margin: 0;
    }
    
    /* Table styles for print */
    table { 
        font-size: 10px; /* Much smaller table text */
        margin-top: 10px;
    }
    
    th, td { 
        padding: 6px; /* Reduced padding */
        font-size: 10px; /* Smaller cell text */
    }
    
    th { 
        background: #f0f0f0 !important; 
        color: #000 !important; 
        font-size: 10px; /* Smaller header text */
        font-weight: bold;
    }
    
    .summary-box {
        padding: 10px; /* Reduced padding */
        margin: 10px 0; /* Reduced margin */
        font-size: 11px; /* Smaller summary text */
    }
    
    .summary-box h4 {
        font-size: 13px; /* Smaller heading */
        margin-bottom: 8px;
    }
    
    .summary-item {
        margin: 5px 0; /* Reduced margin */
        font-size: 11px; /* Smaller text */
    }
    
    .summary-item strong {
        width: 140px; /* Reduced width */
    }
    
    .signature-section { 
        page-break-inside: avoid; 
        margin-top: 30px; /* Reduced margin */
        font-size: 11px; /* Smaller signature text */
    }
    
    .signature-section p {
        margin: 5px 0; /* Reduced margin */
    }
    
    .btn { display: none; }
    input { border: none; background: none; }
    
    /* Logo styling for print */
    .receipt img {
        height: 80px; /* Smaller logo */
        width: 80px;
        margin-bottom: 10px;
    }
    
    /* Make currency amounts slightly more prominent while keeping them small */
    .paid-amount, .due-amount {
        font-weight: bold;
        font-size: 11px;
    }
}
    </style>
</head>
<body>
    <div class="container">
         <div class="top-links">
        <a class="view_payments" href="view_payments.php">View Payments</a>
        <a class="btn-back-dasboard" href="dashboard.php">Back to Dashboard</a>
        <a class="summery_amount" href="summery_amount.php">Summary Amount</a>
    </div>
        <h1 style="text-align: center; color: #1a73e8;">Course Fee Management System</h1>
        
        <!-- Course Fee Settings Section -->
        <div class="section no-print">
            <h3>Course Fee Settings</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Total Course Fee:</label>
                    <input type="number" name="new_total_fee" value="<?= $total_course_fee ?>" required>
                    <button class="btn" type="submit" name="update_fee">Update Course Fee</button>
                </div>
            </form>
            <p><strong>Current Course Fee:</strong> ৳<?= number_format($total_course_fee) ?></p>
        </div>

        <!-- Student Search Section -->
        <div class="section no-print">
            <h3>Search Student for Payment</h3>
            <form method="GET">
                <div class="form-group">
                    <label>Enter Roll Number:</label>
                    <input type="text" name="roll_search" value="<?= htmlspecialchars($search_roll) ?>" required>
                    <button class="btn" type="submit">Search Student</button>
                </div>
            </form>

            <?php if (isset($_GET['roll_search']) && empty($student_info)): ?>
                <div class="alert alert-danger">
                    <strong>Student not found!</strong> Please check the roll number or add student to admission_info table.
                </div>
            <?php endif; ?>

            <?php if ($student_info): ?>
                <div class="alert alert-success">
                    <strong>Student Found:</strong> <?= htmlspecialchars($student_info['student_name']) ?> (Roll: <?= htmlspecialchars($student_info['roll_number']) ?>)
                </div>
                
                <!-- Payment Summary -->
                <div class="summary-box">
                    <h4>Current Payment Status</h4>
                    <div class="summary-item">
                        <strong>Total Course Fee:</strong> ৳<?= number_format($total_course_fee) ?>
                    </div>
                    <div class="summary-item">
                        <strong>Total Paid So Far:</strong> <span class="paid-amount">৳<?= number_format($student_payment_summary['total_paid']) ?></span>
                    </div>
                    <div class="summary-item">
                        <strong>Remaining Due:</strong> <span class="due-amount">৳<?= number_format($total_course_fee - $student_payment_summary['total_paid']) ?></span>
                    </div>
                </div>
                
                <!-- Payment Form -->
                <?php if (($total_course_fee - $student_payment_summary['total_paid']) > 0): ?>
                <form method="POST">
                    <input type="hidden" name="roll" value="<?= htmlspecialchars($student_info['roll_number']) ?>">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($student_info['student_name']) ?>">
                    
                    <div class="form-group">
                        <label>Roll Number:</label>
                        <input type="text" value="<?= htmlspecialchars($student_info['roll_number']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Student Name:</label>
                        <input type="text" value="<?= htmlspecialchars($student_info['student_name']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Payment Date:</label>
                        <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Pay Amount:</label>
                        <input type="number" name="paid" min="1" max="<?= $total_course_fee - $student_payment_summary['total_paid'] ?>" required placeholder="Enter amount to pay">
                    </div>
                    <button class="btn btn-success" type="submit" name="submit_payment">Submit Payment</button>
                </form>
                <?php else: ?>
                <div class="alert alert-success">
                    <strong>Course Fee Fully Paid!</strong> This student has completed all payments.
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Payment Receipt -->
        <?php if ($payment_success && $receipt_data): ?>
        <div class="section receipt-section">
            <div class="receipt">
                <img src="assets/logo.png" alt="Suzit's Special Batch Logo" height="150" width="150" style="display: block; margin: auto;" />
                <h3>PAYMENT RECEIPT</h3>
                <div class="receipt-info">
                    <strong>Date & Time:</strong> <?= $receipt_data['payment_date'] ?> at <?= $receipt_data['time'] ?>
                </div>
                <div class="receipt-info">
                    <strong>Roll Number:</strong> <?= htmlspecialchars($receipt_data['roll']) ?>
                </div>
                <div class="receipt-info">
                    <strong>Student Name:</strong> <?= htmlspecialchars($receipt_data['name']) ?>
                </div>
                <div class="receipt-info">
                    <strong>Total Course Fee:</strong> ৳<?= number_format($receipt_data['total_course_fee']) ?>
                </div>
                <div class="receipt-info">
                    <strong>Paid Today:</strong> <span class="paid-amount">৳<?= number_format($receipt_data['paid_now']) ?></span>
                </div>
                <div class="receipt-info">
                    <strong>Total Paid So Far:</strong> <span class="paid-amount">৳<?= number_format($receipt_data['total_paid_so_far']) ?></span>
                </div>
                <div class="receipt-info">
                    <strong>Remaining Due:</strong> <span class="due-amount">৳<?= number_format($receipt_data['due_amount']) ?></span>
                </div>
                
                <div class="signature-section">
                    <p>_________________________</p>
                    <p><strong>Signature of Officer</strong></p>
                    <p>Suzit Special Batch</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Payment History Section -->
        <div class="section history-section">
            <h3 class="no-print">Payment History</h3>
            <form method="GET" class="no-print">
                <div class="form-group">
                    <label>Search History by Roll:</label>
                    <input type="text" name="history_search" required>
                    <button class="btn" type="submit">Search History</button>
                    <?php if (!empty($payment_history)): ?>
                    <button class="btn" type="button" onclick="printHistory()">Print History</button>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (!empty($payment_history)): ?>
                <?php 
                $history_summary = getStudentPaymentSummary($conn, $_GET['history_search']);
                ?>
                
                <div class="history-print-header" style="display: none;">
                    <h2>PAYMENT HISTORY</h2>
                    <h3>Student: <?= htmlspecialchars($payment_history[0]['name']) ?> (Roll: <?= htmlspecialchars($_GET['history_search']) ?>)</h3>
                </div>
                
                <div class="summary-box">
                    <h4>Payment Summary for Roll: <?= htmlspecialchars($_GET['history_search']) ?></h4>
                    <div class="summary-item">
                        <strong>Total Course Fee:</strong> ৳<?= number_format($history_summary['course_fee']) ?>
                    </div>
                    <div class="summary-item">
                        <strong>Total Paid:</strong> <span class="paid-amount">৳<?= number_format($history_summary['total_paid']) ?></span>
                    </div>
                    <div class="summary-item">
                        <strong>Remaining Due:</strong> <span class="due-amount">৳<?= number_format($history_summary['due']) ?></span>
                    </div>
                </div>

                

                <h4 class="no-print">Payment History Details</h4>
                <table>
                    <tr>
                        <th>Payment Date</th>
                        <th>Roll</th>
                        <th>Name</th>
                        <th>Paid Amount</th>
                        <th>Total Paid Till Date</th>
                        <th>Due After Payment</th>
                    </tr>
                    <?php foreach ($payment_history as $row): ?>
                        <tr>
                            <td><?= $row['payment_date'] ?></td>
                            <td><?= htmlspecialchars($row['roll']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td class="paid-amount">৳<?= number_format($row['paid_amount']) ?></td>
                            <td class="paid-amount">৳<?= number_format($row['total_paid_so_far']) ?></td>
                            <td class="due-amount">৳<?= number_format($row['due_amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                
                <div class="signature-section">
                    <p>_________________________</p>
                    <p><strong>Signature of Officer</strong></p>
                    <p>Suzit Special Batch</p>
                </div>
            <?php elseif (isset($_GET['history_search'])): ?>
                <div class="alert alert-info">
                    No payment history found for this roll number.
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-print receipt
        <?php if ($payment_success): ?>
        setTimeout(function() {
            if (confirm('Payment successful! Do you want to print the receipt?')) {
                printReceipt();
            }
        }, 1000);
        <?php endif; ?>
        
        function printReceipt() {
            document.body.classList.add('print-receipt');
            window.print();
            document.body.classList.remove('print-receipt');
        }
        
        function printHistory() {
            // Show print header for history
            document.querySelector('.history-print-header').style.display = 'block';
            document.body.classList.add('print-history');
            window.print();
            document.body.classList.remove('print-history');
            document.querySelector('.history-print-header').style.display = 'none';
        }
    </script>
</body>
</html>