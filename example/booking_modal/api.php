<?php
include "db.php";
$action = $_GET['action'] ?? '';

if ($action == "fetch") {
    $stmt = $pdo->query("SELECT * FROM tbl_booking_header ORDER BY id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$r) {
        $stmt2 = $pdo->prepare("SELECT * FROM tbl_booking_details WHERE booking_id=?");
        $stmt2->execute([$r['id']]);
        $r['details'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode($rows);
}

if ($action == "save") {
    $data = $_POST;
    $details = json_decode($data['details'], true);

    if (empty($data['id'])) {
        // Insert Header
        $id = uniqid("BKG");
        $sql = "INSERT INTO tbl_booking_header (id, booking_name, email, phone, booth, reservation_id) 
                VALUES (:id,:booking_name,:email,:phone,:booth,:reservation_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":id"=>$id,
            ":booking_name"=>$data["booking_name"],
            ":email"=>$data["email"],
            ":phone"=>$data["phone"],
            ":booth"=>$data["booth"],
            ":reservation_id"=>$data["reservation_id"]
        ]);
    } else {
        // Update Header
        $id = $data['id'];
        $sql = "UPDATE tbl_booking_header SET booking_name=:booking_name,email=:email,phone=:phone,booth=:booth,reservation_id=:reservation_id WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":id"=>$id,
            ":booking_name"=>$data["booking_name"],
            ":email"=>$data["email"],
            ":phone"=>$data["phone"],
            ":booth"=>$data["booth"],
            ":reservation_id"=>$data["reservation_id"]
        ]);
        $pdo->prepare("DELETE FROM tbl_booking_details WHERE booking_id=?")->execute([$id]);
    }

    // Insert Details
    $sql = "INSERT INTO tbl_booking_details (id, booking_id, booking_date, booking_time_start, booking_time_end, car_license_number, car_type_id, driver_name, driver_mobile, booking_data, running_number) 
            VALUES (:id,:booking_id,:booking_date,:booking_time_start,:booking_time_end,:car_license_number,:car_type_id,:driver_name,:driver_mobile,:booking_data,:running_number)";
    $stmt = $pdo->prepare($sql);

    foreach ($details as $index => $d) {
        $stmt->execute([
            ":id"=>uniqid("DTL"),
            ":booking_id"=>$id,
            ":booking_date"=>$d["booking_date"],
            ":booking_time_start"=>$d["booking_time_start"],
            ":booking_time_end"=>$d["booking_time_end"],
            ":car_license_number"=>$d["car_license_number"],
            ":car_type_id"=>$d["car_type_id"],
            ":driver_name"=>$d["driver_name"],
            ":driver_mobile"=>$d["driver_mobile"],
            ":booking_data"=>json_encode($d),
            ":running_number"=>$index+1
        ]);
    }

    echo json_encode(["status"=>"success"]);
}

if ($action == "delete") {
    $id = $_POST['id'];
    $pdo->prepare("DELETE FROM tbl_booking_details WHERE booking_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM tbl_booking_header WHERE id=?")->execute([$id]);
    echo json_encode(["status"=>"deleted"]);
}
?>
