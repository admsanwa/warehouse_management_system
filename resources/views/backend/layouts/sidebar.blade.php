<div class="preloader flex-column justify-content-center align-items-center">
    <!-- Preloader -->
    <img class="animation__shake" src="{{ url('backend/dist/img/AdminLTELogo.png') }}" alt="AdminLTELogo" height="60"
        width="60">
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
            <a class="nav-link" href="{{ url('logout') }}">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('admin/dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/images/logo/bullet-logo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">WMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ url('backend/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->fullname }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search"
                    id="sidebarSearchInput">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        @php
            $authLevel = Auth::user()->level;
            $authDept = Auth::user()->department;
            $authNik = Auth::user()->nik;
            $authEmail = Auth::user()->email;
        @endphp

        <!-- Sidebar Menu -->
        {{-- Dashboard --}}
        @if (
            $authDept == 'IT' ||
                ($authDept == 'Production and Warehouse' ||
                    $authLevel == 'Manager' ||
                    $authLevel == 'Supervisor' ||
                    $authLevel == 'Leader') ||
                $authDept == 'PPIC' ||
                $authDept == 'Purchasing' ||
                $authDept == 'Production' ||
                ($authDept == 'Quality Control' && $authLevel != 'Operator' && $authLevel != 'Magang') ||
                $authDept == 'Procurement, Installation and Delivery')

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <a href="{{ url('admin/dashboard') }}"
                            class="nav-link @if (Request::segment(2) == 'dashboard') active @endif">
                            <i class="nav-icon fa fa-home"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                        @if (
                            $authDept == 'IT' ||
                                ($authDept == 'Production and Warehouse' && $authLevel == 'Manager') ||
                                ($authDept == 'Production and Warehouse' && $authLevel == 'Supervisor') ||
                                $authDept == 'PPIC' ||
                                $authDept == 'Production' ||
                                ($authDept == 'Quality Control' && $authLevel != 'Operator' && $authLevel != 'Magang') ||
                                $authDept == 'Procurement, Installation and Delivery')
                    <li class="nav-item">
                        <a href="{{ url('admin/dashboard-list') }}"
                            class="nav-link @if (Request::segment(2) == 'dashboard-list') active @endif">
                            <i class="nav-icon fa fa-list"></i>
                            <p>
                                Dashboard Plan List
                            </p>
                        </a>
                    </li>
        @endif
        </ul>
        </nav>
        @endif

        {{-- Items --}}
        @if (
            $authDept == 'IT' ||
                $authDept == 'Production and Warehouse' ||
                $authDept == 'Fabrication' ||
                $authDept == 'PPIC' ||
                $authDept == 'Purchasing' ||
                ($authDept == 'Procurement, Installation and Delivery' && $authLevel == 'Manager') ||
                ($authDept == 'Quality Control' && $authLevel == 'Manager'))
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <li class="nav-item {{ Request::is('admin/items/*') ? 'menu-open' : '' }}">
                        <a href="#itemSubMenu" data-toggle="collapse"
                            aria-expanded="{{ Request::is('admin/items/*') ? 'true' : 'false' }}" class="nav-link">
                            <i class="nav-icon fa fa-briefcase"></i>
                            <p>
                                Items
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('admin/items/*') ? 'show' : '' }} itemSubMenu"
                            id="itemSubMenu">
                            @if ($authDept == 'IT')
                                <li class="nav-item">
                                    <a href="{{ url('admin/items/additem') }}"
                                        class="nav-link @if (Request::is('admin/items/additem')) active @endif">
                                        <p>Add/Upload Items</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    $authDept == 'PPIC' ||
                                    $authDept == 'Purchasing' ||
                                    $authDept == 'Production and Warehouse' ||
                                    $authDept == 'Fabrication' ||
                                    $authDept == 'Procurement, Installation and Delivery' ||
                                    ($authDept == 'Quality Control' && $authLevel == 'Manager'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/items/list') }}"
                                        class="nav-link @if (Request::is('admin/items/list')) active @endif">
                                        <p>List Items</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    ($authDept == 'Production and Warehouse' && $authLevel != 'Manager' && $authLevel != 'Supervisor'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/items/barcode') }}"
                                        class="nav-link @if (Request::is('admin/items/barcode')) active @endif">
                                        <p>Barcode Print</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </nav>
        @endif

        {{-- Employees --}}
        @if ($authDept == 'IT')
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <a href="{{ url('admin/employees') }}"
                            class="nav-link @if (Request::segment(2) == 'employees') active @endif">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                Employees
                            </p>
                        </a>
                    </li>
                </ul>
            </nav>
        @endif

        {{-- Purchasing --}}
        @if (
            $authDept == 'IT' ||
                $authDept == 'Purchasing' ||
                ($authDept == 'Production and Warehouse' &&
                    $authLevel != 'Manager' &&
                    $authEmail != 'warehouse_sby@sanwamas.co.id'))
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                    <li class="nav-item {{ Request::is('admin/purchasing/*') ? 'menu-open' : '' }}">
                        <a href="#PurchasingSubMenu" data-toggle="collapse"
                            aria-expanded="{{ Request::is('admin/purchasing/*') ? 'true' : 'false' }}"
                            class="nav-link">
                            <i class="nav-icon fa fa-shopping-cart"></i>
                            <p>
                                Purchasing
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="collapse list-unstyled {{ Request::is('admin/purchasing/*') ? 'show' : '' }} itemSubMenu"
                            id="PurchasingSubMenu">
                            <li class="nav-item">
                                <a href="{{ url('admin/purchasing/purchaseorder') }}"
                                    class="nav-link @if (Request::is('admin/purchasing/purchaseorder')) active @endif">
                                    <p>Purchase Order</p>
                                </a>
                            </li>
                            @if ($authDept == 'IT' || $authDept == 'Production and Warehouse')
                                <li class="nav-item">
                                    <a href="{{ url('admin/purchasing/barcode') }}"
                                        class="nav-link @if (Request::is('admin/purchasing/barcode')) active @endif">
                                        <p>Barcode</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </nav>
        @endif

        {{-- Production --}}
        @if (
            $authDept == 'IT' ||
                $authDept == 'Production and Warehouse' ||
                $authDept == 'PPIC' ||
                $authDept == 'Purchasing' ||
                ($authDept == 'Procurement, Installation and Delivery' && $authLevel == 'Manager') ||
                $authDept == 'Production' ||
                $authDept == 'Quality Control')
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                    <li class="nav-item {{ Request::is('admin/production/*') ? 'menu-open' : '' }}">
                        <a href="#prodSubMenu" data-toggle="collapse"
                            aria-expanded="{{ Request::is('admin/production/*') ? 'true' : 'false' }}"
                            class="nav-link">
                            <i class="nav-icon fa fa-cogs"></i>
                            <p>
                                Production
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="collapse list-unstyled {{ Request::is('admin/production/*') ? 'show' : '' }} itemSubMenu"
                            id="prodSubMenu">
                            @if (
                                $authDept == 'PPIC' ||
                                    $authDept == 'IT' ||
                                    $authDept == 'Production' ||
                                    ($authDept == 'Procurement, Installation and Delivery' && $authLevel == 'Manager') ||
                                    $authDept == 'Fabrication' ||
                                    (($authDept == 'Production and Warehouse' && $authLevel === 'Leader') ||
                                        $authEmail == 'warehouse_sby@sanwamas.co.id'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/production/po') }}"
                                        class="nav-link @if (Request::is('admin/production/po')) active @endif">
                                        <p>Production Order</p>
                                    </a>
                                </li>
                            @endif
                            @if ($authDept == 'IT' || $authDept == 'PPIC')
                                <li class="nav-item">
                                    <a href="{{ url('admin/production/bon') }}"
                                        class="nav-link @if (Request::is('admin/production/bon')) active @endif">
                                        <p>Bon Pembelian Barang</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    $authDept == 'PPIC' ||
                                    $authDept == 'Purchasing' ||
                                    $authDept == 'Procurement, Installation and Delivery')
                                <li class="nav-item">
                                    <a href="{{ url('admin/production/listbon') }}"
                                        class="nav-link @if (Request::is('admin/production/listbon')) active @endif">
                                        <p>List Bon</p>
                                    </a>
                                </li>
                            @endif
                            @if ($authDept == 'IT' || $authDept == 'PPIC' || $authDept == 'Fabrication')
                                <li class="nav-item">
                                    <a href="{{ url('admin/production/memo') }}"
                                        class="nav-link @if (Request::is('admin/production/memo')) active @endif">
                                        <p>Memo</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    $authDept == 'PPIC' ||
                                    $authDept == 'Fabrication' ||
                                    $authDept == 'Procurement, Installation and Delivery' ||
                                    $authDept == 'Production' ||
                                    ($authDept == 'Production and Warehouse' && $authEmail != 'warehouse_sby@sanwamas.co.id') ||
                                    $authDept == 'Quality Control' ||
                                    $authDept == 'Purchasing')
                                <li class="nav-item">
                                    <a href="{{ url('admin/production/listmemo') }}"
                                        class="nav-link @if (Request::is('admin/production/listmemo')) active @endif">
                                        <p>List Memo</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    $authDept == 'Production' ||
                                    ($authDept == 'Production and Warehouse' && $authEmail == 'warehouse_sby@sanwamas.co.id'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/production/barcode') }}"
                                        class="nav-link @if (Request::is('admin/production/barcode')) active @endif">
                                        <p>Barcode</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                ($authDept == 'Production and Warehouse' && $authLevel === 'Leader') ||
                                    $authDept == 'IT' ||
                                    $authDept == 'Quality Control')
                                <li class="nav-item">
                                    <a href="{{ url('/listpreparemat') }}"
                                        class="nav-link @if (Request::is('admin/production/listpreparemat')) active @endif">
                                        <p>List Prepare Material</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </nav>
        @endif

        {{-- Stocks --}}
        @if ($authDept == 'IT' || ($authDept == 'Procurement, Installation and Delivery' && $authLevel == 'Manager'))
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                    <li class="nav-item">
                        <a href="{{ url('admin/stock') }}"
                            class="nav-link @if (Request::segment(2) == 'stock') active @endif"">
                            <i class=" nav-icon fa fa-recycle"></i>
                            <p>
                                Stocks
                            </p>
                        </a>
                    </li>
                </ul>
            </nav>
        @endif

        {{-- Inventory Tf --}}
        @if (
            ($authDept == 'Quality Control' && $authLevel != 'Operator' && $authLevel != 'Magang') ||
                $authDept == 'IT' ||
                $authDept == 'Production and Warehouse' ||
                $authDept == 'Production')
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <li class="nav-item {{ Request::is('admin/inventorytf/*') ? 'menu-open' : '' }}">
                        <a href="#inventorytfSubMenu" data-toggle="collapse"
                            aria-expanded="{{ Request::is('admin/inventorytf/*') ? 'true' : 'false' }}"
                            class="nav-link">
                            <i class="nav-icon fa fa-file-import"></i>
                            <p>
                                Inventory Transfer
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('admin/inventorytf/*') ? 'show' : '' }} itemSubMenu"
                            id="inventorytfSubMenu">
                            @if (
                                $authDept == 'IT' ||
                                    ($authDept == 'Quality Control' && $authLevel == 'Staff') ||
                                    ($authDept == 'Production and Warehouse' && $authLevel != 'Manager') ||
                                    ($authDept == 'Production' && $authLevel == 'Staff'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/inventorytf/create') }}"
                                        class="nav-link @if (Request::is('admin/inventorytf/create')) active @endif">
                                        <p>Create</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    $authDept == 'Quality Control' ||
                                    $authDept == 'Production and Warehouse' ||
                                    ($authDept == 'Production' && $authLevel == 'Staff') ||
                                    $authDept == 'PPIC')
                                <li class="nav-item">
                                    <a href="{{ url('admin/inventorytf/list') }}"
                                        class="nav-link @if (Request::is('admin/inventorytf/list')) active @endif">
                                        <p>List</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </nav>
        @endif

        {{-- Transactions --}}
        @if (
            $authDept == 'IT' ||
                ($authDept == 'Production and Warehouse' &&
                    $authLevel != 'Manager' &&
                    $authEmail != 'spv-gudang@sanwamas.co.id') ||
                $authDept == 'Production')

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <li class="nav-item {{ Request::is('admin/transaction/*') ? 'menu-open' : '' }}">
                        <a href="#transactionSubMenu" data-toggle="collapse"
                            aria-expanded="{{ Request::is('admin/transaction/*') ? 'true' : 'false' }}"
                            class="nav-link">
                            <i class="nav-icon fa fa-file-contract"></i>
                            <p>
                                Transactions
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('admin/transaction/*') ? 'show' : '' }} itemSubMenu"
                            id="transactionSubMenu">
                            @if ($authDept == 'IT' || ($authDept == 'Production and Warehouse' && $authEmail != 'warehouse_sby@sanwamas.co.id'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/transaction/stockin') }}"
                                        class="nav-link @if (Request::is('admin/transaction/stockin') || Request::is('admin/transaction/stockin/*')) active @endif">
                                        <p>GRPO</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    $authDept == 'Production' ||
                                    ($authDept == 'Production and Warehouse' && $authEmail == 'warehouse_sby@sanwamas.co.id'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/transaction/stockout') }}"
                                        class="nav-link @if (Request::is('admin/transaction/stockout/*') || Request::is('admin/transaction/stockout')) active @endif">
                                        <p>Issue For Prod</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    $authDept == 'Production' ||
                                    ($authDept == 'Production and Warehouse' && $authEmail == 'warehouse_sby@sanwamas.co.id'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/transaction/rfp') }}"
                                        class="nav-link @if (Request::is(' admin/transaction/rfp')) active @endif">
                                        <p>Receipt From Prod</p>
                                    </a>
                                </li>
                            @endif
                            @if ($authDept == 'IT' || ($authDept == 'Production and Warehouse' && $authEmail != 'warehouse_sby@sanwamas.co.id'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/transaction/goodissued') }}"
                                        class="nav-link @if (Request::is(' admin/transaction/goodissued')) active @endif">
                                        <p>Good Issue</p>
                                    </a>
                                </li>
                            @endif
                            @if ($authDept == 'IT' || ($authDept == 'Production and Warehouse' && $authEmail != 'warehouse_sby@sanwamas.co.id'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/transaction/goodreceipt') }}"
                                        class="nav-link @if (Request::is('admin/transaction/goodreceipt')) active @endif">
                                        <p>Prepare Good Receipt</p>
                                    </a>
                                </li>
                            @endif
                            @if ($authDept == 'IT' || ($authDept == 'Production and Warehouse' && $authNik == '05950'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/transaction/listgoodreceipt') }}"
                                        class="nav-link @if (Request::is('admin/transaction/listgoodreceipt')) active @endif">
                                        <p>Good Receipt</p>
                                    </a>
                                </li>
                            @endif
                        </ul>

                    </li>
                </ul>
            </nav>
        @endif

        {{-- List Transactions --}}
        @if ($authDept == 'IT')
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <li class="nav-item {{ Request::is('admin/list/*') ? 'menu-open' : '' }}">
                        <a href="#listtransactionSubMenu" data-toggle="collapse"
                            aria-expanded="{{ Request::is('admin/list/*') ? 'true' : 'false' }}" class="nav-link">
                            <i class="nav-icon fa fa-file-contract"></i>
                            <p>
                                List Transactions
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="collapse list-unstyled {{ Request::is('admin/listtransaction/*') ? 'show' : '' }} itemSubMenu"
                            id="listtransactionSubMenu">
                            @if ($authDept == 'IT')
                                <li class="nav-item">
                                    <a href="{{ url('admin/listtransaction/stockin') }}"
                                        class="nav-link @if (Request::is('admin/listtransaction/stockin') || Request::is('admin/listtransaction/stockin/*')) active @endif">
                                        <p>List GRPO</p>
                                    </a>
                                </li>
                            @endif
                            @if ($authDept == 'IT')
                                <li class="nav-item">
                                    <a href="{{ url('admin/listtransaction/stockout') }}"
                                        class="nav-link @if (Request::is('admin/listtransaction/stockout/*') || Request::is('admin/listtransaction/stockout')) active @endif">
                                        <p>List Issue For Prod</p>
                                    </a>
                                </li>
                            @endif
                            @if ($authDept == 'IT')
                                <li class="nav-item">
                                    <a href="{{ url('admin/listtransaction/rfp') }}"
                                        class="nav-link @if (Request::is(' admin/listtransaction/rfp')) active @endif">
                                        <p>List Receipt From Prod</p>
                                    </a>
                                </li>
                            @endif
                            @if ($authDept == 'IT')
                                <li class="nav-item">
                                    <a href="{{ url('admin/listtransaction/goodissued') }}"
                                        class="nav-link @if (Request::is(' admin/listtransaction/goodissued')) active @endif">
                                        <p>List Good Issue</p>
                                    </a>
                                </li>
                            @endif
                            @if ($authDept == 'IT')
                                <li class="nav-item">
                                    <a href="{{ url('admin/listtransaction/goodreceipt') }}"
                                        class="nav-link @if (Request::is(' admin/listtransaction/goodreceipt')) active @endif">
                                        <p>List Good Receipt</p>
                                    </a>
                                </li>
                            @endif
                        </ul>

                    </li>
                </ul>
            </nav>
        @endif

        {{-- Quality --}}
        @if (
            $authDept == 'Quality Control' ||
                $authDept == 'IT' ||
                ($authDept == 'Procurement, Installation and Delivery' && $authLevel == 'Manager') ||
                ($authDept == 'Production and Warehouse' &&
                    $authLevel != 'Operator' &&
                    $authLevel != 'Leader' &&
                    $authEmail != 'warehouse_sby@sanwamas.co.id') ||
                $authDept == 'Production')

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <li class="nav-item {{ Request::is('admin/quality/*') ? 'menu-open' : '' }}">
                        <a href="#qualitySubMenu" data-toggle="collapse"
                            aria-expanded="{{ Request::is('admin/quality/*') ? 'true' : 'false' }}" class="nav-link">
                            <i class="nav-icon fa fa-list"></i>
                            <p>
                                Quality Control
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="collapse list-unstyled {{ Request::is('admin/quality/*') ? 'show' : '' }} itemSubMenu"
                            id="qualitySubMenu">
                            @if (
                                $authDept == 'Procurement, Installation and Delivery' ||
                                    $authDept == 'IT' ||
                                    $authDept == 'Quality Control' ||
                                    $authDept == 'Production and Warehouse' ||
                                    $authDept == 'Production' ||
                                    $authDept == 'PPIC')
                                <li class="nav-item">
                                    <a href="{{ url('admin/quality/list') }}"
                                        class="nav-link @if (Request::is('admin/quality/list')) active @endif">
                                        <p>List</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    $authDept == 'Quality Control' ||
                                    $authDept == 'Production and Warehouse' ||
                                    $authDept == 'PPIC' ||
                                    $authDept == 'Production')
                                <li class="nav-item">
                                    <a href="{{ url('admin/quality/history') }}"
                                        class="nav-link @if (Request::is('admin/quality/history')) active @endif">
                                        <p>History</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </nav>
        @endif

        {{-- Reports --}}
        @if (
            $authDept == 'IT' ||
                $authDept == 'Production and Warehouse' ||
                $authDept == 'PPIC' ||
                $authDept == 'Quality Control' ||
                $authDept == 'Procurement, Installation and Delivery' ||
                $authDept == 'Production' ||
                $authDept == 'Purchasing')

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                    <li
                        class="nav-item {{ Request::is('admin/reports*') || Request::is('reports-memo') || Request::is('reports-bon') ? 'menu-open' : '' }}">
                        <a href="#reportSubMenu" data-toggle="collapse"
                            aria-expanded="{{ Request::is('admin/reports/*') }}" class="nav-link">
                            <i class="nav-icon fa fa-clipboard"></i>
                            <p>
                                Reports
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="collapse list-unstyled {{ Request::is('admin/reports/*') ? 'show' : '' }} itemSubMenu"
                            id="reportSubMenu">
                            @if (
                                $authDept == 'Quality Control' ||
                                    $authDept == 'IT' ||
                                    $authDept == 'Procurement, Installation and Delivery' ||
                                    $authDept == 'PPIC' ||
                                    $authDept == 'Production and Warehouse' ||
                                    $authDept == 'Production')
                                <li class="nav-item">
                                    <a href="{{ url('admin/reports/semifg') }}"
                                        class="nav-link @if (Request::is('admin/reports/semifg')) active @endif">
                                        <p>Semi Finish Goods</p>
                                    </a>
                                </li>
                            @endif

                            @if (
                                $authDept == 'Quality Control' ||
                                    $authDept == 'Procurement, Installation and Delivery' ||
                                    $authDept == 'IT' ||
                                    $authDept == 'PPIC' ||
                                    $authDept == 'Production and Warehouse' ||
                                    $authDept == 'Production')
                                <li class="nav-item">
                                    <a href="{{ url('admin/reports/finishgoods') }}"
                                        class="nav-link @if (Request::is('admin/reports/finishgoods')) active @endif">
                                        <p>Finish Goods</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    ($authDept == 'Production and Warehouse' && $authEmail !== 'warehouse_sby@sanwamas.co.id') ||
                                    $authDept == 'PPIC' ||
                                    $authDept == 'Purchasing')
                                <li class="nav-item">
                                    <a href="{{ url('/reports-bon') }}"
                                        class="nav-link @if (Request::is('admin/reports/bon')) active @endif">
                                        <p>Bon</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    ($authDept == 'Production and Warehouse' && $authEmail !== 'warehouse_sby@sanwamas.co.id') ||
                                    $authDept == 'PPIC' ||
                                    $authDept == 'Quality Control' ||
                                    $authDept == 'Purchasing')
                                <li class="nav-item">
                                    <a href="{{ url('/reports-memo') }}"
                                        class="nav-link @if (Request::is('reports-memo')) active @endif">
                                        <p>Memo</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </nav>
        @endif

        {{-- Delivery --}}
        @if (
            $authDept == 'IT' ||
                $authDept == 'Production and Warehouse' ||
                $authDept == 'Procurement, Installation and Delivery' ||
                $authDept == 'Production' ||
                $authDept == 'Quality Control')

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                    <li class="nav-item {{ Request::is('admin/delivery/*') ? 'menu-open' : '' }}">
                        <a href="#deliverySubMenu" data-toggle="collapse"
                            aria-expanded="{{ Request::is('admin/delivery/*') }}" class="nav-link">
                            <i class="nav-icon fa fa-truck"></i>
                            <p>
                                Delivery
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="collapse list-unstyled {{ Request::is('admin/delivery/*') ? 'show' : '' }} itemSubMenu"
                            id="deliverySubMenu">
                            @if ($authDept == 'IT' || ($authDept == 'Procurement, Installation and Delivery' && $authLevel == 'Leader'))
                                <li class="nav-item">
                                    <a href="{{ url('admin/delivery/list') }}"
                                        class="nav-link @if (Request::is('admin/delivery/list')) active @endif">
                                        <p>List Delivery</p>
                                    </a>
                                </li>
                            @endif
                            @if (
                                $authDept == 'IT' ||
                                    $authDept == 'Production and Warehouse' ||
                                    $authDept == 'PPIC' ||
                                    $authDept == 'Production' ||
                                    $authDept == 'Procurement, Installation and Delivery' ||
                                    $authDept == 'Quality Control')
                                <li class="nav-item">
                                    <a href="{{ url('admin/delivery/history') }}"
                                        class="nav-link @if (Request::is('admin/delivery/history')) active @endif">
                                        <p>History Delivery</p>
                                    </a>
                                </li>
                            @endif
                        </ul>

                    </li>
                </ul>
            </nav>
        @endif

        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('sidebarSearchInput');

        searchInput.addEventListener("keyup", function() {
            const query = this.value.toLowerCase();
            const menuItems = document.querySelectorAll(".nav-sidebar .nav-item");

            menuItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.style.display = ""
                } else {
                    item.style.display = "none";
                }
            });
        })
    })
</script>
