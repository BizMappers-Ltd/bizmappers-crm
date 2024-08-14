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
                    <h4 class="card-title mr-4 mt-2">Pending Refill Applications <span>( {{$refillCount}} )</span></h4>

                </div>

                <!-- Search Field -->
                <div class="w-25 mb-3">
                    <input type="text" id="searchInput" class="form-control rounded" placeholder="Search...">
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
                                <th>Date || Time</th>
                                <th>Ad Account Name</th>
                                <th>Agency Name</th>
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
                                <td>{{ $refill->adAccount->agency->agency_name }}</td>
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


        </div>

        @include('template.home.layouts.footer')
        @include('template.home.layouts.scripts')
        @include('template.home.custom_scripts.refill_application_script')
        @include('template.home.custom_scripts.search_script')

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script>
            $(document).ready(function() {

                $(document).on('click', '.load-more', function() {
                    var page = $(this).data('page');
                    loadMoreData(page);
                });

                function loadMoreData(page) {
                    $.ajax({
                        url: "/refills?page=" + page,
                        type: "get",
                        beforeSend: function() {
                            $('.load-more').html('Loading...');
                        }
                    }).done(function(data) {
                        if (data.html == "") {
                            $('.load-more').html('No more records found');
                            return;
                        }
                        $('.load-more').remove();
                        $('#table-body').append(data);
                    }).fail(function(jqXHR, ajaxOptions, thrownError) {
                        alert('Server error');
                    });
                }
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