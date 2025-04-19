<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Curtaincall</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="public/css/admin.css" rel="stylesheet">
    <link rel="shortcut icon" href="public/images/favicon.ico" type="image/x-icon">
</head>
<body class="admin-login-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header text-center text-white">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <img src="public/images/logo.png" alt="CurtainCall" height="50" class="me-2">
                            <h3 class="my-0">CurtainCall Admin</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['admin_login_error'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['admin_login_error']; unset($_SESSION['admin_login_error']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?= BASE_URL ?>index.php?route=admin/login">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <a href="<?= BASE_URL ?>" class="text-decoration-none">← Return to website</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>