<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Menu {
    private $db; 
    public function __construct(PDO $pdoConnection)
    {
        $this->db = $pdoConnection;
    }
    public function fetchAll()
    {
        $sql = <<<EOD
                SELECT `id`, `parent_id`, `title`, `url`, `icon`, `order_num`
                FROM menu_items 
                ORDER BY parent_id, order_num
                EOD;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();

        return $rs;
    }

    public function fetchById($id):?array
    {
        $sql = <<<EOD
                select `id`, `parent_id`, `title`, `url`, `icon`, `order_num` 
                from menu_items
                where role_id = :id
                EOD;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        
        if (!$rs) {
            return null; // ไม่พบข้อมูล
        }
        return $rs;
    }
}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);