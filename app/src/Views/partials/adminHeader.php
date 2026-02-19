<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Haarlem Festival</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        #admin-wrapper { display: flex; min-height: 100vh; }
        
        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #212529;
            color: #fff;
            padding-top: 20px;
        }
        #sidebar .nav-link {
            color: #adb5bd;
            padding: 12px 20px;
            display: flex;
            align-items: center;
        }
        #sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        #sidebar .nav-link.active {
            color: #fff;
            background: #0d6efd;
        }
        .sidebar-heading {
            padding: 10px 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #6c757d;
        }
        #main-content { flex-grow: 1; padding: 30px; }
    </style>
</head>
<body>

<div id="admin-wrapper">
    <nav id="sidebar">
        <div class="px-4 mb-4">
            <h5 class="text-white">Haarlem CMS</h5>
        </div>
        
        <div class="sidebar-heading">Management</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/admin/dashboard" class="nav-link">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/users" class="nav-link active">
                    <i class="bi bi-people me-2"></i> Manage Users
                </a>
            </li>
        </ul>

        <div class="sidebar-heading mt-4">Events</div>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="/admin/jazz" class="nav-link"><i class="bi bi-music-note-beamed me-2"></i> Jazz</a></li>
            <li class="nav-item"><a href="/admin/dance" class="nav-link"><i class="bi bi-activity me-2"></i> Dance</a></li>
        </ul>

        <div class="mt-auto p-3">
            <a href="/" class="btn btn-outline-light btn-sm w-100 mb-2">View Website</a>
            <a href="/logout" class="btn btn-danger btn-sm w-100">Logout</a>
        </div>
    </nav>

    <main id="main-content">