<?php

function separateArraysByStatus(array $inputArray): array {
  /**
   * แยกค่าจาก array ตามสถานะ 'a', 'b', 'c' ใน PHP
   *
   * @param array $inputArray array ที่ต้องการแยกค่า
   * @return array associative array ที่มี arrays A_arrays, B_arrays, C_arrays
   */
  $A_arrays = [];
  $B_arrays = [];
  $C_arrays = [];

  foreach ($inputArray as $item) {
    if ($item['status'] === 'a') {
      $A_arrays[] = $item;
    } elseif ($item['status'] === 'b') {
      $B_arrays[] = $item;
    } elseif ($item['status'] === 'c') {
      $C_arrays[] = $item;
    }
  }
  return ['A_arrays' => $A_arrays, 'B_arrays' => $B_arrays, 'C_arrays' => $C_arrays];
}

// ตัวอย่างการใช้งาน
$originalArray = [
  ['name' => 'Item 1', 'status' => 'a', 'value' => 10],
  ['name' => 'Item 2', 'status' => 'b', 'value' => 20],
  ['name' => 'Item 3', 'status' => 'c', 'value' => 30],
  ['name' => 'Item 4', 'status' => 'a', 'value' => 40],
  ['name' => 'Item 5', 'status' => 'b', 'value' => 50],
  ['name' => 'Item 6', 'status' => 'c', 'value' => 60],
];

$separatedArrays = separateArraysByStatus($originalArray);

echo "A_arrays: ";
print_r($separatedArrays['A_arrays']);
echo "\n";

echo "B_arrays: ";
print_r($separatedArrays['B_arrays']);
echo "\n";

echo "C_arrays: ";
print_r($separatedArrays['C_arrays']);
echo "\n";

?>