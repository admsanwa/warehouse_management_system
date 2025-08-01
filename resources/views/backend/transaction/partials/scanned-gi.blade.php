<form id="giForm" action="{{ url("admin/transaction/giup")}}" method="post">
    @csrf
    <input type="hidden" name="po" id="po_hidden">
    <input type="hidden" name="io" id="io_hidden">
    <input type="hidden" name="gi" id="gi_hidden">
    <div class="table-responsive">
        <table class="table table-striped table-borderd table-sm">
            @if (isset($scannedBarcodes) && $scannedBarcodes->count())
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Item Code</th>
                        <th>Item Desc</th>
                        <th>Qty</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scannedBarcodes as $index => $goodissue)
                        <tr>
                            <td>{{ $loop->iteration }}
                                <input type="hidden" name="goodissue[{{ $index }}][id]" value="{{ $goodissue->id }}">
                            </td>
                            <td>
                                {{ $goodissue->code }}
                                <input type="hidden" name="goodissue[{{ $index }}][item_code]" value="{{ $goodissue->code }}">
                            </td>                            
                            <td>{{ $goodissue->name }}</td>
                            <td>
                                <input type="number" name="goodissue[{{ $index }}][qty]" class="form-control" value="0">
                            </td>
                            <td>
                                <button type="button" onclick="deleteItem({{ $goodissue->id }})" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endif  
        </table>
        <div class="card">
            <div class="card-footer">
                <div class="col col-sm-11">
                    <button type="submit" onclick="return AddGoodIssueForm();" class="btn btn-success float-right"><i class="fa fa-check"></i> Add</button>
                </div>
            </div>
        </div>
    </div>
</form>

