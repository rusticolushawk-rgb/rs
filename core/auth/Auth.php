<?php
namespace Core\Auth;

use Core\Database\Database;

class Auth
{
    private Database $db;
    private ?array $user = null;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->loadUser();
    }

    private function loadUser(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->user = $this->db->fetch(
                "SELECT u.*, r.name as role_name, r.permissions as role_permissions 
                 FROM users u 
                 LEFT JOIN roles r ON u.role_id = r.id 
                 WHERE u.id = ? AND u.is_active = 1",
                [$_SESSION['user_id']]
            );
        }
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND is_active = 1",
            [$email]
        );

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $this->user = $user;

            // Update last login
            $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

            // Audit log
            $this->logAudit('login', 'users', $user['id'], 'User logged in');

            return true;
        }

        return false;
    }

    public function logout(): void
    {
        if ($this->user) {
            $this->logAudit('logout', 'users', $this->user['id'], 'User logged out');
        }
        session_destroy();
        $this->user = null;
    }

    public function check(): bool
    {
        return $this->user !== null;
    }

    public function user(): ?array
    {
        return $this->user;
    }

    public function id(): ?int
    {
        return $this->user['id'] ?? null;
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->user) return false;
        if ($this->user['role_name'] === 'admin') return true;

        $permissions = json_decode($this->user['role_permissions'] ?? '[]', true);
        return in_array($permission, $permissions);
    }

    public function hasRole(string $role): bool
    {
        return $this->user && $this->user['role_name'] === $role;
    }

    private function logAudit(string $action, string $entity, int $entityId, string $description): void
    {
        try {
            $this->db->insert('audit_logs', [
                'user_id' => $this->user['id'] ?? 0,
                'action' => $action,
                'entity_type' => $entity,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            // Silently fail audit logging
        }
    }
}
