<div class="modal fade" id="modal_{{ $quality->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel_{{ $quality->id }}" 
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ url("admin/quality/" . $quality->prod_no)}}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel_{{ $quality->id }}">Product Nomer: {{ $quality->prod_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="check_{{ $quality->id }}">Quality Check</label>
                    <select name="check" id="check_{{ $quality->id }}" class="form-control" required>
                        <option value="">Select Assessment Quality</option>
                        <option value="1">OK</option>
                        <option value="2">NG</option>
                        <option value="3">Need Approval</option>
                    </select>
                    <input type="text" name="remark" id="remark_{{ $quality->id }}" class="form-control mt-2" placeholder=" Enter Remarks here" required>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>