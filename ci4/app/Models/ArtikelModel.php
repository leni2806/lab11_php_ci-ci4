<?php

namespace App\Models;

use CodeIgniter\Model;

class ArtikelModel extends Model
{
    protected $table            = 'artikel';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    
    // id_kategori wajib masuk sini agar bisa di-input ke database
    protected $allowedFields    = ['judul', 'isi', 'status', 'slug', 'gambar', 'id_kategori'];

    /**
     * Method untuk mengambil data artikel beserta nama kategorinya
     * Ditambahkan pagination agar sinkron dengan Controller
     */
    public function getArtikelDenganKategori()
    {
        // Gunakan 'left' join agar artikel tetap muncul meskipun kategorinya belum diatur
        return $this->select('artikel.*, kategori.nama_kategori')
                    ->join('kategori', 'kategori.id_kategori = artikel.id_kategori', 'left')
                    ->paginate(10); 
    }
}