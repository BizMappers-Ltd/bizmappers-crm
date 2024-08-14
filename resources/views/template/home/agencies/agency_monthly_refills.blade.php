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
                    <h4 class="card-title mr-4 mt-2">Refill Applications of {{ $agency->agency_name }}</h4>

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
                                <th>Date Time</th>
                                <th>Ad Account Name</th>
                                <th>Dollar Rate</th>
                                <th>Amount (Dollar)</th>
                                <th>Amount (Taka)</th>
                                <th>Method</th>
                                <th>Responsible</th>
                                
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
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        @include('template.home.layouts.footer')
        @include('template.home.layouts.scripts')
        @include('template.home.custom_scripts.search_script')


    </div>
</body>

</html>