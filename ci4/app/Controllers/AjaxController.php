<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ArtikelModel;

class AjaxController extends Controller
{
    /**
     * 1. Menampilkan halaman utama AJAX (view)
     * Sesuai instruksi modul halaman 50 [cite: 57, 60]
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen Artikel AJAX'
        ];
        return view('ajax/index', $data);
    }

    /**
     * 2. Mengambil semua data artikel dalam format JSON
     * Sesuai instruksi modul halaman 51 [cite: 61, 71]
     */
    public function getData()
    {
        $model = new ArtikelModel();
        $data = $model->findAll(); 

        return $this->response->setJSON($data); 
    }

    /**
     * 3. Ambil satu data berdasarkan ID (Improvisasi untuk fungsi Ubah)
     */
    public function getById($id)
    {
        $model = new ArtikelModel();
        $data = $model->find($id);

        return $this->response->setJSON($data);
    }

    /**
     * 4. Simpan Data (Tambah & Ubah) - Improvisasi sesuai tugas 
     */
    public function save()
    {
        $model = new ArtikelModel();
        $id = $this->request->getPost('id');
        
        $data = [
            'judul' => $this->request->getPost('judul'),
            'isi'   => $this->request->getPost('isi'),
            'slug'  => url_title($this->request->getPost('judul'), '-', true),
        ];

        if ($id) {
            // Jika ada ID, maka lakukan Update
            $model->update($id, $data);
        } else {
            // Jika tidak ada ID, maka lakukan Insert (Tambah baru)
            $model->insert($data);
        }

        return $this->response->setJSON(['status' => 'OK']);
    }

    /**
     * 5. Menghapus artikel via AJAX
     * Sesuai instruksi modul halaman 51 [cite: 72, 81]
     */
    public function delete($id)
    {
        $model = new ArtikelModel();
        $model->delete($id); 

        $response = [
            'status' => 'OK'
        ];

        return $this->response->setJSON($response); 
    }
}