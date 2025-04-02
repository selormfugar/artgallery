<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get the endpoint path
$path = isset($_GET['action']) ? $_GET['action'] : '';

// Handle different endpoints with switch case
switch ($method) {
    // GET request - Retrieve user data
    case 'GET':
        if ($path === 'profile') {
            // Get user profile data
            $userId = $_SESSION['user_id'];
            global $db;
            
            // Get user data from users table
            $sql = "SELECT user_id, firstname, lastname, email, role, created_at FROM users WHERE user_id = ? AND archived = 0";
            $user = $db->selectOne($sql, [$userId]);
            
            if ($user) {
                // If user is an artist, get additional artist data
                if ($user['role'] === 'artist') {
                    $artistSql = "SELECT bio, profile_picture, social_links FROM artists WHERE user_id = ? AND archived = 0";
                    $artist = $db->selectOne($artistSql, [$userId]);
                    
                    if ($artist) {
                        // Convert social_links from JSON if needed
                        if (!empty($artist['social_links'])) {
                            $artist['social_links'] = json_decode($artist['social_links'], true);
                        }
                        
                        // Merge artist data with user data
                        $user = array_merge($user, $artist);
                    }
                }
                
                $response['success'] = true;
                $response['data'] = $user;
            } else {
                $response['message'] = 'User not found.';
            }
        } else {
            $response['message'] = 'Invalid endpoint.';
        }
        break;
    
    // PUT request - Update user information
    case 'PUT':
        // Get PUT data (PHP doesn't populate $_PUT by default)
        parse_str(file_get_contents('php://input'), $putData);
        
        if ($path === 'profile') {
            // Update user profile
            $userId = $_SESSION['user_id'];
            global $db;
            
            // Validate input data
            $firstname = isset($putData['firstName']) ? sanitizeInput($putData['firstName']) : '';
            $lastname = isset($putData['lastName']) ? sanitizeInput($putData['lastName']) : '';
            $email = isset($putData['email']) ? sanitizeInput($putData['email']) : '';
            
            if (empty($firstname) || empty($lastname) || empty($email)) {
                $response['message'] = 'All fields are required.';
                break;
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Invalid email format.';
                break;
            }
            
            // Check if email already exists (but not for current user)
            $emailCheckSql = "SELECT user_id FROM users WHERE email = ? AND user_id != ? AND archived = 0";
            $existingUser = $db->selectOne($emailCheckSql, [$email, $userId]);
            
            if ($existingUser) {
                $response['message'] = 'Email is already in use.';
                break;
            }
            
            // Update user data
            $userData = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $db->update('users', $userData, 'user_id = ?', [$userId]);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Profile updated successfully.';
                $response['data'] = [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $email
                ];
            } else {
                $response['message'] = 'Error updating profile.';
            }
        } elseif ($path === 'password') {
            // Update user password
            $userId = $_SESSION['user_id'];
            global $db;
            
            // Validate input data
            $currentPassword = isset($putData['currentPassword']) ? $putData['currentPassword'] : '';
            $newPassword = isset($putData['newPassword']) ? $putData['newPassword'] : '';
            $confirmPassword = isset($putData['confirmPassword']) ? $putData['confirmPassword'] : '';
            
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $response['message'] = 'All password fields are required.';
                break;
            }
            
            // Verify password length
            if (strlen($newPassword) < 8) {
                $response['message'] = 'Password must be at least 8 characters long.';
                break;
            }
            
            // Verify passwords match
            if ($newPassword !== $confirmPassword) {
                $response['message'] = 'New passwords do not match.';
                break;
            }
            
            // Get current user password hash
            $userSql = "SELECT password_hash FROM users WHERE user_id = ? AND archived = 0";
            $user = $db->selectOne($userSql, [$userId]);
            
            if (!$user) {
                $response['message'] = 'User not found.';
                break;
            }
            
            // Verify current password
            if (!password_verify($currentPassword, $user['password_hash'])) {
                $response['message'] = 'Current password is incorrect.';
                http_response_code(401);
                break;
            }
            
            // Update password
            $passwordData = [
                'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $db->update('users', $passwordData, 'user_id = ?', [$userId]);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Password updated successfully.';
            } else {
                $response['message'] = 'Error updating password.';
            }
        } else {
            $response['message'] = 'Invalid endpoint.';
        }
        break;
    
    // DELETE request - Delete user account
    case 'DELETE':
        if ($path === 'account') {
            $userId = $_SESSION['user_id'];
            global $db;
            
            // Check user role to handle additional data
            $userSql = "SELECT role FROM users WHERE user_id = ? AND archived = 0";
            $user = $db->selectOne($userSql, [$userId]);
            
            if (!$user) {
                $response['message'] = 'User not found.';
                break;
            }
            
            // Begin transaction
            $db->beginTransaction();
            
            try {
                // If user is an artist, archive artist profile first
                if ($user['role'] === 'artist') {
                    $artistResult = $db->update('artists', 
                        ['archived' => 1, 'updated_at' => date('Y-m-d H:i:s')], 
                        'user_id = ?', 
                        [$userId]
                    );
                    
                    // Archive all artworks by this artist
                    $artworkResult = $db->update('artworks',
                        ['archived' => 1, 'updated_at' => date('Y-m-d H:i:s')],
                        'artist_id = ?',
                        [$userId]
                    );
                }
                
                // Archive user account (soft delete)
                $userResult = $db->update('users', 
                    ['archived' => 1, 'updated_at' => date('Y-m-d H:i:s')], 
                    'user_id = ?', 
                    [$userId]
                );
                
                if ($userResult) {
                    // Commit transaction
                    $db->commit();
                    
                    // Destroy session
                    session_unset();
                    session_destroy();
                    
                    $response['success'] = true;
                    $response['message'] = 'Account deleted successfully.';
                } else {
                    throw new Exception('Error deleting user account.');
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                $db->rollback();
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['message'] = 'Invalid endpoint.';
        }
        break;
    
    default:
        $response['message'] = 'Invalid request method.';
        break;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>