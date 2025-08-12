<?php
namespace Src\Controllers;

use Src\Service\FreelancerService;

class FreelancerController {
    private $freelancerService;

    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->freelancerService = new FreelancerService();
    }

    //display dashboard
    public function dashboard() {
        // Check if user is logged in and is a freelancer
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            header('Location: /SkillConnect/index.php/login');
            exit;
        }

        $projects = $this->freelancerService->getProjectsForFreelancer();
        $stats = $this->freelancerService->getFreelancerStats();

        // Categorize projects by status
        $pendingProjects = array_filter($projects, function($project) {
            return $project['status'] === 'pending';
        });

        $inProgressProjects = array_filter($projects, function($project) {
            return $project['status'] === 'in_progress';
        });

        $finishedProjects = array_filter($projects, function($project) {
            return $project['status'] === 'finished';
        });

        // Convert filtered arrays to indexed arrays (to avoid issues with array_filter keys)
        $pendingProjects = array_values($pendingProjects);
        $inProgressProjects = array_values($inProgressProjects);
        $finishedProjects = array_values($finishedProjects);

        // Include the view - variables will be available in the included file
        include_once __DIR__ . '/../Views/freelancer/dashboard.php';
    }

    //accept project
    public function acceptProject($params) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            header('Location: /SkillConnect/index.php/login');
            exit;
        }

        if (!empty($params) && isset($params[0])) {
            $projectId = $params[0];
            $result = $this->freelancerService->acceptProject($projectId);

            if ($result) {
                $_SESSION['success_message'] = 'Project accepted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to accept project. It may have already been taken.';
            }
        } else {
            $_SESSION['error_message'] = 'Invalid project ID.';
        }

        header('Location: /SkillConnect/index.php/dashboard');
        exit;
    }

   //reject project
    public function rejectProject($params) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            header('Location: /SkillConnect/index.php/login');
            exit;
        }

        if (!empty($params) && isset($params[0])) {
            $projectId = $params[0];
            $result = $this->freelancerService->rejectProject($projectId);

            if ($result) {
                $_SESSION['success_message'] = 'Project rejected.';
            } else {
                $_SESSION['error_message'] = 'Failed to reject project.';
            }
        } else {
            $_SESSION['error_message'] = 'Invalid project ID.';
        }

        header('Location: /SkillConnect/index.php/dashboard');
        exit;
    }

    //mark as finished
    public function finishProject($params) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            header('Location: /SkillConnect/index.php/login');
            exit;
        }

        if (!empty($params) && isset($params[0])) {
            $projectId = $params[0];
            $result = $this->freelancerService->finishProject($projectId);

            if ($result) {
                $_SESSION['success_message'] = 'Project marked as finished!';
            } else {
                $_SESSION['error_message'] = 'Failed to finish project.';
            }
        } else {
            $_SESSION['error_message'] = 'Invalid project ID.';
        }

        header('Location: /SkillConnect/index.php/dashboard');
        exit;
    }

    //cancel project
    public function cancelProject($params) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            header('Location: /SkillConnect/index.php/login');
            exit;
        }

        if (!empty($params) && isset($params[0])) {
            $projectId = $params[0];
            $result = $this->freelancerService->cancelProject($projectId);

            if ($result) {
                $_SESSION['success_message'] = 'Project cancelled.';
            } else {
                $_SESSION['error_message'] = 'Failed to cancel project.';
            }
        } else {
            $_SESSION['error_message'] = 'Invalid project ID.';
        }

        header('Location: /SkillConnect/index.php/dashboard');
        exit;
    }
}