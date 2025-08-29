<form id="stockupForm" action="{{ url("admin/transaction/stockup")}}" method="post">
    @csrf
    <input type="hidden" name="nopo" id="no_po_hidden">
    <input type="hidden" name="grpo" id="grpo_hidden">
    <input type="hidden" name="remarks" id="remark_hidden">
    <div class="table-responsive">
        <table class="table table-striped table-borderd table-sm">
            @if (isset($scannedBarcodes) && $scannedBarcodes->count())
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Item Code</th>
                        <th>Item Desc</th>
                        <th>Qty</th>
                        <th>Uom</th>
                        <th>Delete</th>
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
                                <input type="number" name="stocks[{{ $index }}][qty]" class="form-control-sm text-center" style="max-width: 60px">
                            </td>
                            <td>
                                {{ $stocks->item->uom}}
                            </td>
                            <td>
                                <button type="button" onclick="deleteItem({{ $stocks->id }})" class="btn btn-danger btn-sm">
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
                <div class="col col-sm-12">
                    <button type="submit" onclick="return AddStockupForm();" class="btn btn-success float-right"><i class="fa fa-check"></i> Add</button>
                </div>
            </div>
        </div>
    </div>
</form>

