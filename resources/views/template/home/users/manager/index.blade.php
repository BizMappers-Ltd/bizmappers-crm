<!DOCTYPE html>
<html lang="en">

<head>
    @include('template.home.layouts.head')
    <style>
        .font-sm {
            font-size: 12px;
        }
    </style>
</head>

<body>

    @include('template.home.layouts.navbar')
    @include('template.home.layouts.sidebar')

    <div class="content-body p-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="card-title mr-4 mt-2">All Managers</h4>
                    <a href="{{ route('register.manager') }}">
                        <button class="btn btn-sm btn-secondary text-white">Add New Manager<i class="fa fa-plus color-muted m-r-5 ml-2"></i></button>
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
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>

                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>


                                <td>
                                    <span class="d-flex align-items-center">
                                        <a href="{{ route('manager.edit', $user->id) }}" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <i class="fa fa-pencil color-muted m-r-5 ml-3"></i>
                                        </a>

                                        <div class="basic-dropdown ml-2">
                                            <div class="dropdown">
                                                <i class="fa-solid fa-ellipsis btn btn-sm" data-toggle="dropdown"></i>

                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item">
                                                        <form action="{{ route('manager.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Manager?')">Delete</button>
                                                        </form>
                                                    </a>
                                                    <a class="dropdown-item">
                                                        <!-- Button trigger modal -->
                                                        <button type="button" class="btn btn-sm btn-primary change-role-btn" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-role="{{ $user->role }}">Change Role</button>
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

        <!-- Modal -->
        <div class="modal fade" id="changeRoleModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Role</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form id="changeRoleForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="d-flex">
                                <select name="role" id="roleSelect" class="form-control rounded mr-1">
                                    <option value="admin">Admin</option>
                                    <option value="employee">Employee</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary text-white" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('template.home.layouts.footer')
    @include('template.home.layouts.scripts')
    @include('template.home.custom_scripts.search_script')

    <script>
        $(document).ready(function() {
            $('.change-role-btn').on('click', function() {
                var userId = $(this).data('user-id');
                var userName = $(this).data('user-name');
                var userRole = $(this).data('user-role');

                $('#changeRoleModal .modal-title').text('Change Role for ' + userName);
                $('#roleSelect').val(userRole);
                $('#changeRoleForm').attr('action', '/users/' + userId + '/updateRole');

                $('#changeRoleModal').modal('show');
            });
        });
    </script>

</body>

</html>