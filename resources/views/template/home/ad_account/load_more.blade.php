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