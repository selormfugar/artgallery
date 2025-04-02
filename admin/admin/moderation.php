<?php
// moderation.php

// Include function file
require_once '../includes/config.php';
require_once '../includes/functions.php';


// checkAdminPrivileges();

// Fetch reported content for moderation
$reportedContent = fetchReportedContent();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Moderation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Content Moderation</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Content</th>
                    <th>Reported By</th>
                    <th>Reported At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reportedContent)): ?>
                    <?php foreach ($reportedContent as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['content']); ?></td>
                            <td><?php echo htmlspecialchars($row['reported_by']); ?></td>
                            <td><?php echo htmlspecialchars($row['reported_at']); ?></td>
                            <td>
                                <form method="POST" action="handle_moderation.php" style="display:inline;"></form>
                                    <input type="hidden" name="content_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="approve">Approve</button>
                                    <button type="submit" name="action" value="delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No reported content found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
