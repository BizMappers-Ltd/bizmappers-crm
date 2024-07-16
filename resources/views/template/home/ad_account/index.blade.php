<!DOCTYPE html>
<html lang="en">

<head>
    @include('template.home.layouts.head')
    @include('template.home.custom_styles.custom_style')
</head>

<body>

    @include('template.home.layouts.navbar')
    @include('template.home.layouts.sidebar')

    <div class="content-body p-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="card-title mr-4 mt-2">Ad Account Applications</h4>
                    @if (auth()->user()->role == 'customer')
                    <a href="{{ route('adaccount.adaccount', auth()->user()->id) }}">
                        <button class="btn btn-sm btn-secondary text-white">New Application<i class="fa fa-plus color-muted m-r-5 ml-2"></i></button>
                    </a>
                    @else

                    <a href="{{ route('ad-account-application') }}">
                        <button class="btn btn-sm btn-secondary text-white">New Application<i class="fa fa-plus color-muted m-r-5 ml-2"></i></button>
                    </a>
                    @endif
                </div>

                <!-- Search Field -->
                <div class="mb-1 w-25">
                    <input type="text" id="searchInput" class="form-control rounded" placeholder="Search...">
                </div>

                @if ($adAccounts->hasMorePages())
                <div class="mb-3">
                    <button class="btn btn-sm btn-secondary text-white" id="viewAllButton">View All</button>
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <div class="table-responsive text-nowrap">

                    <table class="table table-bordered table-striped verticle-middle" id="refillTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager' || auth()->user()->role == 'employee')
                                <th>Client Name</th>
                                @endif
                                <th>Ad Account Name</th>
                                <th>Agency</th>
                                <th>Doller Rate</th>
                                <th>Responsible</th>
                                <th></th>
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager' || auth()->user()->role == 'employee')
                                <th>Status</th>
                                @endif
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="adAccountsTableBody">
                            @foreach ($adAccounts as $adAccount)
                            <tr>
                                <td>{{ $adAccount->created_at->format('j F Y') }}</td>
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager' || auth()->user()->role == 'employee')
                                <td>{{ $adAccount->client->name }}</td>
                                @endif
                                <td>
                                    <span>{{ $adAccount->ad_acc_name }}</span><br>
                                    <span class="font-sm mt-1">ID: {{ $adAccount->ad_acc_id }}</span>
                                </td>
                                <td>{{ $adAccount->agency->agency_name }}</td>
                                <td>{{ $adAccount->dollar_rate }}৳</td>
                                <td>{{ $adAccount->assign }}</td>
                                <td class="text-center">
                                    <span class="badge custom-badge-info {{ $adAccount->status == 'pending' ? '' : 'd-none' }}" id="badge-pending-{{ $adAccount->id }}">Pending</span>
                                    <span class="badge custom-badge-primary {{ $adAccount->status == 'in-review' ? '' : 'd-none' }}" id="badge-in-review-{{ $adAccount->id }}">In Review</span>
                                    <span class="badge custom-badge-success {{ $adAccount->status == 'approved' ? '' : 'd-none' }}" id="badge-approved-{{ $adAccount->id }}">Approved</span>
                                    <span class="badge badge-danger px-3 py-1 {{ $adAccount->status == 'rejected' ? '' : 'd-none' }}" id="badge-rejected-{{ $adAccount->id }}">Rejected</span>
                                    <span class="badge badge-warning px-3 py-1 text-white {{ $adAccount->status == 'canceled' ? '' : 'd-none' }}" id="badge-canceled-{{ $adAccount->id }}">Canceled</span>
                                </td>
                                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager' || auth()->user()->role == 'employee')
                                <td>
                                    <form id="status-form-{{ $adAccount->id }}" action="javascript:void(0);">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select-sm custom-status" style="width: 90px;" onchange="updateStatus({{ $adAccount->id }}, this.value)">
                                            <option value="pending" {{ $adAccount->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in-review" {{ $adAccount->status == 'in-review' ? 'selected' : '' }}>In Review</option>
                                            <option value="approved" {{ $adAccount->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="canceled" {{ $adAccount->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                            <option value="rejected" {{ $adAccount->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </form>
                                </td>
                                @endif
                                <td>
                                    <span class="d-flex align-items-center">
                                        <a href="{{ route('ad-account.show', $adAccount->id) }}" data-toggle="tooltip" data-placement="top" title="View">
                                            <i class="fa fa-eye color-muted m-r-5"></i>
                                        </a>
                                        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                                        <a href="{{ route('ad-account.edit', $adAccount->id) }}" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="fa fa-pencil color-muted m-r-5 ml-3"></i>
                                        </a>
                                        @endif
                                        @if(auth()->user()->role == 'admin')
                                        <div class="basic-dropdown ml-2">
                                            <div class="dropdown">
                                                <i class="fa-solid fa-ellipsis btn btn-sm" data-toggle="dropdown"></i>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item">
                                                        <form action="{{ route('ad-account.destroy', $adAccount->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Ad Account Application?')">Delete</button>
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

                    @if ($adAccounts->hasMorePages())
                    <div class="text-center mt-4">
                        <button class="btn btn-primary" id="loadMoreButton">Load More</button>
                    </div>
                    @endif


                </div>
            </div>

        </div>
    </div>

    @include('template.home.layouts.footer')

    @include('template.home.layouts.scripts')

    @include('template.home.custom_scripts.search_script')

    <script>
        $(document).ready(function() {
            let page = 2; // Start from the second page since the first page is already loaded

            $('#loadMoreButton').click(function() {
                loadMore();
            });

            $('#viewAllButton').click(function() {
                viewAll();
            });

            function loadMore() {
                $.ajax({
                    url: "{{ $loadMore }}",
                    type: 'GET',
                    data: {
                        page: page
                    },
                    beforeSend: function() {
                        $('#loadMoreButton').text('Loading...');
                    },
                    success: function(response) {
                        if (response) {
                            $('#adAccountsTableBody').append(response);
                            page++;
                            $('#loadMoreButton').text('Load More');

                            // Hide the buttons if no more pages to load
                            if (page > {{ $adAccounts->lastPage()}}) {
                                $('#loadMoreButton').hide();
                                $('#viewAllButton').hide();
                            }
                        } else {
                            $('#loadMoreButton').text('No More Records');
                        }
                    }
                });
            }

            function viewAll() {
                $.ajax({
                    url: "{{ $loadAll }}",
                    type: 'GET',
                    beforeSend: function() {
                        $('#viewAllButton').text('Loading...');
                    },
                    success: function(response) {
                        if (response) {
                            $('#adAccountsTableBody').html(response);
                            $('#loadMoreButton').hide();
                            $('#viewAllButton').hide();
                        } else {
                            $('#viewAllButton').text('No Records');
                        }
                    }
                });
            }
        });

        function updateStatus(adAccountId, status) {
            $.ajax({
                url: "{{ url('ad-account-application') }}/" + adAccountId + "/status-ajax",
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        // Hide all badges in the specific row
                        var row = $('#badge-pending-' + adAccountId).closest('tr');
                        row.find('.badge').addClass('d-none');
                        // Show the specific badge
                        $('#badge-' + status + '-' + adAccountId).removeClass('d-none');
                        alert(response.success);
                    }
                },
                error: function(xhr) {
                    alert('Something went wrong. Please try again.');
                }
            });
        }
    </script>


</body>

</html>