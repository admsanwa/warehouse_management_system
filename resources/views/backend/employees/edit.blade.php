@extends('backend.layouts.app')

@section('content')
<style>
    /* Required star */
    .required-label::after {
        content: " *";
        color: red;
        font-weight: bold;
    }

    /* Optional text */
    .optional-label::after {
        content: " (optional)";
        color: #28a745;
        font-weight: normal;
        font-size: 14px;
    }
</style>

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

                        <form action="{{ url('admin/employees/edit/' .$getRecord->id)}}" 
                            id="biodataForm" class="form-horizontal" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="card-body">
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label required-label">Username</label>
                                    <div class="col-sm-9">
                                        <span style="color: red">{{ $errors->first('username') }}</span>
                                        <input type="text" name="username" value="{{ $getRecord->username }}" 
                                            class="form-control" required placeholder="Enter Username">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label required-label">Fullname</label>
                                    <div class="col-sm-9">
                                        <span style="color: red">{{ $errors->first('fullname') }}</span>
                                        <input type="text" name="fullname" value="{{ $getRecord->fullname }}" 
                                            class="form-control" required placeholder="Enter Fullname">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label required-label">NIK</label>
                                    <div class="col-sm-9">
                                        <span style="color: red">{{ $errors->first('nik') }}</span>
                                        <input type="number" name="nik" value="{{ $getRecord->nik }}" 
                                            class="form-control" required placeholder="Enter NIK">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label required-label">Department</label>
                                    <div class="col-sm-9">
                                        <span style="color: red">{{ $errors->first('department') }}</span>
                                        <select name="department" id="department" class="form-control" required>
                                            <option value="">Select Department</option>
                                            <option value="IT" {{ $getRecord->department == 'IT' ? 'selected' : ''}}>IT</option>
                                            <option value="Procurement, Installation and Delivery" {{ $getRecord->department == 'Procurement, Installation and Delivery' ? 'selected' : ''}}>Procurement, Installation and Delivery</option>
                                            <option value="Production and Warehouse" {{ $getRecord->department == 'Production and Warehouse' ? 'selected' : ''}}>Production and Warehouse</option>
                                            <option value="PPIC" {{ $getRecord->department == 'PPIC' ? 'selected' : ''}}>PPIC</option>
                                            <option value="Purchasing" {{ $getRecord->department == 'Purchasing' ? 'selected' : ''}}>Purchasing</option>
                                            <option value="Quality Control" {{ $getRecord->department == 'Quality Control' ? 'selected' : ''}}>Quality Control</option>
                                            <option value="Production" {{ $getRecord->department == 'Production' ? 'selected' : ''}}>Production</option>
                                            <option value="Accounting and Finance" {{ $getRecord->department == 'Accounting and Finance' ? 'selected' : ''}}>Accounting and Finance</option>
                                            <option value="Sales and Marketing" {{ $getRecord->department == 'Sales and Marketing' ? 'selected' : ''}}>Sales and Marketing</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label required-label">Level</label>
                                    <div class="col-sm-9">
                                        <span style="color: red">{{ $errors->first('level') }}</span>
                                        <select name="level" id="level" class="form-control" required>
                                            <option value="">Select Level</option>
                                            <option value="Operator" {{$getRecord->level == 'Operator' ? 'selected' : ''}}>Operator</option>
                                            <option value="Staff" {{$getRecord->level == 'Staff' ? 'selected' : ''}}>Staff</option>
                                            <option value="Leader" {{$getRecord->level == 'Leader' ? 'selected' : ''}}>Leader</option>
                                            <option value="Supervisor" {{$getRecord->level == 'Supervisor' ? 'selected' : ''}}>Supervisor</option>
                                            <option value="Manager" {{$getRecord->level == 'Manager' ? 'selected' : ''}}>Manager</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label optional-label">Warehouse Access</label>
                                    <div class="col-sm-9">
                                        <select name="warehouse" id="warehouse" class="form-control">
                                            <option value="">Select Warehouse Access</option>
                                            <option value="BK001" {{$getRecord->warehouse_access == 'BK001' ? 'selected' : ''}}>BK001</option>
                                            <option value="BK002" {{$getRecord->warehouse_access == 'BK002' ? 'selected' : ''}}>BK002</option>
                                            <option value="BK003" {{$getRecord->warehouse_access == 'BK003' ? 'selected' : ''}}>BK003</option>
                                            <option value="SB001" {{$getRecord->warehouse_access == 'SB001' ? 'selected' : ''}}>SB001</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label required-label">Email</label>
                                    <div class="col-sm-9">
                                        <span style="color: red">{{ $errors->first('email') }}</span>
                                        <input type="text" name="email" value="{{ $getRecord->email }}" 
                                            class="form-control" required placeholder="Enter Email">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="" class="col-sm-3 col-form-label optional-label">Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="password" class="form-control" placeholder="Password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="" class="col-sm-3 col-form-label optional-label">Confirm Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label optional-label">Signature</label>
                                    <div class="col-sm-9">
                                        <div class="border rounded p-2 mb-2" style="width: 400px; height: 200px; background: #f8f9fa">
                                            <canvas id="signature" width="380" height="180" 
                                                    style="display: block; width:100%; height:100%; cursor: crosshair;">
                                            </canvas>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-secondary" onclick="clearSignature()">Clear</button><br><br>

                                        <input type="hidden" name="signature" id="signatureData">

                                        @if($getRecord->sign)
                                            <p class="mt-2">Current Signature:</p>
                                            <div class="border rounded p-2" style="width: 400px; height: 200px; background: #fff">
                                                <img src="{{ asset('assets/images/sign/' . $getRecord->sign) }}" 
                                                    alt="User Signature" 
                                                    style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <a href="{{ url('admin/employees') }}" class="btn btn-default">Back</a>
                                <button type="submit" class="btn btn-primary float-right">Update</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
  <script>
    // signature canvas
    const canvas = document.getElementById("signature");
    const signaturePad = new SignaturePad(canvas);

    function clearSignature() {
      signaturePad.clear();
    }

    document.getElementById("biodataForm").addEventListener("submit", function (e) {
      if (!signaturePad.isEmpty()) {
        document.getElementById("signatureData").value = signaturePad.toDataURL("image/png")
      }
    })
  </script>
@endsection
