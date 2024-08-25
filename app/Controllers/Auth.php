<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function register()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'username' => 'required|min_length[3]|is_unique[users.username]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'matches[password]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'errors' => $this->validator->getErrors(),
                    'csrfHash' => csrf_hash(), 
                ]);
            }

          
            $this->userModel->save([
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            ]);

            
            $user = $this->userModel->where('username', $this->request->getPost('username'))->first();

           
            $this->setUserSession($user);

            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registration successful',
                'redirect' => site_url('/welcome'), 
                'csrfHash' => csrf_hash(), 
            ]);
        }

        return view('auth/register');
    }

    public function login()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'username' => 'required',
                'password' => 'required'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $this->validator->getErrors(),
                    'csrfHash' => csrf_hash(), 
                ]);
            }

            $user = $this->userModel->where('username', $this->request->getPost('username'))->first();
            if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
                $this->setUserSession($user);
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'redirect' => site_url('/welcome'), 
                    'csrfHash' => csrf_hash(), 
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid username or password',
                    'csrfHash' => csrf_hash(), 
                ]);
            }
        }

        return view('auth/login');
    }

    private function setUserSession($user)
    {
        $data = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'isLoggedIn' => true,
        ];
        session()->set($data);
        return true;
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }

    public function welcome()
    {
        return view('welcome');  
    }
}
