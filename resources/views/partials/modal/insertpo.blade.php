<div class="modal fade" id="modal_{{ $bon->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel_{{ $bon->id }}" 
    aria-hidden="true">
    <div class="modal-dialog">
        <form id="insertPoForm_{{ $bon->id}}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel_{{ $bon->id }}">Nomer Bon: {{ $bon->no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="check_{{ $bon->id }}">Insert PO</label>
                    <input type="hidden" name="no_bon" id="nobon_{{ $bon->id }}" value="{{ $bon->no}}">
                    <input type="number" name="po" id="po_{{ $bon->id }}" class="form-control mt-2" placeholder="Enter No Purchase Order here" required>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>