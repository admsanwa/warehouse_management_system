<form id="stockupForm" action="{{ url("admin/transaction/rfpup")}}" method="post">
    @csrf
    <input type="hidden" name="prod_order" id="po_hidden">
    <input type="hidden" name="number" id="number_hidden">
    <input type="hidden" name="reason" id="reason_hidden">
    <input type="hidden" name="whse" id="whse_hidden">
    <input type="hidden" name="project_code" id="projectCode_hidden">
    <input type="hidden" name="remarks" id="remarks_hidden">
    <div class="table-responsive">
        <table class="table table-striped table-borderd table-sm">
            @if (isset($scannedBarcodes) && $scannedBarcodes->count())
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Product Nomer</th>
                        <th>Product Desc</th>
                        <th>Qty</th>
                        <th>Uom</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scannedBarcodes as $index => $rfp)
                        <tr>
                            <td>{{ $loop->iteration }}
                                <input type="hidden" name="rfp[{{ $index }}][id]" value="{{ $rfp->id }}">
                            </td>
                            <td>{{ $rfp->production->prod_no }}</td>                            
                            <td>{{ $rfp->production->prod_desc }}</td>
                            <td>
                                <input type="number" name="rfp[{{ $index }}][qty]" class="form-control" value="0">
                            </td>
                            <td>{{ $rfp->items->uom ?? "-" }}</td>
                            <td>
                                <button type="button" onclick="deleteItem({{ $rfp->id }})" class="btn btn-danger btn-sm">
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

