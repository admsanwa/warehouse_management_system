@extends('backend.layouts.app')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Employees</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <a href="{{ url('/register') }}" class="btn btn-primary">Add Employees</a>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Search Employees</h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">Fullname</label>
                                            <input type="text" name="fullname" value="{{ Request()->fullname }}"
                                                class="form-control" placeholder="Enter Fullname">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">NIK</label>
                                            <input type="text" name="nik" value="{{ Request()->nik }}"
                                                class="form-control" placeholder="Enter NIK">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Department</label>
                                            <input type="text" name="department" value="{{ Request()->department }}"
                                                class="form-control" placeholder="Enter Department">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Email</label>
                                            <input type="email" name="email" value="{{ Request()->email }}"
                                                class="form-control" placeholder="Enter Email">
                                        </div>

                                        <div class="form-group col-md-2">
                                            <button class="btn btn-primary" type="submit"
                                                style="margin-top: 30px">Search</button>
                                            <a href="{{ url('admin/employees') }}" class="btn btn-success"
                                                style="margin-top: 30px">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List Employees</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>FullName</th>
                                            <th>NIK</th>
                                            <th>Department</th>
                                            <th>Default Warehouse Access</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($getRecord as $value)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $value->fullname }}</td>
                                                <td>{{ $value->nik }}</td>
                                                <td>{{ $value->department }}</td>
                                                <td>{{ $value->warehouse_access }}</td>
                                                <td>{{ $value->email }}</td>
                                                <td>
                                                    <a href="{{ url('admin/employees/view/' . $value->id) }}"
                                                        class="btn btn-sm btn-info">View</a>
                                                    <a href="{{ url('admin/employees/edit/' . $value->id) }}"
                                                        class="btn btn-sm btn-primary">Edit</a>
                                                    <a href="{{ url('admin/employees/delete/' . $value->id) }}"
                                                        onclick="return confirm('Are you sure you want to delete?')"
                                                        class="btn btn-sm btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%">No Record Found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div style="padding: 10px; float: right">
                                    {!! $getRecord->appends(Illuminate\Support\Facades\Request::except('page'))->links() !!}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection
