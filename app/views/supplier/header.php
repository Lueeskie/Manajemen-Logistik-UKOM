<nav class="navbar navbar-expand-lg bg-white navbar-dashboard">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>dashboard">
                <img src="<?= BASE_URL ?>assets/logo2.png" alt="Astral Express" class="brand-logo">
                <span class="brand-text">AstralExpress</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Supplier'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>profile"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-menu">
                <a href="<?= BASE_URL ?>dashboard" class="sidebar-item">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="bi bi-box-seam"></i>
                    <span>Pengiriman</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="bi bi-boxes"></i>
                    <span>Inventaris Barang</span>
                </a>
                <a href="<?= BASE_URL ?>supplier" class="sidebar-item active">
                    <i class="fa-solid fa-warehouse"></i>
                    <span>Supplier</span>   
                </a>
                <a href="#" class="sidebar-item">
                    <i class="bi bi-people"></i>
                    <span>Pengguna</span>
                </a>
            </div>
        </aside>