@extends("layouts.dashboard")

@section("content")
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data User
                <button class="btn btn-success" href="javascript:void(0)" id="createNewUser"><i class="glyphicon glyphicon-plus"></i>
                        New User
                </button>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="data-users" class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Roles</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Avatar</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
@push('modal')
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="userForm" name="userForm" class="form-horizontal">
                   <input type="hidden" name="user_id" id="user_id">
                   <div class="form-group col-md-12 col-sm-12">
                    <label for="name"> Nama </label>
                    <input type="text" class="form-control" id="name" name="name" value=""
                           placeholder="" required>
                    <span id="error_name" class="has-error"></span>
                </div>
                <div class="form-group col-md-12 col-sm-12">
                    <label for=""> Username </label>
                    <input type="text" class="form-control" id="username" name="username" value=""
                           placeholder="" required>
                    <span id="error_username" class="has-error"></span>
                </div>
                <div class="form-group col-md-12 col-sm-12">
                    <label for="email"> Email </label>
                    <input type="text" class="form-control" id="email" name="email" value=""
                            placeholder="" required>
                    <span id="error_email" class="has-error"></span>
                </div>
                <div class="form-group col-md-12 col-sm-12">
                    <label for="phone"> Phone </label>
                    <input type="text" class="form-control" id="phone" name="phone" value="" placeholder="" required>
                    <span id="error_phone" class="has-error"></span>
                </div>
                <div class="form-group col-md-12 col-sm-12">
                    <label for="address"> Address </label>
                    <textarea  type="text" class="form-control" id="address" name="address" value="" placeholder="" required></textarea>
                    <span id="error_address" class="has-error"></span>
                </div>
                <div class="form-group col-md-12 col-sm-12">
                    <label for="avatar"> Avatar </label>
                    <input type="file" class="form-control" id="avatar" name="avatar" value="" placeholder="" required>
                    <span id="error_avatar" class="has-error"></span>
                </div>
                <div class="form-group col-md-12 col-sm-12">
                    <label for="roles">Roles</label>
                    <br>
                    <input  type="checkbox" name="roles[]" id="ADMIN" value="ADMIN"> <label for="ADMIN">Administrator</label>
                    <input  type="checkbox" name="roles[]" id="STAFF" value="STAFF"> <label for="STAFF">Staff</label>
                    <input type="checkbox" name="roles[]" id="CUSTOMER" value="CUSTOMER"> <label for="CUSTOMER">Customer</label>
                </div>
                <div class="form-group col-md-12 col-sm-12">
                    <label for=""> Password </label>
                    <input type="password" class="form-control" id="password" name="password" value=""
                           placeholder="" required>
                    <span id="error_password" class="has-error"></span>
                </div>
                    <div class="col-sm-offset-2 col-sm-10">
                     <button type="submit" class="btn btn-primary" id="createBtn" value="create-user">Create User
                     </button>
                     <button type="submit" class="btn btn-primary" id="updateBtn" value="update-user">Update User
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush
@push('scripts')
<script type="text/javascript">
$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
  });

  var table = $('#data-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('users.index') }}",
      columns: [
          {data: 'name', name: 'name'},
          {data: 'email', name: 'email'},
          {data: 'username', name: 'username'},
          {data: 'roles', name: 'roles',
              'render' : function (data, type, row){
                  var Jsonroles = JSON.stringify(data);
                  return data;
              }
          },
          {data: 'address', name: 'address'},
          {data: 'phone', name: 'phone'},
          {data: 'avatar', name: 'avatar',
            "render": function(data, type, row) {
              var imgURL = '{{ asset('storage') }}' +'/'+data;
              return  '<img src="'+imgURL+'" height="40px" width="40px" />';
          }
          },
          {data: 'status', name: 'status'},
          {data: 'action', name: 'action', orderable: false, searchable: false},


      ]
  });

  $('#createNewUser').click(function () {
      $('#updateBtn').attr("style","display:none");
      $('#createBtn').attr("style","");
      $('#user_id').val('');
      $('#userForm').trigger("reset");
      $('#modelHeading').html("Add New User");
      $('#ajaxModel').modal('show');
  });

  $('body').on('click', '.editUser', function () {
    $('#createBtn').attr("style","display:none");
    $('#updateBtn').attr("style","");
    var user_id = $(this).data('id');
    $.get("{{ route('users.index') }}" +'/' + user_id +'/edit', function (data) {
        let checked = data.roles;
        $("input[name='roles[]']").each((element)=>{
            if(checked.includes($("input[name='roles[]']")[element].id)){
                $("input[name='roles[]']")[element].checked = true;
            }
        })
        $('#modelHeading').html("Edit User");
        $('#ajaxModel').modal('show');
        $('#user_id').val(data.id);
        $('#name').val(data.name);
        $('#username').val(data.username);

        $('#email').val(data.email);
        $('#phone').val(data.phone);
        $('#address').val(data.address);
    })

    $('#updateBtn').click(function (e) {

        var form = $('#userForm')[0];
        var formData = new FormData(form);
        e.preventDefault();
          $(this).html('Sending..');
          $.ajax({
            data: formData,
            url: `/users/`+user_id +`/update` ,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {

                $('#productForm').trigger("reset");
                $('#ajaxModel').modal('hide');
                table.draw();

            },
            error: function (data) {
                console.log('Error:', data);
                $('#createBtn').html('Save Changes');
            }
        });
      });
 });

  $('#createBtn').click(function (e) {
    var user_id = $(this).data('id');
    var form = $('#userForm')[0];
    var formData = new FormData(form);
      e.preventDefault();
      $(this).html('Sending..');
      $.ajax({
        data: formData,
        url: "{{ route('users.store') }}",
        type: "POST",
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (data) {

            $('#productForm').trigger("reset");
            $('#ajaxModel').modal('hide');
            table.draw();

        },
        error: function (data) {
            console.log('Error:', data);
            $('#createBtn').html('Create User');
        }
    });
  });




  $('body').on('click', '.deleteUser', function () {

      var user_id = $(this).data("id");
      confirm("Are You sure want to delete !");

      $.ajax({
          type: "DELETE",
          url: "{{ route('users.store') }}"+'/'+user_id,
          success: function (data) {
              table.draw();
          },
          error: function (data) {
              console.log('Error:', data);
          }
      });
  });

});
</script>
@endpush
