<?php
require_once '../../includes/auth_check.php';
require_once '../../includes/db.php';
require_once '../../includes/is_artist.php';
require_once '../includes/artist_functions.php';

$artistId = $_SESSION['artist_id'];

// Get artwork statistics
$artworkStats = $pdo->prepare("SELECT a.artwork_id, a.title, COUNT(av.view_id) as view_count
                              FROM artworks a
                              LEFT JOIN artwork_views av ON a.artwork_id = av.artwork_id
                              WHERE a.artist_id = ? AND a.archived = 0
                              GROUP BY a.artwork_id
                              ORDER BY view_count DESC")
                   ->execute([$artistId])
                   ->fetchAll();

// Get total views
$totalViews = array_sum(array_column($artworkStats, 'view_count'));

// Get most popular artwork
$mostPopular = !empty($artworkStats) ? $artworkStats[0] : null;

include '../includes/header.php';
?>

<div class="artist-dashboard">
    <h1>Artwork Statistics</h1>

    <div class="dashboard-tabs">
        <a href="portfolio.php" class="tab-btn">Portfolio</a>
        <a href="subscriptions.php" class="tab-btn">Subscriptions</a>
        <a href="stats.php" class="tab-btn active">Statistics</a>
    </div>

    <div class="stats-overview">
        <div class="stat-card">
            <h3>Total Artwork Views</h3>
            <p class="stat-number"><?= number_format($totalViews) ?></p>
        </div>
        
        <?php if ($mostPopular): ?>
            <div class="stat-card">
                <h3>Most Popular Artwork</h3>
                <p class="stat-title"><?= htmlspecialchars($mostPopular['title']) ?></p>
                <p class="stat-number"><?= number_format($mostPopular['view_count']) ?> views</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="artwork-stats">
        <h2>Artwork Performance</h2>
        
        <?php if (empty($artworkStats)): ?>
            <p>You don't have any artwork statistics yet.</p>
        <?php else: ?>
            <div class="stats-table">
                <table>
                    <thead>
                        <tr>
                            <th>Artwork</th>
                            <th>Views</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($artworkStats as $stat): ?>
                            <tr>
                                <td><?= htmlspecialchars($stat['title']) ?></td>
                                <td><?= number_format($stat['view_count']) ?></td>
                                <td>
                                    <div class="percentage-bar">
                                        <div class="bar" style="width: <?= $totalViews > 0 ? ($stat['view_count'] / $totalViews * 100) : 0 ?>%"></div>
                                        <span><?= $totalViews > 0 ? round($stat['view_count'] / $totalViews * 100, 1) : 0 ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load statistics data
    loadArtworkStats();
    
    // Set up chart
    let artworkChart = null;
    const ctx = document.getElementById('artworkChart')?.getContext('2d');
    
    // Function to load and display statistics
    function loadArtworkStats() {
        fetch('/api/get_artwork_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsOverview(data.stats);
                updateStatsTable(data.stats);
                
                if (ctx && data.stats.length > 0) {
                    renderArtworkChart(data.stats);
                }
            } else {
                showAlert(data.error || 'Failed to load statistics', 'error');
            }
        })
        .catch(error => {
            showAlert('Network error: ' + error.message, 'error');
        });
    }
    
    // Update the stats overview cards
    function updateStatsOverview(stats) {
        const totalViews = stats.reduce((sum, item) => sum + item.view_count, 0);
        const mostPopular = stats.length > 0 ? stats[0] : null;
        
        // Update total views card
        const totalViewsEl = document.querySelector('.stat-card .stat-number');
        if (totalViewsEl) {
            totalViewsEl.textContent = totalViews.toLocaleString();
        }
        
        // Update most popular artwork card
        if (mostPopular) {
            const mostPopularTitle = document.querySelector('.stat-title');
            const mostPopularViews = document.querySelector('.stat-card:nth-child(2) .stat-number');
            
            if (mostPopularTitle) mostPopularTitle.textContent = mostPopular.title;
            if (mostPopularViews) mostPopularViews.textContent = mostPopular.view_count.toLocaleString();
        }
    }
    
    // Update the stats table
    function updateStatsTable(stats) {
        const totalViews = stats.reduce((sum, item) => sum + item.view_count, 0);
        const tbody = document.querySelector('.stats-table tbody');
        
        if (tbody) {
            tbody.innerHTML = '';
            
            stats.forEach(item => {
                const percentage = totalViews > 0 ? (item.view_count / totalViews * 100) : 0;
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>${item.title}</td>
                    <td>${item.view_count.toLocaleString()}</td>
                    <td>
                        <div class="percentage-bar">
                            <div class="bar" style="width: ${percentage}%"></div>
                            <span>${percentage.toFixed(1)}%</span>
                        </div>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
        }
    }
    
    // Render the artwork chart
    function renderArtworkChart(stats) {
        // Prepare data for chart
        const labels = [];
        const datasets = [];
        
        // Get last 30 days labels
        const date = new Date();
        for (let i = 29; i >= 0; i--) {
            const d = new Date();
            d.setDate(date.getDate() - i);
            labels.push(d.toLocaleDateString());
        }
        
        // Create dataset for each artwork
        stats.forEach(item => {
            const data = new Array(30).fill(0);
            
            item.views.forEach(view => {
                const viewDate = new Date(view.view_date);
                const diffTime = date - viewDate;
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays >= 0 && diffDays < 30) {
                    data[29 - diffDays] = view.view_count;
                }
            });
            
            datasets.push({
                label: item.title,
                data: data,
                borderColor: getRandomColor(),
                backgroundColor: 'rgba(0, 0, 0, 0)',
                tension: 0.1
            });
        });
        
        // Destroy previous chart if exists
        if (artworkChart) {
            artworkChart.destroy();
        }
        
        // Create new chart
        artworkChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Artwork Views (Last 30 Days)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Helper function to generate random colors for chart
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    
    // Helper function to show alerts
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        const container = document.querySelector('.artist-dashboard');
        if (container) {
            container.prepend(alertDiv);
            
            // Remove alert after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    }
});
</script>