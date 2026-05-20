<?php

namespace App\Controllers;

use App\Models\UserModel;

class User extends BaseController
{
    /**
     * Menampilkan daftar semua user
     * URL: localhost:8080/user
     */
    public function index()
    {
        $title = 'Daftar User';
        $model = new UserModel();
        $users = $model->findAll();

        return view('user/index', compact('users', 'title'));
    }

    /**
     * Fungsi Login User
     * URL: localhost:8080/user/login
     */
    public function login()
    {
        helper(['form']);
        $session = session();
        $model = new UserModel();

        // Ambil data dari form input
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // 1. Cek jika email kosong (berarti user baru buka halaman login)
        if (!$email) {
            return view('user/login');
        }

        // 2. Cari user berdasarkan email
        $login = $model->where('useremail', $email)->first();

        if ($login) {
            $pass = $login['userpassword'];

            // 3. Verifikasi Password (menggunakan password_verify untuk keamanan)
            if (password_verify($password, $pass)) {
                $login_data = [
                    'user_id'    => $login['id'],
                    'user_name'  => $login['username'],
                    'user_email' => $login['useremail'],
                    'logged_in'  => TRUE,
                ];

                $session->set($login_data);
                return redirect()->to('/admin/artikel');
            } else {
                // Jika password salah
                $session->setFlashdata("flash_msg", "Password salah.");
                return redirect()->to('/user/login');
            }
        } else {
            // Jika email tidak ditemukan
            $session->setFlashdata("flash_msg", "Email tidak terdaftar.");
            return redirect()->to('/user/login');
        }
    }

    /**
     * Fungsi Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/user/login');
    }
}