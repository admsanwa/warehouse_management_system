@extends('backend.layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1 class="m-0">Employees</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Add</a></li>
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
                        <h3 class="card-title">Add Employees</h3>
                    </div>

                    <form accept="{{ url('admin/employees/add')}}" class="form-horizontal" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-lable">Full Name <span style="color: red">*</span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="fullname" value="{{ old('fullname')}}" class="form-control" required placeholder="Enter Fullname">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-lable">NIK <span style="color: red"></span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="number" name="nik" value="{{ old('nik')}}" class="form-control" placeholder="Enter NIK">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-lable">Department <span style="color: red"></span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="department" value="{{ old('department')}}" class="form-control" placeholder="Enter Department">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-lable">Level <span style="color: red"></span>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="level" value="{{ old('level')}}" class="form-control" placeholder="Enter Level">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-lable">Email<span style="color: red">*</span>
                                </label>
                                <div class="col-sm-10">
                                    <span style="color: red">{{ $errors->first('email')}}</span>
                                    <input type="email" name="email" value="{{ old('email')}}" class="form-control" required placeholder="Enter Email ID">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-lable">Phone Number <span style="color: red"></span>
                                </label>
                                <div class="col-sm-10">
                                    <span style="color: red">{{ $errors->first('phone_number')}}</span>
                                    <input type="number" name="phone_number" value="{{ old('phone_number')}}" class="form-control" placeholder="Enter Phone Number">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-lable" required >Manager Name<span style="color: red">*</span>
                                </label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="manager_id" id="">
                                        <option value="">Select Manager Name :</option>
                                        <option value="1">Robert Albert</option>
                                        <option value="2">Ibkrar</option>
                                        <option value="3">Yuda Ragil</option>
                                        <option value="4">Indra Kento</option>
                                        <option value="5">Amirullah</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-lable" required >Department Name<span style="color: red">*</span>
                                </label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="department_id" required>
                                        <option value="">Select Department Name</option>
                                        <option value="1">UI/UX Designer</option>
                                        <option value="2">Backend Developer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ url('admin/employees')}}" class="btn btn-default">Back</a>
                            <button type="submit" class="btn btn-primary float-right">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
@endsection
