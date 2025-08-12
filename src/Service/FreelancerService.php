<?php

namespace Src\Service;

use Src\Models\projectModel;

class FreelancerService {
    private $projectModel;

    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->projectModel = new projectModel();
    }
    //get all the projects
    public function getProjectsForFreelancer(): array
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            return [];
        }

        $freelancerId = $_SESSION['user']['id'];
        $allProjects = $this->projectModel->getAll();

        $filteredProjects = [];

        foreach ($allProjects as $project) {
            // Show pending projects to all freelancers
            if ($project['status'] === 'pending') {
                $filteredProjects[] = $project;
            }
            // Show in_progress/finished projects only to assigned freelancer
            elseif (in_array($project['status'], ['in_progress', 'finished']) &&
                $project['freelancer_id'] == $freelancerId) {
                $filteredProjects[] = $project;
            }
        }

        return $filteredProjects;
    }

     //Accept a project and assign it to the current freelancer

    public function acceptProject($id): bool
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            return false;
        }

        $freelancerId = $_SESSION['user']['id'];

        // First check if project is still pending
        $project = $this->projectModel->getById($id);
        if (!$project || $project['status'] !== 'pending') {
            return false;
        }

        // Assign freelancer and update status
        return $this->projectModel->assignFreelancer($id, $freelancerId);
    }


     //Reject a project

    public function rejectProject($id): bool
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            return false;
        }

        // Only allow rejecting pending projects
        $project = $this->projectModel->getById($id);
        if (!$project || $project['status'] !== 'pending') {
            return false;
        }

        return $this->projectModel->updateStatus($id, 'rejected');
    }

    //cancel project
    public function cancelProject($id): bool
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            return false;
        }

        $freelancerId = $_SESSION['user']['id'];
        $project = $this->projectModel->getById($id);

        // Only allow canceling if freelancer is assigned to this project
        if (!$project || $project['freelancer_id'] != $freelancerId) {
            return false;
        }

        return $this->projectModel->updateStatus($id, 'cancelled');
    }

    //finish project
    public function finishProject($id): bool
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            return false;
        }

        $freelancerId = $_SESSION['user']['id'];
        $project = $this->projectModel->getById($id);

        // Only allow finishing if freelancer is assigned to this project
        if (!$project || $project['freelancer_id'] != $freelancerId || $project['status'] !== 'in_progress') {
            return false;
        }

        return $this->projectModel->updateStatus($id, 'finished');
    }

    //stats
    public function getFreelancerStats(): array
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'freelancer') {
            return [];
        }

        $freelancerId = $_SESSION['user']['id'];
        $projects = $this->projectModel->getByFreelancerId($freelancerId);

        return [
            'total_projects' => count($projects),
            'in_progress' => count(array_filter($projects, fn($p) => $p['status'] === 'in_progress')),
            'finished' => count(array_filter($projects, fn($p) => $p['status'] === 'finished')),
            'total_earnings' => array_sum(array_map(fn($p) => $p['status'] === 'finished' ? $p['cost'] : 0, $projects))
        ];
    }
}