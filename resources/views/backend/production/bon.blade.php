@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>BON Pembelian Barang</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/production/listbon') }}" class="btn btn-primary btn-sm">List Bon</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col col-md-12">
                        <div class="card">
                            @include('_message')
                            <div class="card-header">
                                <h3 class="card-title">Create BON</h3>
                            </div>
                            <form action="{{ url('admin/production/bon')}}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Nomer :</label>
                                        <div class="col-sm-6">
                                            <input name="no" id="no" value="{{ $number }}" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Tanggal :</label>
                                        <div class="col-sm-6">
                                            <input type="date" name="date" id="date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Bagian :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="section" id="section" class="form-control" value="PPIC" placeholder="Masukkan Bagian" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Pemesan :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="created_by" id="created_by" class="form-control" value="{{ $user->fullname }}" placeholder="Masukkan Pemasan" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-label">IO :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="io" id="io" class="form-control" placeholder="Masukkan IO">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-label">Project :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="project" id="project" class="form-control" placeholder="Masukkan Project">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Make to Order/Stock :</label>
                                        <div class="col-sm-6">
                                            <select name="make_to" id="make_to" class="form-control">
                                                <option value="">Make to Order/Stock</option>
                                                <option value="Stock">Stock</option>
                                                <option value="Order">Order</option>
                                            </select>
                                        </div>
                                    </div>
                                   <div id="unitContainer">
                                    <div class="unit-block">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nama Barang :</label>
                                            <div class="col-sm-2">
                                                <select name="item_code[]" class="select2" style="width: 100%">
                                                    <option value="">Select Option Items</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->code }}">{{ $item->code . ' - ' . $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <p>Qty :</p>
                                            <div class="col-sm-1">
                                                <input type="number" name="qty[]" class="form-control" placeholder="Masukkan Qty">
                                            </div>
                                            <p>Uom :</p>
                                            <div class="col-sm-1">
                                                <input type="text" name="uom[]" class="form-control" placeholder="Masukkan Uom" list="uomList">
                                                <datalist id="uomList">
                                                    <option value="Pcs">
                                                    <option value="Unit">  
                                                    <option value="Lbr">   
                                                    <option value="Btg">
                                                    <option value="Set"> 
                                                </datalist>
                                            </div>
                                            <p>Keterangan :</p>
                                            <div class="col-sm-2">
                                                <input type="text" name="remark[]" class="form-control" placeholder="Masukkan Keterangan">
                                            </div>
                                            <div class="col-sm-1 mt-1">
                                                <button type="button" class="btn btn-success btn-add-unit"><i class="fa fa-plus-circle"></i></button>
                                                <button type="button" class="btn btn-danger btn-remove-unit" style="display: none;"><i class="fa fa-minus-circle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hidden clean template --}}
                                <template id="unitTemplate">
                                    <div class="unit-block">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Nama Barang :</label>
                                            <div class="col-sm-2">
                                                <select name="item_code[]" class="select2" style="width: 100%">
                                                    <option value="">Select Option Items</option>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->code }}">{{ $item->code . ' - ' . $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <p>Qty :</p>
                                            <div class="col-sm-1">
                                                <input type="number" name="qty[]" class="form-control" placeholder="Masukkan Qty">
                                            </div>
                                            <p>Uom :</p>
                                            <div class="col-sm-1">
                                                <input type="text" name="uom[]" class="form-control" placeholder="Masukkan Uom" list="uomList">
                                            </div>
                                            <p>Keterangan :</p>
                                            <div class="col-sm-2">
                                                <input type="text" name="remark[]" class="form-control" placeholder="Masukkan Keterangan">
                                            </div>
                                            <div class="col-sm-1 mt-1">
                                                <button type="button" class="btn btn-success btn-add-unit"><i class="fa fa-plus-circle"></i></button>
                                                <button type="button" class="btn btn-danger btn-remove-unit"><i class="fa fa-minus-circle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i>Simpan</button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
   $(document).ready(function() {
    // Initialize Select2 for the first row
    $('.select2').select2({  
            tags: true,           // Allow typing new values
            placeholder: "Select or type item",
            allowClear: true 
        });

    $(document).on('click', '.btn-add-unit', function() {
        // Clone from hidden template
        let clone = $($('#unitTemplate').html());

        // Append new row
        $('#unitContainer').append(clone);

        // Initialize Select2 for the new row
        clone.find('.select2').select2({  
            tags: true,           // Allow typing new values
            placeholder: "Select or type item",
            allowClear: true 
        });
    });

    $(document).on('click', '.btn-remove-unit', function() {
        $(this).closest('.unit-block').remove();
    });
});

</script>

@endsection