<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an artist
requireArtist();

// Get sales data
global $db;
$sales = $db->select("
    SELECT o.*, a.title, a.image_url, u.username as buyer_name
    FROM orders o 
    JOIN artworks a ON o.artwork_id = a.artwork_id 
    JOIN users u ON o.buyer_id = u.user_id
    WHERE a.artist_id = ? AND o.archived = 0
    ORDER BY o.created_at DESC", 
    [$_SESSION['artist_id']]
);

// Get sales statistics
$stats = [
    'total_sales' => 0,
    'total_revenue' => 0,
    'completed_sales' => 0,
    'pending_sales' => 0,
    'failed_sales' => 0
];

foreach ($sales as $sale) {
    $stats['total_sales']++;
    
    if ($sale['payment_status'] == 'completed') {
        $stats['total_revenue'] += $sale['total_price'];
        $stats['completed_sales']++;
    } else if ($sale['payment_status'] == 'pending') {
        $stats['pending_sales']++;
    } else if ($sale['payment_status'] == 'failed') {
        $stats['failed_sales']++;
    }
}

// Get monthly sales data for chart
$monthlySales = $db->select("
    SELECT 
        DATE_FORMAT(o.created_at, '%Y-%m') as month,
        COUNT(*) as count,
        SUM(CASE WHEN o.payment_status = 'completed' THEN o.total_price ELSE 0 END) as revenue
    FROM orders o 
    JOIN artworks a ON o.artwork_id = a.artwork_id 
    WHERE a.artist_id = ? AND o.archived = 0
    GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12", 
    [$_SESSION['artist_id']]
);

// Reverse the array to show oldest to newest
$monthlySales = array_reverse($monthlySales);

// Include header
include_once '../includes/header.php';

// Include sidebar
include_once '../includes/sidebar.php';
?>

<!-- Main Content -->
<div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Sales Overview</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportCSV">Export CSV</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="printReport">Print Report</button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" id="timeRangeDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-calendar me-1"></i>
                All Time
            </button>
            <ul class="dropdown-menu" aria-labelledby="timeRangeDropdown">
                <li><a class="dropdown-item" href="#" data-range="week">This Week</a></li>
                <li><a class="dropdown-item" href="#" data-range="month">This Month</a></li>
                <li><a class="dropdown-item" href="#" data-range="year">This Year</a></li>
                <li><a class="dropdown-item" href="#" data-range="all">All Time</a></li>
            </ul>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_sales']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatCurrency($stats['total_revenue']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Completed Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['completed_sales']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Pending Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pending_sales']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Sales</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Breakdown -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="revenuePieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="me-2">
                            <i class="fas fa-circle text-primary"></i> Completed
                        </span>
                        <span class="me-2">
                            <i class="fas fa-circle text-warning"></i> Pending
                        </span>
                        <span class="me-2">
                            <i class="fas fa-circle text-danger"></i> Failed
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Sales History</h6>
            <div class="input-group w-25">
                <input type="text" class="form-control form-control-sm" placeholder="Search..." id="salesSearch">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="salesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Artwork</th>
                            <th>Buyer</th>
                            <th>Date</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($sales) > 0): ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td>#<?php echo $sale['order_id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo UPLOAD_URL . $sale['image_url']; ?>" alt="<?php echo $sale['title']; ?>" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            <span><?php echo $sale['title']; ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $sale['buyer_name']; ?></td>
                                    <td><?php echo formatDate($sale['created_at']); ?></td>
                                    <td><?php echo formatCurrency($sale['total_price']); ?></td>
                                    <td>
                                        <?php if ($sale['payment_status'] == 'completed'): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php elseif ($sale['payment_status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Failed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-sale" data-id="<?php echo $sale['order_id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary message-buyer" data-id="<?php echo $sale['buyer_id']; ?>" data-name="<?php echo $sale['buyer_name']; ?>">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No sales records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sale Details Modal -->
    <div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-labelledby="saleDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saleDetailsModalLabel">Sale Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Order ID:</th>
                                    <td id="orderIdDetail"></td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td id="orderDateDetail"></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td id="orderStatusDetail"></td>
                                </tr>
                                <tr>
                                    <th>Price:</th>
                                    <td id="orderPriceDetail"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Buyer Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Name:</th>
                                    <td id="buyerNameDetail"></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td id="buyerEmailDetail"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6>Artwork Information</h6>
                            <div class="d-flex">
                                <img id="artworkImageDetail" src="/placeholder.svg" alt="Artwork" class="img-thumbnail me-3" style="width: 150px; height: 150px; object-fit: cover;">
                                <div>
                                    <h5 id="artworkTitleDetail"></h5>
                                    <p id="artworkDescriptionDetail"></p>
                                    <p><strong>Category:</strong> <span id="artworkCategoryDetail"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="printInvoiceBtn">Print Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Buyer Modal -->
    <div class="modal fade" id="messageBuyerModal" tabindex="-1" aria-labelledby="messageBuyerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageBuyerModalLabel">Message Buyer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="messageBuyerForm">
                        <input type="hidden" id="receiverId" name="receiver_id">
                        <div class="mb-3">
                            <label for="receiverName" class="form-label">To:</label>
                            <input type="text" class="form-control" id="receiverName" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="messageContent" class="form-label">Message:</label>
                            <textarea class="form-control" id="messageContent" name="content" rows="5" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendMessageBtn">Send Message</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const monthlySalesData = <?php echo json_encode($monthlySales); ?>;
    
    const months = monthlySalesData.map(item => {
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    });
    
    const salesCount = monthlySalesData.map(item => item.count);
    const salesRevenue = monthlySalesData.map(item => item.revenue);
    
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Sales Count',
                    type: 'bar',
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    data: salesCount,
                    yAxisID: 'y-axis-1',
                },
                {
                    label: 'Revenue',
                    type: 'line',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                    fill: false,
                    data: salesRevenue,
                    yAxisID: 'y-axis-2',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                'y-axis-1': {
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Sales Count'
                    },
                    ticks: {
                        beginAtZero: true
                    }
                },
                'y-axis-2': {
                    type: 'linear',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    },
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return '$' + value;
                        }
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    
    // Revenue Pie Chart
    const pieCtx = document.getElementById('revenuePieChart').getContext('2d');
    const revenuePieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'Failed'],
            datasets: [{
                data: [
                    <?php echo $stats['completed_sales']; ?>,
                    <?php echo $stats['pending_sales']; ?>,
                    <?php echo $stats['failed_sales']; ?>
                ],
                backgroundColor: ['#4e73df', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#e0ad0e', '#d52a1a'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw;
                            const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        },
    });
    
    // View Sale Details
    document.querySelectorAll('.view-sale').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-id');
            
            // Fetch sale details via AJAX
            fetch(`../api/sales.php?action=details&order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    // Populate modal with sale details
                    document.getElementById('orderIdDetail').textContent = '#' + data.order_id;
                    document.getElementById('orderDateDetail').textContent = new Date(data.created_at).toLocaleDateString();
                    
                    let statusHtml = '';
                    if (data.payment_status === 'completed') {
                        statusHtml = '<span class="badge bg-success">Completed</span>';
                    } else if (data.payment_status === 'pending') {
                        statusHtml = '<span class="badge bg-warning text-dark">Pending</span>';
                    } else {
                        statusHtml = '<span class="badge bg-danger">Failed</span>';
                    }
                    
                    document.getElementById('orderStatusDetail').innerHTML = statusHtml;
                    document.getElementById('orderPriceDetail').textContent = '$' + parseFloat(data.total_price).toFixed(2);
                    
                    document.getElementById('buyerNameDetail').textContent = data.buyer_name;
                    document.getElementById('buyerEmailDetail').textContent = data.buyer_email;
                    
                    document.getElementById('artworkImageDetail').src = '<?php echo UPLOAD_URL; ?>' + data.image_url;
                    document.getElementById('artworkTitleDetail').textContent = data.title;
                    document.getElementById('artworkDescriptionDetail').textContent = data.description;
                    document.getElementById('artworkCategoryDetail').textContent = data.category;
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('saleDetailsModal'));
                    modal.show();
                });
        });
    });
    
    // Message Buyer
    document.querySelectorAll('.message-buyer').forEach(button => {
        button.addEventListener('click', function() {
            const buyerId = this.getAttribute('data-id');
            const buyerName = this.getAttribute('data-name');
            
            document.getElementById('receiverId').value = buyerId;
            document.getElementById('receiverName').value = buyerName;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('messageBuyerModal'));
            modal.show();
        });
    });
    
    // Send Message
    document.getElementById('sendMessageBtn').addEventListener('click', function() {
        const form = document.getElementById('messageBuyerForm');
        const receiverId = document.getElementById('receiverId').value;
        const content = document.getElementById('messageContent').value;
        
        if (!content.trim()) {
            alert('Please enter a message.');
            return;
        }
        
        // Send AJAX request to send message
        fetch('../api/messages.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=send&receiver_id=${receiverId}&content=${encodeURIComponent(content)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close the modal
                bootstrap.Modal.getInstance(document.getElementById('messageBuyerModal')).hide();
                
                // Clear the form
                document.getElementById('messageContent').value = '';
                
                // Show success message
                alert('Message sent successfully!');
            } else {
                alert('Error sending message: ' + data.message);
            }
        });
    });
    
    // Search functionality
    document.getElementById('searchButton').addEventListener('click', function() {
        const searchTerm = document.getElementById('salesSearch').value.toLowerCase();
        const tableRows = document.querySelectorAll('#salesTable tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Export CSV
    document.getElementById('exportCSV').addEventListener('click', function() {
        // Get table data
        const table = document.getElementById('salesTable');
        let csv = [];
        const rows = table.querySelectorAll('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length; j++) {
                // Get the text content and clean it up
                let data = cols[j].textContent.replace(/(\r\n|\n|\r)/gm, '').trim();
                
                // Remove the actions column
                if (j === cols.length - 1 && i > 0) {
                    continue;
                }
                
                // Escape quotes and wrap in quotes
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            
            csv.push(row.join(','));
        }
        
        // Download CSV file
        const csvString = csv.join('\n');
        const filename = 'sales_report_' + new Date().toISOString().slice(0, 10) + '.csv';
        
        const link = document.createElement('a');
        link.style.display = 'none';
        link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString));
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
    
    // Print Report
    document.getElementById('printReport').addEventListener('click', function() {
        window.print();
    });
    
    // Print Invoice
    document.getElementById('printInvoiceBtn').addEventListener('click', function() {
        const orderId = document.getElementById('orderIdDetail').textContent;
        const orderDate = document.getElementById('orderDateDetail').textContent;
        const buyerName = document.getElementById('buyerNameDetail').textContent;
        const artworkTitle = document.getElementById('artworkTitleDetail').textContent;
        const price = document.getElementById('orderPriceDetail').textContent;
        
        // Create a new window for printing
        const printWindow = window.open('', '_blank');
        
        // Generate invoice HTML
        const invoiceHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Invoice ${orderId}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                    .invoice-header { text-align: center; margin-bottom: 30px; }
                    .invoice-details { margin-bottom: 30px; }
                    .invoice-table { width: 100%; border-collapse: collapse; }
                    .invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    .invoice-table th { background-color: #f2f2f2; }
                    .invoice-total { margin-top: 30px; text-align: right; }
                    .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; }
                    @media print {
                        .no-print { display: none; }
                        body { margin: 0; padding: 15px; }
                    }
                </style>
            </head>
            <body>
                <div class="invoice-header">
                    <h1>INVOICE</h1>
                    <p>Art Marketplace</p>
                </div>
                
                <div class="invoice-details">
                    <p><strong>Invoice Number:</strong> ${orderId}</p>
                    <p><strong>Date:</strong> ${orderDate}</p>
                    <p><strong>Buyer:</strong> ${buyerName}</p>
                </div>
                
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Description</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Artwork</td>
                            <td>${artworkTitle}</td>
                            <td>${price}</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="invoice-total">
                    <p><strong>Total:</strong> ${price}</p>
                </div>
                
                <div class="footer">
                    <p>Thank you for your purchase!</p>
                    <p>Art Marketplace Inc. | 123 Art Street, City, Country | support@artmarketplace.com</p>
                </div>
                
                <div class="no-print" style="text-align: center; margin-top: 30px;">
                    <button onclick="window.print();" class="print-button">Print Invoice</button>
                </div>
            </body>
            </html>
        `;
        
        printWindow.document.open();
        printWindow.document.write(invoiceHtml);
        printWindow.document.close();
        
        // Wait for content to load before printing
        setTimeout(function() {
            printWindow.print();
        }, 500);
    });
    
    // Time range filter
    document.querySelectorAll('[data-range]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const range = this.getAttribute('data-range');
            document.getElementById('timeRangeDropdown').textContent = this.textContent;
            
            // AJAX request to get filtered data
            fetch(`../api/sales.php?action=filter&range=${range}&artist_id=<?php echo $_SESSION['artist_id']; ?>`)
                .then(response => response.json())
                .then(data => {
                    // Update table with filtered data
                    const tableBody = document.querySelector('#salesTable tbody');
                    tableBody.innerHTML = '';
                    
                    if (data.sales.length > 0) {
                        data.sales.forEach(sale => {
                            let statusBadge = '';
                            if (sale.payment_status === 'completed') {
                                statusBadge = '<span class="badge bg-success">Completed</span>';
                            } else if (sale.payment_status === 'pending') {
                                statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
                            } else {
                                statusBadge = '<span class="badge bg-danger">Failed</span>';
                            }
                            
                            tableBody.innerHTML += `
                                <tr>
                                    <td>#${sale.order_id}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo UPLOAD_URL; ?>${sale.image_url}" alt="${sale.title}" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            <span>${sale.title}</span>
                                        </div>
                                    </td>
                                    <td>${sale.buyer_name}</td>
                                    <td>${new Date(sale.created_at).toLocaleDateString()}</td>
                                    <td>$${parseFloat(sale.total_price).toFixed(2)}</td>
                                    <td>${statusBadge}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-sale" data-id="${sale.order_id}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary message-buyer" data-id="${sale.buyer_id}" data-name="${sale.buyer_name}">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        // Reattach event listeners
                        attachEventListeners();
                    } else {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center">No sales records found for this time period.</td>
                            </tr>
                        `;
                    }
                    
                    // Update stats
                    document.querySelector('.card-body .h5:nth-of-type(1)').textContent = data.stats.total_sales;
                    document.querySelector('.card-body .h5:nth-of-type(2)').textContent = '$' + parseFloat(data.stats.total_revenue).toFixed(2);
                    document.querySelector('.card-body .h5:nth-of-type(3)').textContent = data.stats.completed_sales;
                    document.querySelector('.card-body .h5:nth-of-type(4)').textContent = data.stats.pending_sales;
                });
        });
    });
    
    function attachEventListeners() {
        // View Sale Details
        document.querySelectorAll('.view-sale').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                
                // Fetch sale details via AJAX
                fetch(`../api/sales.php?action=details&order_id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Populate modal with sale details
                        document.getElementById('orderIdDetail').textContent = '#' + data.order_id;
                        document.getElementById('orderDateDetail').textContent = new Date(data.created_at).toLocaleDateString();
                        
                        let statusHtml = '';
                        if (data.payment_status === 'completed') {
                            statusHtml = '<span class="badge bg-success">Completed</span>';
                        } else if (data.payment_status === 'pending') {
                            statusHtml = '<span class="badge bg-warning text-dark">Pending</span>';
                        } else {
                            statusHtml = '<span class="badge bg-danger">Failed</span>';
                        }
                        
                        document.getElementById('orderStatusDetail').innerHTML = statusHtml;
                        document.getElementById('orderPriceDetail').textContent = '$' + parseFloat(data.total_price).toFixed(2);
                        
                        document.getElementById('buyerNameDetail').textContent = data.buyer_name;
                        document.getElementById('buyerEmailDetail').textContent = data.buyer_email;
                        
                        document.getElementById('artworkImageDetail').src = '<?php echo UPLOAD_URL; ?>' + data.image_url;
                        document.getElementById('artworkTitleDetail').textContent = data.title;
                        document.getElementById('artworkDescriptionDetail').textContent = data.description;
                        document.getElementById('artworkCategoryDetail').textContent = data.category;
                        
                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('saleDetailsModal'));
                        modal.show();
                    });
            });
        });
        
        // Message Buyer
        document.querySelectorAll('.message-buyer').forEach(button => {
            button.addEventListener('click', function() {
                const buyerId = this.getAttribute('data-id');
                const buyerName = this.getAttribute('data-name');
                
                document.getElementById('receiverId').value = buyerId;
                document.getElementById('receiverName').value = buyerName;
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('messageBuyerModal'));
                modal.show();
            });
        });
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>

