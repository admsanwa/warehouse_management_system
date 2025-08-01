<div class="modal fade" id="modal_{{ $delivery->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel_{{ $delivery->id }}"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ url("admin/delivery/estimate/" . $delivery->prod_no)}}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel_{{ $delivery->id }}">Product Nomer: {{ $delivery->prod_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="status_{{ $delivery->id }}">Delivery Check</label>
                    <select name="status" id="status_{{ $delivery->id }}" class="form-control" required>
                        <option value="">Select Tracking Delivery</option>
                        <option value="Pick Up">Pick Up</option>
                        <option value="On Delivery">On Delivery</option>
                        <option value="Done">Done</option>
                    </select>
                    <input type="datetime-local" name="date" id="date_{{ $delivery->id }}" class="form-control mt-2" required>
                    <input type="text" name="remark" id="remark_{{ $delivery->id }}" class="form-control mt-2" placeholder="Enter Remarks here" required>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>