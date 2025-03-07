@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="container mt-5">
                    

                    
    
                    <!-- jQuery and DataTables JS -->
                    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
                    <!-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> -->
                    <!-- Bootstrap JS -->
                    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->


                    
                    
                    <!-- Success Message -->
                    <div id="success-message" class="alert alert-success d-none"></div>

                </div>

                <div class="container mt-5">
                    <h2>Appraisal Data</h2>
                    <button id="syncButton" class="btn btn-primary mb-3" style="display: block;">Sync Appraisal Users</button>
                    <table id="appraisalTable" class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Employee Name</th>
                                <th>Designation</th>
                                <th>Reporting Officer</th>
                                <th>Appraiser Officer</th>
                            </tr>
                        </thead>
                    </table>

    <!-- Bootstrap Modal -->
    <!-- Modal -->
    <!-- Modal -->
                    <div class="modal fade" id="appraisalModal" tabindex="-1" aria-labelledby="appraisalModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="appraisalModalLabel">Synced Appraisal Users</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="appraisalForm" method="POST">
                                        <table id="appraisalDataTable" class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="selectAll" checked> <!-- Select All Checkbox -->
                                                    </th>
                                                    <th>Employee Name</th>
                                                    <th>Employee Code</th>
                                                    <th>Designation</th>
                                                    <th>Department</th>
                                                    <th>Reporting Officer</th>
                                                    <th>Appraiser Officer</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data will be appended dynamically -->
                                            </tbody>
                                        </table>
                                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>

                <script>
                    $(document).ready(function () {

                        $('#appraisalTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: "{{ route('appraisaldata') }}", // Ensure this matches your named route
                            columns: [
                                { data: 'employee_name', name: 'employee_name' },
                                { data: 'designation', name: 'designation' },
                                { data: 'reporting_officer_name', name: 'reporting_officer_name' },
                                { data: 'appraiser_officer_name', name: 'appraiser_officer_name' },
                            ],
                            pageLength: 10, // Show 10 records per page
                            "initComplete": function(settings, json) {
                                if (json.data.length === 0) {
                                    //$('#syncButton').show();
                                }
                            }
                        });

                        $('#syncButton').click(function() {
                            $.ajax({
                                url: "{{ route('syncappraisalusers') }}",
                                type: "POST",
                                data: {_token: "{{ csrf_token() }}"},
                                success: function(response) {
                                    alert("Data synced successfully!");
                                    fetchSyncedUsers(); // Fetch synced users after syncing
                                },
                                error: function(xhr) {
                                    alert("Error syncing data.");
                                }
                            });
                        });

                        function fetchSyncedUsers() {
                            $.ajax({
                                url: "{{ route('getSyncedAppraisalUsers') }}",
                                type: "GET",
                                success: function(data) {
                                    let tableBody = $("#appraisalDataTable tbody");
                                    tableBody.empty(); // Clear existing data
                                    
                                    data.forEach(user => {
                                        let row = `<tr>
                                            <td>
                                                <input type="checkbox" class="user-checkbox" name="selected_users[]" value="${user.id}" checked>
                                            </td>
                                            <td>${user.username}</td>
                                            <td>${user.employee_code}</td>
                                            <td>${user.designation}</td>
                                            <td>${user.department_name}</td>
                                            <td>${user.reporting_officer_name}</td>
                                            <td>${user.appraiser_officer_name}</td>
                                        </tr>`;
                                        tableBody.append(row);
                                    });

                                    // Initialize or reinitialize DataTable
                                    if ($.fn.DataTable.isDataTable("#appraisalDataTable")) {
                                        $("#appraisalDataTable").DataTable().destroy();
                                    }
                                    $("#appraisalDataTable").DataTable({
                                        paging: false
                                    });

                                    // Show modal
                                    $("#appraisalModal").modal("show");
                                },
                                error: function(xhr) {
                                    alert("Error fetching data.");
                                }
                            });
                        }

                        $(document).on('change', '#selectAll', function () {
                            $(".user-checkbox").prop("checked", this.checked);
                        });

                        $("#appraisalForm").submit(function(e) {
                            e.preventDefault();

                            let selectedUsers = [];
                            $(".user-checkbox:checked").each(function() {
                                selectedUsers.push($(this).val());
                            });

                            if (selectedUsers.length === 0) {
                                alert("Please select at least one user.");
                                return;
                            }

                            $.ajax({
                                url: "{{ route('storeAppraisalUsers') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    users: selectedUsers
                                },
                                success: function(response) {
                                    alert("Data submitted successfully!");
                                    $("#appraisalModal").modal("hide");
                                    location.reload();
                                },
                                error: function(xhr) {
                                    alert("Error submitting data.");
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