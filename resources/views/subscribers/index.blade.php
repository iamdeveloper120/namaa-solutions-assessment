@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                Manage Subscribers
                <a href="#" class="btn btn-primary text-end create-new-subscriber" data-bs-toggle="modal" data-bs-target="#createSubscriber">Create New Subscriber</a>
            </h5>
            <div class="card-body">
                <table class="table table-bordered data-table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createSubscriber" tabindex="-1" aria-labelledby="modelHeading" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelHeading">Create New Subscriber</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <form id="createSubscriberForm">
                        <div class="alert alert-danger" id="errorMessage" role="alert">
                            A simple danger alert—check it out!
                        </div>
                        <div class="alert alert-success" id="successMessage" role="alert">
                            A simple success alert—check it out!
                        </div>
                        <input type="hidden" class="form-control" name="id" id="id" value="">
                        <div class="row">
                            <div class="col">
                                <label for="subscriberName" class="col-form-label">Name:</label>
                                <input type="text" class="form-control" id="subscriberName" name="name" required>
                            </div>
                            <div class="col">
                                <label for="subscriberUsername" class="col-form-label">Username:</label>
                                <input type="text" class="form-control" id="subscriberUsername" name="username" required>
                            </div>
                        </div>
                        <div class="mb-3" id="passwordSection">
                            <label for="subscriberPassword" class="col-form-label">Password:</label>
                            <input type="password" class="form-control" id="subscriberPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="" for="selectSubscriberStatus">Status: </label>
                            <select name="status" class="form-select" id="selectSubscriberStatus" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteSubscriber" tabindex="-1" aria-labelledby="deleteModelHeading" aria-modal="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModelHeading">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="deleteModalBody">Are you sure you want to delete?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="submit" class="btn-danger btn" id="deleteBtn">Yes, Delete!</button>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        // $('body').html('<h1>Hello World!</h1>');
        $(function () {
            const table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('subscribers.index') }}",
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'username', name: 'username'},
                    {data: 'password', name: 'password'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            let saveButton = $('#saveBtn');
            let createSubscriberForm = $('#createSubscriberForm');
            let body = $('body');
            let subscriberName = $('#subscriberName');
            let subscriberUsername = $('#subscriberUsername');
            let subscriberPassword = $('#subscriberPassword');
            let selectSubscriberStatus = $('#selectSubscriberStatus');
            let errorMessage = $('#errorMessage');
            let successMessage = $('#successMessage');
            let id = $('#id');
            let modelHeading = $('#modelHeading');
            let deleteButton = $('#deleteBtn');
            let deleteModalBody = $('#deleteModalBody');

            body.on('click', '.create-new-subscriber', function () {
                $('#modelHeading').html("Create new subscriber");
                createSubscriberForm.trigger("reset");
                saveButton.html("Save Subscriber");
                errorMessage.hide()
                successMessage.hide()
                subscriberPassword.prop('readOnly', false);
                id.val('');
            });

            // click on edit button
            body.on('click', '.editRow', function () {
                const subscriberId = $(this).data('id');
                id.val(subscriberId);
                errorMessage.hide()
                successMessage.hide()
                $.get('subscribers/' + subscriberId + '/edit', function (data) {
                    modelHeading.html("Edit Product");
                    saveButton.html("Save Changes");
                    saveButton.val("saveChanges");
                    subscriberName.val(data.name);
                    subscriberUsername.val(data.username);
                    subscriberPassword.val(data.password);
                    subscriberPassword.prop('readOnly', true);
                    selectSubscriberStatus.val('active');
                })
            });

            body.on('click', '#saveBtn', function (e) {
                e.preventDefault();
                if(!subscriberName.val()) {
                    errorMessage.show();
                    errorMessage.html("Subscriber name is required")
                    return;
                }
                if(!subscriberUsername.val()) {
                    errorMessage.show();
                    errorMessage.html("Subscriber username is required")
                    return;
                }
                if(!subscriberPassword.val()) {
                    errorMessage.show();
                    errorMessage.html("Subscriber password is required")
                    return;
                }
                if(!selectSubscriberStatus.val()) {
                    errorMessage.show();
                    errorMessage.html("Subscriber status is required")
                    return;
                }
                $.ajax({
                    data: createSubscriberForm.serialize(),
                    url: "subscribers",
                    type: "POST",
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (data) {
                        createSubscriberForm.trigger("reset");
                        table.draw();
                        successMessage.show();
                        successMessage.html("Looks all good!");
                        if(id.val) {
                            $('.btn-close').click();
                            id.val('');
                        }
                    },
                    error: function (data) {
                        console.log(data.responseJSON.message);
                        if(data && data.responseJSON.status === 422) {
                            errorMessage.show();
                            errorMessage.html(data.responseJSON.message);
                        }
                        $('#saveBtn').html('Save Changes');
                    }
                });
            });

            // click on edit button
            body.on('click', '.deleteRow', function () {
                const subscriberId = $(this).data('id');
                console.log(subscriberId);
                id.val(subscriberId);
                errorMessage.hide()
                successMessage.hide()
                deleteModalBody.html('Are you sure you want to delete this?');
                deleteButton.prop('disabled', false);
            });

            body.on('click', '#deleteBtn', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "subscribers/"+id.val(),
                    type: "DELETE",
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (data) {
                        table.draw();
                        successMessage.show();
                        successMessage.html("Record deleted successfully");
                        if(id.val) {
                            deleteButton.html('Deleted Successfully');
                            deleteButton.prop('disabled', true);
                        }
                    },
                    error: function (data) {
                        console.log(data.responseJSON.message);
                        if(data && data.responseJSON.status === 422) {
                            errorMessage.show();
                            deleteModalBody.html('Deleted Successfully');
                        }
                        deleteButton.html('Yes, Delete!');
                    }
                });
            });
        });
    </script>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
