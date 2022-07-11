<template>
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-smile"></i>
                </div>
                <div class="sidebar-brand-text mx-3">DODOLAN HAPE</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="/dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Barang
            </div>

            <li class="nav-item">
                <a class="nav-link" href="/barang">
                    <i class="fas fa-fw fa-mobile-alt"></i>
                    <span>Barang</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/tambah_barang">
                    <i class="fas fa-fw fa-pen-alt"></i>
                    <span>Tambah Barang</span></a>
            </li>
            <!-- Nav Item - Pages Collapse Menu -->

            <!-- Nav Item - Utilities Collapse Menu -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Penjualan Offline
            </div>
            <li class="nav-item">
                <a class="nav-link" href="/tambah_penjualan_offline">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Tambah Penjualan Offline</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/penjualan_offline">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Daftar Penjualan Offline</span></a>
            </li>
            
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Penjualan
            </div>
            <li class="nav-item">
                <a class="nav-link" href="/penjualan_sudah_dibayar">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Sudah Dibayar</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/penjualan_sedang_diproses">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Sedang Diproses</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/penjualan_sedang_dikirim">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Sedang Dikirim</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/penjualan_terkirim">
                    <i class="fas fa-fw fa-check"></i>
                    <span>Selesai</span></a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                Komplain
            </div>
            <li class="nav-item">
                <a class="nav-link" href="/komplain">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Komplain</span></a>
            </li>
            <!-- Nav Item - Pages Collapse Menu -->
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                Stok
            </div>
            <li class="nav-item">
                <a class="nav-link" href="/tambah_stok">
                    <i class="fas fa-fw fa-pen-alt"></i>
                    <span>Tambah Stok</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/stok">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Stok</span></a>
            </li>
            <!-- Nav Item - Charts -->
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                Voucher
            </div>
            <li class="nav-item">
                <a class="nav-link" href="/tambah_voucher">
                    <i class="fas fa-fw fa-pen-alt"></i>
                    <span>Tambah Voucher</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/voucher">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Daftar Voucher</span></a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                Banner
            </div>
            <li class="nav-item">
                <a class="nav-link" href="/tambah_banner">
                    <i class="fas fa-fw fa-pen-alt"></i>
                    <span>Tambah Banner</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/banner">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Daftar Banner</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
</template>