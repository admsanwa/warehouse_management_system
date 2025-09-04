@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Inventory Transfer Details</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/inventorytf/create') }}" class="btn btn-primary btn-sm">Create Inventory TF</a>
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
                                <h3 class="card-title">View Inventory Transfer</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Status</label>
                                    <div class="col-sm-4">: Open</div>
                                    <label for="" class="col-sm-2 col-form-lable">Vendor Maklon List</label>
                                    <div class="col-sm-4">: </div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Doc Number</label>
                                    <div class="col-sm-4">: 100001758</div>
                                    <label for="" class="col-sm-2 col-form-lable">PO Maklon</label>
                                    <div class="col-sm-4">: </div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Bussiness Partner</label>
                                    <div class="col-sm-4">: C010000840</div>
                                    <label for="" class="col-sm-2 col-form-lable">Default Warehouse</label>
                                    <div class="col-sm-4">: JK001</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Vendor</label>
                                    <div class="col-sm-4">: JAYA OBAYASHI, PT</div>
                                    <label for="" class="col-sm-2 col-form-lable">Default Project Code</label>
                                    <div class="col-sm-4">: JK5706</div>
                                </div>  
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Posting Date</label>
                                    <div class="col-sm-4">: 2025/09/01</div>
                                    <label for="" class="col-sm-2 col-form-lable">Type Inventory Transaction</label>
                                    <div class="col-sm-4">: for Order</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Series</label>
                                    <div class="col-sm-4">: BKS-25</div>
                                    <label for="" class="col-sm-2 col-form-lable">SO</label>
                                    <div class="col-sm-4">: 200000524</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">From Warehouse</label>
                                    <div class="col-sm-4">: BK001</div>
                                    <label for="" class="col-sm-2 col-form-lable">IO</label>
                                    <div class="col-sm-4">: 328/10/1/VIII/24</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">To Warehouse</label>
                                    <div class="col-sm-4">: JK001</div>
                                    <label for="" class="col-sm-2 col-form-lable">Internal No</label>
                                    <div class="col-sm-4">: 1385/SMI/FG/IX/25</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Sales Employee</label>
                                    <div class="col-sm-4">: SOJK-Wigrha Rizky</div>
                                    <label for="" class="col-sm-2 col-form-lable">Default Distr Rule</label>
                                    <div class="col-sm-4">: BK-FIN</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">No Surat Jalan</label>
                                    <div class="col-sm-4">: 1385/SMI/FG/IX/25</div>
                                    <label for="" class="col-sm-2 col-form-lable">No Inventory Transfer </label>
                                    <div class="col-sm-4">: </div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Lokasi</label>
                                    <div class="col-sm-4">: HOKKAN FACTORY CIAWI</div>
                                    <label for="" class="col-sm-2 col-form-lable">No Produksi </label>
                                    <div class="col-sm-4">: 179/P/HWS/KTAX1/3</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Ref 2 (No SJ barang datang)</label>
                                    <div class="col-sm-4">: </div>
                                    <label for="" class="col-sm-2 col-form-lable">No Production Order</label>
                                    <div class="col-sm-4">: 100014489</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Refer No. Good Issue</label>
                                    <div class="col-sm-4">: </div>
                                    <label for="" class="col-sm-2 col-form-lable">Hari & Tanggal Kirim</label>
                                    <div class="col-sm-4">: </div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Komponen Tambahan</label>
                                    <div class="col-sm-10">: </div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Remarks</label>
                                    <div class="col-sm-10">: IT dari BK001 ke JK001 order JAYA OBAYASHI</div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="table table-responsive">
                                    <table class="table table-stripped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Item Code</th>
                                                    <th>Description</th>
                                                    <th>Open Qty</th>
                                                    <th>Qty</th>
                                                    <th>Uom</th>
                                                    <th>Whse</th>
                                                    <th>Acct Code</th>
                                                    <th>Jumlah Kemasan</th>
                                                    <th>Keterangan</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>RM0559C00653</td>
                                                    <td>BAUT & MUR M 10 x 40 (Hexagon)</td>
                                                    <td>75</td>
                                                    <td>50</td>
                                                    <td>Pcs</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>1</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>RM0559000792</td>
                                                    <td>SCREW ROUFING M 8 x 20 mm</td>
                                                    <td>200</td>
                                                    <td>120</td>
                                                    <td>Pcs</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                </tr>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button onclick="history.back()" class="btn btn-default">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection