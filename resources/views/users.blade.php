@extends('layouts.app')
@section('css')
    <style type="text/css">
        .panel-heading button {
            position: absolute;
            right: 20px;
            top: 5px;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Users
                        <button id="create" class="btn btn-primary btn-sm pull-right">Create New</button>
                    </div>
                    <div class="panel-body">
                        <table class="table table-hover">
                            <th class="col-md-5">Name Surname</th>
                            <th class="col-md-4">Email</th>
                            <th class="col-md-3">Action</th>
                            <tbody id="users">
                            @if (isset($users) && count($users) > 0)
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-primary btn-xs" id="edit" value="{{ $user->id }}"> Edit </button>

                                                <button class="btn btn-danger btn-xs" id="delete" value="{{ $user->id }}"> Delete </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="4"> No records found </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <b>Total Users </b> : {{ isset($users) ? count($users) : 0 }}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="userCreateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title text-center">Create User</h4>
                    </div>
                    <div class="modal-body">
                        <form id="user-create-form" method="POST" action="{{ url('users') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Name Surname</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="" required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="user-create">Create</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title text-center">Edit User</h4>
                    </div>
                    <div class="modal-body">
                        <form id="user-edit-form" method="POST" action="{{ url('users') }}">
                            @csrf
                            <input id="user-id" name="id" type="hidden" value="">
                            <input name="_method" type="hidden" value="PUT">

                            <div class="form-group row">
                                <label for="name-update" class="col-md-4 col-form-label text-md-right">Name</label>

                                <div class="col-md-6">
                                    <input id="name-update" type="text" class="form-control" name="name" value="" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email-update" class="col-md-4 col-form-label text-md-right">Email</label>

                                <div class="col-md-6">
                                    <input id="email-update" type="email" class="form-control" name="email" value="" required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="user-update">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $('#create').click(function(){
            $('#userCreateModal').modal('show');
        });

        $(document).on('click','#edit',function(){
            var user_id = $(this).val();

            $.get('users/' + user_id +'/edit', function (data) {
                $('#name-update').val(data.name);
                $('#email-update').val(data.email);
                $('#user-id').val(data.id);
                $('#userEditModal').modal('show');
            })
        });

        $("#user-create").click(function () {
            var formData = $('#user-create-form').serialize();

            $.post( 'users', formData)
                .done(function() {
                    location.reload();
                })

                .fail(function(data) {
                    if (data.responseJSON.message) {
                        window.alert(data.responseJSON.message);
                    }
                    else {
                        window.alert(data.responseJSON);
                    }
                });
        });

        $("#user-update").click(function (e) {
            var user_id  = $('#user-id').val();
            var formData = $('#user-edit-form').serialize();

            $.post( 'users/' +user_id, formData)
                .done(function(data) {
                    location.reload();
                })

                .fail(function(data) {
                    if (data.responseJSON.message) {
                        window.alert(data.responseJSON.message);
                    }
                    else {
                        window.alert(data.responseJSON);
                    }
                });
        });

        $(document).on('click','#delete',function(){
            var user_id = $(this).val();

            $.post('users/' + user_id, {
                '_token': $('meta[name=csrf-token]').attr('content'),
                _method : 'DELETE'
            })
                .done(function(data) {
                    location.reload();
                })

                .fail(function(data) {
                    if (data.responseJSON.message) {
                        window.alert(data.responseJSON.message);
                    }
                    else {
                        window.alert(data.responseJSON);
                    }
                });
        });
    </script>
@endsection
