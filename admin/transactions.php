<?php
require_once('includes/config.php');
require_once('includes/db.php');
require_once('includes/functions.php');
// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
// Get the completed transactions directly
$result = getCompletedTransactions();
if (!$result) {
    die("Database query failed");
}

// Calculate total revenue from transactions
$totalRevenue = 0;
$monthlySales = array_fill(0, 12, 0);
$currentYear = date('Y');

foreach ($result as $row) {
    $totalRevenue += $row['total_amount'];
    
    // For monthly chart data
    $transactionDate = strtotime($row['transaction_date']);
    $transactionYear = date('Y', $transactionDate);
    
    if ($transactionYear == $currentYear) {
        $month = date('n', $transactionDate) - 1; // 0-based index for months
        $monthlySales[$month] += $row['total_amount'];
    }
}

// Get recent transactions (latest 5)
$recentTransactions = array_slice($result, 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Transaction Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body class="dark-theme">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include_once 'includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-exchange-alt me-2"></i>Transaction Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="exportCSV">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="printReport">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item filter-option" data-period="all" href="#">All Time</a></li>
                                <li><a class="dropdown-item filter-option" data-period="today" href="#">Today</a></li>
                                <li><a class="dropdown-item filter-option" data-period="week" href="#">This Week</a></li>
                                <li><a class="dropdown-item filter-option" data-period="month" href="#">This Month</a></li>
                                <li><a class="dropdown-item filter-option" data-period="year" href="#">This Year</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Overview -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Revenue</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format($totalRevenue, 2); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Transactions</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($result); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Average Sale</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            $<?php echo number_format(count($result) > 0 ? $totalRevenue / count($result) : 0, 2); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Latest Transaction</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                            if (!empty($result)) {
                                                echo date('M d, Y', strtotime($result[0]['transaction_date']));
                                            } else {
                                                echo "N/A";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-xl-8 col-lg-7">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Monthly Sales (<?php echo $currentYear; ?>)</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-area">
                                    <canvas id="monthlySalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Transactions</h6>
                                <a href="#" class="btn btn-sm btn-primary view-all">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive recent-transactions">
                                    <table class="table table-borderless table-sm">
                                        <tbody>
                                            <?php foreach ($recentTransactions as $transaction): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-light rounded-circle me-3">
                                                            <i class="fas fa-paint-brush text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0"><?php echo $transaction['artwork_title']; ?></h6>
                                                            <small class="text-muted"><?php echo $transaction['first_name'] . ' ' . $transaction['last_name']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <h6 class="mb-0 text-success">$<?php echo number_format($transaction['total_amount'], 2); ?></h6>
                                                    <small class="text-muted"><?php echo date('M d', strtotime($transaction['transaction_date'])); ?></small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($recentTransactions)): ?>
                                            <tr>
                                                <td colspan="2" class="text-center">No recent transactions found</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">All Transactions</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="transactionsTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Artwork</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result as $row): ?>
                                    <tr>
                                        <td><?php echo $row['transaction_id']; ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($row['transaction_date'])); ?></td>
                                        <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                        <td><?php echo $row['artwork_title']; ?></td>
                                        <td class="text-end">$<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td class="text-center">
                                            <a href="transaction-details.php?id=<?php echo $row['transaction_id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="generate-invoice.php?id=<?php echo $row['transaction_id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#transactionsTable').DataTable({
            order: [[1, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            responsive: true
        });

        // Export CSV functionality
        $('#exportCSV').on('click', function() {
            window.location.href = 'export-transactions.php?format=csv';
        });

        // Print report functionality
        $('#printReport').on('click', function() {
            window.print();
        });

        // Filter options functionality
        $('.filter-option').on('click', function(e) {
            e.preventDefault();
            var period = $(this).data('period');
            window.location.href = 'transactions.php?filter=' + period;
        });

        // Monthly Sales Chart
        var ctx = document.getElementById('monthlySalesChart').getContext('2d');
        var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var monthlySalesData = <?php echo json_encode(array_values($monthlySales)); ?>;
        
        var monthlySalesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Monthly Sales',
                    data: monthlySalesData,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
    </script>

    <!-- Custom Admin JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>