@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
        <div class="row mb-2">
            <div class="col-auto ms-auto">
           
            <select id="appraisalCycleDropdown" class="form-select me-2">
                <option value="">Select Appraisal Cycle</option>
                @foreach($appraisalCycles as $cycle)
                    <option value="{{ $cycle->id }}">{{ $cycle->appraisal_cycle }}</option>
                @endforeach
            </select>
        </div>
        

        <div class="col-auto">
           
            <button id="fetchAppraisalsButton" class="btn btn-light me-1">Search</button>
            <button id="exportAppraisalsButton" class="btn btn-success ms-3">Export to Excel</button>
            
            <button type="button" id="sentMailToAppraisee" class="btn btn-primary" data-bs-toggle="modal">
              Sent Mail
            </button>
             
        </div>
        
        </div>
        <div class="row">
            <div class="col">
                
    
                    
                    <div id="success-message" class="alert alert-success d-none"></div>
                    
                    
                    <table id="appraisalTable" class="table table-bordered">
                        <thead >
                            <tr>
                                <th>Employee Name</th>
                                <th>Designation</th>
                                <th>Appraiser Officer</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appraisals as $appraisal)
                                <tr>
                                    <td>{{ $appraisal->employee_name }}</td>
                                    <td>{{ $appraisal->designation }}</td>
                                    <td>{{ $appraisal->appraiser_officer_name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="delete-appraisal action-icon" data-id="{{ $appraisal->id }}">
                                            <i class="bi bi-trash  mx-1"></i>
                                        </a>
                                        
                                        <a href="{{ route('edit-appraisal', $appraisal->id) }}" class="action-icon">
                                            <i class="bi bi-pencil"></i>
                                        </a>


                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>



                    <div class="d-flex justify-content-center">
                        {{ $appraisals->links('pagination::bootstrap-4') }}
                    </div>

                    
                    <!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="sendEmails">Send Emails</button>
      </div>


    </div>
  </div>
</div>

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



                

                <script>
                    $(document).ready(function () {
                        $("#sentMailToAppraisee").click(function () {
                            $.ajax({
                                url: "{{ route('getappraisaluserscontent') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    let modalBody = $("#exampleModal .modal-body");
                                    modalBody.empty(); // Clear previous content

                                    if (response.length > 0) {
                                        let table = `<table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Full Name</th>
                                                                <th>Email</th>
                                                                <th>Emp Code</th>
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>`;

                                        response.forEach(user => {
                                            table += `<tr data-appraisal-id="${user.appraisal_form_id}">
                                                        <td>${user.full_name}</td>
                                                        <td>${user.email}</td>
                                                        <td>${user.emp_code}</td>
                                                        
                                                      </tr>`;
                                        });

                                        table += `</tbody></table>`;
                                        modalBody.append(table);
                                    } else {
                                        modalBody.html("<p>No data available.</p>");
                                    }

                                    $("#exampleModal").modal("show"); // Show modal
                                },
                                error: function (xhr, status, error) {
                                    console.error("AJAX Error:", error);
                                    $("#exampleModal .modal-body").html("<p>Error loading data. Please try again.</p>");
                                    $("#exampleModal").modal("show");
                                }
                            });
                        });

                        $("#sendEmails").click(function () {
                            let users = [];
                            $("#exampleModal .modal-body tbody tr").each(function () {
                                let fullName = $(this).find("td:nth-child(1)").text();
                                let email = $(this).find("td:nth-child(2)").text();
                                let appraisalFormId = $(this).attr("data-appraisal-id");

                                users.push({
                                    fullName:fullName,
                                    email: email,
                                    appraisal_form_id: appraisalFormId
                                });
                            });

                            if (users.length === 0) {
                                alert("No users available to send emails.");
                                return;
                            }

                            $.ajax({
                                url: "{{ route('sendAppraisalEmails') }}", 
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    users: users
                                },

                                success: function (response) {
                                    alert("Emails sent successfully!");
                                    //$("#exampleModal").modal("hide");
                                },
                                error: function (xhr, status, error) {
                                    console.error("Error:", error);
                                    //alert("Failed to send emails.");
                                }
                            })
                        })

                        $(".delete-appraisal").click(function () {
                            let appraisalId = $(this).data("id");

                            if (confirm("Are you sure you want to delete this record?")) {
                                $.ajax({
                                    url: "/delete-appraisal/" + appraisalId,
                                    type: "DELETE",
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function (response) {
                                        toastr.success(response.message, "Success");
                                        setTimeout(() => location.reload(), 1500); // Reload after 1.5s
                                    },
                                    error: function (xhr) {
                                        let errorMessage = "Something went wrong. Try again!";
                                        if (xhr.responseJSON && xhr.responseJSON.message) {
                                            errorMessage = xhr.responseJSON.message;
                                        }
                                        toastr.error(errorMessage, "Error");
                                    }
                                });
                            }
                        });

                        $("#exportAppraisalsButton").click(function () {
                            let selectedCycleId = $("#appraisalCycleDropdown").val();
                            let url = "{{ route('exportAppraisalsToExcel') }}?appraisal_cycle_id=" + selectedCycleId;
                            window.location.href = url;
                        });

                        $("#fetchAppraisalsButton").click(function () {
                            let selectedCycleId = $("#appraisalCycleDropdown").val();

                            if (!selectedCycleId) {
                                toastr.error("Please select an appraisal cycle.");
                                return;
                            }

                            $.ajax({
                                url: "{{ route('filterAppraisalsByCycle') }}",
                                type: "GET",
                                data: { appraisal_cycle_id: selectedCycleId },
                                success: function (response) {
                                    let tableBody = $("#appraisalTable tbody");
                                    tableBody.empty(); // Clear existing data
                                    
                                    response.appraisals.forEach(appraisal => {
                                        let row = `<tr>
                                            <td>${appraisal.employee_name}</td>
                                            <td>${appraisal.designation}</td>
                                            <td>${appraisal.reporting_officer_name ?? 'N/A'}</td>
                                            <td>${appraisal.appraiser_officer_name ?? 'N/A'}</td>
                                        </tr>`;
                                        tableBody.append(row);
                                    });

                                    toastr.success("Data loaded successfully.");
                                },
                                error: function () {
                                    toastr.error("Error fetching appraisals.");
                                }
                            });
                        });

                        $('#syncButton').click(function() {
                            let loader = $(this).find('.loader');
                            loader.show();
                            $.ajax({
                                url: "{{ route('syncappraisalusers') }}",
                                type: "POST",
                                data: {_token: "{{ csrf_token() }}"},
                                success: function(response) {
                                    toastr.success("Data Synced Successfully");
                                    fetchSyncedUsers();
                                },
                                error: function(xhr) {
                                    toastr.error("Error syncing data. Please try again.");
                                    
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