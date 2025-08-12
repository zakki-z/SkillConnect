<?php

namespace Src\Controllers;

use Src\Service\ClientService;

class ClientController
{
    private $clientService;

    public function __construct()
    {
        $this->clientService = new ClientService();
    }

    /**
     * Main client dashboard - shows all projects with filtering
     */
    public function client(): void
    {


        $projects = $this->clientService->getAllProjectsById();
        include __DIR__ . '/../Views/Client/ClientView.php';
    }

    /**
     * Handle project creation
     */
    public function addProject()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Debug: Log all POST data
        error_log('POST data received: ' . print_r($_POST, true));
        error_log('Session data: ' . print_r($_SESSION ?? [], true));

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
            error_log('Access denied - user not logged in or not a client');
            header('Location: /SkillConnect/index.php/login');
            exit;
        }

        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            header("Location: /SkillConnect/index.php/client");
            exit;
        }

        // Collect form data
        $projectData = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'cost' => $_POST['cost'] ?? '',
            'dev_time' => $_POST['dev_time'] ?? '',
            'status' => $_POST['status'] ?? 'pending'
        ];

        error_log('Raw project data: ' . print_r($projectData, true));

        // Validate and sanitize input
        $projectData = $this->sanitizeProjectData($projectData);

        error_log('Sanitized project data: ' . print_r($projectData, true));

        if ($this->validateProjectData($projectData)) {
            error_log('Project data validation: PASSED');

            // Debug database before inserting
            $this->clientService->debugDatabase();

            $result = $this->clientService->addProject($projectData);

            if ($result) {
                $_SESSION['success_message'] = 'Project created successfully!';
                error_log('Project creation: SUCCESS');
            } else {
                $_SESSION['error_message'] = 'Failed to create project. Please try again.';
                error_log('Project creation: FAILED');
            }
        } else {
            $_SESSION['error_message'] = 'Please fill in all required fields correctly.';
            error_log('Project data validation: FAILED');
        }

        header("Location: /SkillConnect/index.php/client");
        exit;
    }

    /**
     * Update project status
     */
    public function updateProjectStatus()
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /SkillConnect/index.php/client");
            exit;
        }

        $projectId = $_POST['project_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if ($projectId && $status) {
            $result = $this->clientService->updateProjectStatus($projectId, $status);

            if ($result) {
                $_SESSION['success_message'] = 'Project status updated successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to update project status.';
            }
        } else {
            $_SESSION['error_message'] = 'Invalid project data.';
        }

        header("Location: /SkillConnect/index.php/client");
        exit;
    }

    /**
     * Sanitize project data
     */
    private function sanitizeProjectData($data)
    {
        return [
            'title' => htmlspecialchars(trim($data['title']), ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars(trim($data['description']), ENT_QUOTES, 'UTF-8'),
            'cost' => (float)$data['cost'],
            'dev_time' => (int)$data['dev_time'],
            'status' => htmlspecialchars(trim($data['status']), ENT_QUOTES, 'UTF-8')
        ];
    }

    /**
     * Validate project data
     */
    private function validateProjectData($data)
    {
        // Check required fields
        if (empty($data['title']) || empty($data['description']) ||
            $data['cost'] <= 0 || $data['dev_time'] <= 0) {
            return false;
        }

        // Validate status
        $validStatuses = ['pending', 'in_progress', 'cancelled'];
        if (!in_array($data['status'], $validStatuses)) {
            return false;
        }

        return true;
    }
}