<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

// Only admins can access this
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

if ($action === 'fetch_cashiers') {
    try {
        $stmt = $pdo->query("SELECT id, emp_id, full_name, username, role, created_at FROM users ORDER BY created_at DESC");
        $cashiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'cashiers' => $cashiers]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
    exit;
}

if ($action === 'add_staff') {
    $fullName = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'cashier';

    if (empty($fullName) || empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    if (!in_array($role, ['admin', 'cashier'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
        exit;
    }

    // Check if username exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username already taken']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$fullName, $username, $hashedPassword, $role])) {
            log_action("Register " . ucfirst($role), "Added new $role: $fullName ($username)");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add staff']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'delete_cashier') {
    $id = $_POST['id'];
    
    if ($id == $_SESSION['id']) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete yourself']);
        exit;
    }

    try {
        // Get user info for logging before delete
        $user_stmt = $pdo->prepare("SELECT full_name, username, role FROM users WHERE id = ?");
        $user_stmt->execute([$id]);
        $user_to_delete = $user_stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id])) {
            if ($user_to_delete) {
                log_action("Revoke Access", "Deleted {$user_to_delete['role']}: {$user_to_delete['full_name']} ({$user_to_delete['username']})");
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete staff']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete staff with active history.']);
    }
    exit;
}
