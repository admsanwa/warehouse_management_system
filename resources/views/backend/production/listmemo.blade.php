@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>List Memo</h1>
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
                                <h3 class="card-title">Search Memo List</h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">No :</label>
                                            <input type="text" name="no" id="no" class="form-control" placeholder="Enter Nomer Memo">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Desc :</label>
                                            <input type="text" name="description" id="description" class="form-control" placeholder="Enter Description">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Project :</label>
                                            <input type="text" name="project" id="project" class="form-control" placeholder="Enter Project">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">IO :</label>
                                            <input type="text" name="io" id="io" class="form-control" placeholder="Enter IO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px;"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/production/listmemo')}}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List of All Memo</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Description</th>
                                                <th>Project</th>
                                                <th>IO</th>
                                                <th>Due Date</td>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        @forelse ($getRecord as $memo)
                                            <tbody>
                                                <tr>
                                                    <td>{{ $memo->no }}</td>
                                                    <td>{{ $memo->description }}</td>
                                                    <td>{{ $memo->project }}</td>
                                                    <td>{{ $memo->io }}</td>
                                                    <td>{{ $memo->due_date }}</td>
                                                    <td>
                                                        <a href="{{ url('admin/production/memodetails/' . $memo->id) }}" class="btn btn-primary"><div class="fa fa-eye"></div></a>
                                                    </td>
                                                </tr>
                                        @empty
                                                <tr><td colspan="100%">Not Record Found</td></tr>
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