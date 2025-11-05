<div class="modal fade" id="modal_{{ $bon->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalLabel_{{ $bon->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form id="insertPoForm_{{ $bon->id }}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel_{{ $bon->id }}">Nomer Bon: {{ $bon->no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="no_bon" id="nobon_{{ $bon->id }}" value="{{ $bon->no }}">
                    <label class="col-sm-16 col-form-label">Insert Purchase Order :</label>
                    <div class="col-sm-16 row">
                        <div class="col-lg-4 col-sm-12 mb-2">
                            <select name="series" class="form-control" id="seriesSelect_{{ $bon->id }}"
                                required></select>
                        </div>
                        <div class="col-lg-8 col-sm-12">
                            <select name="po" id="po_{{ $bon->id }}" class="form-control"
                                data-docnum="{{ $po ?? '' }}" data-docentry="{{ $docEntry ?? '' }}" required>
                            </select>
                            <small class="text-muted">Memilih series akan mempermudah pencarian data PO yang
                                sesuai.</small>
                        </div>
                    </div>
                    {{-- <input type="number" name="po" id="po_{{ $bon->id }}" class="form-control mt-2" placeholder="Enter No Purchase Order here" required> --}}
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
    <script>
        $("#seriesSelect_{{ $bon->id }}").select2({
            placeholder: "Choose Series",
            allowClear: true,
            width: "100%",
            dropdownParent: $("#modal_{{ $bon->id }}"), // 👈 tambahkan ini
            language: {
                inputTooShort: function() {
                    return "Type series for searching...";
                },
                noResults: function() {
                    return "Data not found";
                },
                searching: function() {
                    return "Stiil searching...";
                },
            },
            ajax: {
                url: "/purchasing/seriesSearch",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        ObjectCode: '22'
                    };
                },
                processResults: function(data) {
                    console.log("Response dari server:", data);
                    return {
                        results: (data.results || []).map(item => ({
                            id: item.id,
                            text: item.text
                        }))
                    };
                }
            }
        });

        $("#po_{{ $bon->id }}").select2({
            placeholder: "Select No Purchase Order",
            allowClear: true,
            width: "100%",
            dropdownParent: $("#modal_{{ $bon->id }}"), // 👈 ini juga penting
            minimumInputLength: 3,
            language: {
                inputTooShort: function() {
                    return "Type min 3 character";
                },
                noResults: function() {
                    return "Data not found";
                },
                searching: function() {
                    return "Still searching...";
                }
            },
            ajax: {
                url: "/purchaseOrderSearch",
                dataType: "json",
                delay: 600,
                data: function(params) {
                    const seriesData = $("#seriesSelect_{{ $bon->id }}").select2('data');
                    const series = seriesData.length > 0 ? seriesData[0].id : null;

                    return {
                        q: params.term,
                        limit: 5,
                        series: series,
                        status: "Open",
                    };
                },
                processResults: function(data) {
                    tempPoData = data.po || [];
                    return {
                        results: (data.results || []).map(item => ({
                            id: item.docnum,
                            text: item.text,
                        }))
                    };
                },
                cache: true
            }
        });
    </script>
@endpush
