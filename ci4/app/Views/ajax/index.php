<?= $this->include('template/admin_header'); ?> 

<div style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Data Artikel (AJAX)</h1> 
        <button id="btnTambah" class="btn btn-primary" style="width: auto; height: fit-content;">+ Tambah Artikel</button>
    </div>
     
    <table class="table-data" id="artikelTable" border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse: collapse;"> 
        <thead> 
            <tr style="background-color: #f2f2f2;"> 
                <th>ID</th> 
                <th>Judul</th> 
                <th>Status</th> 
                <th>Aksi</th> 
            </tr> 
        </thead> 
        <tbody>
            </tbody> 
    </table> 
</div>

<div id="modalArtikel" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999;">
    <div style="background:#fff; width:450px; margin:50px auto; padding:25px; border-radius:8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h2 id="modalTitle" style="margin-top:0;">Form Artikel</h2>
        <hr>
        <form id="formArtikel">
            <input type="hidden" name="id" id="artikelId">
            
            <div style="margin: 15px 0;">
                <label style="font-weight:bold;">Judul Artikel</label><br>
                <input type="text" name="judul" id="judul" required style="width:100%; padding:8px; margin-top:5px; box-sizing: border-box;">
            </div>

            <div style="margin: 15px 0;">
                <label style="font-weight:bold;">Isi Artikel</label><br>
                <textarea name="isi" id="isi" required style="width:100%; height:120px; padding:8px; margin-top:5px; box-sizing: border-box;"></textarea>
            </div>

            <div style="text-align:right; margin-top:20px;">
                <button type="button" id="btnBatal" class="btn btn-danger" style="width: auto;">Batal</button>
                <button type="submit" class="btn btn-primary" style="width: auto;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script> 

<script> 
    $(document).ready(function() { 
        
        // 1. Fungsi menampilkan pesan loading [cite: 98, 99]
        function showLoadingMessage() { 
            $('#artikelTable tbody').html('<tr><td colspan="4" style="text-align:center;">Sedang memuat data...</td></tr>'); 
        } 
 
        // 2. Fungsi memanggil data dari AjaxController [cite: 61, 107, 115]
        function loadData() { 
            showLoadingMessage(); 
            $.ajax({ 
                url: "<?= base_url('admin/ajax/getData') ?>", 
                method: "GET", 
                dataType: "json", 
                success: function(data) { 
                    var tableBody = ""; 
                    if (data.length === 0) {
                        tableBody = '<tr><td colspan="4" style="text-align:center;">Tidak ada data artikel.</td></tr>';
                    } else {
                        for (var i = 0; i < data.length; i++) { 
                            var row = data[i]; 
                            var idArtikel = row.id ? row.id : row.id_artikel; 

                            tableBody += '<tr>'; 
                            tableBody += '<td>' + idArtikel + '</td>'; 
                            tableBody += '<td>' + row.judul + '</td>'; 
                            tableBody += '<td><span style="color: green; font-weight: bold;">Aktif</span></td>'; 
                            tableBody += '<td>'; 
                            // Link Edit diubah menjadi tombol AJAX
                            tableBody += '<button class="btn btn-primary btn-edit" data-id="' + idArtikel + '" style="margin-right: 5px; width: auto;">Edit</button>'; 
                            tableBody += '<button class="btn btn-danger btn-delete" data-id="' + idArtikel + '" style="width: auto;">Delete</button>'; 
                            tableBody += '</td>'; 
                            tableBody += '</tr>'; 
                        } 
                    }
                    $('#artikelTable tbody').html(tableBody); 
                },
                error: function() {
                    $('#artikelTable tbody').html('<tr><td colspan="4" style="text-align:center; color:red;">Gagal memuat data! Periksa koneksi atau URL.</td></tr>');
                }
            }); 
        } 
 
        loadData(); 

        // 3. Aksi Tombol Tambah (Reset Form & Buka Modal)
        $('#btnTambah').click(function() {
            $('#formArtikel')[0].reset();
            $('#artikelId').val('');
            $('#modalTitle').text('Tambah Artikel Baru');
            $('#modalArtikel').fadeIn();
        });

        // 4. Aksi Tombol Edit (Ambil Data via AJAX & Isi Form)
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "<?= base_url('admin/ajax/getById') ?>/" + id,
                method: "GET",
                dataType: "json",
                success: function(row) {
                    $('#artikelId').val(row.id ? row.id : row.id_artikel);
                    $('#judul').val(row.judul);
                    $('#isi').val(row.isi);
                    $('#modalTitle').text('Edit Artikel #' + id);
                    $('#modalArtikel').fadeIn();
                }
            });
        });

        // 5. Simpan Data (Tambah/Update) via POST AJAX
        $('#formArtikel').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= base_url('admin/ajax/save') ?>",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.status === 'OK') {
                        alert('Data berhasil disimpan!');
                        $('#modalArtikel').fadeOut();
                        loadData();
                    }
                }
            });
        });

        // 6. Tombol Batal Modal
        $('#btnBatal').click(function() { $('#modalArtikel').fadeOut(); });
 
        // 7. Logika Hapus Data sesuai Modul [cite: 141, 146, 150]
        $(document).on('click', '.btn-delete', function(e) { 
            e.preventDefault(); 
            var id = $(this).data('id'); 
            if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) { 
                $.ajax({ 
                    url: "<?= base_url('admin/ajax/delete') ?>/" + id, 
                    method: "POST", 
                    dataType: "json",
                    success: function(response) { 
                        if (response.status === 'OK') {
                            alert('Artikel berhasil dihapus!');
                            loadData(); 
                        }
                    }, 
                    error: function(jqXHR, textStatus) { 
                        alert('Gagal menghapus artikel: ' + textStatus); 
                    } 
                }); 
            } 
        }); 
    }); 
</script> 
 
<?= $this->include('template/admin_footer'); ?>