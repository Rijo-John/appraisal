@extends('layouts.app')
@section('content')
<main class="col  ms-sm-auto  content-wrapper-no-left-tab">
    <div class="row align-items-center mb-3">
        <div class="col">
            <h3 class="heading-color mb-0">Appraisal Data</h3>
        </div>
        <div class="col-auto">

            <button id="syncButton" class="btn btn-primary " >Sync Appraisal Users<div class="loader ms-2" style="display: none;"></div></button>
        </div>
    </div>
    
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col">

                
                
                    
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
                    
                    <div id="success-message" class="alert alert-success d-none"></div>

             

                <div class="container">
                    
                    
                    <table id="appraisalTable" class="table table-bordered">
                        <thead >
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
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="appraisalModalLabel">Synced Appraisal Users</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="appraisalForm" method="POST">
                                <div class="modal-body" style="max-height: calc(100vh - 173px);">
                                    
                                        <table id="appraisalDataTable" class="table table-bordered">
                                            <thead>
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
                                        
                                  
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                                </div>  
                            </form>
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
                            let loader = $(this).find('.loader');
                            loader.show();
                            $.ajax({
                                url: "{{ route('syncappraisalusers') }}",
                                type: "POST",
                                data: {_token: "{{ csrf_token() }}"},
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'Data synced successfully!',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'OK'
                                    });
                                    fetchSyncedUsers();
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops!',
                                        text: 'Error syncing data. Please try again.',
                                        confirmButtonColor: '#d33',
                                        confirmButtonText: 'Close'
                                    });
                                },
                                complete: function() {
                                    // Hide loader when request completes (success or error)
                                    loader.hide();
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
</main>
@endsection