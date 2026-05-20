<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Artikel extends BaseController
{
    // ==========================================
    // 1. Tampilan untuk User (Pengunjung)
    // ==========================================
    public function index()
    {
        $title = 'Daftar Artikel';
        $model = new ArtikelModel();

        $data = [
            'title'   => $title,
            'artikel' => $model->getArtikelDenganKategori(), // Method ini harus me-return paginate()
            'pager'   => $model->pager
        ];

        return view('artikel/index', $data);
    }

    // ==========================================
    // 2. Tampilan Admin (Daftar Artikel Management)
    // ==========================================
public function admin_index()
{
    $title = 'Daftar Artikel (Admin)';
    $model = new ArtikelModel();
    $kategoriModel = new KategoriModel();

    $q = $this->request->getVar('q') ?? '';
    $kategori_id = $this->request->getVar('kategori_id') ?? '';
    $page = $this->request->getVar('page') ?? 1;

    // TUGAS 4: Ambil parameter sorting (default: urut berdasarkan ID desc)
    $sortBy = $this->request->getVar('sort_by') ?? 'artikel.id';
    $sortOrder = $this->request->getVar('sort_order') ?? 'desc';

    $builder = $model->table('artikel');
    $builder->select('artikel.*, kategori.nama_kategori');
    $builder->join('kategori', 'kategori.id_kategori = artikel.id_kategori', 'left');

    if ($q != '') {
        $builder->like('artikel.judul', $q);
    }

    if ($kategori_id != '') {
        $builder->where('artikel.id_kategori', $kategori_id);
    }

    // TUGAS 4: Terapkan fungsi orderBy pada query builder
    $builder->orderBy($sortBy, $sortOrder);

    $artikel = $builder->paginate(10, 'default', $page);
    $pager = $model->pager;

    $data = [
        'title'       => $title,
        'q'           => $q,
        'kategori_id' => $kategori_id,
        'sort_by'     => $sortBy,     // Kirim balik ke AJAX
        'sort_order'  => $sortOrder,  // Kirim balik ke AJAX
        'artikel'     => $artikel,
        'pager'       => $pager
    ];

    if ($this->request->isAJAX()) {
        return $this->response->setJSON($data);
    } else {
        $data['kategori'] = $kategoriModel->findAll();
        return view('artikel/admin_index', $data);
    }
}

    // ==========================================
    // 3. Tambah Artikel
    // ==========================================
    public function add()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'judul' => 'required',
            'id_kategori' => 'required'
        ]);

        $isDataValid = $validation->withRequest($this->request)->run();

        if ($isDataValid) {
            $file = $this->request->getFile('gambar');
            
            // Proses upload gambar jika file valid
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $file->move(ROOTPATH . 'public/gambar');
                $namaGambar = $file->getName();
            } else {
                $namaGambar = null;
            }

            $artikel = new ArtikelModel();
            $artikel->insert([
                'judul'       => $this->request->getPost('judul'),
                'isi'         => $this->request->getPost('isi'),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'slug'        => url_title($this->request->getPost('judul'), '-', true),
                'gambar'      => $namaGambar,
            ]);

            return redirect()->to('/admin/artikel');
        }

        $kategoriModel = new KategoriModel();
        $data = [
            'title'    => 'Tambah Artikel',
            'kategori' => $kategoriModel->findAll()
        ];

        return view('artikel/form_add', $data);
    }

    // ==========================================
    // 4. Edit Artikel
    // ==========================================
    public function edit($id)
    {
        $model = new ArtikelModel();
        $kategoriModel = new KategoriModel();

        if ($this->request->getMethod() === 'post' && $this->validate([
            'judul'       => 'required',
            'id_kategori' => 'required'
        ])) {
            $model->update($id, [
                'judul'       => $this->request->getPost('judul'),
                'isi'         => $this->request->getPost('isi'),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'slug'        => url_title($this->request->getPost('judul'), '-', true)
            ]);

            return redirect()->to('/admin/artikel');
        }

        $data = [
            'title'    => "Edit Artikel",
            'artikel'  => $model->find($id),
            'kategori' => $kategoriModel->findAll()
        ];

        if (!$data['artikel']) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('artikel/form_edit', $data);
    }

    // ==========================================
    // 5. Hapus Artikel
    // ==========================================
    public function delete($id)
    {
        $model = new ArtikelModel();
        $model->delete($id);

        return redirect()->to('/admin/artikel');
    }

    // ==========================================
    // 6. View Detail (Slug)
    // ==========================================
    public function view($slug)
    {
        $model = new ArtikelModel();
        $data['artikel'] = $model->where('slug', $slug)->first();

        if (empty($data['artikel'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $data['title'] = $data['artikel']['judul'];

        return view('artikel/detail', $data);
    }
}