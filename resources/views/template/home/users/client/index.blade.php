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
                    <h4 class="card-title mr-4 mt-2">All Clients</h4>
                    <a href="{{ route('register') }}">
                        <button class="btn btn-sm btn-secondary text-white">Add New Client<i class="fa fa-plus color-muted m-r-5 ml-2"></i></button>
                    </a>
                </div>

                <!-- Search Field -->
                <div class="mb-3 w-25">
                    <input type="text" id="searchInput" class="form-control rounded" placeholder="Search...">
                </div>

                @if ($users->hasMorePages())
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
                                <th>Name</th>
                                <th>Username</th>
                                <th>Business Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="clientsTableBody">
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->business_name }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="d-flex align-items-center">
                                        <a href="{{ route('adaccount.adaccount', $user->id) }}" data-toggle="tooltip" data-placement="top" title="Add New Ad Account">
                                            <i class="fa fa-plus color-muted m-r-5 ml-2"></i>
                                        </a>
                                        <a href="{{ route('client.show', $user->id) }}" data-toggle="tooltip" data-placement="top" title="View">
                                            <i class="fa fa-eye color-muted m-r-5 ml-3"></i>
                                        </a>
                                        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                                        <a href="{{ route('client.edit', $user->id) }}" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="fa fa-pencil color-muted m-r-5 ml-3"></i>
                                        </a>
                                        @endif
                                        @if (auth()->user()->role == 'admin')
                                        <div class="basic-dropdown ml-2">
                                            <div class="dropdown">
                                                <i class="fa-solid fa-ellipsis btn btn-sm" data-toggle="dropdown"></i>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item">
                                                        <form action="{{ route('client.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Client?')">Delete</button>
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
                    @if ($users->hasMorePages())
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        let page = 2; // Start from the second page since the first page is already loaded

        $('#loadMoreButton').click(function() {
            $.ajax({
                url: "{{ route('clients.load-more') }}",
                type: 'GET',
                data: { page: page },
                beforeSend: function() {
                    $('#loadMoreButton').text('Loading...');
                },
                success: function(response) {
                    if (response) {
                        $('#clientsTableBody').append(response);
                        page++;
                        $('#loadMoreButton').text('Load More');

                        // Hide the button if no more pages to load
                        if (page > {{$users->lastPage()}}) {
                            $('#loadMoreButton').hide();
                            $('#viewAllButton').hide(); // Hide the View All button if all pages are loaded
                        }
                    } else {
                        $('#loadMoreButton').text('No More Records');
                    }
                },
                error: function() {
                    $('#loadMoreButton').text('Load More');
                }
            });
        });

        $('#viewAllButton').click(function() {
            $.ajax({
                url: "{{ route('clients.view-all') }}",
                type: 'GET',
                beforeSend: function() {
                    $('#viewAllButton').text('Loading...');
                },
                success: function(response) {
                    if (response) {
                        $('#clientsTableBody').html(response);
                        $('#loadMoreButton').hide(); // Hide the Load More button
                        $('#viewAllButton').hide(); // Hide the View All button
                    } else {
                        $('#viewAllButton').text('No More Records');
                    }
                },
                error: function() {
                    $('#viewAllButton').text('View All');
                }
            });
        });
    });
</script>


</body>

</html>