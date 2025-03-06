 @extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="container mt-5">
                    <h2>Assign Admin Role</h2>
                    
                    <!-- Success Message -->
                    <div id="success-message" class="alert alert-success d-none"></div>

                    <form id="assignAdminForm">
                        @csrf
                        <table id="usersTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Employee Code</th>
                                </tr>
                            </thead>
                        </table>

                        <button type="submit" class="btn btn-primary mt-3">Assign as Admin</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">Back to Dashboard</a>
                    </form>
                </div>

                <!-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> -->

                <script>
                    $(document).ready(function () {
                        // Initialize DataTable
                        let table = $('#usersTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: "{{ route('assign.admin.users') }}",
                            columns: [
                                { data: 'checkbox', orderable: false, searchable: false },
                                { data: 'username' },
                                { data: 'email' },
                                { data: 'emp_code' }
                            ]
                        });

                        // Select all checkboxes
                        $('#select-all').on('click', function () {
                            $('.user-checkbox').prop('checked', this.checked);
                        });

                        // Handle form submission via AJAX
                        $('#assignAdminForm').on('submit', function (e) {
                            e.preventDefault();

                            let selectedUsers = [];
                            $('.user-checkbox:checked').each(function () {
                                selectedUsers.push($(this).val());
                            });

                            if (selectedUsers.length === 0) {
                                alert("Please select at least one user.");
                                return;
                            }

                            $.ajax({
                                url: "{{ route('assign.admin.submit') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    user_ids: selectedUsers
                                },
                                success: function (response) {
                                    $('#success-message').text(response.success).removeClass('d-none');
                                    table.ajax.reload(); // Reload table
                                }
                            });
                        });
                    });
                </script>
            </div>
        </div>

    </div>
</div>
@endsection