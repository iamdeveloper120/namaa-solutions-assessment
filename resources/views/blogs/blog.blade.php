@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                Manage Blogs
                <a href="#" class="btn btn-primary text-end create-new-blog" data-bs-toggle="modal" data-bs-target="#createBlog">Add New Blog</a>
            </h5>
            <div class="card-body">
                <table class="table table-bordered data-table">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Publish Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createBlog" tabindex="-1" aria-labelledby="modelHeading" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelHeading">Add New Blog Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <form id="createBlogForm" method="post" enctype="multipart/form-data">
                        <div class="alert alert-danger" id="errorMessage" role="alert">
                            A simple danger alert—check it out!
                        </div>
                        <div class="alert alert-success" id="successMessage" role="alert">
                            A simple success alert—check it out!
                        </div>
                        <input type="hidden" class="form-control" name="id" id="id" value="">
                        <div class="row">
                            <div class="col">
                                <label for="blogFile" class="col-form-label">Choose image</label>
                                <input class="form-control" type="file" id="blogFile" name="image" required>
                            </div>
                            <div class="col">
                                <label for="blogTitle" class="col-form-label">Title:</label>
                                <input type="text" class="form-control" id="blogTitle" name="title" required>
                            </div>
                        </div>
                        <div class="mb-3" id="">
                            <label for="blogPublishDate" class="col-form-label">Publish Date:</label>
                            <input type="datetime-local" class="form-control" id="blogPublishDate" name="publish_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="" for="selectBlogStatus">Status: </label>
                            <select name="status" class="form-select" id="selectBlogStatus" required>
                                <option value="published" selected>Publish</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="blogContent" class="form-label">Content</label>
                            <textarea class="form-control" id="blogContent" rows="3" name="content"></textarea>
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

    <div class="modal fade" id="deleteBlog" tabindex="-1" aria-labelledby="deleteModelHeading" aria-modal="true">
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
        $(function () {
            const table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('blogs.index') }}",
                columns: [
                    {data: 'image', name: 'image'},
                    {data: 'title', name: 'title'},
                    {data: 'status', name: 'status'},
                    {data: 'publish_date', name: 'publish_date'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            let saveButton = $('#saveBtn');
            let createBlogForm = $('#createBlogForm');
            let body = $('body');
            let blogTitle = $('#blogTitle');
            let blogFile = $('#blogFile');
            let blogContent = $('#blogContent');
            let blogPublishDate = $('#blogPublishDate');
            let selectBlogStatus = $('#selectBlogStatus');
            let errorMessage = $('#errorMessage');
            let successMessage = $('#successMessage');
            let id = $('#id');
            let modelHeading = $('#modelHeading');
            let deleteButton = $('#deleteBtn');
            let deleteModalBody = $('#deleteModalBody');

            body.on('click', '.create-new-blog', function () {
                id.val('');
                $('#modelHeading').html("Create New Blog");
                createBlogForm.trigger("reset");
                saveButton.html("Save Blog");
                errorMessage.hide()
                successMessage.hide()
            });

            body.on('click', '.editRow', function () {
                const blogId = $(this).data('id');
                id.val(blogId);
                errorMessage.hide()
                successMessage.hide()
                $.get('blogs/' + blogId + '/edit', function (data) {
                    modelHeading.html("Edit Product");
                    saveButton.html("Save Changes");
                    saveButton.val("saveChanges");
                    blogTitle.val(data.title);
                    blogContent.val(data.content);
                    blogPublishDate.val(data.publish_date);
                    selectBlogStatus.val(data.status);
                })
            });

            body.on('click', '#saveBtn', function (e) {
                e.preventDefault();
                if(!blogTitle.val()) {
                    errorMessage.show();
                    errorMessage.html("Blog title is required")
                    return;
                }
                if(!blogContent.val()) {
                    errorMessage.show();
                    errorMessage.html("Blog content is required")
                    return;
                }
                if(!blogPublishDate.val()) {
                    errorMessage.show();
                    errorMessage.html("Publish date is required")
                    return;
                }
                if(!selectBlogStatus.val()) {
                    errorMessage.show();
                    errorMessage.html("Blog status is required")
                    return;
                }
                const form = $('form')[0];
                const formData = new FormData(form);
                formData.append('image', $('input[type=file]')[0].files[0]);
                formData.append('title', blogTitle.val());
                formData.append('content', blogContent.val());
                formData.append('status', selectBlogStatus.val());
                formData.append('publish_date', blogPublishDate.val());
                if(id.val) {
                    formData.append('id', id.val());
                }
                console.log(formData);
                $.ajax({
                    data: formData, //createBlogForm.serialize(),
                    url: "blogs",
                    type: "POST",
                    enctype: 'multipart/form-data',
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (data) {
                        createBlogForm.trigger("reset");
                        table.draw();
                        successMessage.show();
                        successMessage.html("Looks all good!");
                        if(id.val) {
                            id.val('');
                            $('.btn-close').click();
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

            body.on('click', '.deleteRow', function () {
                const blogId = $(this).data('id');
                console.log(blogId);
                id.val(blogId);
                errorMessage.hide()
                successMessage.hide()
                deleteModalBody.html('Are you sure you want to delete this?');
                deleteButton.prop('disabled', false);
            });

            body.on('click', '#deleteBtn', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "blogs/"+id.val(),
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
