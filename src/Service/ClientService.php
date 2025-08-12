<?php

namespace Src\Service;

use PDO;

class ClientService
{
    private $pdo;

    public function __construct()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->pdo = DatabaseService::getConnection();

        // Set PDO error mode to exception for debugging
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Get all projects by client ID
     */
    public function getAllProjectsById()
    {
        if (!isset($_SESSION['user'])) {
            error_log('User not logged in');
            return [];
        }

        $user = $_SESSION['user'];

        if (!isset($user['role']) || $user['role'] !== 'client') {
            error_log('Access denied - user is not a client');
            return [];
        }

        $clientId = $user['id'];
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE client_id = ? ORDER BY created_at DESC");
        $stmt->execute([$clientId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new project to the database
     */
    public function addProject($projectData)
    {
        if (!isset($_SESSION['user'])) {
            error_log('User not logged in');
            return false;
        }

        $user = $_SESSION['user'];

        if (!isset($user['role']) || $user['role'] !== 'client') {
            error_log('Access denied - user is not a client');
            return false;
        }

        // Validate required fields
        $requiredFields = ['title', 'description', 'cost', 'dev_time', 'status'];
        foreach ($requiredFields as $field) {
            if (!isset($projectData[$field]) || empty($projectData[$field])) {
                error_log("Missing required field: $field");
                return false;
            }
        }

        try {
            // Debug: Log the data being inserted
            error_log('Attempting to insert project with data: ' . print_r($projectData, true));
            error_log('Client ID from session: ' . $user['id']);

            $stmt = $this->pdo->prepare("
                INSERT INTO projects 
                (title, description, freelancer_id, cost, dev_time, client_id, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            // Debug: Log the SQL query
            error_log('SQL Query: INSERT INTO projects (title, description, freelancer_id, cost, dev_time, client_id, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');

            $executeData = [
                $projectData['title'],
                $projectData['description'],
                $projectData['freelancer_id'] ?? null,
                $projectData['cost'],
                $projectData['dev_time'],
                $user['id'], // client_id from session
                $projectData['status']
            ];

            // Debug: Log the execute data
            error_log('Execute data: ' . print_r($executeData, true));

            $result = $stmt->execute($executeData);

            if ($result) {
                $insertedId = $this->pdo->lastInsertId();
                error_log('Project added successfully with ID: ' . $insertedId);

                // Verify the insert by selecting the row
                $verifyStmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
                $verifyStmt->execute([$insertedId]);
                $insertedProject = $verifyStmt->fetch(PDO::FETCH_ASSOC);

                if ($insertedProject) {
                    error_log('Verification successful - project exists in database');
                } else {
                    error_log('WARNING: Project insert reported success but cannot find inserted row');
                }

                return true;
            } else {
                error_log('Failed to insert project: ' . implode(", ", $stmt->errorInfo()));
                return false;
            }

        } catch (\PDOException $e) {
            error_log('PDO Exception adding project: ' . $e->getMessage());
            error_log('Error Code: ' . $e->getCode());
            error_log('Error Info: ' . print_r($e->errorInfo ?? [], true));
            return false;
        } catch (\Exception $e) {
            error_log('General Exception adding project: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a specific project by ID (ensuring it belongs to the current client)
     */
    public function getProjectById($projectId)
    {
        if (!isset($_SESSION['user'])) {
            error_log('User not logged in');
            return null;
        }

        $user = $_SESSION['user'];

        if (!isset($user['role']) || $user['role'] !== 'client') {
            error_log('Access denied - user is not a client');
            return null;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ? AND client_id = ?");
        $stmt->execute([$projectId, $user['id']]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update project status
     */
    public function updateProjectStatus($projectId, $status)
    {
        if (!isset($_SESSION['user'])) {
            error_log('User not logged in');
            return false;
        }

        $user = $_SESSION['user'];

        if (!isset($user['role']) || $user['role'] !== 'client') {
            error_log('Access denied - user is not a client');
            return false;
        }

        try {
            // Debug logging
            error_log("Updating project ID: $projectId to status: $status for client: " . $user['id']);

            $stmt = $this->pdo->prepare("
                UPDATE projects 
                SET status = ?, updated_at = NOW()
                WHERE id = ? AND client_id = ?
            ");

            $result = $stmt->execute([$status, $projectId, $user['id']]);

            if ($result) {
                $rowsAffected = $stmt->rowCount();
                error_log("Project $projectId status updated to $status. Rows affected: $rowsAffected");
                return $rowsAffected > 0;
            } else {
                error_log('Failed to update project status: ' . implode(", ", $stmt->errorInfo()));
                return false;
            }

        } catch (\PDOException $e) {
            error_log('PDO Exception updating project status: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log('General Exception updating project status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Debug method to check database connection and table structure
     */
    public function debugDatabase()
    {
        try {
            // Check connection
            error_log('Database connection test...');
            $stmt = $this->pdo->query("SELECT 1");
            error_log('Database connection: OK');

            // Check if projects table exists
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'projects'");
            $tableExists = $stmt->fetch();
            if ($tableExists) {
                error_log('Projects table: EXISTS');

                // Get table structure
                $stmt = $this->pdo->query("DESCRIBE projects");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log('Projects table structure: ' . print_r($columns, true));
            } else {
                error_log('Projects table: DOES NOT EXIST');
            }

            // Check current session
            if (isset($_SESSION['user'])) {
                error_log('Session user: ' . print_r($_SESSION['user'], true));
            } else {
                error_log('No user in session');
            }

        } catch (\Exception $e) {
            error_log('Database debug error: ' . $e->getMessage());
        }
    }
}