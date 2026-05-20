<?= $this->extend('layout/main'); ?> <?= $this->section('content'); ?> <h1><?= $title; ?></h1> 
    <hr> 
    <p><?= $content; ?></p> 

<?= $this->endSection(); ?> ```

---

### Kenapa Pakai Cara Ini Lebih Enak?
1. **Satu Pintu:** Semua pengaturan HTML (seperti `<head>`, CSS, dan Footer) sekarang terpusat di `app/Views/layout/main.php`.
2. **Otomatis Ada Sidebar:** Karena di file `main.php` tadi kita sudah menambahkan `<aside>` (Sidebar), maka halaman About kamu sekarang otomatis punya sidebar di sebelah kanan tanpa perlu nulis kodenya lagi di sini.
3. **Kode Lebih Bersih:** File view kamu (seperti `about.php`) jadi benar-benar fokus hanya ke isinya saja, tidak tercampur kode struktur HTML yang berulang-ulang.



### Tips Penting:
* Pastikan file **`app/Views/layout/main.php`** sudah kamu buat ya.
* Perhatikan penulisan `section('content')`. Kata **'content'** harus sama persis dengan yang kamu tulis di `renderSection('content')` di file `main.php`. Kalau beda satu huruf saja, isinya tidak akan muncul.

Gimana, sudah dicoba ganti? Harusnya sekarang tampilan halaman kamu jadi lebih rapi dan punya sidebar di sampingnya! 😎