<aside class="left-sidebar with-vertical">
      <div><!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="../main/index.html" class="text-nowrap logo-img">
            <img src="{{ asset('') }}logo.png" width="150" class="dark-logo" alt="Logo-Dark" />
            <img src="{{ asset('') }}logo.png" width="150" class="light-logo" alt="Logo-light" />
          </a>
          <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
            <i class="ti ti-x"></i>
          </a>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
          <ul id="sidebarnav">
            <!-- ---------------------------------- -->
            <!-- Home -->
            <!-- ---------------------------------- -->
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Apps</span>
            </li>

            <!-- ---------------------------------- -->
            <!-- Dashboard -->
            <!-- ---------------------------------- -->
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('brands.*') ? 'active' : '' }}" href="{{ route('brands.index') }}" aria-expanded="false" onclick="document.location.href='{{ route('home') }}'">
                <span>
                  <i class="ti ti-clipboard-list"></i>
                </span>
                <span class="hide-menu">Brand</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('campaigns.*') ? 'active' : '' }}" href="{{ route('campaigns.index') }}"  aria-expanded="false">
                <span>
                  <i class="ti ti-brand-campaignmonitor"></i>
                </span>
                <span class="hide-menu">Campaign</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('blasts.*') ? 'active' : '' }}" href="{{ route('blasts.index') }}"  aria-expanded="false">
                <span>
                  <i class="ti ti-brand-whatsapp"></i>
                </span>
                <span class="hide-menu">Blast Pesan</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('insights.*') ? 'active' : '' }}" href="{{ route('insights.index') }}"  aria-expanded="false">
                <span>
                  <i class="ti ti-chart-bar"></i>
                </span>
                <span class="hide-menu">Insight Sosmed</span>
              </a>
            </li>

            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Pengaturan</span>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link {{ request()->routeIs('settings.message') ? 'active' : '' }}" href="{{ route('settings.message') }}"  aria-expanded="false">
                <span>
                  <i class="ti ti-mail"></i>
                </span>
                <span class="hide-menu">Pengaturan Pesan</span>
              </a>
            </li>
          </ul>
        </nav>

        <!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
      </div>
    </aside>
