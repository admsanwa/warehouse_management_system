<form id="stockupForm" action="{{ url("admin/transaction/stockoutup")}}" method="post">
    @csrf
    <input type="hidden" name="prod_order" id="prod_order_hidden">
    <input type="hidden" name="reason" id="reason_hidden">
    <input type="hidden" name="remarks" id="remarks_hidden">
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
                            <td>{{ $loop->iteration }}
                                <input type="hidden" name="stocks[{{ $index }}][id]" value="{{ $stocks->id }}">
                            </td>
                            <td>
                                {{ $stocks->item->code }}
                                <input type="hidden" name="stocks[{{ $index }}][item_code]" value="{{ $stocks->item->code }}">
                            </td>                            
                            <td>{{ $stocks->item->name }}</td>
                            <td>
                                <input type="number" name="stocks[{{ $index }}][qty]" class="form-control-sm" style="max-width: 60px" value="0" step="0.0001">
                            </td>
                            <td>{{ $stocks->item->uom ?? "-"}}</td>
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
                <div class="col col-sm-11">
                    <button type="submit" onclick="return AddStockupForm();" class="btn btn-success float-right"><i class="fa fa-check"></i> Add</button>
                </div>
            </div>
        </div>
    </div>
</form>

