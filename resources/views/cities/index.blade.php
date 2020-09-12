@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
            <div class="col-md-10">
                <!-- <div class="card">
                    <div class="card-header">Ciudades</div>

                    <div class="card-body">

                    </div>
                </div> -->

                <div class="row">
                    <div class="col-12 text-right">
                        <a href="javascript:void(0)" class="btn btn-success mb-3" id="create-new-post" onclick="addPost()">Add City</a>
                    </div>
                </div>
                <div class="row" style="clear: both;margin-top: 18px;">
                    <div class="col-12">
                        <table id="laravel_crud" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cities as $city)
                                <tr id="row_{{$city->id}}">
                                    <td>{{ $city->id  }}</td>
                                    <td>{{ $city->name }}</td>
                                    <td>
                                        <a href="javascript:void(0)" data-id="{{ $city->id }}" onclick="editPost(event.target)" class="btn btn-info">Edit</a>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" data-id="{{ $city->id }}" class="btn btn-danger" onclick="deletePost(event.target)">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="post-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form name="userForm" class="form-horizontal">
                        <input type="hidden" name="city_id" id="city_id">
                        <div class="form-group">
                            <label for="name" class="col-sm-2">Name</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                                <span id="titleError" class="alert-message"></span>
                            </div>
                        </div>

                        <!-- <div class="form-group">
                            <label class="col-sm-2">Description</label>
                            <div class="col-sm-12">
                        <textarea class="form-control" id="description" name="description" placeholder="Enter description" rows="4" cols="50">
                        </textarea>
                                <span id="descriptionError" class="alert-message"></span>
                            </div>
                        </div> -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="createPost()">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script><script>
        $('#laravel_crud').DataTable();
        function addPost() {
            $('#post-modal').modal('show');
        }

        function editPost(event) {
            var id  = $(event).data("id");
            let _url = `/cities/${id}`;
            $('#titleError').text('');
            $('#descriptionError').text('');

            $.ajax({
                url: _url,
                type: "GET",
                success: function(response) {
                    if(response) {
                        $("#city_id").val(response.id);
                        $("#name").val(response.name);
                        //$("#description").val(response.description);
                        $('#post-modal').modal('show');
                    }
                }
            });
        }

        function createPost() {
            var name = $('#name').val();
            //var description = $('#description').val();
            var id = $('#city_id').val();

            let _url     = `/cities`;
            let _token   = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: _url,
                type: "POST",
                data: {
                    id: id,
                    name: name,
                    //description: description,
                    _token: _token
                },
                success: function(response) {
                    if(response.code == 200) {
                        if(id != ""){
                            $("#row_"+id+" td:nth-child(2)").html(response.data.name);
                            //$("#row_"+id+" td:nth-child(3)").html(response.data.description);
                        } else {
                            $('table tbody').prepend('<tr id="row_'+response.data.id+'"><td>'+response.data.id+'</td><td>'+response.data.name+'</td><td>'+response.data.description+'</td><td><a href="javascript:void(0)" data-id="'+response.data.id+'" onclick="editPost(event.target)" class="btn btn-info">Edit</a></td><td><a href="javascript:void(0)" data-id="'+response.data.id+'" class="btn btn-danger" onclick="deletePost(event.target)">Delete</a></td></tr>');
                        }
                        $('#name').val('');
                        //$('#description').val('');

                        $('#post-modal').modal('hide');
                    }
                },
                error: function(response) {
                    $('#titleError').text(response.responseJSON.errors.name);
                    $('#descriptionError').text(response.responseJSON.errors.description);
                }
            });
        }

        function deletePost(event) {
            var id  = $(event).data("id");
            let _url = `/posts/${id}`;
            let _token   = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: _url,
                type: 'DELETE',
                data: {
                    _token: _token
                },
                success: function(response) {
                    $("#row_"+id).remove();
                }
            });
        }

    </script>
@endsection
