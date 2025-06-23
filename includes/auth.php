<?php
// Authentication helper functions

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit;
    }
    
    if (!isAdmin()) {
        // Redirect to index if logged in but not admin
        header("Location: ../index.php");
        exit;
    }
}

function getUserInfo() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['role']
    ];
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header("Location: admin/jobs.php");
        } else {
            header("Location: index.php");
        }
        exit;
    }
}
?> 