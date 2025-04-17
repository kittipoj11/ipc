<!-- Main Content Start -->
<?php
require_once  'class/po_class.php';
require_once  'class/supplier_class.php';
require_once  'class/location_class.php';

$po_id = $_REQUEST['po_id'];

$po = new Po;
$rsPoMain = $po->getPoMainByPoId($po_id);
$rsPoPeriod = $po->getPoPeriodByPoId($po_id);

$supplier = new Supplier;
$supplier_rs = $supplier->getRecordAll();

$location = new Location;
$location_rs = $location->getRecordAll();

?>
<style>
  #tablePeriod thead {
    cursor: default;
  }
</style>
<!-- Content Wrapper. Contains page content -->
<!-- <div class="content-wrapper"> -->
<!-- Content Header (Page header) -->
<section class="container-fluid content-header">
  <div class="col-sm-6 d-flex">
    <h6 class="m-1 fw-bold text-uppercase">Purchase Order</h6>
  </div>
  <!-- /.container-fluid -->
</section>

<!-- Main content -->
<section>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">

        <div class="card">
          <div class="card-header">
            <h6 class="m-1 fw-bold"><?= $rsPoMain['po_number'] . " : " . $rsPoMain['supplier_id'] . " - " . $rsPoMain['supplier_name'] ?></h6>
          </div>

          <div class="card-body m-0 p-0">
            <form name="myForm" id="myForm" action="" method="post">
              <input type="text" class="d-none" name="po_id" id="po_id" value=<?= $po_id ?>>

              <div class="row m-1">
                <div class="col-4 input-group input-group-sm">
                  <label for="po_number" class="input-group-text">เลขที่ PO</label>
                  <input type="text" class="form-control" name="po_number" id="po_number" value=<?= $rsPoMain['po_number'] ?> disabled>
                </div>
              </div>

              <div class="row m-1">
                <div class="col-4 input-group input-group-sm">
                  <label for="project_name" class="input-group-text">ชื่อโครงการ</label>
                  <input type="text" class="form-control" name="project_name" id="project_name" value="<?= $rsPoMain['project_name'] ?>">
                </div>

                <div class="col-4 input-group input-group-sm">
                  <label for="supplier_id" class="input-group-text">ผู้รับเหมา</label>
                  <select class="form-select form-control" name="supplier_id" id="supplier_id" value=<?= $rsPoMain['supplier_id'] ?>>
                    <option value="">...</option>
                    <?php
                    foreach ($supplier_rs as $row) :
                      $selected_attr = ($rsPoMain['supplier_id'] == $row['supplier_id']) ? " selected" : "";
                      echo "<option value='{$row['supplier_id']}' {$selected_attr}>{$row['supplier_name']}</option>";
                    // echo "<option value='" . $row['supplier_id'] . "'" . ($rsPoMain['supplier_id'] == $row['supplier_id'] ? " selected" : "") . ">" . htmlspecialchars($row['supplier_name']) . "</option>";
                    endforeach ?>
                  </select>
                </div>

                <div class="col-3 input-group input-group-sm">
                  <label for="location_id" class="input-group-text">สถานที่</label>
                  <select class="form-select form-control" name="location_id" id="location_id">
                    <option value="">...</option>
                    <?php
                    foreach ($location_rs as $row) :
                      $selected_attr = ($rsPoMain['location_id'] == $row['location_id']) ? " selected" : "";
                      echo "<option value='{$row['location_id']}' {$selected_attr}>{$row['location_name']}</option>";
                    endforeach ?>
                  </select>
                </div>
              </div>

              <div class="row m-1">
                <div class="col-4 input-group input-group-sm">
                  <label for="working_name_th" class="input-group-text">ชื่องาน(ภาษาไทย)</label>
                  <input type="text" class="form-control" name="working_name_th" id="working_name_th" value="<?= $rsPoMain['working_name_th'] ?>">
                </div>

                <div class="col-4 input-group input-group-sm">
                  <label for="working_name_en" class="input-group-text">ชื่องาน(ภาษาอังกฤษ)</label>
                  <input type="text" class="form-control" name="working_name_en" id="working_name_en" value="<?= $rsPoMain['working_name_en'] ?>"">
                      </div>
                    </div>
                    <hr>

                    <div class=" row m-1">
                  <div class="col-4 input-group input-group-sm">
                    <label for="contract_value_before" class="input-group-text">PO ไม่รวม VAT</label>
                    <input type="number" step="0.01" class="form-control" name="contract_value_before" id="contract_value_before" value=<?= $rsPoMain['contract_value_before'] ?>>
                  </div>

                  <div class="col-4 input-group input-group-sm">
                    <label for="contract_value" class="input-group-text">PO รวม VAT</label>
                    <input type="number" step="0.01" class="form-control" name="contract_value" id="contract_value" value=<?= $rsPoMain['contract_value'] ?>>
                  </div>

                  <div class="col-2 input-group input-group-sm">
                    <label for="vat" class="input-group-text">VAT</label>
                    <input type="number" step="0.01" class="form-control" name="vat" id="vat" value=<?= $rsPoMain['vat'] ?> data-vat_rate=<?= VAT_RATE ?> readonly>
                  </div>

                  <div class="col-2 input-group input-group-sm">
                    <div class="form-check">
                      <?php
                      $checked_attr = $rsPoMain['is_deposit'] ? "checked" : "";
                      ?>
                      <input class="form-check-input" type="checkbox" name="is_deposit" id="is_deposit" <?= $checked_attr ?>>
                    </div>
                    <label class="form-check-label" for="deposit_percent">เงินมัดจำ</label>
                    <input type="number" step="0.01" class="form-control" name="deposit_percent" id="deposit_percent" value=<?= $rsPoMain['deposit_percent'] ?>>%
                  </div>
                </div>
                <hr>

                <div class="row m-1">
                  <div class="col-4">
                    <div class="row-1 input-group input-group-sm">
                      <label for="working_date_from" class="input-group-text ">ระยะเวลาดำเนินการ</label>
                      <input type="date" class="form-control " name="working_date_from" id="working_date_from" value="<?php echo isset($rsPoMain['working_date_from']) ? htmlspecialchars($rsPoMain['working_date_from']) : ''; ?>">

                    </div>
                  </div>
                  <div class="col-4">
                    <div class="row-1 input-group input-group-sm">
                      <label for="working_date_to" class="input-group-text "> ถึง </label>
                      <input type="date" class="form-control " name="working_date_to" id="working_date_to" value="<?php echo isset($rsPoMain['working_date_to']) ? htmlspecialchars($rsPoMain['working_date_to']) : ''; ?>">
                    </div>
                  </div>

                  <div class="col-2 input-group input-group-sm">
                    <label for="working_day" class="input-group-text">รวม</label>
                    <input type="number" class="form-control" name="working_day" id="working_day" value="<?php echo isset($rsPoMain['working_day']) ? htmlspecialchars($rsPoMain['working_day']) : ''; ?>" readonly>
                  </div>
                </div>

                <hr>

                <div class="card border border-1 border-dark m-1">
                  <h6 class="m-1 fw-bold">รายการงวดงาน</h6>
                  <!-- <div class="card-header" style="display: flex;"> -->
                  <div class="m-1">
                    <a id="btnAdd" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="right" title="เพิ่มงวดงาน">
                      Add Period
                    </a>

                    <a id="btnDeleteLast" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="ลบงวดงานล่าสุด">
                      Delete last period
                    </a>
                    <a id="btnClear" class="btn btn-danger btn-sm d-none" data-toggle="tooltip" data-placement="right" title="ลบงวดงานทั้งหมด">
                      Clear all period
                    </a>
                  </div>

                  <div class="card-body p-0">
                    <!-- สร้าง Table ตามปกติ -->
                    <table class="table table-bordered justify-content-center text-center" id="tablePeriod">
                      <thead>
                        <tr>
                          <th class="p-1" width="5%">งวดงาน</th>
                          <th class="p-1" width="15%">งานที่แล้วเสร็จตามแผน(%)</th>
                          <th class="p-1" width="15%">จำนวนเงิน</th>
                          <th class="p-1" width="10%">คิดเป็น(%)</th>
                          <th class="p-1">เงื่อนไขการจ่ายเงิน</th>
                          <th class="p-1" width="5%">Crud</th>
                          <th class="p-1 d-nonex" width="5%">period_id</th>
                        </tr>
                      </thead>

                      <tbody id="tbody-period">
                        <?php foreach ($rsPoPeriod as $row) { ?>
                          <tr class="firstTr" crud='s'>
                            <!-- กำหนดลำดับ Auto 1, 2, 3, ... -->
                            <td class="input-group-sm p-0"><input type="number" step="0.01" name="period_numbers[]" class="form-control period_number" value="<?php echo isset($row['period_number']) ? htmlspecialchars($row['period_number']) : ''; ?>" readonly>
                            </td>
                            <td class="input-group-sm p-0"><input type="number" step="0.01" name="workload_planned_percents[]" class="form-control workload_planned_percent" value="<?php echo isset($row['workload_planned_percent']) ? htmlspecialchars($row['workload_planned_percent']) : ''; ?>">
                            </td>
                            <td class="input-group-sm p-0"><input type="number" step="0.01" name="interim_payments[]" class="form-control interim_payment" value="<?php echo isset($row['interim_payment']) ? htmlspecialchars($row['interim_payment']) : ''; ?>">
                            </td>
                            <td class="input-group-sm p-0"><input type="number" step="0.01" name="interim_payment_percents[]" class="form-control interim_payment_percent" value="<?php echo isset($row['interim_payment_percent']) ? htmlspecialchars($row['interim_payment_percent']) : ''; ?>">
                            </td>
                            <td class="input-group-sm p-0">
                              <input type="text" name="remarks[]" class="form-control remark" value="<?php echo isset($row['remark']) ? htmlspecialchars($row['remark']) : ''; ?>">
                            </td>
                            <td class="input-group-sm p-0">
                              <input type="text" name="cruds[]" class="form-control crud" value="s">
                            </td>
                            <td class="input-group-sm p-0 d-nonex"><input type="text" name="period_ids[]" class="form-control period_id" value="<?php echo isset($row['period_id']) ? htmlspecialchars($row['period_id']) : ''; ?>" readonly></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- <div class="card-footer p-0 d-flex justify-content-end">
                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm m-1" value="บันทึก">
                        <button type="button" name="btnCancel" id="btnCancel" class="btn btn-secondary btn-sm m-1">ยกเลิก</button>
                      </div> -->

                <div class="container-fluid  p-0 d-flex justify-content-between">
                  <button type="button" name="btnBack" class="btn btn-primary btn-sm m-1 btnBack"> <i class="fi fi-rr-left"></i> </button>
                  <div>
                    <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm m-1" value="บันทึก" data-current_approval_level="<?= $rsInspectionPeriod['current_approval_level'] ?>">
                    <button type="button" name="btnCancel" class="btn btn-warning btn-sm m-1 btnCancel">ยกเลิก</button>
                  </div>
                </div>

            </form>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
<!-- /.content -->
<!-- </div> -->
<!-- /.content-wrapper -->

<script>
  function po_edit() {
    // $(".card").ready(function() {
      $("#myForm").on("submit", function(e) {
        // $(document).on("click", "#btnSave", function (e) {
        // console.log('submit');
        e.preventDefault();
        let can_save = true;
        if (can_save == true) {
          let data_sent = $("#myForm").serializeArray();
          data_sent.push({
            name: "action",
            value: "update",
          });
          // console.log(data_sent);
          // return;
          $.ajax({
            type: "POST",
            url: "po_crud.php",
            // data: $(this).serialize(),
            data: data_sent,
            success: function(response) {
              Swal.fire({
                icon: "success",
                title: "Data saved successfully",
                color: "#716add",
                allowOutsideClick: false,
                background: "black",
                // backdrop: `
                //                     rgba(0,0,123,0.4)
                //                     url("_images/paw.gif")
                //                     left bottom
                //                     no-repeat
                //                     `,
                // showConfirmButton: false,
                // timer: 15000
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = "po.php";
                  // window.location.reload();
                }
              });
              // window.location.href = 'main.php?page=open_area_schedule';
            },
          });
        }
      });

      $("#btnAdd").click(function() {
        let period;
        // console.log($(".firstTr:last").find(".period:last").val());
        // $(".firstTr:has(.crud:not([value='d'])):last")//แบบที่ 1
        // $(".firstTr").has(".crud:not([value='d'])").last()//แบบที่ 2
        if ($("#tbody-period").has(".firstTr[crud!='d']").length > 0) {
          period = $(".firstTr[crud!='d']:last").find(".period_number:last").val();
          period++;
          $(".firstTr[crud!='d']:last")
            .clone(false)
            .attr("crud", "i")
            .removeClass("d-none")

            .find(".period_number:last")
            .val(period)
            .end()

            .find(".workload_planned_percent:last")
            .val("")
            .end()

            .find(".interim_payment:last")
            .val("")
            .end()

            .find(".interim_payment_percent:last")
            .val("")
            .end()

            .find(".remark:last")
            .val("")
            .end()

            .find(".period_id:last")
            .val("")
            .end()

            .find("td input.crud")
            .val("i")
            .end()

            // .find("a:first")
            // .css("display", "inline")
            // .css("color", "red")
            // .end()

            // .find("a:last")
            // .css("display", "inline")
            // .css("color", "red")
            // .end()

            // .find("a:first")
            // .attr("iid", "" + i + "")
            // .end()

            .appendTo("#tbody-period");
        } else {
          // Create the new tr element using jQuery
          const firstTr = `<tr class='firstTr' crud='i'>
                            <td class='input-group-sm p-0'><input type='number' name='period_numbers[]' class='form-control period_number' value='1' readonly></td>
                            <td class='input-group-sm p-0'><input type='number' name='workload_planned_percents[]' class='form-control workload_planned_percent'></td>
                            <td class='input-group-sm p-0'><input type='number' name='interim_payments[]' class='form-control interim_payment'></td>
                            <td class='input-group-sm p-0'><input type='number' name='interim_payment_percents[]' class='form-control interim_payment_percent'></td>
                            <td class='input-group-sm p-0'><input type='text' name='remarks[]' class='form-control remark'></td>
                            <td class='input-group-sm p-0'><input type='text' name='cruds[]' class='form-control crud' value='i'></td>
                            <td class='input-group-sm p-0 d-nonex'><input type='text' name='period_id[]' class='form-control period_id' readonly></td>
                          </tr>`;

          $("#tbody-period").append(firstTr);
        }
      });

      $("#btnDeleteLast").click(function() {
        let period;
        // ลบ tr ตัวล่างสุดที่ไม่ใช่ tr ตัวแรก ใน #tbody-period
        // $("#tbody-period").find("tr:not(:first):last").remove();
        $("#tbody-period .firstTr[crud!='d']:last")
          // $("#tbody-period tr:not(:first)[crud!='d']:last")
          .attr("crud", "d")
          .addClass("d-none")

          .find("td input.crud")
          .val("d")
          .end();
      });

      $("#btnClear").click(function() {
        // ลบ tr ทั้งหมดที่ไม่ใช่ตัวแรกใน #tbody-period
        // $("#tbody-period tr:gt(0)").remove();
        // หรือ
        // $("#tbody-period").find("tr:not(:first)").remove();
        // หรือ
        $("#tbody-period").find("tr:gt(0)").remove();
      });

      $(".btnCancel , .btnBack").click(function() {
        window.history.back();
        // window.history.go(-1);
        // window.location.href = "po.php";
      });

      $("#contract_value_before").on("change keyup", function() {
        let contract_value_before = parseFloat($(this).val());
        let vat_rate = parseFloat($("#vat").data("vat_rate"));

        if (!isNaN(contract_value_before) && !isNaN(vat_rate)) {
          var vat_amount = contract_value_before * (vat_rate / 100);
          var contract_value = contract_value_before + vat_amount;

          $("#contract_value").val(contract_value.toFixed(2)); // แสดงผลรวม VAT (ทศนิยม 2 ตำแหน่ง)
          $("#vat").val(vat_amount.toFixed(2)); // แสดงผลรวม VAT (ทศนิยม 2 ตำแหน่ง)
        } else {
          $("#contract_value").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
          $("#vat").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
        }
      });

      $("#contract_value").on("change keyup", function() {
        let contract_value = parseFloat($(this).val());
        let vat_rate = parseFloat($("#vat").data("vat_rate"));

        if (!isNaN(contract_value) && !isNaN(vat_rate)) {
          var contract_value_before = contract_value / (1 + vat_rate / 100);
          $("#contract_value_before").val(contract_value_before.toFixed(2)); // แสดงผลลัพธ์ (ทศนิยม 2 ตำแหน่ง)
          $("#vat").val((contract_value - contract_value_before).toFixed(2)); // แสดงผลรวม VAT (ทศนิยม 2 ตำแหน่ง)
        } else {
          $("#contract_value_before").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
          $("#vat").val(""); // ล้างค่าถ้าป้อนไม่ถูกต้อง
        }
      });

      $("#working_date_from, #working_date_to").on("change", function() {
        var working_date_from = $("#working_date_from").val();
        var working_date_to = $("#working_date_to").val();

        if (working_date_from && working_date_to) {
          var start = new Date(working_date_from);
          var end = new Date(working_date_to);
          var timeDiff = Math.abs(end.getTime() - start.getTime());
          var working_day = Math.ceil(timeDiff / (1000 * 3600 * 24));
          $("#working_day").val(working_day + 1);
        } else {
          $("#working_day").val("");
        }
      });
    // });

  }
</script>