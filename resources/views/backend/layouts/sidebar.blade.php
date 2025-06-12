<div class="preloader flex-column justify-content-center align-items-center">
  <!-- Preloader -->
  <img class="animation__shake" src="{{ url('backend/dist/img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
</div>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Navbar Search -->
    <li class="nav-item">
      <a class="nav-link" href="{{ url('logout')}}">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </li>
  </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index3.html" class="brand-link">
    <img src="{{ url('backend/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">WMS</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{ url('backend/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block">{{ Auth::user()->name}}</a>
      </div>
    </div>

    <!-- SidebarSearch Form -->
    <div class="form-inline">
      <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="fas fa-search fa-fw"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{ url('admin/dashboard')}}" class="nav-link @if(Request::segment(2) == 'dashboard') active @endif">
            <i class="nav-icon fa fa-home"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>
      </ul>
    </nav>

    <!-- Items Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item {{ Request::is('admin/items/*') ? 'menu-open' : '' }}">
          <a href="#itemSubMenu" data-toggle="collapse" aria-expanded="{{ Request::is('admin/items/*') ? 'true' : 'false' }}" class="nav-link">
            <i class="nav-icon fa fa-briefcase"></i>
            <p>
              Items
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="collapse list-unstyled {{ Request::is('admin/items/*') ? 'show' : '' }} itemSubMenu" id="itemSubMenu">
            <li class="nav-item">
              <a href="{{ url('admin/items/additem') }}" class="nav-link @if (Request::is('admin/items/additem')) active @endif">
                <p>Add/Upload Items</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('admin/items/list') }}" class="nav-link @if (Request::is('admin/items/list')) active @endif">
                <p>List Items</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('admin/items/barcode') }}" class="nav-link @if (Request::is('admin/items/barcode')) active @endif">
                <p>Barcode Print</p>
              </a>
            </li>
          </ul>
        </li>
          
      </ul>
    </nav>
    
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{ url('admin/employees')}}" class="nav-link @if (Request::segment(2) == 'employees')
             active @endif">
            <i class="nav-icon fa fa-users"></i>
            <p>
              Employees
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{ url('admin/purchasing')}}" class="nav-link @if (Request::segment(2) == 'purchasing') active @endif">
            <i class="nav-icon fa fa-shopping-cart"></i>
            <p>
              Purchasing
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{ url('admin/production')}}" class="nav-link @if (Request::segment(2) == 'production') active @endif">
            <i class="nav-icon fa fa-cogs"></i>
            <p>
              Production
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{ url('admin/stock')}}" class="nav-link @if (Request::segment(2) == 'stock') active @endif"">
              <i class=" nav-icon fa fa-recycle"></i>
            <p>
              Stock
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item {{ Request::is('admin/transaction/*') ? 'menu-open' : '' }}">
          <a href="#transactionSubMenu" data-toggle="collapse" aria-expanded="{{ Request::is('admin/transaction/*') ? 'true' : 'false' }}" class="nav-link">
            <i class="nav-icon fa fa-file-contract"></i>
            <p>
              Transactions
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="collapse list-unstyled {{ Request::is('admin/transaction/*') ? 'show' : ''}} itemSubMenu" id="transactionSubMenu">
            <li class="nav-item">
              <a href="{{ url('admin/transaction/stockin')}}" class="nav-link @if (Request::is('admin/transaction/stockin')) active @endif">
                <p>Stock In</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('admin/transaction/stockout')}}" class="nav-link @if (Request::is('admin/transaction/stockout')) active @endif">
                <p>Stock Out</p>
              </a>
            </li>
          </ul>
          
        </li>
      </ul>
    </nav>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{ url('admin/countries')}}" class="nav-link">
            <i class="nav-icon fa fa-flag"></i>
            <p>
              Countries
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{ url('admin/locations')}}" class="nav-link">
            <i class="nav-icon fa fa-map-marker-alt"></i>
            <p>
              Locations
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{ url('admin/regions')}}" class="nav-link">
            <i class="nav-icon fa fa-asterisk"></i>
            <p>
              Regions
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>