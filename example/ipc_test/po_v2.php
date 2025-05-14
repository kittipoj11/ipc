    <?php
    require_once  'class/po_class.php';
    require_once  'class/supplier_class.php';
    require_once  'class/location_class.php';
    $po = new Po;
    $rsPoMainAll = $po->getPoMainAll();
    $rsPoPeriod = $po->getPoPeriodByPoId(0);
    ?>

    <!-- Content Wrapper. Contains page content -->
    <!-- <div class="content-wrapper"> -->
    <!-- Content Header (Page header) -->
    <section class="container-fluid content-header">
      <div class="col-sm-6 d-flex">
        <h6 class="m-1 fw-bold text-uppercase">Purchase Order</h6>
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- <div class="result"></div> -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header d-flex">
                <h6 class="m-1 fw-bold">All Purchase Order</h6>
                <a href="po_create.php" class="btn btn-success btn-sm btnNew" title="New" style="margin: 0px 5px 5px 5px;">
                  <i class="fa-solid fa-plus"></i>
                </a>
              </div>

              <div class="card-body p-0" id="card-body">
                <table id="tableMain" class="table table-bordered table-striped table-sm">
                  <thead>
                    <tr>
                      <th class="text-center p-1 d-none">#</th>
                      <th class="text-center p-1" style="width: 150px;">เลขที่ PO</th>
                      <th class="text-center p-1">โครงการ</th>
                      <th class="text-center p-1">ผู้รับเหมา</th>
                      <th class="text-center p-1">สถานที่</th>
                      <th class="text-center p-1">งาน</th>
                      <th class="text-center p-1">มูลค่า PO ไม่รวม VAT</th>
                      <th class="text-center p-1">มูลค่า PO</th>
                      <th class="text-center p-1">จำนวนงวดงาน</th>
                      <th class="text-center p-1 d-none" style="width: 120px;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="tbody">
                    <?php foreach ($rsPoMainAll as $row) {
                      $html = <<<EOD
                                        <tr data-id='{$row['po_id']}'>
                                            <td class="tdMain p-0 d-none">{$row['po_id']}</td>
                                            <td class="tdMain p-0"><a class='link-opacity-100 pe-auto po_number' title='Edit' style='margin: 0px 5px 5px 5px' data-id='{$row['po_number']}'>{$row['po_number']}</a></td>
                                            <td class="tdMain p-0">{$row['project_name']}</td>
                                            <td class="tdMain p-0">{$row['supplier_name']}</td>
                                            <td class="tdMain p-0">{$row['location_name']}</td>
                                            <td class="tdMain p-0">{$row['working_name_th']}</td>
                                            <td class="tdMain p-0 text-right">{$row['contract_value_before']}</td>
                                            <td class="tdMain p-0 text-right">{$row['contract_value']}</td>
                                            <td class="tdMain p-0 text-right">{$row['number_of_period']}</td>
                                            <td class="tdMain p-0 action" align='center'>
                                                <div class='btn-group-sm'>
                                                    <a class='btn btn-warning btn-sm btnEdit' style='margin: 0px 5px 5px 5px' data-id='{$row['po_id']}'>
                                                        <i class='fa-regular fa-pen-to-square'></i>
                                                    </a>
                                                    <a class='btn btn-danger btn-sm btnDelete' style='margin: 0px 5px 5px 5px' data-id='{$row['po_id']}'>
                                                        <i class='fa-regular fa-trash-can'></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        EOD;
                      echo $html;
                    } ?>
                  </tbody>
                </table>
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

    <section class="content-period d-none">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0" id="card-body">
                <table class="table table-bordered justify-content-center text-center" id="tablePeriod">
                  <thead>
                    <tr>
                      <th class="text-center p-1" width="5%">งวดงาน</th>
                      <th class="text-center p-1" width="15%">งานที่แล้วเสร็จตามแผน(%)</th>
                      <th class="text-center p-1" width="15%">จำนวนเงิน</th>
                      <th class="text-center p-1" width="10%">คิดเป็น(%)</th>
                      <th class="text-center p-1">เงื่อนไขการจ่ายเงิน</th>
                    </tr>
                  </thead>
                  <tbody id="tbody-period">
                    <!-- < ?php foreach ($rsPoPeriod as $row) {
                        $html = <<<EOD
                                        <tr>
                                            <td class="p-0 d-none">{$row['po_periods_id']}</td>
                                            <td class="text-left py-0 px-1">{$row['period']}</td>
                                            <td class="text-left py-0 px-1">{$row['interim_payment']}</td>
                                            <td class="text-left py-0 px-1">{$row['interim_payment_percent']}</td>
                                            <td class="text-left py-0 px-1">{$row['remark']}</td>
                                        </tr>
                                    EOD;
                        echo $html;
                      } ?> -->
                  </tbody>
                </table>
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
      function po() {
        // $(document).ready(function() {
        // $('.btnDelete').on('click', function() { // แบบนี้ -> ไม่สามารถใช้งานได้เมื่อสร้างปุ่มขึ้นมาที่หลัง
        $(".card").on("click", ".btnDelete", function(e) {
          e.preventDefault();
          // console.log("Click in PO");
          // const po_id = $(this).data("id");
          const tr = $(this).parents("tr");
          const po_id = $(this).parents("tr").data("id");
          const po_number = $(this).parents("tr").find("a:first").data("id");

          Swal.fire({
            title: "Are you sure?",
            text: `You want to delete PO NO: ${po_number}!`,
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: "gray",
            confirmButtonColor: "red",
            confirmButtonText: "Yes, delete it!",
            // width: 600,
            // padding: '3em',
            color: "#ff0000",
            background: "black",
            // backdrop: `
            //                           rgba(0,0,123,0.4)
            //                           url("_images/Pyh.gif")
            //                           left top
            //                           no-repeat
            //                           `,
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: "po_crud.php", // ให้ส่งข้อมูลไปตาม url ที่กำหนด
                data: {
                  action: "delete",
                  po_id: po_id
                }, //จะทำการส่งเป็นรูปแบบ java object ->{name: value}
                method: "POST", //เป็นวิธีการส่ง POST หรือ GET อาจจะใช้เป็น type: 'post'
                success: function(response) {
                  //ถ้าดึงข้อมูลมาเสร็จเรียบร้อยแล้วข้อมูลจะถูกส่งกลับมาไว้ที่ response
                  if (response == "success") {
                    Swal.fire({
                      title: "Deleted!",
                      text: "Your data has been deleted.",
                      icon: "success",
                      // width: 600,
                      // padding: '3em',
                      color: "#716add",
                      background: "black", //display dialog is black
                      // confirmButtonColor: '#3085d6',
                      confirmButtonText: "OK",
                      // backdrop: `
                      //                 rgba(0,0,123,0.4)
                      //                 url("_images/Pyh.gif")
                      //                 left top
                      //                 no-repeat
                      //                 `,
                    }).then((result) => {
                      if (result.isConfirmed) {
                        tr.remove();
                        $("#tbody-period").addClass("d-none");

                        // window.location.reload(); //เปลี่ยนเป็นโหลด Table Body ใหม่เพื่อไม่ให้หน้าเพจกระพริบ
                      }
                    });
                  } else {
                    // alert(response);
                    // alert('ไม่สามารถลบรายการนี้ได้');
                    Swal.fire({
                      title: "Oops...!",
                      text: `Something went wrong!`,
                      icon: "error",
                      // width: 600,
                      // padding: '3em',
                      color: "#716add",
                      background: "black", //display dialog is black
                      // backdrop: `
                      //                 rgba(0,0,123,0.4)
                      //                 url("_images/Pyh.gif")
                      //                 left top
                      //                 no-repeat
                      //                 `,
                    });
                  }
                },
                //หรืออาจจะสั่งให้แสดง Modal ขึ้นมา เช่น $('#myModal').modal('show'); //ถ้าใช้ Bootstrap Modal
              });
            }
          });
        });

        $(".card").on("click", ".btnEdit", function(e) {
          const po_id = $(this).parents("tr").data("id");
          const content_filename = "po_edit_v2.php?po_id=" + po_id;
          // window.location.href = "po_edit.php?po_id=" + po_id;
          console.log("Click in btnEdit");
          loadContent(content_filename, "po_edit");

        });

        // $(".card").on("click", ".tdMain:has(a)", function (e) {
        $(".card").on("click", "a.po_number", function(e) {
          e.preventDefault(); // ป้องกันการทำงาน default ของลิงก์ (ไม่ต้องเปลี่ยนหน้า)
          const po_id = $(this).parents("tr").data("id");
          // window.location.href = "po_edit.php?po_id=" + po_id + "&href=inspect.php";
          window.location.href = "po_edit.php?po_id=" + po_id;
        });

        $(".card").on("click", ".tdMain:not(:has(a),.action)", function(e) {
          //, (comma) ภายใน :not(...): ใช้เพื่อรวมเงื่อนไขหลายอย่าง ในที่นี้คือ :has(a), :has(.action)
          // หมายความว่า :not() จะกรอง <td> ที่ ไม่มีทั้ง <a> และ ไม่มีทั้ง .action
          e.preventDefault();
          $(".content-period").removeClass("d-none");
          // หรือ
          // $(".content-period").removeClass('d-none').addClass('d-flex');

          let po_id = $(this).parents("tr").data("id"); //$(this).closest("tr")
          let po_number = $(this).parents("tr").find("a:first").data("id");
          // let po_id = $(this).closest('tr').attr('po-id');
          $(".card-title").html(po_number);

          $.ajax({
            url: "po_crud.php",
            type: "POST",
            data: {
              po_id: po_id,
              dataType: "json",
              action: "selectperiod",
            },
            success: function(response) {
              // console.log(`response=${response}`);
              // data = JSON.parse(response);
              // console.log(data);

              $("#tbody-period").html(response);
            },
          });
        });
        // });
      }
    </script>