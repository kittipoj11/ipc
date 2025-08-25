<?php
// เริ่มต้น Session เพื่อจำลองการ Login ของผู้ใช้งาน
session_start();

// กำหนดบทบาทของผู้ใช้งาน (ในชีวิตจริง ควรดึงข้อมูลมาจากฐานข้อมูล)
// บทบาทที่กำหนดไว้มีดังนี้: 'contractor', 'project_manager', 'finance_manager'
// ผู้รับเหมา: สามารถส่งเอกสารได้
// ผู้จัดการโครงการ: สามารถอนุมัติในขั้นตอนแรกได้
// ผู้จัดการฝ่ายการเงิน: สามารถอนุมัติในขั้นตอนสุดท้ายได้
$currentUserRole = 'project_manager'; // คุณสามารถเปลี่ยนค่าเพื่อทดสอบบทบาทต่างๆ ได้

// ข้อมูลจำลองของเอกสาร Payment Claim (ในชีวิตจริง ควรดึงมาจากฐานข้อมูล)
$claimData = [
    'id' => 12345,
    'projectName' => 'โครงการพัฒนาอาคาร A',
    'contractNo' => 'ABC-001-2025',
    'claimNo' => 'CLAIM-001',
    'amount' => 150000.00,
    'status' => 'Submitted', // สถานะเริ่มต้นของเอกสาร
    'approvedByPM' => false,
    'approvedByFinance' => false,
    'lastActionMessage' => ''
];

// ตรวจสอบการกระทำของผู้ใช้ (การกดปุ่ม)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // การอนุมัติโดยผู้จัดการโครงการ
    if (isset($_POST['approve_pm']) && $currentUserRole === 'project_manager') {
        if ($claimData['status'] === 'Submitted') {
            $claimData['status'] = 'Approved by PM';
            $claimData['approvedByPM'] = true;
            $claimData['lastActionMessage'] = 'เอกสารถูกอนุมัติโดยผู้จัดการโครงการแล้ว';
        }
    }
    // การอนุมัติโดยผู้จัดการฝ่ายการเงิน
    else if (isset($_POST['approve_finance']) && $currentUserRole === 'finance_manager') {
        if ($claimData['status'] === 'Approved by PM') {
            $claimData['status'] = 'Approved by Finance';
            $claimData['approvedByFinance'] = true;
            $claimData['lastActionMessage'] = 'เอกสารถูกอนุมัติโดยฝ่ายการเงินแล้ว';
        }
    }
    // การไม่อนุมัติ (ปฏิเสธ)
    else if (isset($_POST['reject'])) {
        $claimData['status'] = 'Rejected';
        $claimData['lastActionMessage'] = 'เอกสารถูกปฏิเสธ';
    }

    // ในระบบจริง จะต้องมีการบันทึกข้อมูล $claimData ที่อัปเดตแล้วลงฐานข้อมูล
    // เช่น: updateClaimStatus($claimData['id'], $claimData['status']);
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interim Payment Claim Form with Approval</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .status-box {
            text-align: center;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            font-weight: bold;
        }

        .status-submitted {
            background-color: #ffc107;
            color: #333;
        }

        .status-pm {
            background-color: #28a745;
            color: white;
        }

        .status-finance {
            background-color: #007bff;
            color: white;
        }

        .status-rejected {
            background-color: #dc3545;
            color: white;
        }

        .approval-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-size: 16px;
            margin: 5px;
        }

        .btn-approve {
            background-color: #28a745;
        }

        .btn-reject {
            background-color: #dc3545;
        }

        .info-box {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Interim Payment Claim (พร้อมระบบอนุมัติ)</h2>
        <div class="info-box">
            สถานะการเข้าสู่ระบบ: <span style="font-weight: bold;"><?php echo ucwords(str_replace('_', ' ', $currentUserRole)); ?></span>
        </div>

        <?php
        $statusClass = 'status-submitted';
        if ($claimData['status'] === 'Approved by PM') {
            $statusClass = 'status-pm';
        } else if ($claimData['status'] === 'Approved by Finance') {
            $statusClass = 'status-finance';
        } else if ($claimData['status'] === 'Rejected') {
            $statusClass = 'status-rejected';
        }
        ?>
        <div class="status-box <?php echo $statusClass; ?>">
            สถานะปัจจุบัน: <?php echo htmlspecialchars($claimData['status']); ?>
        </div>

        <?php if (!empty($claimData['lastActionMessage'])): ?>
            <p style="text-align: center; color: #333;"><?php echo $claimData['lastActionMessage']; ?></p>
        <?php endif; ?>

        <h3>รายละเอียดเอกสาร</h3>
        <ul>
            <li><b>ชื่อโครงการ:</b> <?php echo htmlspecialchars($claimData['projectName']); ?></li>
            <li><b>เลขที่สัญญา:</b> <?php echo htmlspecialchars($claimData['contractNo']); ?></li>
            <li><b>เลขที่เอกสาร:</b> <?php echo htmlspecialchars($claimData['claimNo']); ?></li>
            <li><b>มูลค่าที่เรียกเก็บ:</b> <?php echo number_format($claimData['amount'], 2); ?> บาท</li>
        </ul>

        <div class="approval-buttons">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <?php
                // แสดงปุ่ม "อนุมัติโดยผู้จัดการโครงการ" เมื่อเงื่อนไขถูกต้อง
                if ($currentUserRole === 'project_manager' && $claimData['status'] === 'Submitted') {
                    echo '<button type="submit" name="approve_pm" class="btn btn-approve">อนุมัติโดย PM</button>';
                    echo '<button type="submit" name="reject" class="btn btn-reject">ปฏิเสธ</button>';
                }
                // แสดงปุ่ม "อนุมัติโดยผู้จัดการฝ่ายการเงิน" เมื่อเงื่อนไขถูกต้อง
                if ($currentUserRole === 'finance_manager' && $claimData['status'] === 'Approved by PM') {
                    echo '<button type="submit" name="approve_finance" class="btn btn-approve">อนุมัติโดยฝ่ายการเงิน</button>';
                    echo '<button type="submit" name="reject" class="btn btn-reject">ปฏิเสธ</button>';
                }
                ?>
            </form>
        </div>

    </div>

</body>

</html>