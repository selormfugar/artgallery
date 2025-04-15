<?php
// settings.php


// Include database connection
require_once '../includes/config.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../login.php');
//     exit();
// }

// Handle form submission
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $site_name = $_POST['site_name'] ?? '';
//     $admin_email = $_POST['admin_email'] ?? '';

//     // Update settings in the database
//     $query = "UPDATE settings SET site_name = ?, admin_email = ? WHERE id = 1";
//     $stmt = $conn->prepare($query);
//     $stmt->bind_param('ss', $site_name, $admin_email);

//     if ($stmt->execute()) {
//         $success_message = "Settings updated successfully.";
//     } else {
//         $error_message = "Failed to update settings.";
//     }
//     $stmt->close();
//     } else {
//         $error_message = "Failed to update settings.";
//     }


// Fetch current settings
// $query = "SELECT site_name, admin_email FROM settings WHERE id = 1";
// $result = $conn->query($query);
// $settings = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Settings</h1>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="settings.php" method="POST">
            <div class="form-group">
                <label for="site_name">Site Name</label>
                <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="admin_email">Admin Email</label>
                <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($settings['admin_email']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>