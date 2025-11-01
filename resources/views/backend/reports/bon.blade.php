@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1> Report BON Pembelian Barang</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Search List BON
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">Dari Tanggal</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Sampai Tanggal</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="button" id="filter" class="btn btn-primary"
                                                style="margin-top: 20px"><i class="fa fa-search"></i> Search</button>
                                            <button type="button" id="reset" class="btn btn-warning"
                                                style="margin-top: 20px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" id="titleDateRange">List From - To -</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="bonTable" class="table table-bordered table-striped"></table>
                                    <p></p>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end px-2 py-2">
                                    <div style="overflow-x: auto; max-width:100%">
                                        {{-- {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!} --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <!-- jQuery (only one, do not load twice) -->

    <!-- DataTables Core -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <!-- DataTables Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>

    <!-- Excel Export Support -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <!-- Print Support -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let table = $("#bonTable").DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                dom: 'Bfrtip', // 👈 Aktifkan button
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Report BON Pembelian',
                        className: 'btn btn-success btn-sm mr-2',
                        text: '<i class="fa fa-file-excel"</i> Export Excell',
                    },
                    {
                        extend: 'print',
                        title: '',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fa fa-print"></i> Print',
                        title: '', // kosongkan agar kita buat manual
                        customize: function(win) {

                            let start = $('#start_date').val() ? $('#start_date').val() : '-';
                            let end = $('#end_date').val() ? $('#end_date').val() : '-';

                            $(win.document.body)
                                .css('font-size', '14px')
                                .prepend(`
                                <div style="text-align:center; margin-bottom:20px;">
                                    <h4 style="margin:0;">Report BON Pembelian Barang</h4>
                                    <p style="margin-top:5px;">Periode: ${start} s/d ${end}</p>
                                    <hr style="border-top:1px solid #000; margin-top:10px;">
                                </div>
                            `);

                            // Styling table agar lebih rapi
                            $(win.document.body).find('table')
                                .addClass('table table-bordered')
                                .css({
                                    'font-size': '12px',
                                    'width': '100%',
                                });
                        }
                    }
                ],
                order: [
                    [1, "desc"]
                ],
                ajax: {
                    type: "POST",
                    url: "{{ route('report.bon.data') }}",
                    data: function(d) {
                        d.start_date = $("#start_date").val();
                        d.end_date = $("#end_date").val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        title: 'No',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'no',
                        title: 'No Bon'
                    },
                    {
                        data: 'date',
                        title: 'Tanggal Bon'
                    },
                    {
                        data: 'item_name',
                        title: 'Komponen'
                    },
                    {
                        data: 'uom',
                        title: 'Satuan'
                    },
                    {
                        data: 'qty',
                        title: 'Qty'
                    },
                    {
                        data: 'remark',
                        title: 'Keterangan'
                    },
                    {
                        data: 'receipt_date',
                        title: 'Tanggal Terima'
                    }
                ]
            });



            $("#filter").click(function() {
                let startDate = $("#start_date").val();
                let endDate = $("#end_date").val();

                function format(date) {
                    return new Date(date).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                }

                if (startDate && endDate) {
                    $("#titleDateRange").text("List From " + format(startDate) + " To " + format(endDate));
                }
                table.ajax.reload();
            });
            $("#reset").click(function() {
                $('#start_date, #end_date, #no_bon').val('');
                $("#titleDateRange").text("List From - To -");
                table.ajax.reload();
            });
        });
    </script>
@endpush
