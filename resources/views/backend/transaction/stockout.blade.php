@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="content-header">
                <div class="container-fluid">
                    <section class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Transactions</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <a href="{{ url('admin/transaction/stockin') }}" class="btn btn-primary">Stock In</a>
                            </ol>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    @include('_message')
                    <div class="card-header">
                        <h3 class="card-title">Stock Out</h3>
                    </div>
                    <form action="{{ url('admin/transaction/stockout')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-lable">Scan Barcode :</label>
                                <div class="col-sm-8">
                                    <span class="badge bg-info text-dark mb-2">
                                        <i class="fas fa-info-circle"> Untuk Scan Item/Barang keluar dari Warehouse</i>
                                    </span>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger mr-1" onclick="startCamera()">Use Camera</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showFileInput()">Upload Image</button>
                                    </div>
                                    <div id="reader" style="width: 300px; display:none;"></div>
                                    <div id="fileInput" style="display: none;">
                                        <input type="file" accept="image/*" onchange="scanImage(this)" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-lable">Item Code :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="item_code" id="item_code" class="form-control mt-2" readonly required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-lable">Item Description :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="item_desc" id="item_desc" class="form-control mt-2" readonly required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-lable">Qty :</label>
                                <div class="col-sm-6">
                                    <input type="number" name="qty" id="qty" class="form-control mt-2" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection