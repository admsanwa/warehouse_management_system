<div class="modal fade" id="modal_{{ $quality['DocEntry'] }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel_{{ $quality['DocEntry'] }}" 
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ url("admin/quality/" . $quality['DocEntry'])}}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel_{{ $quality['DocEntry'] }}">Product Description: {{ $quality['ItemName'] }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="check_{{ $quality['DocEntry'] }}">Quality Check</label>
                    <select name="check" id="check_{{ $quality['DocEntry'] }}" class="form-control" required>
                        <option value="">Select Assessment Quality</option>
                        @if ($user->department === "Production" )
                            <option value="5">Painting by Inhouse</option>
                            <option value="6">Painting by Makloon</option>
                        @else
                            <option value="1">OK</option>
                            <option value="2">NG</option>
                            @if ($user->department != "Production" || $user->nik != "06067")
                                <option value="3">Need Approval</option>
                                <option value="4">Need Paint</option>
                            @endif
                        @endif
                    </select>
                    <input type="text" name="remark" id="remark_{{ $quality['DocEntry'] }}" class="form-control mt-2" placeholder=" Enter Remarks here" required>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>