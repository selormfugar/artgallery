<?php
// Include necessary files
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';



// Date range filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : '30'; // Default 30 days
$custom_start = isset($_GET['custom_start']) ? $_GET['custom_start'] : '';
$custom_end = isset($_GET['custom_end']) ? $_GET['custom_end'] : '';

// Calculate date ranges based on filter
$end_date = date('Y-m-d');
$start_date = '';

switch ($filter) {
    case '7':
        $start_date = date('Y-m-d', strtotime('-7 days'));
        break;
    case '30':
        $start_date = date('Y-m-d', strtotime('-30 days'));
        break;
    case '90':
        $start_date = date('Y-m-d', strtotime('-90 days'));
        break;
    case '365':
        $start_date = date('Y-m-d', strtotime('-365 days'));
        break;
    case 'custom':
        if (!empty($custom_start) && !empty($custom_end)) {
            $start_date = $custom_start;
            $end_date = $custom_end;
        } else {
            $start_date = date('Y-m-d', strtotime('-30 days'));
        }
        break;
    default:
        $start_date = date('Y-m-d', strtotime('-30 days'));
}

// Function to get dashboard statistics
function getDashboardStats($start_date, $end_date) {
    global $db;
    
    // Summary statistics
    $stats = [];
    
    // Total users
    $sql = "SELECT COUNT(*) as total, 
            (SELECT COUNT(*) FROM users WHERE created_at BETWEEN DATE_SUB('$start_date', INTERVAL 1 DAY) AND '$end_date' AND archived = 0) as period_count,
            (SELECT COUNT(*) FROM users WHERE created_at BETWEEN DATE_SUB('$start_date', INTERVAL 30 DAY) AND DATE_SUB('$start_date', INTERVAL 1 DAY) AND archived = 0) as previous_period_count
            FROM users WHERE archived = 0";
    $result = $db->query($sql);
    $user_stats = $result->fetch(PDO::FETCH_ASSOC);
    $stats['users'] = [
        'total' => $user_stats['total'],
        'period_count' => $user_stats['period_count'],
        'previous_period_count' => $user_stats['previous_period_count'],
        'growth' => $user_stats['previous_period_count'] > 0 ? 
                   (($user_stats['period_count'] - $user_stats['previous_period_count']) / $user_stats['previous_period_count'] * 100) : 0
    ];
    
    // Total revenue
    $sql = "SELECT SUM(total_price) as total, 
            (SELECT SUM(total_price) FROM orders WHERE created_at BETWEEN '$start_date' AND '$end_date' AND archived = 0) as period_revenue,
            (SELECT SUM(total_price) FROM orders WHERE created_at BETWEEN DATE_SUB('$start_date', INTERVAL 30 DAY) AND DATE_SUB('$start_date', INTERVAL 1 DAY) AND archived = 0) as previous_period_revenue
            FROM orders WHERE archived = 0";
    $result = $db->query($sql);
    $revenue_stats = $result->fetch(PDO::FETCH_ASSOC);
    $stats['revenue'] = [
        'total' => $revenue_stats['total'] ?: 0,
        'period_revenue' => $revenue_stats['period_revenue'] ?: 0,
        'previous_period_revenue' => $revenue_stats['previous_period_revenue'] ?: 0,
        'growth' => $revenue_stats['previous_period_revenue'] > 0 ? 
                   (($revenue_stats['period_revenue'] - $revenue_stats['previous_period_revenue']) / $revenue_stats['previous_period_revenue'] * 100) : 0
    ];
    
    // Total artworks
    $sql = "SELECT COUNT(*) as total, 
            (SELECT COUNT(*) FROM artworks WHERE created_at BETWEEN '$start_date' AND '$end_date' AND archived = 0) as period_count,
            COUNT(CASE WHEN moderation_status = 'pending' THEN 1 END) as pending_moderation
            FROM artworks WHERE archived = 0";
    $result = $db->query($sql);
    $artwork_stats = $result->fetch(PDO::FETCH_ASSOC);
    $stats['artworks'] = [
        'total' => $artwork_stats['total'],
        'period_count' => $artwork_stats['period_count'],
        'pending_moderation' => $artwork_stats['pending_moderation']
    ];
    
    // Content flags
    $sql = "SELECT COUNT(*) as total, 
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending
            FROM content_flags WHERE archived = 0";
    $result = $db->query($sql);
    $flag_stats = $result->fetch(PDO::FETCH_ASSOC);
    $stats['flags'] = [
        'total' => $flag_stats['total'],
        'pending' => $flag_stats['pending']
    ];
    
    // Monthly data
    $monthly_data = [];
    
    // Monthly users and revenue
    $sql = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as new_users
            FROM users
            WHERE created_at BETWEEN DATE_SUB('$end_date', INTERVAL 12 MONTH) AND '$end_date' AND archived = 0
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC";
    $result = $db->query($sql);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $monthly_data[$row['month']]['new_users'] = $row['new_users'];
    }
    
    $sql = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            SUM(total_price) as revenue,
            COUNT(*) as orders
            FROM orders
            WHERE created_at BETWEEN DATE_SUB('$end_date', INTERVAL 12 MONTH) AND '$end_date' AND archived = 0
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC";
    $result = $db->query($sql);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $monthly_data[$row['month']]['revenue'] = $row['revenue'];
        $monthly_data[$row['month']]['orders'] = $row['orders'];
    }
    
    $sql = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as new_artworks
            FROM artworks
            WHERE created_at BETWEEN DATE_SUB('$end_date', INTERVAL 12 MONTH) AND '$end_date' AND archived = 0
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC";
    $result = $db->query($sql);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $monthly_data[$row['month']]['new_artworks'] = $row['new_artworks'];
    }
    
    // Fill in missing values
    foreach ($monthly_data as $month => $data) {
        $monthly_data[$month]['new_users'] = $monthly_data[$month]['new_users'] ?? 0;
        $monthly_data[$month]['revenue'] = $monthly_data[$month]['revenue'] ?? 0;
        $monthly_data[$month]['orders'] = $monthly_data[$month]['orders'] ?? 0;
        $monthly_data[$month]['new_artworks'] = $monthly_data[$month]['new_artworks'] ?? 0;
        
        // Calculate average order value
        $monthly_data[$month]['avg_order_value'] = $monthly_data[$month]['orders'] > 0 ? 
                                                 $monthly_data[$month]['revenue'] / $monthly_data[$month]['orders'] : 0;
    }
    
    $stats['monthly_data'] = $monthly_data;
    
    // Top categories
    $sql = "SELECT c.name, COUNT(a.artwork_id) as count
            FROM artworks a
            JOIN categories c ON a.category = c.name
            WHERE a.archived = 0
            GROUP BY c.name
            ORDER BY count DESC
            LIMIT 5";
    $result = $db->query($sql);
    $categories = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row;
    }
    $stats['categories'] = $categories;
    
    // User geographical distribution
    // $sql = "SELECT COUNT(*) as total FROM users WHERE archived = 0";
    // $result = $db->query($sql);
    // $total_users = $result->fetch(PDO::FETCH_ASSOC)['total'];
    
    // This would typically be based on address or location fields
    // Using a mock distribution for demonstration
    // $geo_distribution = [
    //     ['country' => 'United States', 'count' => round($total_users * 0.4)],
    //     ['country' => 'United Kingdom', 'count' => round($total_users * 0.15)],
    //     ['country' => 'Canada', 'count' => round($total_users * 0.12)],
    //     ['country' => 'Australia', 'count' => round($total_users * 0.08)],
    //     ['country' => 'Germany', 'count' => round($total_users * 0.06)],
    //     ['country' => 'France', 'count' => round($total_users * 0.05)],
    //     ['country' => 'Others', 'count' => round($total_users * 0.14)]
    // ];
    
    // foreach ($geo_distribution as $key => $country) {
    //     $geo_distribution[$key]['percentage'] = ($country['count'] / $total_users) * 100;
    // }
    
    // $stats['geo_distribution'] = $geo_distribution;
    
    // Recent activity
    $sql = "SELECT 'new_user' as type, u.user_id as id, CONCAT(u.firstname, ' ', u.lastname) as name, u.created_at as timestamp
            FROM users u
            WHERE u.archived = 0 AND u.created_at BETWEEN '$start_date' AND '$end_date'
            UNION ALL
            SELECT 'new_artwork' as type, a.artwork_id as id, a.title as name, a.created_at as timestamp
            FROM artworks a
            WHERE a.archived = 0 AND a.created_at BETWEEN '$start_date' AND '$end_date'
            UNION ALL
            SELECT 'new_order' as type, o.order_id as id, a.title as name, o.created_at as timestamp
            FROM orders o
            JOIN artworks a ON o.artwork_id = a.artwork_id
            WHERE o.archived = 0 AND o.created_at BETWEEN '$start_date' AND '$end_date'
            ORDER BY timestamp DESC
            LIMIT 5";
    $result = $db->query($sql);
    $recent_activity = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $recent_activity[] = $row;
    }
    $stats['recent_activity'] = $recent_activity;
    
    // Subscription statistics
    $sql = "SELECT 
            COUNT(*) as total_subscriptions,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_subscriptions,
            COUNT(CASE WHEN status = 'expired' THEN 1 END) as expired_subscriptions,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_subscriptions
            FROM user_subscriptions";
    $result = $db->query($sql);
    $sub_stats = $result->fetch(PDO::FETCH_ASSOC);
    $stats['subscriptions'] = $sub_stats;
    
    // Auction statistics
    $sql = "SELECT 
            COUNT(*) as total_auctions,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_auctions,
            COUNT(CASE WHEN status = 'ended' THEN 1 END) as ended_auctions,
            AVG(current_bid) as avg_final_bid
            FROM auctions
            WHERE archived = 0";
    $result = $db->query($sql);
    $auction_stats = $result->fetch(PDO::FETCH_ASSOC);
    $stats['auctions'] = $auction_stats;
    
    return $stats;
}

// Call getDashboardStats function with date parameters
$dashboard_data = getDashboardStats($start_date, $end_date);

// Include header
include 'includes/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reports and Analytics</h1>
        <div>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="print-dashboard">
                <i class="fas fa-print fa-sm text-white-50"></i> Print Dashboard
            </a>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" id="export-reports">
                <i class="fas fa-download fa-sm text-white-50"></i> Export Reports
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Date Range Filter</h6>
        </div>
        <div class="card-body">
            <form method="get" action="" class="form-inline">
                <div class="form-group mb-2 mr-3">
                    <label for="filter" class="mr-2">Time Period:</label>
                    <select name="filter" id="filter" class="form-control">
                        <option value="7" <?php echo $filter == '7' ? 'selected' : ''; ?>>Last 7 Days</option>
                        <option value="30" <?php echo $filter == '30' ? 'selected' : ''; ?>>Last 30 Days</option>
                        <option value="90" <?php echo $filter == '90' ? 'selected' : ''; ?>>Last 90 Days</option>
                        <option value="365" <?php echo $filter == '365' ? 'selected' : ''; ?>>Last 365 Days</option>
                        <option value="custom" <?php echo $filter == 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                    </select>
                </div>
                <div id="custom-date-range" style="<?php echo $filter == 'custom' ? '' : 'display: none;'; ?>" class="form-inline">
                    <div class="form-group mb-2 mr-3">
                        <label for="custom_start" class="mr-2">Start Date:</label>
                        <input type="date" name="custom_start" id="custom_start" class="form-control" value="<?php echo $custom_start; ?>">
                    </div>
                    <div class="form-group mb-2">
                        <label for="custom_end" class="mr-2">End Date:</label>
                        <input type="date" name="custom_end" id="custom_end" class="form-control" value="<?php echo $custom_end; ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mb-2 ml-3">Apply Filter</button>
            </form>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['users']['total']); ?></div>
                            <div class="text-xs font-weight-bold <?php echo $dashboard_data['users']['growth'] >= 0 ? 'text-success' : 'text-danger'; ?> mt-2">
                                <?php echo $dashboard_data['users']['growth'] >= 0 ? '+' : ''; ?>
                                <?php echo number_format($dashboard_data['users']['growth'], 1); ?>% growth
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format($dashboard_data['revenue']['total'], 2); ?></div>
                            <div class="text-xs font-weight-bold <?php echo $dashboard_data['revenue']['growth'] >= 0 ? 'text-success' : 'text-danger'; ?> mt-2">
                                <?php echo $dashboard_data['revenue']['growth'] >= 0 ? '+' : ''; ?>
                                <?php echo number_format($dashboard_data['revenue']['growth'], 1); ?>% growth
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artworks Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Artworks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['artworks']['total']); ?></div>
                            <div class="text-xs font-weight-bold text-info mt-2">
                                <?php echo number_format($dashboard_data['artworks']['pending_moderation']); ?> pending moderation
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-image fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Required Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Actions Required</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['flags']['pending']); ?></div>
                            <div class="text-xs font-weight-bold text-warning mt-2">
                                Content flags requiring attention
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
 <!-- Content Row -->
    <div class="row">
        <!-- Subscription Statistics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subscription Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Subscriptions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['subscriptions']['total_subscriptions']); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-star fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Subscriptions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['subscriptions']['active_subscriptions']); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Expired Subscriptions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['subscriptions']['expired_subscriptions']); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body"><div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Cancelled Subscriptions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['subscriptions']['cancelled_subscriptions']); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auction Statistics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Auction Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Auctions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['auctions']['total_auctions']); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-gavel fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Auctions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['auctions']['active_auctions']); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ended Auctions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($dashboard_data['auctions']['ended_auctions']); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-flag-checkered fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg. Final Bid</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format($dashboard_data['auctions']['avg_final_bid'], 2); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->
    <div class="row">
        <!-- Growth and Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Growth and Revenue Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Chart Options:</div>
                            <a class="dropdown-item chart-type" data-type="line" href="#">Line Chart</a>
                            <a class="dropdown-item chart-type" data-type="bar" href="#">Bar Chart</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" id="download-chart">Download Chart</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Distribution Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Category Distribution</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Chart Options:</div>
                            <a class="dropdown-item category-chart-type" data-type="pie" href="#">Pie Chart</a>
                            <a class="dropdown-item category-chart-type" data-type="doughnut" href="#">Doughnut Chart</a>
                            <a class="dropdown-item category-chart-type" data-type="polarArea" href="#">Polar Area Chart</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Monthly Performance Table -->
    


    <!-- Content Row -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Sales Activity</h6>
                </div>
                <div class="card-body">
                    <div class="timeline-stream">
                        <?php foreach ($dashboard_data['recent_activity'] as $activity): ?>
                            <div class="timeline-item">
                                <div class="timeline-item-marker">
                                    <?php if ($activity['type'] == 'new_user'): ?>
                                        <i class="fas fa-user-plus text-primary"></i>
                                    <?php elseif ($activity['type'] == 'new_artwork'): ?>
                                        <i class="fas fa-image text-info"></i>
                                    <?php elseif ($activity['type'] == 'new_order'): ?>
                                        <i class="fas fa-shopping-cart text-success"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="timeline-item-content">
                                    <span class="font-weight-bold">
                                        <?php 
                                        if ($activity['type'] == 'new_user') {
                                            echo 'New User: ' . htmlspecialchars($activity['name']);
                                        } elseif ($activity['type'] == 'new_artwork') {
                                            echo 'New Artwork: ' . htmlspecialchars($activity['name']);
                                        } elseif ($activity['type'] == 'new_order') {
                                            echo 'New Order: ' . htmlspecialchars($activity['name']);
                                        }
                                        ?>
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo date('M j, Y g:i A', strtotime($activity['timestamp'])); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 mb-6">
           
                    <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Monthly Performance Analysis</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="monthlyDataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Month</th>
                                                <th>New Users</th>
                                                <th>New Artworks</th>
                                                <th>Orders</th>
                                                <th>Revenue</th>
                                                <th>Avg. Order Value</th>
                                                <th>Conversion Rate</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Total/Average</th>
                                                <th>
                                                    <?php
                                                    $total_users = 0;
                                                    foreach ($dashboard_data['monthly_data'] as $data) {
                                                        $total_users += $data['new_users'];
                                                    }
                                                    echo number_format($total_users);
                                                    ?>
                                                </th>
                                                <th>
                                                    <?php
                                                    $total_artworks = 0;
                                                    foreach ($dashboard_data['monthly_data'] as $data) {
                                                        $total_artworks += $data['new_artworks'];
                                                    }
                                                    echo number_format($total_artworks);
                                                    ?>
                                                </th>
                                                <th>
                                                    <?php
                                                    $total_orders = 0;
                                                    foreach ($dashboard_data['monthly_data'] as $data) {
                                                        $total_orders += $data['orders'];
                                                    }
                                                    echo number_format($total_orders);
                                                    ?>
                                                </th>
                                                <th>
                                                    <?php
                                                    $total_revenue = 0;
                                                    foreach ($dashboard_data['monthly_data'] as $data) {
                                                        $total_revenue += $data['revenue'];
                                                    }
                                                    echo '$' . number_format($total_revenue, 2);
                                                    ?>
                                    </th>
                                    <th>
                                        <?php
                                        $avg_order_values = [];
                                        foreach ($dashboard_data['monthly_data'] as $data) {
                                            if ($data['orders'] > 0) {
                                                $avg_order_values[] = $data['avg_order_value'];
                                            }
                                        }
                                        $overall_avg = count($avg_order_values) > 0 ? array_sum($avg_order_values) / count($avg_order_values) : 0;
                                        echo '$' . number_format($overall_avg, 2);
                                        ?>
                                    </th>
                                    <th>
                                        <?php
                                        $total_conversion = $total_users > 0 ? ($total_orders / $total_users) * 100 : 0;
                                        echo number_format($total_conversion, 2) . '%';
                                        ?>
                                    </th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php foreach ($dashboard_data['monthly_data'] as $month => $data): ?>
                                    <tr>
                                        <td><?php echo date('F Y', strtotime($month . '-01')); ?></td>
                                        <td><?php echo number_format($data['new_users']); ?></td>
                                        <td><?php echo number_format($data['new_artworks']); ?></td>
                                        <td><?php echo number_format($data['orders']); ?></td>
                                        <td>$<?php echo number_format($data['revenue'], 2); ?></td>
                                        <td>$<?php echo number_format($data['avg_order_value'], 2); ?></td>
                                        <td>
                                            <?php 
                                            $conversion_rate = $data['new_users'] > 0 ? ($data['orders'] / $data['new_users']) * 100 : 0;
                                            echo number_format($conversion_rate, 2) . '%'; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                    </div>
           
        </div>
        <!-- Geographical Distribution -->
        <!-- <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Geographical Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Users</th>
                                    <th>Percentage</th>
                                    <th>Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dashboard_data['geo_distribution'] as $country): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($country['country']); ?></td>
                                    <td><?php echo number_format($country['count']); ?></td>
                                    <td><?php echo number_format($country['percentage'], 1); ?>%</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $country['percentage']; ?>%" 
                                                aria-valuenow="<?php echo $country['percentage']; ?>" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> -->
    </div>

   
  
    <!-- Additional Analytics Section -->
    <div class="row">
        <!-- Collection Analytics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Collection Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p><strong>Total Collections: </strong>
                            <?php 
                            $collections_query = "SELECT COUNT(DISTINCT collection_name) as total FROM user_collections WHERE archived = 0";
                            $collections_result = $db->query($collections_query);
                            $collections_count = $collections_result->fetch(PDO::FETCH_ASSOC)['total'];
                            echo number_format($collections_count);
                            ?>
                        </p>
                        <p><strong>Total Collection Folders: </strong>
                            <?php 
                            $folders_query = "SELECT COUNT(*) as total FROM collection_folders WHERE archived = 0";
                            $folders_result = $db->query($folders_query);
                            $folders_count = $folders_result->fetch(PDO::FETCH_ASSOC)['total'];
                            echo number_format($folders_count);
                            ?>
                        </p>
                        <p><strong>Public Collections: </strong>
                            <?php 
                            $public_query = "SELECT COUNT(*) as total FROM user_collections WHERE is_public = 1 AND archived = 0";
                            $public_result = $db->query($public_query);
                            $public_count = $public_result->fetch(PDO::FETCH_ASSOC)['total'];
                            echo number_format($public_count);
                            ?>
                        </p>
                        <p><strong>Items per Collection (Average): </strong>
                            <?php 
                            $avg_query = "SELECT AVG(item_count) as avg FROM (
                                SELECT collection_name, COUNT(*) as item_count 
                                FROM user_collections 
                                WHERE archived = 0 
                                GROUP BY user_id, collection_name
                            ) as collection_counts";
                            $avg_result = $db->query($avg_query);
                            $avg_items = $avg_result->fetch(PDO::FETCH_ASSOC)['avg'];
                            echo number_format($avg_items, 1);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Artist Analytics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Artist Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p><strong>Total Artists: </strong>
                            <?php 
                            $artists_query = "SELECT COUNT(*) as total FROM artists WHERE archived = 0";
                            $artists_result = $db->query($artists_query);
                            $artists_count = $artists_result->fetch(PDO::FETCH_ASSOC)['total'];
                            echo number_format($artists_count);
                            ?>
                        </p>
                        <p><strong>Top Selling Artists (by Revenue): </strong></p>
                        <ol>
                            <?php 
                            $top_artists_query = "SELECT a.artist_id, u.firstname, u.lastname, SUM(o.total_price) as total_revenue
                                FROM orders o 
                                JOIN artworks aw ON o.artwork_id = aw.artwork_id
                                JOIN artists a ON aw.artist_id = a.artist_id
                                JOIN users u ON a.user_id = u.user_id
                                WHERE o.archived = 0 AND o.payment_status = 'completed'
                                GROUP BY a.artist_id
                                ORDER BY total_revenue DESC
                                LIMIT 5";
                            $top_artists_result = $db->query($top_artists_query);
                            while ($artist = $top_artists_result->fetch(PDO::FETCH_ASSOC)) {
                                echo "<li>" . htmlspecialchars($artist['firstname'] . ' ' . $artist['lastname']) . " - $" . number_format($artist['total_revenue'], 2) . "</li>";
                            }
                            ?>
                        </ol>
                        <p><strong>Artists with Subscription Plans: </strong>
                            <?php 
                            $sub_artists_query = "SELECT COUNT(DISTINCT artist_id) as total FROM subscription_plans WHERE archived = 0";
                            $sub_artists_result = $db->query($sub_artists_query);
                            $sub_artists_count = $sub_artists_result->fetch(PDO::FETCH_ASSOC)['total'];
                            echo number_format($sub_artists_count);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php
// Include footer
include 'includes/footer.php';
?>

<!-- Page level custom scripts -->
<script>
    // Toggle custom date range when filter changes
    document.getElementById('filter').addEventListener('change', function() {
        const customDateRange = document.getElementById('custom-date-range');
        if (this.value === 'custom') {
            customDateRange.style.display = '';
        } else {
            customDateRange.style.display = 'none';
        }
    });

    // Print Dashboard
    document.getElementById('print-dashboard').addEventListener('click', function(e) {
        e.preventDefault();
        window.print();
    });

    // Export Reports (mock functionality)
    document.getElementById('export-reports').addEventListener('click', function(e) {
        e.preventDefault();
        alert('Exporting reports...');
        // Actual export functionality would go here
    });

    // Chart initialization
    $(document).ready(function() {
        // Set new default font family and font color to mitigate bootstrap's influence
        Chart.defaults.font.family = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.color = '#858796';

        // Revenue and Growth Chart
        var ctx = document.getElementById('revenueGrowthChart').getContext('2d');
        
        // Format monthly data for chart
        var months = <?php 
            $formatted_months = [];
            foreach ($dashboard_data['monthly_data'] as $month => $data) {
                $formatted_months[] = date('M Y', strtotime($month . '-01'));
            }
            echo json_encode($formatted_months); 
        ?>;
        
        var new_users = <?php 
            $new_users_data = [];
            foreach ($dashboard_data['monthly_data'] as $data) {
                $new_users_data[] = $data['new_users'];
            }
            echo json_encode($new_users_data); 
        ?>;
        
        var revenue = <?php 
            $revenue_data = [];
            foreach ($dashboard_data['monthly_data'] as $data) {
                $revenue_data[] = $data['revenue'];
            }
            echo json_encode($revenue_data); 
        ?>;
        
        var new_artworks = <?php 
            $new_artworks_data = [];
            foreach ($dashboard_data['monthly_data'] as $data) {
                $new_artworks_data[] = $data['new_artworks'];
            }
            echo json_encode($new_artworks_data); 
        ?>;
        
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
            labels: months,
            datasets: [{
                label: 'New Users',
                data: new_users,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                yAxisID: 'y'
            },
            {
                label: 'Revenue ($)',
                data: revenue,
                backgroundColor: 'rgba(28, 200, 138, 0.05)',
                borderColor: 'rgba(28, 200, 138, 1)',
                pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                borderWidth: 2,
                yAxisID: 'y1'
            },
            {
                label: 'New Artworks',
                data: new_artworks,
                backgroundColor: 'rgba(246, 194, 62, 0.05)',
                borderColor: 'rgba(246, 194, 62, 1)',
                pointBackgroundColor: 'rgba(246, 194, 62, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(246, 194, 62, 1)',
                borderWidth: 2,
                hidden: true,
                yAxisID: 'y'
            }]
            },
            options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                display: true,
                position: 'top'
                },
                tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyColor: "#858796",
                titleMarginBottom: 10,
                titleColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                padding: 15,
                displayColors: false
                }
            },
            scales: {
                y: {
                type: 'linear',
                position: 'left',
                grid: {
                    color: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                },
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    color: "#858796"
                }
                },
                y1: {
                type: 'linear',
                position: 'right',
                grid: {
                    display: false
                },
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    color: "#858796",
                    callback: function(value) {
                    return '$' + value.toLocaleString();
                    }
                }
                },
                x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7,
                    padding: 10,
                    color: "#858796"
                }
                }
            }
            }
        });

        // Categories Chart
        var ctxCategories = document.getElementById('categoriesChart').getContext('2d');
        var categoryNames = <?php 
            $names = array_map(function($cat) { return $cat['name']; }, $dashboard_data['categories']);
            echo json_encode($names);
        ?>;
        var categoryCounts = <?php 
            $counts = array_map(function($cat) { return $cat['count']; }, $dashboard_data['categories']);
            echo json_encode($counts);
        ?>;

        var categoriesChart = new Chart(ctxCategories, {
            type: 'pie',
            data: {
                labels: categoryNames,
                datasets: [{
                    data: categoryCounts,
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                        'rgba(54, 185, 204, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Chart type switchers
        $('.chart-type').click(function(e) {
            e.preventDefault();
            var type = $(this).data('type');
            myChart.config.type = type;
            myChart.update();
        });

        $('.category-chart-type').click(function(e) {
            e.preventDefault();
            var type = $(this).data('type');
            categoriesChart.config.type = type;
            categoriesChart.update();
        });

        // Initialize DataTable
        $('#monthlyDataTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 12
        });
    });