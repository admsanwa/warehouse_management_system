@extends('backend.layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Employee</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Edit</a></li>
              <li class="breadcrumb-item active">Employees</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Edit Employees</h3>
                        </div>

                        <form action="{{ url('admin/employees/edit/' .$getRecord->id)}}" class="form-horizontal" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Username <span style="color: red">*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="username" value="{{ $getRecord->username}}" class="form-control" required placeholder="Enter First Username">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Fullname<span style="color: red"></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="fullname" value="{{ $getRecord->fullname }}" class="form-control" placeholder="Enter Fullname">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">NIK<span style="color: red">*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <span style="color: red">{{ $errors->first('nik')}}</span>
                                        <input type="number" name="nik" value="{{ $getRecord->nik }}" class="form-control" required placeholder="Enter NIK">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Department <span style="color: red">*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="department" value="{{ $getRecord->department }}" class="form-control" placeholder="Enter Department">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">level<span style="color: red"></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="level" value="{{ $getRecord->level}}" class="form-control required" required placeholder="Enter Level">
                                    </div>
                                </div>
                                 <div class="form-group row">
                                    <label class="col-sm-2 col-form-lable">Email<span style="color: red">*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <span style="color: red">{{ $errors->first('email')}}</span>
                                        <input type="text" name="email" value="{{ $getRecord->email }}" class="form-control" required placeholder="Enter Email">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ url('admin/employees')}}" class="btn btn-default">Back</a>
                                <button type="submit" class="btn btn-primary float-right">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
@endsection
