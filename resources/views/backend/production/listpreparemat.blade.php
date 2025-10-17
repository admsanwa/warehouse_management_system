@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>List Prepare Material</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Search List Bon</h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">IO :</label>
                                            <input type="text" name="io" id="io" class="form-control"
                                                placeholder="Enter IO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Doc Number :</label>
                                            <input type="text" name="doc_num" id="doc_num" class="form-control"
                                                placeholder="Enter Document Number">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product No :</label>
                                            <input type="text" name="prod_no" id="prod_no" class="form-control"
                                                placeholder="Enter Product No">
                                        </div>
                                        @php
                                            $currentSeries = $getRecord->first()->series ?? '';
                                        @endphp
                                        <div class="form-group col-md-2">
                                            <label for="">Series :</label>
                                            <select name="series" id="series" class="form-control">
                                                <option value="">Select Series</option>
                                                @foreach($seriesList as $series)
                                                    <option value="{{ $series->series }}"
                                                        {{ $series->series == $currentSeries ? 'selected' : '' }}>
                                                        {{ $series->series }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="0" @selected(Request::get('status') === "0")>Prepare Material Done</option>
                                                <option value="1" @selected(Request::get('status') === "1")>Transfer Done</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px;"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('/listpreparemat') }}" class="btn btn-warning"
                                                style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List of All Prepare Material</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>IO</th>
                                                <th>Doc Number</th>
                                                <th>Product No</th>
                                                <th>Series</th>
                                                <th>Status</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        @forelse ($getRecord as $preparemat)
                                            <tbody>
                                                <tr class="{{ $preparemat->status != 1 ? 'table-primary' : '' }}">
                                                    <td>
                                                        @if ($preparemat->status != 1)
                                                            <i class="fa fa-circle text-primary ms-2"
                                                                style="font-size:10px; margin-right:10px;"
                                                                title="Recommended"></i>
                                                        @endif
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    <td>{{ $preparemat->io ?? '-'}}</td>
                                                    <td>{{ $preparemat->doc_num ?? '-' }}</td>
                                                    <td>{{ $preparemat->prod_no ?? '-' }}</td>
                                                    <td>{{ $preparemat->series }}</td>
                                                    <td>
                                                        @if ($preparemat->status)
                                                            Transfer Done
                                                        @else 
                                                            Prepare Material Done
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('preparematdetails/' . $preparemat->doc_entry) }}"
                                                            class="btn btn-primary">
                                                            <div class="fa fa-eye"></div>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="100%">Not Record Found</td>
                                                </tr>
                                            </tbody>
                                        @endforelse
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
                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection
