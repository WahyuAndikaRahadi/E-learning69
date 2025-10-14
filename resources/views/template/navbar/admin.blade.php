<!-- BEGIN SIDEBAR -->
<div class="sidebar-wrapper sidebar-theme">
    <nav id="sidebar">
        <div class="profile-info">
            <figure class="user-cover-image" style="background: #fafafa"></figure>
            <div class="user-info">
                <img src="{{ asset('assets/user-profile/' . $admin->avatar) }}" alt="avatar" class="bg-white">
                <h6>{{ $admin->nama_admin }}</h6>
                <p>Admin e-Learning 69</p>
            </div>
        </div>

        <div class="shadow-bottom"></div>

        <ul class="list-unstyled menu-categories" id="accordionExample">

            <!-- DASHBOARD -->
            <li class="menu {{ ($menu['menu'] == 'dashboard') ? 'active' : '' }}">
                <a href="{{ url('/admin') }}"
                    aria-expanded="{{ ($menu['expanded'] == 'dashboard') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div>
                        <span data-feather="airplay"></span>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>

            <!-- HEADER: MASTER DATA -->
            <li class="menu menu-heading">
                <div class="heading">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-minus">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>MASTER DATA</span>
                </div>
            </li>

            <!-- MASTER: USER (Siswa & Guru) -->
            <li class="menu {{ ($menu['menu'] == 'master') ? 'active' : '' }}">
                <a href="#master-user" data-toggle="collapse"
                    aria-expanded="{{ ($menu['expanded'] == 'master') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div>
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                            <path d="M17 21v-2a4 4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>User</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ ($menu['collapse'] == 'master') ? 'show' : '' }}"
                    id="master-user" data-parent="#accordionExample">
                    <li class="{{ ($menu['sub'] == 'siswa') ? 'active' : '' }}">
                        <a href="{{ url('/admin/siswa') }}">Siswa</a>
                    </li>
                    <li class="{{ ($menu['sub'] == 'guru') ? 'active' : '' }}">
                        <a href="{{ url('/admin/guru') }}">Guru</a>
                    </li>
                </ul>
            </li>

            <!-- MASTER: KELAS -->
            <li class="menu {{ ($menu['menu'] == 'kelas') ? 'active' : '' }}">
                <a href="{{ url('/admin/kelas') }}"
                    aria-expanded="{{ ($menu['expanded'] == 'kelas') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div>
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span>Kelas</span>
                    </div>
                </a>
            </li>

            <!-- MASTER: MAPEL -->
            <li class="menu {{ ($menu['menu'] == 'mapel') ? 'active' : '' }}">
                <a href="{{ url('/admin/mapel') }}"
                    aria-expanded="{{ ($menu['expanded'] == 'mapel') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div>
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <span>Mapel</span>
                    </div>
                </a>
            </li>

            <!-- MASTER: RELASI -->
            <li class="menu {{ ($menu['menu'] == 'relasi') ? 'active' : '' }}">
                <a href="{{ url('/admin/relasi') }}"
                    aria-expanded="{{ ($menu['expanded'] == 'relasi') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div>
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                            <line x1="6" y1="3" x2="6" y2="15"></line>
                            <circle cx="18" cy="6" r="3"></circle>
                            <circle cx="6" cy="18" r="3"></circle>
                            <path d="M18 9a9 9 0 0 1-9 9"></path>
                        </svg>
                        <span>Relasi</span>
                    </div>
                </a>
            </li>

            <!-- HEADER: USER MENU -->
            <li class="menu menu-heading">
                <div class="heading">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-minus">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>USER MENU</span>
                </div>
            </li>

            <li class="menu {{ ($menu['menu'] == 'password-requests') ? 'active' : '' }}">
                <a href="{{ url('/admin/password-requests') }}"
                    aria-expanded="{{ ($menu['expanded'] == 'password-requests') ? 'true' : 'false' }}"
                    class="dropdown-toggle">
                    <div>
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Ganti Password</span>
                    </div>
                </a>
            </li>




            <!-- USER MENU: PROFILE -->
            <li class="menu {{ ($menu['menu'] == 'profile') ? 'active' : '' }}">
                <a href="{{ url('/admin/profile') }}"
                    aria-expanded="{{ ($menu['expanded'] == 'profile') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div>
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Profile</span>
                    </div>
                </a>
            </li>

        </ul>
    </nav>
</div>
<!-- END SIDEBAR -->