<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a client
//if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'client') {
//    header('Location: /SkillConnect/index.php/login');
//    exit;
//}

$user = $_SESSION['user'];

// Initialize projects array if not set
if (!isset($projects)) {
    $projects = [];
}

// Get filter parameter
$filter = $_GET['filter'] ?? 'all';

// Filter projects based on status
$filteredProjects = [];
if (isset($projects) && is_array($projects)) {
    if ($filter === 'all') {
        $filteredProjects = $projects;
    } else {
        $filteredProjects = array_filter($projects, function($project) use ($filter) {
            return $project['status'] === $filter;
        });
    }
}

// Count projects by status
$statusCounts = [
    'all' => count($projects ?? []),
    'pending' => count(array_filter($projects ?? [], fn($p) => $p['status'] === 'pending')),
    'in_progress' => count(array_filter($projects ?? [], fn($p) => $p['status'] === 'in_progress')),
    'cancelled' => count(array_filter($projects ?? [], fn($p) => $p['status'] === 'cancelled'))
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - SkillConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-handshake"></i> SkillConnect</a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">Welcome, <?= htmlspecialchars($user['name'] ?? 'User') ?></span>
            <a class="btn btn-outline-light btn-sm" href="/SkillConnect/">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-project-diagram"></i> My Projects</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                    <i class="fas fa-plus"></i> Create New Project
                </button>
            </div>
        </div>
    </div>

    <!-- Category Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <a href="?filter=all" class="text-decoration-none <?= $filter === 'all' ? 'text-primary fw-bold' : 'text-dark' ?>">
                                <div class="p-3 rounded <?= $filter === 'all' ? 'bg-primary bg-opacity-10' : '' ?>">
                                    <i class="fas fa-list-alt fa-2x mb-2"></i>
                                    <h5><?= $statusCounts['all'] ?></h5>
                                    <p class="mb-0">All Projects</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="?filter=pending" class="text-decoration-none <?= $filter === 'pending' ? 'text-warning fw-bold' : 'text-dark' ?>">
                                <div class="p-3 rounded <?= $filter === 'pending' ? 'bg-warning bg-opacity-10' : '' ?>">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h5><?= $statusCounts['pending'] ?></h5>
                                    <p class="mb-0">Pending</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="?filter=in_progress" class="text-decoration-none <?= $filter === 'in_progress' ? 'text-info fw-bold' : 'text-dark' ?>">
                                <div class="p-3 rounded <?= $filter === 'in_progress' ? 'bg-info bg-opacity-10' : '' ?>">
                                    <i class="fas fa-spinner fa-2x mb-2"></i>
                                    <h5><?= $statusCounts['in_progress'] ?></h5>
                                    <p class="mb-0">In Progress</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="?filter=cancelled" class="text-decoration-none <?= $filter === 'cancelled' ? 'text-danger fw-bold' : 'text-dark' ?>">
                                <div class="p-3 rounded <?= $filter === 'cancelled' ? 'bg-danger bg-opacity-10' : '' ?>">
                                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                                    <h5><?= $statusCounts['cancelled'] ?></h5>
                                    <p class="mb-0">Cancelled</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects List -->
    <div class="row">
        <div class="col-12">
            <?php if (empty($filteredProjects)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No projects found</h5>
                        <p class="text-muted">
                            <?php if ($filter === 'all'): ?>
                                You haven't created any projects yet. Click "Create New Project" to get started!
                            <?php else: ?>
                                No projects with "<?= ucfirst(str_replace('_', ' ', $filter)) ?>" status.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($filteredProjects as $project): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="card-title mb-2"><?= htmlspecialchars($project['title']) ?></h5>
                                    <p class="card-text text-muted mb-2"><?= htmlspecialchars($project['description']) ?></p>
                                    <div class="d-flex gap-3 text-sm">
                                        <span><i class="fas fa-dollar-sign"></i> $<?= number_format($project['cost'], 2) ?></span>
                                        <span><i class="fas fa-calendar-alt"></i> <?= $project['dev_time'] ?> days</span>
                                        <span><i class="fas fa-clock"></i> Created: <?= date('M d, Y', strtotime($project['created_at'])) ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <?php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusIcon = [
                                        'pending' => 'clock',
                                        'in_progress' => 'spinner',
                                        'cancelled' => 'times-circle'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusClass[$project['status']] ?? 'secondary' ?> mb-2">
                                            <i class="fas fa-<?= $statusIcon[$project['status']] ?? 'question' ?>"></i>
                                            <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
                                        </span>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Project Modal -->
<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Create New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/SkillConnect/index.php/add-project">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="title" class="form-label">Project Title *</label>
                            <input type="text" class="form-control" name="title" id="title" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Project Description *</label>
                            <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cost" class="form-label">Budget ($) *</label>
                            <input type="number" class="form-control" name="cost" id="cost" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dev_time" class="form-label">Development Time (days) *</label>
                            <input type="number" class="form-control" name="dev_time" id="dev_time" min="1" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="status" class="form-label">Initial Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden form for status updates -->
<form id="statusUpdateForm" method="POST" action="/SkillConnect/index.php/update-project-status" style="display: none;">
    <input type="hidden" name="project_id" id="statusProjectId">
    <input type="hidden" name="status" id="statusValue">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function updateStatus(projectId, status) {
        if (confirm(`Are you sure you want to change the project status to "${status.replace('_', ' ')}"?`)) {
            document.getElementById('statusProjectId').value = projectId;
            document.getElementById('statusValue').value = status;
            document.getElementById('statusUpdateForm').submit();
        }
    }

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