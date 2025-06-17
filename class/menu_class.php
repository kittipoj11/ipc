<?php
// require_once 'config.php';
require_once 'connection_class.php';

class Menu extends Connection
{
    public function fetchAll()
    {
        $sql = <<<EOD
                SELECT `id`, `parent_id`, `title`, `url`, `icon`, `order_num`
                FROM menu_items 
                ORDER BY parent_id, order_num
                EOD;
        $stmt = $this->myConnect->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();

        return $rs;
    }

    public function fetchById($id)
    {
        $sql = <<<EOD
                select `id`, `parent_id`, `title`, `url`, `icon`, `order_num` 
                from menu_items
                where role_id = :id
                EOD;

        $stmt = $this->myConnect->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs;
    }
}


// $stmt = $conn->prepare('select ...where :');
// $stmt->bindParam(':building_id', $building_id, PDO::PARAM_STR);
// $stmt->bindParam(':building_name', $building_name, PDO::PARAM_STR);
// $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
// $stmt->execute();
// $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);