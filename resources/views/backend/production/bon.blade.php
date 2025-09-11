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
                            <form action="{{ url('admin/production/bon') }}" class="form-horizontal" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Tipe Bon :</label>
                                        <div class="col-sm-6">
                                            <select name="type" id="type" class="form-control">
                                                <option value="">Select tipe bon Lokal/Import</option>
                                                <option value="Lokal">Lokal</option>
                                                <option value="Import">Import</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Nomer :</label>
                                        <div class="col-sm-6">
                                            <input name="no" id="no" value="{{ $number }}"
                                                class="form-control" readonly>
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
                                            <input type="text" name="section" id="section" class="form-control"
                                                value="PPIC" placeholder="Masukkan Bagian" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Pemesan :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="created_by" id="created_by" class="form-control"
                                                value="{{ $user->fullname }}" placeholder="Masukkan Pemasan" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-label">IO :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="io" id="io" class="form-control"
                                                placeholder="Masukkan IO">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-label">Project :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="project" id="project" class="form-control"
                                                placeholder="Masukkan Project">
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
                                        <div class="unit-block mb-3 p-3 border rounded bg-light">
                                            <div class="form-row align-items-end">
                                                <!-- Nama Barang -->
                                                <div class="form-group col-md-4">
                                                    <label for="item_code">Nama Barang</label>
                                                    <select name="item_code[]" class="select2 form-control"
                                                        style="width: 100%"></select>
                                                    <input type="hidden" name="item_desc[]" class="form-control"
                                                        placeholder="Masukkan Uom" readonly>

                                                </div>

                                                <!-- UOM -->
                                                <div class="form-group col-md-2">
                                                    <label for="uom">UOM</label>
                                                    <input type="text" name="uom[]" class="form-control"
                                                        placeholder="Masukkan Uom" readonly>
                                                </div>

                                                <!-- Qty -->
                                                <div class="form-group col-md-2">
                                                    <label for="qty">Qty</label>
                                                    <input type="number" name="qty[]" class="form-control"
                                                        placeholder="Masukkan Qty">
                                                </div>

                                                <!-- Keterangan -->
                                                <div class="form-group col-md-3">
                                                    <label for="remark">Keterangan</label>
                                                    <input type="text" name="remark[]" class="form-control"
                                                        placeholder="Masukkan Keterangan">
                                                </div>

                                                <!-- Action buttons -->
                                                <div class="form-group col-md-1 text-center">
                                                    <button type="button" class="btn btn-success btn-add-unit mb-1">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-remove-unit"
                                                        style="display: none;">
                                                        <i class="fa fa-minus-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    {{-- Hidden clean template --}}
                                    <template id="unitTemplate">
                                        <div class="unit-block mb-3 p-3 border rounded bg-light">
                                            <div class="form-row align-items-end">
                                                <!-- Nama Barang -->
                                                <div class="form-group col-md-4">
                                                    <label>Nama Barang</label>
                                                    <select name="item_code[]" class="select2 form-control"
                                                        style="width: 100%">
                                                    </select>
                                                    <input type="hidden" name="item_desc[]" class="form-control"
                                                        placeholder="Masukkan Uom" readonly>
                                                </div>

                                                <!-- Qty -->
                                                <div class="form-group col-md-2">
                                                    <label>Qty</label>
                                                    <input type="number" name="qty[]" class="form-control"
                                                        placeholder="Masukkan Qty">
                                                </div>

                                                <!-- UOM -->
                                                <div class="form-group col-md-2">
                                                    <label>Uom</label>
                                                    <input type="text" name="uom[]" class="form-control"
                                                        placeholder="Masukkan Uom" readonly>
                                                </div>

                                                <!-- Keterangan -->
                                                <div class="form-group col-md-3">
                                                    <label>Keterangan</label>
                                                    <input type="text" name="remark[]" class="form-control"
                                                        placeholder="Masukkan Keterangan">
                                                </div>

                                                <!-- Action buttons -->
                                                <div class="form-group col-md-1 text-center">
                                                    <button type="button" class="btn btn-success btn-add-unit mb-1">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-remove-unit">
                                                        <i class="fa fa-minus-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

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
        $(document).ready(function() {
            function initSelect2($el) {
                $el.select2({
                    placeholder: "Ketik kode barang...",
                    allowClear: true,
                    width: "100%",
                    language: {
                        inputTooShort: function() {
                            return "Ketik kode barang untuk mencari...";
                        },
                        noResults: function() {
                            return "Tidak ada data ditemukan";
                        },
                        searching: function() {
                            return "Sedang mencari...";
                        },
                    },
                    ajax: {
                        url: "/onhandSearch",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: (data.results || []).map((item) => ({
                                    id: item.id,
                                    text: item.text,
                                    uom: item.uom,
                                    item_desc: item.item_desc,
                                })),
                            };
                        },
                    },
                });

                // Event ketika pilih barang
                $el.on("select2:select", function(e) {
                    let data = e.params.data;
                    let $block = $(this).closest(".unit-block");
                    $block.find('input[name="uom[]"]').val(data.uom || "");
                    $block.find('input[name="item_desc[]"]').val(data.item_desc || "");
                });
            }

            initSelect2($(".select2"));

            $(document).on("click", ".btn-add-unit", function() {
                let clone = $($("#unitTemplate").html());
                $("#unitContainer").append(clone);

                initSelect2(clone.find(".select2"));
            });

            $(document).on("click", ".btn-remove-unit", function() {
                $(this).closest(".unit-block").remove();
            });
        });
    </script>
@endsection
