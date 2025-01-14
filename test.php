<?php
try {
  echo '1/0 = ';
  echo 1/0;
  throw new Exception("xxx");
// } catch (Throwable $e) {
// } catch (DivisionByZeroError $e) {
//   echo "My error1: " . 'DivisionByZeroError';
} catch (Exception $e) {
  // throw new Exception("xxx");
  echo "My error2: ". $e->getMessage();
}