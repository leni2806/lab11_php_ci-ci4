<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Portal Artikel</title>
    <link rel="stylesheet" href="<?= base_url('/style.css');?>">
</head>
<body>
    <div id="login-wrapper">
        <div class="login-box">
            <h1>Sign In</h1>

            <?php if(session()->getFlashdata('flash_msg')):?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('flash_msg') ?>
                </div>
            <?php endif;?>

            <form action="" method="post">
                <div class="mb-3">
                    <label for="InputForEmail" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="InputForEmail" value="<?= set_value('email') ?>" placeholder="Masukkan email kamu" required>
                </div>

                <div class="mb-3">
                    <label for="InputForPassword" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="InputForPassword" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <p class="footer-text"><a href="<?= base_url('/'); ?>"> Kembali ke Home</a></p>
        </div>
    </div>
</body>
</html>