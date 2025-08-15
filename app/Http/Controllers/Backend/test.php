@extends('backend.layouts.master')

@section('title')
Form Candidate Details
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .form-container {
        margin: 20px 0;
    }

    .breadcrumb-section {
        margin-bottom: 15px;
    }

    .section-title {
        margin-top: 20px;
        font-weight: bold;
        font-size: 1.3rem;
        color: #000;
        margin-bottom: 25px;
    }

    .form-container .card {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
    }

    .form-container .card-header {
        background-color: white;
        color: #6a7a8c;
        text-align: center;
    }

    .form-container .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: bold;
    }

    .form-group .form-control {
        border: none;
        border-bottom: 1px solid #ddd;
        border-radius: 0;
        padding: 10px 5px;
        width: 100%;
    }

    .signature-section {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
    }

    .signature-box {
        width: 25%;
        text-align: center;
        padding: 10px;
    }

    .signature-title {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .signature-placeholder {
        height: 65px;
        border-bottom: 1px solid #000;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .signature-image {
        max-width: 50%;
        height: auto;
    }

    .card-footer {
        text-align: center;
        margin-top: 20px;
    }

    .mini-sidebar {
        width: 80px;
        /* Adjust as per your design */
    }

    .mini-sidebar .data-sidebartype {
        display: none;
        /* or another appropriate change */
    }


    @media print {
        body {
            font-size: 12px;
        }

        .form-container {
            margin: 0;
        }

        .form-group {
            display: flex;
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 14px;
        }

        .form-group .form-control {
            font-size: 14px;
            border: none;
            border-bottom: 1px solid #000;
        }

        .card-header,
        .card-footer {
            display: none;
        }

        .form-container .card {
            box-shadow: none;
            border: none;
        }

        .signature-placeholder {
            height: 65px;
            border-bottom: 1px solid #000;
        }

        .btn {
            display: none;
        }
    }
</style>
@endsection

@section('admin-content')
<div class="container-fluid">
    @include('backend.layouts.partials.messages')
    @include('backend.layouts.partials.notif', [
    'modalId' => 'alertApproval',
    'tittle' => '',
    'message' => ''
    ])
    @include('backend.layouts.partials.notif2', [
    'modalId2' => 'alertApproval2',
    'tittle' => '',
    'message' => ''
    ])

    <div class="form-container">
        <div class="card">


            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="text-left">
                    <img src="{{ asset('public/assets/images/logo/logo-sanwamas.png') }}" alt="Logo-Sanwa" width="200" class="mb-2" />
                    <p class="mb-0" style="color:#000;">Jl. Raya Bekasi KM. 27, K.A Bungur Pondok Ungu Bekasi</p>
                </div>
                <div class="text-center flex-grow-1">
                    <h2 class="mb-0" style="font-weight: 450; color: #000;">NEW EMPLOYEE REQUEST FORM</h2>
                </div>
            </div>

            <div class="card-body">
                <!-- Form Fields -->

                <h3 class="section-title">Description</h3>
                <div class="d-flex">
                    <div class="flex-fill mr-3">
                        <div class="form-group row">
                            <label for="doc_number" class="col-md-3">Document Number:</label>
                            <div class="col-md-8">
                                <input type="text" id="doc_number" class="form-control" value="{{ $category->doc_number }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="request_date" class="col-md-3">Request Date:</label>
                            <div class="col-md-8">
                                <input type="text" id="request_date" class="form-control" value="{{ $category->request_date }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="request_by" class="col-md-3">Request Name:</label>
                            <div class="col-md-8">
                                <input type="text" id="request_by" class="form-control" value="{{ $category->request_by }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="department_name" class="col-md-3">Department Name:</label>
                            <div class="col-md-8">
                                <input type="text" id="department_name" class="form-control" value="{{ $category->department_name }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="applied_to_position" class="col-md-3">For Position:</label>
                            <div class="col-md-8">
                                <input type="text" id="applied_to_position" class="form-control" value="{{ $category->applied_to_position }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="form-group row">
                            <label for="level" class="col-md-3">Level:</label>
                            <div class="col-md-8">
                                <input type="text" id="level" class="form-control" value="{{ $category->level }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-md-3">Status:</label>
                            <div class="col-md-8">
                                <input type="text" id="status" class="form-control" value="{{ $category->status }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="priority" class="col-md-3">Priority:</label>
                            <div class="col-md-8">
                                <input type="text" id="priority" class="form-control" value="{{ $category->priority }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="qty_request" class="col-md-3">Head Count:</label>
                            <div class="col-md-8">
                                <input type="text" id="qty_request" class="form-control" value="{{ $category->qty_request }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="site" class="col-md-3">Site:</label>
                            <div class="col-md-8">
                                <input type="text" id="site" class="form-control" value="{{ $category->site == '01' ? 'West Bekasi' : 'East Surabaya' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="section-title">Requirements</h3>
                <div class="d-flex">
                    <div class="flex-fill mr-3">
                        <div class="form-group row">
                            <label for="education_min" class="col-md-3">Education Min:</label>
                            <div class="col-md-8">
                                <input type="text" id="education_min" class="form-control" value="{{ $category->education_min }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="education_max" class="col-md-3">Education Max:</label>
                            <div class="col-md-8">
                                <input type="text" id="education_max" class="form-control" value="{{ $category->education_max }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="experience" class="col-md-3">Experience:</label>
                            <div class="col-md-8">
                                <input type="text" id="experience" class="form-control" value="{{ $category->experience }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="form-group row">
                            <label for="gender" class="col-md-3">Gender:</label>
                            <div class="col-md-8">
                                <input type="text" id="gender" class="form-control" value="{{ $category->gender }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="age" class="col-md-3">Age:</label>
                            <div class="col-md-8">
                                <input type="text" id="age" class="form-control" value="{{ $category->age }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="skills" class="col-md-3">Skill:</label>
                            <div class="col-md-8">
                                @php
                                // Check if $category->skills is an array or a string
                                $skillsText = is_array($category->skills) ? implode(', ', $category->skills) : $category->skills;

                                // Use regex to add line breaks before numbers followed by a dot (e.g., "2. ", "3. ", etc.) but skip the first number
                                $formattedSkills = preg_replace('/(?<!^)(\d+)\.\s* /', "\n$1. " , $skillsText);
                                    @endphp
                                    <textarea id="skills" class="form-control" rows="3" readonly style="white-space: pre-line;">{{ $formattedSkills }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Uraian Section -->
                <h3 class="section-title">Notes</h3>
                <div class="d-flex">
                    <div class="flex-fill mr-3">
                        <div class="form-group row">
                            <label for="reason" class="col-md-3">Reason:</label>
                            <div class="col-md-9">
                                <textarea id="reason" class="form-control" rows="3" readonly>{{ $category->reason }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="form-group row">
                            <label for="desc_reason" class="col-md-3">Description Reason:</label>
                            <div class="col-md-9">
                                <textarea id="desc_reason" class="form-control" rows="3" readonly>{{ $category->desc_reason }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signatures Section -->
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-title">Requestor Signature, </div>
                        <div class="signature-placeholder">
                            @if ($signature && $signature->sign)
                            <img src="{{ asset('storage/assets/images/sign/'. $signature->sign) }}" alt="User Signature" width="200" class="mb-2" />
                            @else
                            No signature available.
                            @endif
                        </div>
                        <div>{{ $category->request_by }}</div>
                        <div>{{ $category->department_name }}</div>
                    </div>

                    <div class="signature-box">
                        <div class="signature-title">Approved Signature, </div>
                        <div class="signature-placeholder">
                            @if ($approveValue === 0 && !$nameApp2)
                            <img src="{{ asset('storage/assets/images/rejected.png') }}" alt="Management Signature" width="200" class="mb-3">
                            @elseif ($signatureApp && $signatureApp->sign)
                            <img src="{{ asset('storage/assets/images/sign/'. $signatureApp->sign) }}" alt="Management Signature" width="200" class="signature1 mb-2">
                            @else
                            <div>No signature available.</div>
                            @endif
                        </div>
                        <div>
                            @if ($nameApp && $nameApp->first_name)
                            {{ $nameApp->first_name . ' ' . $nameApp->last_name }}
                            @else
                            No name available
                            @endif
                        </div>
                        <div>
                            @if ($nameApp && $nameApp->department_name)
                            {{ $nameApp->department_name }}
                            @else
                            -
                            @endif
                        </div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-title">Approved Signature, </div>
                        <div class="signature-placeholder">
                            @if ($approveValue === 0 && $nameApp2)
                            <img src="{{ asset('storage/assets/images/rejected.png') }}" alt="Management Signature" width="200" class="mb-3">
                            @elseif ($signatureApp2 && $signatureApp2->sign)
                            <img src="{{ asset('storage/assets/images/sign/'. $signatureApp2->sign) }}" alt="Management Signature" width="200" class="mb-2">
                            @else
                            <div>No signature available.</div>
                            @endif
                        </div>
                        <div>
                            @if ($nameApp2 && $nameApp2->first_name)
                            {{ $nameApp2->first_name . ' ' . $nameApp2->last_name }}
                            @else
                            No name available
                            @endif
                        </div>
                        <div>
                            @if ($nameApp2 && $nameApp2->department_name)
                            {{ $nameApp2->department_name }}
                            @else
                            -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="card-footer" id="no-print">
                @if (!$roles->contains('Users'))
                <button class="btn btn-info" data-category-id="{{ $category->id }}" onclick="approveAll();"><i class="fa fa-check"> Approve Form</i></button>
                <button class="btn btn-danger" data-category-id="{{ $category->id }}"><i class="fa fa-times"> Reject Form</i></button>
                @endif
                <button class="btn btn-success" onclick="saveForm();"><i class="fa fa-print"> Print Form</i></button>
                <button class="btn btn-dark ml-2" onclick="window.history.back();"><i class="fa fa-arrow-left"> Back to Page</i></button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    function saveForm() {
        const noPrint = document.getElementById('no-print');
        noPrint.style.display = 'none';

        window.print();

        noPrint.style.display = 'block';
    }

    // Action when approve button is clicked
    function approveAll() {
        const approveButton = document.querySelector('.btn-info');

        if (approveButton) {
            approveButton.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category-id');
                const csrfToken = '{{ csrf_token() }}';
                const signatureImage = document.querySelector('.signature1');
                const url = signatureImage && signatureImage.getAttribute('src') ?
                    '/category/' + categoryId + '/approve2' :
                    '/category/' + categoryId + '/approve';
                const url1 = '/category/' + categoryId + '/approve';
                const url2 = '/category/' + categoryId + '/approve2';

                $('#alertApproval2').modal('show');

                // Handle confirmation button click inside modal
                document.getElementById('confirmApprovalButton').addEventListener('click', function() {
                    Promise.all([
                            fetch(url1, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({})
                            }),
                            fetch(url2, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({})
                            })
                        ])
                        .then(responses => Promise.all(responses.map(res => res.json())))
                        .then(([data1, data2]) => {
                            if (data1.success && data2.success) {
                                document.querySelector('.modal-title').innerText = 'Success';
                                document.querySelector('.modal-body').innerText = 'Both approvals were successful!';
                                $('#alertApproval').modal('show');

                                setTimeout(() => {
                                    window.location.href = '/admin/contacts';
                                }, 2000);
                            } else {
                                alert('Error: One or both approvals failed.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while approving.');
                        });

                    $('#alertApproval2').modal('hide');
                });

                // If the user cancels, fetch only the single URL
                document.querySelector('.btn-warning').addEventListener('click', function() {
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector('.modal-title').innerText = 'Success';
                                document.querySelector('.modal-body').innerText = data.success;
                                $('#alertApproval').modal('show');

                                setTimeout(() => {
                                    window.location.href = '/admin/contacts';
                                }, 4000);

                            } else {
                                alert('Error: ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred.');
                        });

                    // Hide the confirmation modal
                    $('#alertApproval').modal('hide');
                });
            });
        } else {
            console.log("Approve button not found because user is not an Admin");
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // set mini-sidebar
        setTimeout(function() {
            const currentPage = window.location.pathname;
            const mainWrapper = document.getElementById('main-wrapper');

            if (currentPage.includes('/admin/categories/')) {
                mainWrapper.setAttribute('data-sidebartype', 'mini-sidebar');
            } else {
                mainWrapper.setAttribute('data-sidebartype', 'full');
            }
        }, 1000);

        const rejectButton = document.querySelector('.btn-danger');
        if (rejectButton) {
            rejectButton.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category-id');
                const csrfToken = '{{ csrf_token() }}';

                fetch('/category/' + categoryId + '/reject', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector('.modal-title').innerText = 'Success';
                            document.querySelector('.modal-body').innerText = data.success;

                            $('#alertApproval').modal('show');

                            setTimeout(() => {
                                window.location.href = '/admin/contacts'
                            }, 2000);
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occured.');
                    })
            });
        } else {
            console.log("Reject button not found bcs not Admin user")
        }
    });
</script>
@endsection