<?= $this->include('template/header'); ?>

<?php if ($artikel): foreach ($artikel as $row): ?>
    <article class="entry">
        <h2>
            <a href="<?= base_url('/artikel/' . $row['slug']); ?>">
                <?= $row['judul']; ?>
            </a>
        </h2>

        <p><small>Kategori: <strong><?= $row['nama_kategori'] ?? 'Tanpa Kategori'; ?></strong></small></p>

        <?php if (!empty($row['gambar'])): ?>
            <img src="<?= base_url('/gambar/' . $row['gambar']); ?>" alt="<?= $row['judul']; ?>" style="max-width: 200px; display: block; margin-bottom: 10px;">
        <?php endif; ?>

        <p><?= substr($row['isi'], 0, 200); ?>...</p>
    </article>
    <hr class="divider" />
<?php endforeach; else: ?>
    <article class="entry">
        <h2>Belum ada data.</h2>
    </article>
<?php endif; ?>

<?php if (isset($pager)): ?>
    <?= $pager->links(); ?>
<?php endif; ?>

<?= $this->include('template/footer'); ?>