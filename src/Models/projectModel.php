<?php

namespace Src\Models;

use PDO;
use Src\Service\DatabaseService;

class projectModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseService::getConnection();
    }

    /**
     * Get all projects ordered by deadline
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get projects ordered by deadline (if deadline column exists)
     */
    public function getAllByDeadline(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM projects ORDER BY deadline ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            // If deadline column doesn't exist, fall back to created_at
            error_log('Deadline column not found, using created_at: ' . $e->getMessage());
            return $this->getAll();
        }
    }

    /**
     * Get a project by ID
     */
    public function getById($id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Get projects by client ID
     */
    public function getByClientId($clientId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE client_id = ? ORDER BY created_at DESC");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get projects by freelancer ID
     */
    public function getByFreelancerId($freelancerId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE freelancer_id = ? ORDER BY created_at DESC");
        $stmt->execute([$freelancerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get projects by status
     */
    public function getByStatus($status): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE status = ? ORDER BY created_at DESC");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new project
     */
    public function create($data): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO projects 
                (title, description, freelancer_id, cost, dev_time, client_id, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            return $stmt->execute([
                $data['title'],
                $data['description'],
                $data['freelancer_id'] ?? null,
                $data['cost'],
                $data['dev_time'],
                $data['client_id'],
                $data['status'] ?? 'pending'
            ]);
        } catch (\Exception $e) {
            error_log('Error creating project: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update project status
     */
    public function updateStatus($id, $status): bool
    {
        try {
            // Convert ID to integer to ensure it's treated correctly
            $id = (int)$id;

            // Prepare and execute the update statement
            $stmt = $this->pdo->prepare("
                UPDATE projects 
                SET status = ?, updated_at = NOW()
                WHERE id = ?
            ");

            // Debug information
            error_log("Updating project ID: $id to status: $status");

            $result = $stmt->execute([$status, $id]);

            if (!$result) {
                error_log("Error updating project: " . implode(", ", $stmt->errorInfo()));
            }

            return $result;
        } catch (\Exception $e) {
            error_log('Exception updating project status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update project details
     */
    public function update($id, $data): bool
    {
        try {
            $id = (int)$id;

            $stmt = $this->pdo->prepare("
                UPDATE projects 
                SET title = ?, description = ?, freelancer_id = ?, cost = ?, 
                    dev_time = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ");

            return $stmt->execute([
                $data['title'],
                $data['description'],
                $data['freelancer_id'] ?? null,
                $data['cost'],
                $data['dev_time'],
                $data['status'],
                $id
            ]);
        } catch (\Exception $e) {
            error_log('Error updating project: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a project
     */
    public function delete($id): bool
    {
        try {
            $id = (int)$id;
            $stmt = $this->pdo->prepare("DELETE FROM projects WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\Exception $e) {
            error_log('Error deleting project: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Assign freelancer to project
     */
    public function assignFreelancer($projectId, $freelancerId): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE projects 
                SET freelancer_id = ?, status = 'in_progress', updated_at = NOW()
                WHERE id = ?
            ");

            return $stmt->execute([$freelancerId, $projectId]);
        } catch (\Exception $e) {
            error_log('Error assigning freelancer: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get project statistics
     */
    public function getStats(): array
    {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    status,
                    COUNT(*) as count,
                    AVG(cost) as avg_cost
                FROM projects 
                GROUP BY status
            ");

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Error getting project stats: ' . $e->getMessage());
            return [];
        }
    }
}