@extends('backend.layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">View Employees</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">View</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">View Employee Details</h3>
                        </div>

                        <form class="form-horizontal" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Username<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        {{ $getRecord->username}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">FullName<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        {{ $getRecord->fullname}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">NIK<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        {{ $getRecord->nik}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Department<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        {{ $getRecord->department}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Level<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        {{ $getRecord->level}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Email<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        {{ $getRecord->email}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Created At<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        {{ date('d-m-Y H:i A', strtotime($getRecord->created_at)) }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Updated At<span style="color: red">*</span></label>
                                    <div class="col-sm-10">
                                        {{ date('d-m-Y H:i A', strtotime($getRecord->updated_at)) }}
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ url('admin/employees')}}" class="btn btn-default">Back</a>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection