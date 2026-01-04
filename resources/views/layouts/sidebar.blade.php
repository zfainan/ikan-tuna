<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-center align-items-center">
                <div class="logo">
                    <a href="/admin">
                        <img src="{{ asset('img/Logo.png') }}" alt="Logo" srcset=""
                            style="object-fit: contain; height: 200px; width: 200px;">
                    </a>
                </div>

                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu" style="margin-top: -30px;">
                <li class="sidebar-title">Menu</li>
                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                    <li class="sidebar-item {{ Request::is('admin') ? 'active' : '' }}">
                        <a href="/admin" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @else
                    <li class="sidebar-item {{ Request::is('karyawan') ? 'active' : '' }}">
                        <a href="/karyawan" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1)
                    <li
                        class="sidebar-item has-sub {{ Request::is('akun') || Request::is('suppliers') || Request::is('grade') || Request::is('kategori_berat_penerimaan') || Request::is('gradel') || Request::is('grade_service') || Request::is('grade_hservice') || Request::is('kategori-byproduk-ct') || Request::is('kategori-produk') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-database-fill"></i>
                            <span>Data Master</span>
                        </a>

                        <ul class="submenu">
                            <li class="submenu-item {{ Request::is('akun') ? 'active' : '' }}">
                                <a href="/akun" class="submenu-link">Account</a>
                            </li>

                            <li class="submenu-item {{ Request::is('suppliers') ? 'active' : '' }}">
                                <a href="/suppliers" class="submenu-link">Supplier</a>
                            </li>

                            <li class="submenu-item {{ Request::is('grade') ? 'active' : '' }}">
                                <a href="/grade" class="submenu-link">Grading Penerimaan</a>
                            </li>

                            <li class="submenu-item {{ Request::is('kategori_berat_penerimaan') ? 'active' : '' }}">
                                <a href="/kategori_berat_penerimaan" class="submenu-link">Sizing Penerimaan</a>
                            </li>

                            <li class="submenu-item {{ Request::is('gradel') ? 'active' : '' }}">
                                <a href="/gradel" class="submenu-link">Grade/Sizing Loin</a>
                            </li>

                            <li class="submenu-item {{ Request::is('grade_service') ? 'active' : '' }}">
                                <a href="/grade_service" class="submenu-link">Grading RM Service</a>
                            </li>

                            <li class="submenu-item {{ Request::is('grade_hservice') ? 'active' : '' }}">
                                <a href="/grade_hservice" class="submenu-link">Grade/Sizing Service</a>
                            </li>

                            <li class="submenu-item {{ Request::is('kategori-byproduk-ct') ? 'active' : '' }}">
                                <a href="/kategori-byproduk-ct" class="submenu-link">By Produk</a>
                            </li>

                            <li class="submenu-item {{ Request::is('kategori-produk') ? 'active' : '' }}">
                                <a href="/kategori-produk" class="submenu-link">Produk</a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li
                    class="sidebar-item has-sub {{ Request::is('penerimaan_ikan') || Request::is('cutting') || Request::is('cuttingl') || Request::is('servicel') || Request::is('service') || Request::is('packings') || Request::is('stock') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-stack"></i>
                        <span>Proses Produksi</span>
                    </a>

                    <ul class="submenu">
                        <li class="submenu-item {{ Request::is('penerimaan_ikan') ? 'active' : '' }}">
                            <a href="/penerimaan_ikan" class="submenu-link">Ikan Masuk</a>
                        </li>

                        <li class="submenu-item {{ Request::is('cutting') ? 'active' : '' }}">
                            <a href="/cutting" class="submenu-link">Cutting by Produk</a>
                        </li>

                        <li class="submenu-item {{ Request::is('cuttingl') ? 'active' : '' }}">
                            <a href="/cuttingl" class="submenu-link">Cutting by Loin RM</a>
                        </li>

                        <li class="submenu-item {{ Request::is('servicel') ? 'active' : '' }}">
                            <a href="/servicel" class="submenu-link">Service by Loin CCM</a>
                        </li>

                        <li class="submenu-item {{ Request::is('service') ? 'active' : '' }}">
                            <a href="/service" class="submenu-link">Service by Produk</a>
                        </li>

                        <li class="submenu-item {{ Request::is('packings') ? 'active' : '' }}">
                            <a href="/packings" class="submenu-link">Packing</a>
                        </li>

                        <li class="submenu-item {{ Request::is('stock') ? 'active' : '' }}">
                            <a href="/stock" class="submenu-link">Stock</a>
                        </li>
                    </ul>
                </li>

                @if (Auth::user()->role_id == 1)
                    <li class="sidebar-item has-sub {{ Request::is('reports/penerimaan-ikan') || Request::is('reports/packing') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-collection-fill"></i>
                            <span>Laporan</span>
                        </a>

                        <ul class="submenu">
                            <li class="submenu-item {{ Request::is('reports/penerimaan-ikan') ? 'active' : '' }}">
                                <a href="/reports/penerimaan-ikan" class="submenu-link">Ikan Masuk</a>
                            </li>

                            <li class="submenu-item {{ Request::is('reports/packing') ? 'active' : '' }}">
                                <a href="/reports/packing" class="submenu-link">Packing</a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="sidebar-item">
                    <a href="/logout" class='sidebar-link'>
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
