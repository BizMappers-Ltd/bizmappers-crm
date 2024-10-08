<!DOCTYPE html>
<html lang="en">

<head>
    @include('template.home.layouts.head')
    @include('template.home.custom_styles.custom_style')
</head>

<body>

    @include('template.home.layouts.navbar')
    @include('template.home.layouts.sidebar')

    <div class="content-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="card-title mr-4 mt-2">All Deposits</h4>

                                <a href="{{ route('deposit.create') }}">
                                    <button class="btn btn-sm btn-secondary text-white">New Deposit<i class="fa fa-plus color-muted m-r-5 ml-2"></i></button>
                                </a>
                            </div>

                            <!-- Search Field -->
                            <div class="mb-3 w-25">
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
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Amount (USD)</th>
                                            <th>Rate (BDT)</th>

                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deposits as $deposit)
                                        <tr>
                                            <td>{{ $deposit->created_at->format('j F Y') }}</td>
                                            <td>{{ $deposit->name }}</td>
                                            <td>{{ $deposit->amount_usd }}</td>
                                            <td>{{ $deposit->rate_bdt }}</td>

                                            <td>
                                                <form action="{{ route('deposit.updateStatus', $deposit->id) }}" method="post">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-select-sm custom-status" style="width: 90px;" onchange="this.form.submit()">
                                                        <option value="pending" {{ $deposit->status == 'pending' ? 'selected' : '' }}>Pending
                                                        </option>
                                                        <option value="received" {{ $deposit->status == 'received' ? 'selected' : '' }}>Received
                                                        </option>
                                                        <option value="canceled" {{ $deposit->status == 'canceled' ? 'selected' : '' }}>Canceled
                                                        </option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <span class="d-flex align-items-center">
                                                    <a href="{{ route('deposit.show', $deposit->id) }}" data-toggle="tooltip" data-placement="top" title="View">
                                                        <i class="fa fa-eye color-muted m-r-5"></i>
                                                    </a>

                                                    <a href="{{ route('deposit.edit', $deposit->id) }}" data-toggle="tooltip" data-placement="top" title="Edit">
                                                        <i class="fa fa-pencil color-muted m-r-5 ml-3"></i>
                                                    </a>

                                                    <div class="basic-dropdown ml-2">
                                                        <div class="dropdown">
                                                            <i class="fa-solid fa-ellipsis btn btn-sm" data-toggle="dropdown"></i>

                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item">
                                                                    <form action="{{ route('deposit.destroy', $deposit->id) }}" method="POST" style="display:inline-block;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Deposit?')">Delete</button>
                                                                    </form>
                                                                </a>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </span>

                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Last three months total deposit and average USD rate</h5>
                            @foreach ($averageRates as $rate)
                            <div class="row">
                                <b class="col-5">{{ \Carbon\Carbon::create()->month($rate->month)->format('F') }}
                                    ({{ $rate->year }})</b>
                                <p class="col-7">{{ $rate->total_usd }} ({{ number_format($rate->average_rate, 2) }})</p>

                            </div>
                            @endforeach

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
    </div>

    @include('template.home.layouts.footer')
    @include('template.home.layouts.scripts')
    @include('template.home.custom_scripts.search_script')

</body>

</html>