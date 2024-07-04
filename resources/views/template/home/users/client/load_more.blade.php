@foreach ($users as $user)
<tr>
    <td>{{ $user->name }}</td>
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
