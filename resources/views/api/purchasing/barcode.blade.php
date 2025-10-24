@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Barcode Print</h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col-md-12">
                        @include('_message')
                       <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <a href="{{ url('admin/purchasing/purchaseorder') }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-list"></i> Browse PO List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recently Added</h3>
                            </div>
                            @if ($addedBarcodesLast['code'] != 'Z-Biaya Maklon')
                                <form action="{{ url('/print/barcodes/pdfpo') }}" method="GET">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                @if ($addedBarcodes->count())
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Item Code</th>
                                                            <th>Item Desc</th>
                                                            <th>Qty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($addedBarcodes as $barcode)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $barcode->code }}</td>
                                                                <td>{{ $barcode->name }}</td>
                                                                <td>
                                                                    <input type="hidden" name="codes[]" value="{{ $barcode->code }}">
                                                                    <input type="number" class="form-control" name="qtys[]" placeholder="0">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="5" class="text-right">
                                                                <div class="d-flex flex-wrap justify-content-end">
                                                                    <button type="submit" class="btn btn-success mb-2">
                                                                        <i class="fa fa-file-pdf"></i> Print (PDF)
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <form action="{{ url('/print/pdfmaklon') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                                    @csrf
                                    <input type="hidden" name="docDate" value="{{ $docDate }}" readonly>

                                    <div class="card-body p-0">
                                        <div class="container">
                                            <div id="unitContainer">
                                                <div class="unit-block mb-3 p-3 border rounded bg-light">
                                                    <div class="form-row align-items-end">
                                                        <!-- Nama Barang -->
                                                        <div class="form-group col-md-4">
                                                            <label>Nama Barang</label>
                                                            <select name="codes[]" class="select2 form-control" style="width: 100%"></select>
                                                            <input type="hidden" name="names[]" class="form-control" readonly>
                                                        </div>

                                                        <!-- Qty -->
                                                        <div class="form-group col-md-2">
                                                            <label>Qty</label>
                                                            <input type="number" name="qtys[]" class="form-control" placeholder="Enter Qty">
                                                        </div>

                                                        <!-- Action buttons -->
                                                        <div class="form-group col-md-1 text-center">
                                                            <button type="button" class="btn btn-success btn-add-unit mb-1">
                                                                <i class="fa fa-plus-circle"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-remove-unit" style="display:none;">
                                                                <i class="fa fa-minus-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Hidden Template -->
                                            <template id="unitTemplate">
                                                <div class="unit-block mb-3 p-3 border rounded bg-light">
                                                    <div class="form-row align-items-end">
                                                        <div class="form-group col-md-4">
                                                            <label>Nama Barang</label>
                                                            <select name="codes[]" class="select2 form-control" style="width:100%"></select>
                                                            <input type="hidden" name="names[]" class="form-control" readonly>
                                                        </div>

                                                        <div class="form-group col-md-2">
                                                            <label>Qty</label>
                                                            <input type="number" name="qtys[]" class="form-control" placeholder="Enter Qty">
                                                        </div>

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

                                            <div class="text-right mt-3">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-file-pdf"></i> Print (PDF)
                                                </button>
                                            </div>
                                            <br>
                                        </div>
                                    </div>
                                </form>
                            @endif
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
                placeholder: "Typing for search item code...",
                allowClear: true,
                width: "100%",
                ajax: {
                    url: "/onhandSearch",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return {
                            results: (data.results || []).map((item) => ({
                                id: item.id,
                                text: item.text,
                                names: item.item_desc,
                            })),
                        };
                    },
                },
                language: {
                    inputTooShort: () => "Typing item code for search...",
                    noResults: () => "Not found",
                    searching: () => "Loading...",
                },
            });

            // When user selects an item
            $el.on("select2:select", function(e) {
                let data = e.params.data;
                let $block = $(this).closest(".unit-block");
                $block.find('input[name="names[]"]').val(data.names || "");
            });
        }

        initSelect2($(".select2"));

        // Add new unit
        $(document).on("click", ".btn-add-unit", function() {
            let clone = $($("#unitTemplate").html());
            $("#unitContainer").append(clone);

            // Initialize select2 inside new block
            initSelect2(clone.find(".select2"));

            // Show remove button on cloned ones
            clone.find(".btn-remove-unit").show();
        });

        // Remove unit
        $(document).on("click", ".btn-remove-unit", function() {
            $(this).closest(".unit-block").remove();
        });
    });

    </script>
@endsection
