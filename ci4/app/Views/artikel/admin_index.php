<?= $this->include('template/admin_header'); ?>

<div style="text-align: left; padding: 20px;">
    <h2><?= $title; ?></h2>

    <div style="margin-bottom: 20px;">
        <form id="search-form" class="form-search" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="q" id="search-box" value="<?= $q; ?>" placeholder="Cari judul artikel..." 
                   style="padding: 8px; width: 250px; border: 1px solid #ddd; border-radius: 4px;">
            
            <select name="kategori_id" id="category-filter" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">-- Semua Kategori --</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= $k['id_kategori']; ?>" <?= ($kategori_id == $k['id_kategori']) ? 'selected' : ''; ?>>
                        <?= $k['nama_kategori']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

           <input type="submit" value="Cari" class="btn-cari">
            
            <a href="<?= base_url('/admin/artikel/add'); ?>" class="btn btn-success" style="padding: 8px 20px; text-decoration: none; background-color: #28a745; color: white; border-radius: 4px;">Tambah Artikel</a>
        </form>
    </div>

    <table class="table" border="1" width="100%" style="border-collapse: collapse; margin-top: 10px;">
        <thead>
            <tr style="background-color: #f4f4f4;">
                <th width="50" style="padding: 10px; cursor: pointer;" class="th-sort" data-column="artikel.id">ID <span>↕</span></th>
                <th style="padding: 10px; cursor: pointer;" class="th-sort" data-column="artikel.judul">Judul & Deskripsi <span>↕</span></th>
                <th width="150" style="padding: 10px;">Kategori</th>
                <th width="100" style="padding: 10px;">Status</th>
                <th width="150" style="padding: 10px;">Aksi</th>
            </tr>
        </thead>
        <tbody id="article-container"></tbody>
    </table>

    <div id="pagination-container" style="margin-top: 20px;"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const articleContainer = $('#article-container');
    const paginationContainer = $('#pagination-container');
    const searchForm = $('#search-form');
    const searchBox = $('#search-box');
    const categoryFilter = $('#category-filter');

    // TUGAS 4: Variabel global untuk menyimpan status sorting aktif
    let currentSortBy = 'artikel.id';
    let currentSortOrder = 'desc';

    // Fungsi mengambil data via AJAX
    const fetchData = (url) => {
        // TUGAS 3: Indikator loading
        articleContainer.html('<tr><td colspan="5" style="text-align:center; padding: 20px; color: #666;"><b>🔄 Sedang memuat data...</b></td></tr>');
        paginationContainer.html('');

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                // Update status sorting dari response server
                currentSortBy = data.sort_by;
                currentSortOrder = data.sort_order;
                
                renderArticles(data.artikel);
                renderPagination(data.pager, data.q, data.kategori_id);
                updateSortIcons();
            },
            error: function(xhr, status, error) {
                articleContainer.html('<tr><td colspan="5" style="text-align:center; padding: 20px; color: red;">Gagal memuat data.</td></tr>');
            }
        });
    };

    // Fungsi render baris tabel artikel
    const renderArticles = (articles) => {
        let html = '';
        if (articles.length > 0) {
            articles.forEach(article => {
                let ringkasan = article.isi ? article.isi.substring(0, 50) : '';
                let kategori = article.nama_kategori ? article.nama_kategori : 'Umum';
                let status = (article.status == '1') ? 'Publish' : 'Draft';
                
                html += `<tr>
                    <td style="padding: 10px; text-align: center;">${article.id}</td>
                    <td style="padding: 10px;">
                        <b>${article.judul}</b>
                        <p style="margin: 5px 0 0 0;"><small>${ringkasan}...</small></p>
                    </td>
                    <td style="padding: 10px; text-align: center;"><span class="badge">${kategori}</span></td>
                    <td style="padding: 10px; text-align: center;">${status}</td>
                    <td style="padding: 10px; text-align: center;">
                        <a class="btn btn-sm btn-info" href="/admin/artikel/edit/${article.id}">Ubah</a>
                        <a class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data?');" href="/admin/artikel/delete/${article.id}" style="color: red; margin-left: 10px;">Hapus</a>
                    </td>
                </tr>`;
            });
        } else {
            html += '<tr><td colspan="5" style="text-align:center; padding: 20px;">Belum ada data artikel.</td></tr>';
        }
        articleContainer.html(html);
    };

    // Fungsi render pagination link
    const renderPagination = (pager, q, kategori_id) => {
        if (!pager || !pager.links || pager.links.length === 0) {
            paginationContainer.html('');
            return;
        }

        let html = '<nav><ul class="pagination" style="display:flex; list-style:none; padding-left:0; gap:5px;">';
        pager.links.forEach(link => {
            // TUGAS 4: Sertakan parameter sort_by & sort_order ke link pagination agar sorting tidak reset saat pindah halaman
            let url = link.url ? `${link.url}&q=${q}&kategori_id=${kategori_id}&sort_by=${currentSortBy}&sort_order=${currentSortOrder}` : '#';
            let activeStyle = link.active ? 'background-color: #007bff; color: white; border-color: #007bff;' : 'background-color: white; color: #007bff; border: 1px solid #ddd;';
            
            html += `<li class="page-item">
                <a class="page-link" href="${url}" style="padding: 8px 12px; text-decoration: none; border-radius: 4px; ${activeStyle}">${link.title}</a>
            </li>`;
        });
        html += '</ul></nav>';
        paginationContainer.html(html);
    };

    // TUGAS 4: Fungsi untuk memperbarui indikator panah sorting pada TH tabel
    const updateSortIcons = () => {
        $('.th-sort').each(function() {
            const col = $(this).data('column');
            if (col === currentSortBy) {
                $(this).find('span').text(currentSortOrder === 'asc' ? '▲' : '▼');
            } else {
                $(this).find('span').text('↕');
            }
        });
    };

    // TUGAS 4: Event Klik Header Kolom untuk Sorting
    $('.th-sort').on('click', function() {
        const column = $(this).data('column');
        // Jika kolom yang diklik sama, balikkan urutannya (asc <-> desc)
        let order = 'asc';
        if (currentSortBy === column && currentSortOrder === 'asc') {
            order = 'desc';
        }
        
        const q = searchBox.val();
        const kategori_id = categoryFilter.val();
        fetchData(`/admin/artikel?q=${q}&kategori_id=${kategori_id}&sort_by=${column}&sort_order=${order}`);
    });

    // Event handler: Submit Form Cari
    searchForm.on('submit', function(e) {
        e.preventDefault();
        const q = searchBox.val();
        const kategori_id = categoryFilter.val();
        // Saat cari baru, sertakan kondisi sorting yang sedang aktif
        fetchData(`/admin/artikel?q=${q}&kategori_id=${kategori_id}&sort_by=${currentSortBy}&sort_order=${currentSortOrder}`);
    });

    // Event handler: Dropdown Kategori
    categoryFilter.on('change', function() {
        searchForm.trigger('submit');
    });

    // Event handler: Klik Pagination
    paginationContainer.on('click', '.page-link', function(e) {
        e.preventDefault();
        let targetUrl = $(this).attr('href');
        if (targetUrl && targetUrl !== '#') {
            fetchData(targetUrl);
        }
    });

    // Jalankan pemuatan awal (Gunakan sorting default)
    fetchData(`/admin/artikel?sort_by=${currentSortBy}&sort_order=${currentSortOrder}`);
});
</script>

<?= $this->include('template/admin_footer'); ?>