<!DOCTYPE html>
<html lang="en">

<head>
    @include('template.home.layouts.head')
    @include('template.home.custom_styles.custom_style')
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
</head>

<body>
    @include('template.home.layouts.navbar')
    @include('template.home.layouts.sidebar')

    <div class="content-body p-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="card-title mr-4 mt-2">Refill Applications <span>( {{$refillCount}} )</span></h4>

                    <a href="#" data-toggle="modal" data-target="#refillModal">
                        <button class="btn btn-sm btn-secondary text-white">New Refill<i class="fa fa-plus color-muted m-r-5 ml-2"></i></button>
                    </a>
                </div>

                <!-- Search Field -->
                <div class="w-25 mb-3">
                    <input type="text" id="searchInput" class="form-control rounded" placeholder="Search...">
                </div>

                <div>
                    <form action="{{ route('refills.date.generate') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <div class="input-group date" id="startDatePicker">
                                    <input type="text" class="form-control" name="start_date" placeholder="Start Date" required>
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group date" id="endDatePicker">
                                    <input type="text" class="form-control" name="end_date" placeholder="End Date" required>
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Date Range Search</button>
                            </div>
                        </div>
                    </form>
                </div>

                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-striped verticle-middle" id="refillTable">
                        <thead>
                            <tr>
                                <th>Date Time</th>
                                <th>Ad Account Name</th>
                                <th>Dollar Rate</th>
                                <th>Amount (Dollar)</th>
                                <th>Amount (Taka)</th>
                                <th>Method</th>
                                <th>Responsible</th>
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager' || auth()->user()->role == 'employee')
                                <th></th>
                                @endif
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @foreach ($refills as $refill)
                            <tr>
                                <td>{{ $refill->created_at->format('j F Y  g:i a') }}</td>
                                <td>{{ $refill->adAccount->ad_acc_name }}</td>
                                <td>{{ $refill->adAccount->dollar_rate }}</td>
                                <td>{{ $refill->amount_dollar }}</td>
                                <td>{{ $refill->amount_taka }}</td>
                                <td>{{ $refill->payment_method }}</td>
                                <td>{{ $refill->assign }}</td>
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager' || auth()->user()->role == 'employee')
                                <td class="text-center">
                                    @if ($refill->sent_to_agency == 0 && $refill->payment_method != 'Transferred')
                                    <form id="sendToAgencyForm_{{ $refill->id }}" style="display:inline-block;">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-primary" onclick="sendToAgency({{ $refill->id }})">
                                            Send to Agency
                                        </button>
                                    </form>
                                    @else
                                    <span class="badge custom-badge-success" id="buttonText_{{ $refill->id }}">Sent</span>
                                    @endif
                                </td>
                                @endif
                                <td>
                                    @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager' || auth()->user()->role == 'employee')
                                    <form id="updateStatusForm_{{ $refill->id }}" action="{{ route('refills.updateStatus', $refill->id) }}" method="post">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select-sm custom-status" style="width: 90px;" onchange="updateStatus({{ $refill->id }}, this.value)">
                                            <option value="pending" {{ $refill->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $refill->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ $refill->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </form>
                                    @elseif(auth()->user()->role == 'customer')
                                    @if ($refill->status == 'pending')
                                    <span class="badge custom-badge-info">Pending</span>
                                    @endif
                                    @if ($refill->status == 'approved')
                                    <span class="badge custom-badge-success">Approved</span>
                                    @endif
                                    @if ($refill->status == 'rejected')
                                    <span class="badge badge-danger px-3 py-1">Rejected</span>
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="d-flex align-items-center">
                                        <a href="{{ route('refills.show', $refill->id) }}" data-toggle="tooltip" data-placement="top" title="View">
                                            <i class="fa fa-eye color-muted m-r-5"></i>
                                        </a>
                                        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                                        <a href="{{ route('refills.edit', $refill->id) }}" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="fa fa-pencil color-muted m-r-5 ml-3"></i>
                                        </a>
                                        @endif
                                        @if (auth()->user()->role == 'admin')
                                        <div class="basic-dropdown ml-2">
                                            <div class="dropdown">
                                                <i class="fa-solid fa-ellipsis btn btn-sm" data-toggle="dropdown"></i>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item">
                                                        <form action="{{ route('refills.destroy', $refill->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Refill Application?')">Delete</button>
                                                        </form>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- refill modal -->

            <div class="modal fade" id="refillModal" tabindex="-1" role="dialog" aria-labelledby="refillModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="refillModalLabel">New Refill</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('refill.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div>
                                    <label class="col-form-label">Client Name:</label>
                                    <select id="client-select" name="client_id" class="form-control rounded">
                                        <option>Select</option>
                                        @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="col-form-label">Ad Account Name:</label>
                                    <select id="ad-account-select" name="ad_account_id" class="form-control rounded">
                                        <option>Select</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="col-form-label">Dollar Rate:</label>
                                    <input id="dollar-rate-input" type="text" placeholder="Dollar Rate" class="form-control rounded" readonly>
                                </div>

                                <div>
                                    <label class="col-form-label">Amount:</label><br>

                                    <div class="d-flex justify-content-between">
                                        <div class="w-50 mr-2">
                                            <input id="taka-input" type="text" name="amount_taka" placeholder="Taka" class="form-control rounded">
                                        </div>
                                        <div class="w-50">
                                            <input id="dollar-input" type="text" name="amount_dollar" placeholder="Dollar" class="form-control rounded">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="col-form-label">Payment Method</label>
                                    <select id="payment_method" name="payment_method" class="form-control rounded">
                                        <option>Select</option>
                                        @foreach ($paymentMethods as $paymentMethod)
                                        <option value="{{ $paymentMethod->value }}" data-details="{{ $paymentMethod->details }}">{{ $paymentMethod->value }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="details" class="d-none">
                                    <p class="col-form-label font-bold">Payment Method Details: </p>
                                    <p id="payment_details"></p>
                                </div>

                                <div>
                                    <label class="col-form-label">Transaction Id:</label>
                                    <input type="text" name="transaction_id" placeholder="Transaction Id" class="form-control rounded">
                                </div>

                                <div class="mt-2">
                                    <label class="col-form-label">Screenshot:</label>
                                    <div class="custom-file">
                                        <input type="file" id="screenshot" name="screenshot" class="custom-file-input">
                                        <label class="custom-file-label">Choose file</label>
                                    </div>
                                </div>

                                <div>
                                    <label class="col-form-label">Date</label>
                                    <input type="date" name="new_date" class="form-control rounded w-50">
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <input type="submit" name="submit" value="Refill" class="btn btn-primary">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('template.home.layouts.footer')
        @include('template.home.layouts.scripts')
        @include('template.home.custom_scripts.refill_application_script')
        @include('template.home.custom_scripts.search_script')

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#startDatePicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });
                $('#endDatePicker').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true
                });

            });

            function sendToAgency(refillId) {

                const formId = `sendToAgencyForm_${refillId}`;
                const form = document.getElementById(formId);
                const token = form.querySelector('input[name="_token"]').value;

                fetch(`{{ url('refill/${refillId}/send-to-agency') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            form.innerHTML = '<span class="badge custom-badge-success" id="buttonText_' + refillId + '">Sent</span>';

                        } else {
                            alert('There was an error sending the deposit to the agency.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function updateStatus(refillId, status) {
                $.ajax({
                    url: '/refills/' + refillId + '/status',
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(response) {
                        alert('Status updated successfully.');
                    },
                    error: function(xhr) {
                        alert('An error occurred while updating the status.');
                    }
                });
            }
        </script>

    </div>
</body>

</html>