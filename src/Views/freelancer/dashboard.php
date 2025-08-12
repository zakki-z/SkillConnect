<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a freelancer
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'freelancer') {
    header('Location: /SkillConnect/index.php/login');
    exit;
}

$user = $_SESSION['user'];

// Initialize stats if not set
if (!isset($stats)) {
    $stats = [
        'total_projects' => 0,
        'in_progress' => 0,
        'finished' => 0,
        'total_earnings' => 0
    ];
}

// Initialize project arrays if not set
if (!isset($pendingProjects)) {
    $pendingProjects = [];
}
if (!isset($inProgressProjects)) {
    $inProgressProjects = [];
}
if (!isset($finishedProjects)) {
    $finishedProjects = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer Dashboard - SkillConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .project-card {
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .project-card:hover {
            transform: translateY(-5px);
        }
        .section-header {
            font-weight: 600;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .pending {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
        }
        .in-progress {
            background-color: #d1ecf1;
            border-left: 5px solid #17a2b8;
        }
        .finished {
            background-color: #d4edda;
            border-left: 5px solid #28a745;
        }
        .card-header {
            font-weight: bold;
        }
        .price-tag {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
        }
        .deadline {
            color: #dc3545;
            font-weight: 500;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-light">
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/SkillConnect/index.php/dashboard">
            <i class="fas fa-laptop-code"></i> SkillConnect - Freelancer
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="navbar-text me-3">Welcome, <?= htmlspecialchars($user['name'] ?? 'Freelancer') ?></span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm" href="/SkillConnect/">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-tachometer-alt"></i> Freelancer Dashboard</h2>
            <p class="text-muted">Manage your projects and track your progress</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <i class="fas fa-briefcase fa-2x mb-2"></i>
                    <h4><?= $stats['total_projects'] ?></h4>
                    <p class="mb-0">Total Projects</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-spinner fa-2x mb-2"></i>
                    <h4><?= $stats['in_progress'] ?></h4>
                    <p class="mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h4><?= $stats['finished'] ?></h4>
                    <p class="mb-0">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <h4>$<?= number_format($stats['total_earnings'], 2) ?></h4>
                    <p class="mb-0">Total Earnings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Projects (Available to all freelancers) -->
    <div class="section-header pending">
        <h3><i class="fas fa-hourglass-half me-2"></i>Available Projects (<?= count($pendingProjects) ?>)</h3>
        <small class="text-muted">These projects are available for any freelancer to accept</small>
    </div>

    <?php if (empty($pendingProjects)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No pending projects available at the moment.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($pendingProjects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card project-card">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-project-diagram me-2"></i><?= htmlspecialchars($project['title']) ?>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...</p>

                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar-alt me-1"></i>Development Time: <?= $project['dev_time'] ?> days
                                </small>
                                <small class="text-muted d-block">
                                    <i class="fas fa-clock me-1"></i>Posted: <?= date('M d, Y', strtotime($project['created_at'])) ?>
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">$<?= number_format($project['cost'], 2) ?></span>
                                <div>
                                    <a href="/SkillConnect/index.php/accept-project/<?= $project['id'] ?>"
                                       class="btn btn-success btn-sm me-1"
                                       onclick="return confirm('Are you sure you want to accept this project?')">
                                        <i class="fas fa-check"></i> Accept
                                    </a>
                                    <a href="/SkillConnect/index.php/reject-project/<?= $project['id'] ?>"
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to reject this project?')">
                                        <i class="fas fa-times"></i> Pass
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- In Progress Projects (Only assigned to current freelancer) -->
    <div class="section-header in-progress mt-4">
        <h3><i class="fas fa-spinner me-2"></i>My Projects In Progress (<?= count($inProgressProjects) ?>)</h3>
        <small class="text-muted">Projects you are currently working on</small>
    </div>

    <?php if (empty($inProgressProjects)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No projects in progress at the moment.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($inProgressProjects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card project-card">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-cogs me-2"></i><?= htmlspecialchars($project['title']) ?>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...</p>

                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar-alt me-1"></i>Development Time: <?= $project['dev_time'] ?> days
                                </small>
                                <small class="text-muted d-block">
                                    <i class="fas fa-play me-1"></i>Started: <?= date('M d, Y', strtotime($project['updated_at'])) ?>
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">$<?= number_format($project['cost'], 2) ?></span>
                                <div>
                                    <a href="/SkillConnect/index.php/finish-project/<?= $project['id'] ?>"
                                       class="btn btn-success btn-sm me-1"
                                       onclick="return confirm('Mark this project as finished?')">
                                        <i class="fas fa-check-circle"></i> Finish
                                    </a>
                                    <a href="/SkillConnect/index.php/cancel-project/<?= $project['id'] ?>"
                                       class="btn btn-outline-warning btn-sm"
                                       onclick="return confirm('Are you sure you want to cancel this project?')">
                                        <i class="fas fa-times-circle"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Finished Projects (Only assigned to current freelancer) -->
    <div class="section-header finished mt-4">
        <h3><i class="fas fa-check-circle me-2"></i>My Completed Projects (<?= count($finishedProjects) ?>)</h3>
        <small class="text-muted">Projects you have successfully completed</small>
    </div>

    <?php if (empty($finishedProjects)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No finished projects yet. Complete some projects to see them here!
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($finishedProjects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card project-card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-trophy me-2"></i><?= htmlspecialchars($project['title']) ?>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...</p>

                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar-check me-1"></i>Completed: <?= date('M d, Y', strtotime($project['updated_at'])) ?>
                                </small>
                                <small class="text-muted d-block">
                                    <i class="fas fa-clock me-1"></i>Duration: <?= $project['dev_time'] ?> days
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">$<?= number_format($project['cost'], 2) ?></span>
                                <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
    <p class="mb-0">&copy; <?= date('Y') ?> SkillConnect. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Display success/error messages
    <?php if (isset($_SESSION['success_message'])): ?>
    alert('<?= addslashes($_SESSION['success_message']) ?>');
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
    alert('<?= addslashes($_SESSION['error_message']) ?>');
    <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
</script>
</body>
</html>