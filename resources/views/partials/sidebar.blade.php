<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <div class="sidebar-brand d-flex align-items-center justify-content-center">
        <div class="sidebar-brand-text mx-3">SPK </div>
    </div>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.admin') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Kriteria -->
    <li class="nav-item {{ request()->routeIs('kriteria.index.admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kriteria.index.admin') }}">
            <i class="fas fa-fw fa-database"></i>
            <span>Kelola Kriteria</span>
        </a>
    </li>

    <!-- Alternatif -->
    <li class="nav-item {{ request()->routeIs('alternatif.index.admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('alternatif.index.admin') }}">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>Kelola Alternatif</span>
        </a>
    </li>

    <!-- Perbandingan Kriteria -->
    <li class="nav-item {{ request()->routeIs('perbandingan-kriteria.index.admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{  route('perbandingan-kriteria.index.admin') }}">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Perbandingan Kriteria</span>
        </a>
    </li>

    <!-- Hasil Perbandingan Kriteria -->
    <li class="nav-item {{ request()->routeIs('hasil-perbandingan-kriteria.index.admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('hasil-perbandingan-kriteria.index.admin') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Hasil Perbandingan Kriteria</span>
        </a>
    </li>

    <!-- Perbandingan Alternatif -->
    <li class="nav-item {{ request()->routeIs('perbandingan-alternatif.index.admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('perbandingan-alternatif.index.admin') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Perbandingan Alternatif</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('hasil-perbandingan-alternatif.index.admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('hasil-perbandingan-alternatif.index.admin') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Hasil Perbandingan Alternatif</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('ranking-akhir.index.admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('ranking-akhir.index.admin') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Ranking Akhir</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->