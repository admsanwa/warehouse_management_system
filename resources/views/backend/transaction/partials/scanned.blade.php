<form id="stockupForm" action="{{ url("admin/transaction/stockup")}}" method="post">
    @csrf
    <input type="hidden" name="nopo" id="no_po_hidden">
    <input type="hidden" name="grpo" id="grpo_hidden">
    <div class="table-responsive">
        <table class="table table-striped table-borderd table-sm">
            @if (isset($scannedBarcodes) && $scannedBarcodes->count())
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Item Code</th>
                        <th>Item Desc</th>
                        <th>Qty</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scannedBarcodes as $index => $stocks)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $stocks->item->code }}
                                <input type="hidden" name="stocks[{{ $index }}][item_code]" value="{{ $stocks->item->code }}">
                            </td>                            
                            <td>{{ $stocks->item->name }}</td>
                            <td>
                                <input type="hidden" name="stocks[{{ $index }}][id]" value="{{ $stocks->id }}">
                                <input type="number" name="stocks[{{ $index }}][qty]" class="form-control" value="0">
                            </td>
                            <td>
                                <a href="{{ url('admin/transaction/stockdel/' . $stocks->id)  }}" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endif  
        </table>
        <div class="card">
            <div class="card-footer">
                <div class="col col-sm-11">
                    <button type="submit" onclick="return AddStockupForm();" class="btn btn-success float-right"><i class="fa fa-check"></i> Add</button>
                </div>
            </div>
        </div>
    </div>
</form>

