@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Memo</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/production/listmemo') }}" class="btn btn-primary btn-sm">List Memo</a>
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
                                <h3 class="card-title">Create Memo</h3>
                            </div>
                            <form action="{{ url('admin/production/memo') }}" class="form-horizontal" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Nomer :</label>
                                        <div class="col-sm-6">
                                            <input name="no" id="no" value="{{ $number }}"
                                                class="form-control" placeholder="Enter Nomer Terbit" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Tanggal :</label>
                                        <div class="col-sm-6">
                                            <input type="date" name="date" id="date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Hal :</label>
                                        <div class="col-sm-6">
                                            <select name="description" id="description" class="form-control" required>
                                                <option value="">Pilih hal disini</option>
                                                <option value="Stock Produksi">Stock Produksi</option>
                                                <option value="Kirim barang ke maklon">Kirim barang ke maklon</option>
                                                <option value="Pengiriman material/komponen">Pengiriman material/komponen
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-label">Proyek/Order<span style="color:springgreen"> (optional) </span>:</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="project" id="project" class="form-control"
                                                placeholder="Enter Proyek atau Order">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-label">IO :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="io" id="io" class="form-control"
                                                placeholder="Enter IO" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-label">Due Date :</label>
                                        <div class="col-sm-6">
                                            <input type="date" name="duedate" id="duedate" class="form-control" required>
                                        </div>
                                    </div>
                                    <div id="unitContainer">
                                        <div class="unit-block mb-3 p-3 border rounded bg-light">
                                            <div class="form-group row">
                                                <label for="" class="col-sm-3 col-form-label">Kebutuhan :</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="needs[]" class="form-control"
                                                        placeholder="Enter Kebutuhan" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="col-sm-3 col-form-lable">Unit :</label>
                                                <div class="col-sm-2">
                                                    <input type="text" name="unit[]" class="form-control"
                                                        placeholder="Enter Unit">
                                                </div>
                                                <p>W :</p>
                                                <div class="col-sm-1">
                                                    <input type="number" name="width[]" class="form-control"
                                                        placeholder="Enter Width">
                                                </div>
                                                <p>H :</p>
                                                <div class="col-sm-1">
                                                    <input type="number" name="height[]" class="form-control"
                                                        placeholder="Enter Height">
                                                </div>
                                                <p>Qty :</p>
                                                <div class="col-sm-1">
                                                    <input type="number" name="qty[]" class="form-control"
                                                        placeholder="Enter Quantity">
                                                </div>
                                                <p>Uom :</p>
                                                <div class="col-sm-1">
                                                    <input type="text" name="uom[]" class="form-control"
                                                        placeholder="Enter Uom" list="uomList">
                                                    <datalist id="uomList">
                                                        <option value="Pcs">
                                                        <option value="Unit">
                                                        <option value="Lbr">
                                                        <option value="Ltr">
                                                        <option value="Btg">
                                                        <option value="Set">
                                                    </datalist>
                                                </div>
                                                <div class="col-sm-1 mt-1">
                                                    <button type="button" class="btn btn-success btn-add-unit"><i
                                                            class="fa fa-plus-circle"></i></button>
                                                    <button type="button" class="btn btn-danger btn-remove-unit"
                                                        style="display: none;"><i class="fa fa-minus-circle"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="fa fa-floppy-o"></i>Simpan</button>
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
        $(document).on('click', '.btn-add-unit', function() {
            // clone block
            let $block = $(this).closest('.unit-block');
            let $clone = $block.clone();

            // clear values in the cloned inputs
            $clone.find('input').val('');
            $clone.find('.btn-remove-unit').show();

            // append clone to container
            $('#unitContainer').append($clone);
        });
        $(document).on('click', '.btn-remove-unit', function() {
            $(this).closest('.unit-block').remove();
        });
    </script>
@endsection
