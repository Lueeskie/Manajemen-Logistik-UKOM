<head>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

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
                            <?php 
                            $foto = $_SESSION['foto_profil'] ?? null;
                            $default = BASE_URL . 'assets/default-avatar.png';
                            $fotoPath = $foto ? BASE_URL . 'uploads/profile/' . htmlspecialchars($foto) : $default;
                            ?>
                            <?php if ($foto): ?>
                                <img src="<?= $fotoPath ?>" 
                                     alt="<?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>" 
                                     class="avatar-img rounded-circle"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <i class="bi bi-person-fill d-none"></i>
                            <?php else: ?>
                                <i class="bi bi-person-fill"></i>
                            <?php endif; ?>
                        </div>
                        <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>profile">Profil</a></li>
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-menu">
            <?php 
            $role = $_SESSION['role'] ?? '';
            $isAdmin = $role === 'Admin';
            $isSupplier = $role === 'Supplier';
            $isDriver = $role === 'Driver';
            $isCustomer = $role === 'Customer';
            ?>
            <a href="<?= BASE_URL ?>dashboard" class="sidebar-item <?= ($active_page ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>Dashboard
            </a>

            <?php if ($isAdmin || $isDriver || $isCustomer): ?>
            <a href="<?= BASE_URL ?>pengiriman" class="sidebar-item <?= ($active_page ?? '') === 'pengiriman' ? 'active' : '' ?>">
                <i class="bi bi-box-seam"></i>Pengiriman
            </a>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
            <a href="<?= BASE_URL ?>inventaris_barang" class="sidebar-item <?= ($active_page ?? '') === 'inventaris' ? 'active' : '' ?>">
                <i class="bi bi-boxes"></i>Inventaris Barang
            </a>
            <?php endif; ?>

            <?php if ($isAdmin || $isSupplier): ?>
            <a href="<?= BASE_URL ?>supplier" class="sidebar-item <?= ($active_page ?? '') === 'supplier' ? 'active' : '' ?>">
                <i class="fa-solid fa-warehouse"></i>Supplier
            </a>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
            <a href="<?= BASE_URL ?>pengguna" class="sidebar-item <?= ($active_page ?? '') === 'pengguna' ? 'active' : '' ?>">
                <i class="bi bi-people"></i>Pengguna
            </a>
            <?php endif; ?>
        </div>
    </aside>