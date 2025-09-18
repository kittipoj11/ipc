<?php
// --- Database Connection ---
require_once  'class/connection_class.php';
$connection = new Connection;
$pdo = $connection->getDbConnection();
// $pdo = new PDO("mysql:host=localhost;dbname=ipc_db;charset=utf8", "root", "");

// --- Summary Data ---
$totalPO = $pdo->query("SELECT COUNT(*) FROM po_main")->fetchColumn();
$totalInspection = $pdo->query("SELECT COUNT(*) FROM inspection")->fetchColumn();
$totalIPC = $pdo->query("SELECT COUNT(*) FROM ipc")->fetchColumn();
$closedPO = $pdo->query("SELECT COUNT(*) FROM po_main P INNER JOIN po_status S ON P.po_status = S.po_status_id WHERE S.po_status_name='Closed'")->fetchColumn();

// --- Table Data ---
$stmt = $pdo->query("
    SELECT P.po_id, P.po_number, S.po_status_name,
           (SELECT COUNT(*) FROM po_periods d WHERE d.po_id = P.po_id) AS total_periods,
           (SELECT COUNT(*) FROM inspection i JOIN po_periods d ON i.period_id=d.period_id WHERE d.po_id = P.po_id) AS total_inspections,
           (SELECT COUNT(*) FROM ipc ic JOIN inspection i ON ic.inspection_id=i.inspection_id JOIN po_periods d ON i.period_id=d.period_id WHERE d.po_id = P.po_id) AS total_ipc
    FROM po_main p
    INNER JOIN po_status S ON P.po_status = S.po_status_id
    ORDER BY P.po_id DESC
");
$poList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Chart Data ---
$chartPO = $pdo->query("SELECT po_status_name, COUNT(*) as cnt FROM po_main INNER JOIN po_status ON po_status = po_status_id  GROUP BY po_status_name")->fetchAll(PDO::FETCH_ASSOC);
$chartInspection = $pdo->query("SELECT inspection_status, COUNT(*) as cnt FROM inspection GROUP BY inspection_status")->fetchAll(PDO::FETCH_ASSOC);
$chartIPC = $pdo->query("SELECT ipc_status, COUNT(*) as cnt FROM ipc GROUP BY ipc_status")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase Order Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: #f8f9fa;
        }

        .card-summary {
            border-left: 5px solid;
        }

        .card-po {
            border-color: #004691ff;
            background: #4e9df1ff;
        }

        .card-inspection {
            border-color: #a17a05ff;
            background: #ffc107;
        }

        .card-ipc {
            border-color: #19692cff;
            background: #28a745;
        }

        .card-closed {
            border-color: #972284ff;
            background: #ec33ceff;
        }

        .status {
            width: 40%;
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <!-- <h2 class="mb-4"><i class="bi bi-bar-chart"></i> Purchase Order Dashboard</h2> -->

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-summary card-po shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-black col-6">Total PO</h5>
                        <h2><?= $totalPO ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-summary card-inspection shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-black col-6">Total Inspection</h5>
                        <h2><?= $totalInspection ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-summary card-ipc shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-black col-6">Total IPC</h5>
                        <h2><?= $totalIPC ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-summary card-closed shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-black col-6">Closed PO</h5>
                        <h2><?= $closedPO ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-4 statusx">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">PO by Status</h5>
                        <canvas id="poChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 statusx">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Inspection by Status</h5>
                        <canvas id="inspectionChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 statusx">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">IPC by Status</h5>
                        <canvas id="ipcChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">PO Periods</h5>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>PO No</th>
                            <th>Status</th>
                            <th>Periods</th>
                            <th>Inspections</th>
                            <th>IPC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($poList as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['po_number']) ?></td>
                                <td><span class="badge bg-<?= $row['po_status_name'] == 'Closed' ? 'secondary' : 'primary' ?>">
                                        <?= htmlspecialchars($row['po_status_name']) ?></span></td>
                                <td><?= $row['total_periods'] ?></td>
                                <td><?= $row['total_inspections'] ?></td>
                                <td><?= $row['total_ipc'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Chart Data
        const poData = {
            labels: <?= json_encode(array_column($chartPO, 'po_status_name')) ?>,
            datasets: [{
                label: 'PO Count',
                data: <?= json_encode(array_column($chartPO, 'cnt')) ?>,
                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#6c757d', '#007bff']
            }]
        };
        const inspectionData = {
            labels: <?= json_encode(array_column($chartInspection, 'inspection_status')) ?>,
            datasets: [{
                label: 'Inspection Count',
                data: <?= json_encode(array_column($chartInspection, 'cnt')) ?>,
                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#6c757d', '#007bff']
            }]
        };
        const ipcData = {
            labels: <?= json_encode(array_column($chartIPC, 'ipc_status')) ?>,
            datasets: [{
                label: 'IPC Count',
                data: <?= json_encode(array_column($chartIPC, 'cnt')) ?>,
                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#6c757d', '#007bff']
            }]
        };

        // Render Charts
        new Chart(document.getElementById('poChart'), {
            type: 'pie',
            data: poData
        });
        new Chart(document.getElementById('inspectionChart'), {
            type: 'pie',
            data: inspectionData
        });
        new Chart(document.getElementById('ipcChart'), {
            type: 'pie',
            data: ipcData
        });
    </script>
</body>

</html>