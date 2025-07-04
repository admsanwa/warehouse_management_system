@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Upload Data</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/purchasing') }}" class="btn btn-primary btn-sm">Purchasing List</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Upload Data Purchasing List (<span style="color: red"> .csv</span>)
                                </h3>
                            </div>
                            @include('_message')

                               <div class="card-body">
                                <form action="{{ url('admin/purchasing/upload')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="csvFile">Choose CSV File</label>
                                        <input class="form-control" type="file" name="file" id="csvFile" required>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary w-100 w-md-auto" type="submit">
                                            <i class="fa fa-upload"></i> Upload
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection