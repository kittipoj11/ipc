<?php
header('Content-Type: application/json');
require 'db_config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Invalid action.'];

try {
    switch ($action) {
        // --- Admin Actions ---
        case 'create_event':
            $pdo->beginTransaction();

            // 1. สร้าง Event หลักก่อน
            $stmt = $pdo->prepare("INSERT INTO events (name) VALUES (?)");
            $stmt->execute([$_POST['name']]);
            $event_id = $pdo->lastInsertId();

            // 2. แยกส่วนข้อมูลลานจอดและ Insert ลงตาราง event_parking_lots
            $lots_str = trim($_POST['parking_lots']);
            if (!empty($lots_str)) {
                $stmt_lot = $pdo->prepare("INSERT INTO event_parking_lots (event_id, lot_name, capacity) VALUES (?, ?, ?)");
                $pairs = explode(',', $lots_str);
                foreach ($pairs as $pair) {
                    list($key, $value) = explode(':', trim($pair));
                    $stmt_lot->execute([$event_id, trim($key), (int)trim($value)]);
                }
            }
            $pdo->commit();
            $response = ['status' => 'success', 'message' => 'สร้างงานและกำหนดลานจอดสำเร็จ!'];
            break;

        case 'add_slot':
            // Logic ส่วนนี้เหมือนเดิม ไม่มีการเปลี่ยนแปลง
            $pdo->beginTransaction();
            $start_date = new DateTime($_POST['date_from']);
            $end_date = new DateTime($_POST['date_to']);
            $end_date->modify('+1 day'); // เพิ่ม 1 วันเพื่อให้ loop ครอบคลุมวันสุดท้าย
            $interval = new DateInterval('P1D');
            $date_range = new DatePeriod($start_date, $interval, $end_date);

            $added_count = 0;
            foreach ($date_range as $date) {
                $current_date = $date->format('Y-m-d');
                $start_time = $_POST['time_from'];
                $end_time = $_POST['time_to'];

                // ตรวจสอบการทับซ้อนของเวลา
                $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM booking_slots WHERE event_id = ? AND slot_date = ? AND (? < end_time AND ? > start_time)");
                $stmt_check->execute([$_POST['event_id'], $current_date, $start_time, $end_time]);
                if ($stmt_check->fetchColumn() > 0) {
                    throw new Exception("มีช่วงเวลาที่กำหนดทับซ้อนกันในวันที่ $current_date");
                }

                $stmt_insert = $pdo->prepare("INSERT INTO booking_slots (event_id, slot_date, start_time, end_time) VALUES (?, ?, ?, ?)");
                $stmt_insert->execute([$_POST['event_id'], $current_date, $start_time, $end_time]);
                $added_count++;
            }
            $pdo->commit();
            $response = ['status' => 'success', 'message' => "เพิ่มช่วงเวลาสำเร็จ ($added_count วัน)!"];
            break;

        // --- Vendor/Shared Actions ---
        case 'get_active_events':
            // เหมือนเดิม
            $stmt = $pdo->query("SELECT e.id, e.name FROM events e JOIN booking_slots bs ON e.id = bs.event_id WHERE bs.slot_date >= CURDATE() GROUP BY e.id, e.name ORDER BY e.name ASC");
            $response = ['status' => 'success', 'data' => $stmt->fetchAll()];
            break;

        case 'get_event_details':
            $event_id = $_GET['event_id'];
            // ดึงข้อมูลลานจอดจาก
            $stmt_lots = $pdo->prepare("SELECT lot_name FROM event_parking_lots WHERE event_id = ? ORDER BY lot_name ASC");
            $stmt_lots->execute([$event_id]);
            $lots = $stmt_lots->fetchAll(PDO::FETCH_COLUMN);

            // ดึงวันที่ที่ยังจองได้
            $stmt_dates = $pdo->prepare("SELECT DISTINCT slot_date FROM booking_slots WHERE event_id = ? AND slot_date >= CURDATE() ORDER BY slot_date ASC");
            $stmt_dates->execute([$event_id]);
            $dates = $stmt_dates->fetchAll(PDO::FETCH_COLUMN);

            $response = ['status' => 'success', 'data' => ['lots' => $lots, 'dates' => $dates]];
            break;

        case 'get_time_slots':
            // เหมือนเดิม
            $event_id = $_GET['event_id'];
            $date = $_GET['date'];
            $stmt = $pdo->prepare("SELECT start_time, end_time FROM booking_slots WHERE event_id = ? AND slot_date = ? AND CONCAT(slot_date, ' ', start_time) > NOW() ORDER BY start_time ASC");
            $stmt->execute([$event_id, $date]);
            $response = ['status' => 'success', 'data' => $stmt->fetchAll()];
            break;

        case 'get_availability':
            $event_id = $_GET['event_id'];
            $lot = $_GET['lot'];
            $date = $_GET['date'];
            $time = $_GET['time'];

            // หาความจุเริ่มต้น
            $stmt = $pdo->prepare("SELECT capacity FROM event_parking_lots WHERE event_id = ? AND lot_name = ?");
            $stmt->execute([$event_id, $lot]);
            $initial_capacity = $stmt->fetchColumn() ?? 0;

            // หายอดจองทั้งหมด
            $stmt_booked = $pdo->prepare("SELECT SUM(num_cars) as total_booked FROM bookings WHERE event_id = ? AND parking_lot = ? AND booking_date = ? AND start_time = ?");
            $stmt_booked->execute([$event_id, $lot, $date, $time]);
            $total_booked = $stmt_booked->fetchColumn() ?? 0;

            $available = $initial_capacity - $total_booked;
            $response = ['status' => 'success', 'data' => ['available' => $available]];
            break;

        case 'create_booking':
            $vendor_name = $_POST['vendor_name'];
            $event_id = $_POST['event_id'];
            $parking_lot = $_POST['parking_lot'];
            $booking_date = $_POST['booking_date'];
            $start_time = $_POST['start_time'];
            $num_cars = (int)$_POST['num_cars'];

            if ($num_cars <= 0 || $num_cars > 10) {
                throw new Exception("จำนวนรถต้องอยู่ระหว่าง 1 ถึง 10 คัน");
            }

            $pdo->beginTransaction();
            // 1. ตรวจสอบว่า vendor จองวันนั้นไปหรือยัง
            $stmt_check_vendor = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE vendor_name = ? AND event_id = ? AND booking_date = ?");
            $stmt_check_vendor->execute([$vendor_name, $event_id, $booking_date]);
            if ($stmt_check_vendor->fetchColumn() > 0) {
                throw new Exception("คุณได้ทำการจองสำหรับงานนี้ในวันที่ $booking_date ไปแล้ว");
            }

            // 2. ตรวจสอบจำนวนที่ว่างอีกครั้ง (ป้องกัน Race Condition)
            $stmt_cap = $pdo->prepare("SELECT capacity FROM event_parking_lots WHERE event_id = ? AND lot_name = ?");
            $stmt_cap->execute([$event_id, $parking_lot]);
            $initial_capacity = $stmt_cap->fetchColumn() ?? 0;

            $stmt_booked = $pdo->prepare("SELECT SUM(num_cars) as total_booked FROM bookings WHERE event_id = ? AND parking_lot = ? AND booking_date = ? AND start_time = ? FOR UPDATE");
            $stmt_booked->execute([$event_id, $parking_lot, $booking_date, $start_time]);
            $total_booked = $stmt_booked->fetchColumn() ?? 0;

            if (($total_booked + $num_cars) > $initial_capacity) {
                throw new Exception("ขออภัย ช่องจอดไม่เพียงพอ ที่จอดเหลือ " . ($initial_capacity - $total_booked) . " คัน");
            }

            // 3. ถ้าผ่านหมด ให้ทำการจอง
            $stmt_insert_booking = $pdo->prepare("INSERT INTO bookings (vendor_name, event_id, parking_lot, booking_date, start_time, num_cars) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_insert_booking->execute([$vendor_name, $event_id, $parking_lot, $booking_date, $start_time, $num_cars]);

            $pdo->commit();
            $response = ['status' => 'success', 'message' => "การจองสำเร็จ!"];
            break;

        default:
            $response = ['status' => 'error', 'message' => 'Action not found.'];
            break;
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
