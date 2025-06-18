<?php
// ไม่ต้อง require_once ที่นี่ แต่จะไป require ในไฟล์ที่ใช้งานจริง
// require_once 'connection_class.php';

class Location
{
    /** @var PDO */
    private $db; // เปลี่ยนเป็น private และใช้ชื่อที่สื่อความหมาย

    /**
     * รับ PDO connection object เข้ามาทาง Constructor
     */
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }

    public function fetchAll()
    {
        $sql = <<<EOD
                    select location_id, location_name, is_deleted 
                    from locations 
                    where is_deleted = false
                EOD;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();
        return $rs;
    }

    public function fetchById($id): ?array
    {
        $sql = <<<EOD
                select location_id, location_name, is_deleted 
                from locations
                where is_deleted = false
                and location_id = :id
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();

        // แบบใหม่
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }
        return $rs;

        // แบบเก่า
        // if ($rs) {
        //     // ❗️ สำคัญ: คืนค่าเป็น array ข้อมูล Data ทั้งหมด
        //     return $rs;
        //     // return true;
        // } else {
        //     // ถ้าไม่เจอ Data  ให้คืนค่า false
        //     return false;
        // }
    }

    /**
     * สร้างข้อมูลใหม่ในฐานข้อมูล (INSERT)
     * @param array $getData ข้อมูลในรูปแบบ associative array
     * @return string|false ID ของที่สร้างใหม่ หรือ false หากล้มเหลว
     */
    public function create(array $getData)
    {
        $sql = "INSERT INTO locations(location_name)
                VALUES (:location_name)";

        try {
            $stmt = $this->db->prepare($sql);
            /* //รูปแบบเดิม
            $stmt->bindParam(':location_name', $location_name, PDO::PARAM_STR);
            $stmt->execute();
            */

            $stmt->execute([
                ':location_name'      => $getData['location_name']
            ]);

            // คืนค่า ID ของแถวที่เพิ่งเพิ่มเข้าไปใหม่
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // ในสถานการณ์จริง ควรจะ Log error แทนการ echo
            // error_log($e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตข้อมูล Data  (UPDATE)
     * @param int $getId ID ของ Data ที่ต้องการแก้ไข
     * @param array $getData ข้อมูลใหม่ที่ต้องการอัปเดต
     * @return bool true หากสำเร็จ, false หากล้มเหลว
     */
    public function update(int $getId, array $getData)
    {
        $sql = "UPDATE locations 
                SET location_name = :location_name
                WHERE location_id = :location_id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':location_name'     => $getData['location_name'],
                ':location_id'       => $getId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * ลบผู้ใช้ (DELETE)
     * @param int $getId ID ของผู้ใช้ที่ต้องการลบ
     * @return bool true หากสำเร็จ, false หากล้มเหลว
     */
    public function delete(int $getId)
    {
        // คำแนะนำ: ในระบบงานจริงส่วนใหญ่นิยมใช้วิธี "Soft Delete"
        // คือการอัปเดต field เช่น is_deleted = 1 แทนการลบข้อมูลจริงออกจากฐานข้อมูล
        $sql = "UPDATE locations 
                SET is_deleted = 1
                WHERE location_id = :location_id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':location_id' => $getId]);
        } catch (PDOException $e) {
            return false;
        }
    }
    /*
        create() คืนค่า lastInsertId() เพื่อให้เรารู้ว่าข้อมูลใหม่ที่สร้างมี ID อะไร สามารถนำไปใช้ต่อได้ทันที
        update() และ delete() คืนค่าเป็น boolean (true/false) เพื่อบอกสถานะความสำเร็จให้โค้ดที่เรียกใช้ทราบได้ง่ายๆ
    */

    public function getHtmlData()
    {
        $sql = "select location_id, location_name, is_deleted 
                from locations 
                where is_deleted = false";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetchAll();

        $html = "<p>รายงาน Location ทั้งหมด</p>";

        // เรียกใช้งาน ฟังก์ชั่นดึงข้อมูลไฟล์มาใช้งาน
        $html .= "<style>";
        $html .= "table, th, td {";
        $html .= "border: 1px solid black;";
        $html .= "border-radius: 10px;";
        $html .= "background-color: #b3ffb3;";
        $html .= "padding: 5px;}";
        $html .= "</style>";
        $html .= "<table cellspacing='0' cellpadding='1' style='width:1100px;'>";
        $html .= "<tr>";
        $html .= "<th align='center' bgcolor='F2F2F2'>รหัส Location </th>";
        $html .= "<th align='center' bgcolor='F2F2F2'> Location </th>";
        $html .= "</tr>";
        foreach ($rs as $row) :
            $html .=  "<tr bgcolor='#c7c7c7'>";
            $html .=  "<td>{$row['location_id']}</td>";
            $html .=  "<td>{$row['location_name']}</td>";
            $html .=  "</tr>";
        endforeach;

        $html .= "</table>";

        return $html;
    }
}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);