<?php
namespace Core\Controllers;

class UserController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();

        $page = max(1, (int)$this->input('page', 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $search = $this->input('search', '');

        $where = '1=1';
        $params = [];
        if ($search) {
            $where .= ' AND (u.name LIKE ? OR u.email LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $total = $db->count('users u', $where, $params);
        $users = $db->fetchAll(
            "SELECT u.*, r.display_name as role_name 
             FROM users u 
             LEFT JOIN roles r ON u.role_id = r.id 
             WHERE {$where} 
             ORDER BY u.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $roles = $db->fetchAll("SELECT * FROM roles ORDER BY name");

        if ($this->isAjax()) {
            $this->json(['users' => $users, 'total' => $total, 'page' => $page]);
        }

        $this->view('users.index', [
            'users' => $users,
            'roles' => $roles,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'pageTitle' => t('user_management'),
            'breadcrumb' => [['label' => t('users'), 'url' => '/users']],
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('admin');
        $roles = $this->app->getDb()->fetchAll("SELECT * FROM roles ORDER BY name");

        $this->view('users.form', [
            'user' => null,
            'roles' => $roles,
            'pageTitle' => t('add_user'),
            'breadcrumb' => [
                ['label' => t('users'), 'url' => '/users'],
                ['label' => t('add_user'), 'url' => '/users/create'],
            ],
        ]);
    }

    public function store(): void
    {
        $this->requirePermission('admin');
        $db = $this->app->getDb();

        $data = [
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'password' => password_hash($this->input('password'), PASSWORD_DEFAULT),
            'role_id' => (int)$this->input('role_id', 3),
            'phone' => $this->input('phone', ''),
            'is_active' => (int)$this->input('is_active', 1),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $id = $db->insert('users', $data);
        $this->audit('create', 'users', $id, "Created user: {$data['name']}");

        if ($this->isAjax()) {
            $this->json(['success' => true, 'id' => $id, 'message' => t('user_created')]);
        }
        $this->redirect('/users');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();

        $user = $db->fetch(
            "SELECT u.*, r.display_name as role_name 
             FROM users u LEFT JOIN roles r ON u.role_id = r.id 
             WHERE u.id = ?",
            [$id]
        );

        if (!$user) {
            $this->redirect('/users');
        }

        $roles = $db->fetchAll("SELECT * FROM roles ORDER BY name");

        $this->view('users.form', [
            'user' => $user,
            'roles' => $roles,
            'pageTitle' => t('edit_user'),
            'breadcrumb' => [
                ['label' => t('users'), 'url' => '/users'],
                ['label' => $user['name'], 'url' => "/users/{$id}"],
            ],
        ]);
    }

    public function update(string $id): void
    {
        $this->requirePermission('admin');
        $db = $this->app->getDb();

        $data = [
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'role_id' => (int)$this->input('role_id', 3),
            'phone' => $this->input('phone', ''),
            'is_active' => (int)$this->input('is_active', 1),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $password = $this->input('password');
        if ($password) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $db->update('users', $data, 'id = ?', [$id]);
        $this->audit('update', 'users', (int)$id, "Updated user: {$data['name']}");

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => t('user_updated')]);
        }
        $this->redirect('/users');
    }

    public function delete(string $id): void
    {
        $this->requirePermission('admin');
        $db = $this->app->getDb();

        $db->update('users', ['is_active' => 0], 'id = ?', [$id]);
        $this->audit('delete', 'users', (int)$id, 'Deactivated user');

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => t('user_deleted')]);
        }
        $this->redirect('/users');
    }
}
