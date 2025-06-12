@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Prodcution List Details</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <a href="{{ url('admin/purchasing') }}" class="btn btn-primary">Production List</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-md-12">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">View Production Order</h3>
                            </div>

                            <form method="post" class="form-horizontal" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Product No :</label>
                                        <div class="col-sm-10">{{ $getRecord->prod_no }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Product Desc :</label>
                                        <div class="col-sm-10">{{ $getRecord->prod_desc }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Remarks :</label>
                                        <div class="col-sm-10">{{ $getRecord->remarks }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Doc Number :</label>
                                        <div class="col-sm-10">{{ $getRecord->doc_num }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">IO No :</label>
                                        <div class="col-sm-10">{{ $getRecord->io_no }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Due Date :</label>
                                        <div class="col-sm-10">{{ $getRecord->due_date}}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Item Code :</label>
                                        <div class="col-sm-10">{{ $getRecord->item_code }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Item Type :</label>
                                        <div class="col-sm-10">{{ $getRecord->item_type }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Item Desc :</label>
                                        <div class="col-sm-10">{{ $getRecord->item_desc }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Qty :</label>
                                        <div class="col-sm-10">{{ $getRecord->qty }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Uom :</label>
                                        <div class="col-sm-10">{{ $getRecord->uom }}</div>
                                    </div>
                                     <div class="form-group row">
                                        <label for="" class="col-sm-2 col-form-lable">Status :</label>
                                        <div class="col-sm-10">{{ $getRecord->status == 0 ? 'Planed' : 'Released' }}</div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ url('admin/purchasing') }}" class="btn btn-default">Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection