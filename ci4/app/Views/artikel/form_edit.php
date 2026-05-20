<?= $this->include('template/admin_header'); ?>

<div class="form-container">
    <h2><?= $title; ?></h2>
    <form action="" method="post">
        
        <div class="group-input">
            <label for="judul">Judul Artikel</label>
            <input type="text" name="judul" id="judul" value="<?= $artikel['judul']; ?>" placeholder="Masukkan judul artikel" required>
        </div>

        <div class="group-input">
            <label for="id_kategori">Kategori</label>
            <select name="id_kategori" id="id_kategori" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach($kategori as $k): ?>
                    <option value="<?= $k['id_kategori']; ?>" <?= ($artikel['id_kategori'] == $k['id_kategori']) ? 'selected' : ''; ?>>
                        <?= $k['nama_kategori']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="group-input">
            <label for="isi">Isi Artikel</label>
            <textarea name="isi" id="isi" rows="10" placeholder="Tuliskan isi artikel di sini"><?= $artikel['isi']; ?></textarea>
        </div>

        <div class="group-button">
            <input type="submit" value="Simpan Perubahan" class="btn btn-submit">
            <a href="<?= base_url('/admin/artikel');?>" class="btn btn-cancel">Batal</a>
        </div>

    </form>
</div>

<?= $this->include('template/admin_footer'); ?>