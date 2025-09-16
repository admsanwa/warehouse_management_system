@extends("backend.layouts.app")
@section("content")
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Semi Finish Goods</h1>
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
                                    Search List Semi Finish Goods
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">IO No</label>
                                            <input type="text" name="io" class="form-control"
                                                placeholder="Enter Nomor IO">
                                        </div>
                                         <div class="form-group col-md-2">
                                            <label for="">Product Order</label>
                                            <input type="text" name="prod_order" class="form-control"
                                                placeholder="Enter Product Order">
                                        </div>
                                      <div class="form-group col-md-2">
                                            <label for="">Product No</label>
                                            <input type="text" name="prod_no" id="prod_no" class="form-control"
                                                placeholder="Enter Product Nomor">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Desc</label>
                                            <input type="text" name="prod_desc" class="form-control"
                                                placeholder="Enter Product Description">
                                        </div>
                                        {{-- <div class="form-group col-md-2">
                                            <label for="series">Series</label>
                                            <select name="series" class="form-control" id="seriesSelect">
                                            </select>
                                        </div> --}}
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 20px"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/reports/semifg') }}" class="btn btn-warning"
                                                style="margin-top: 20px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include("_message")
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List Semi Finish Goods</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>IO</th>
                                                <th>Prod Order</th>
                                                <th>Prod Nomor</th>
                                                <th>Prod Description</th>
                                                <th>Complete Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getRecord as $semifg)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $semifg->io }}</td>
                                                    <td>{{ $semifg->prod_order }}</td>
                                                    <td><a href="{{ url('admin/production/view?docEntry=' . $semifg->doc_entry . '&docNum=' . $semifg->prod_order) }}">{{ $semifg->prod_no }}</a> 
                                                    <td>{{ $semifg->prod_desc }}</td>
                                                    <td>{{ formatDecimalsSAP($semifg->qty) }}</div>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="100%">No Record Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end px-2 py-2">
                                    <div style="overflow-x: auto; max-width:100%">
                                        {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection