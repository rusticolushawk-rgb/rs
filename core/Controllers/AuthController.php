<?php
namespace Core\Controllers;

class AuthController extends BaseController
{
    public function loginForm(): void
    {
        if ($this->app->getAuth()->check()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth.login');
    }

    public function login(): void
    {
        $email = $this->input('email', '');
        $password = $this->input('password', '');

        if ($this->app->getAuth()->attempt($email, $password)) {
            if ($this->isAjax()) {
                $this->json(['success' => true, 'redirect' => '/dashboard']);
            }
            $this->redirect('/dashboard');
        }

        if ($this->isAjax()) {
            $this->json(['success' => false, 'error' => t('invalid_credentials')], 401);
        }

        $_SESSION['login_error'] = t('invalid_credentials');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->app->getAuth()->logout();
        session_start();
        $_SESSION['flash']['success'] = t('logged_out');
        $this->redirect('/login');
    }
}
