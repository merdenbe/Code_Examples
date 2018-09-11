<?php require_once(dirname(__FILE__)."/./api/db.php");
  if(!isset($_SESSION['name'])) header ("Location: ./index.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <title>Laidig Dashboard - Users</title>
   <!-- =============== VENDOR STYLES ===============-->
   <!-- FONT AWESOME-->
   <link rel="stylesheet" href="app/vendor/font-awesome/css/font-awesome.css">
   <!-- SIMPLE LINE ICONS-->
   <link rel="stylesheet" href="app/vendor/simple-line-icons/css/simple-line-icons.css">
   <!-- ANIMATE.CSS-->
   <link rel="stylesheet" href="app/vendor/animate.css/animate.css">
   <!-- WHIRL (spinners)-->
   <link rel="stylesheet" href="app/vendor/whirl/dist/whirl.css">
   <!-- SWEET ALERT-->
   <link rel="stylesheet" href="app/vendor/sweetalert/dist/sweetalert.css">
   <!-- =============== PAGE VENDOR STYLES ===============-->
   <!-- Datatables-->
   <link rel="stylesheet" href="app/vendor/datatables.net-bs4/css/dataTables.bootstrap4.css">
   <link rel="stylesheet" href="app/vendor/datatables.net-keytable-bs/css/keyTable.bootstrap.css">
   <link rel="stylesheet" href="app/vendor/datatables.net-responsive-bs/css/responsive.bootstrap.css">
   <!-- =============== BOOTSTRAP STYLES ===============-->
   <link rel="stylesheet" href="app/css/bootstrap.css" id="bscss">
   <!-- =============== APP STYLES ===============-->
   <link rel="stylesheet" href="app/css/app.css" id="maincss">
   <!-- =============== SPINNER ===============-->
   <link rel="stylesheet" href="app/vendor/loaders.css/loaders.css">
   <!-- Global Styling Changes-->
   <link rel="stylesheet" href="app/css/global.css">

</head>

<body>

  <?php include 'loadingSpinner.php'; ?>

   <div class="wrapper">
      <?php include 'topHeader.php'; ?>
      <?php include 'leftSidebar.php'; ?>
      <!-- Main section-->
      <section class="section-container">
         <!-- Page content-->
         <div class="content-wrapper">
            <div class="content-heading">
               <div>Users
                  <small>Add, Delete, and Edit Users</small>
               </div>
            </div>
            <div class="container-fluid">
               <!-- DATATABLE DEMO 2-->
               <div class="card">
                 <div class="card-header">
                    <span><b>Add Users</b></span>
                 </div>
                  <div class="card-body">
                    <p>
                      <form>
                        <div class="form-group">
                          <input class="form-control mb-2" id="addUsername" style="width: 40%" type="text" placeholder="enter name" value="" size="30">
                          <input class="form-control mb-2" id="addPassword" style="width: 40%" type="text" placeholder="enter password" value="" size="30">
                        </div>
                        <div class="form-group">
                        <select class="form-control" style="width: 20%" id="selectRole">
                                   <option value="empty">Select Role</option>
                                   <option value="Admin">Admin</option>
                                   <option value="Engineering">Engineering</option>
                                   <option value="Reference">Reference</option>
                                   <option value="Upload">Upload</option>
                        </select>
                      </div>
                      <button class="btn btn-square btn-success" type="button" onclick="addNewUser();">Add User</button>
                      </form>
                    </p>
                  </div>
               </div>

               <div class="card">
                <div class="card-body">
                  <table id="allUsers" class="table table-striped my-4 w-100">
                      <thead>
                          <tr>
                              <th id="DBID_col">ID</th>
                              <th>Name</th>
                              <th>Role</th>
                              <th>Password</th>
                              <th>Edit</th>
                              <th>Delete</th>
                          </tr>
                      </thead>
                  </table>
                </div>
             </div>

            </div>
         </div>
      </section>
      <!-- Page footer-->
      <footer class="footer-container">
         <span>&copy; 2018 - Laidig Systems</span>
      </footer>
   </div>

   <!-- Modal for editing user -->
   <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelSmall" aria-hidden="true">
      <div class="modal-dialog modal-sm">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myModalLabelSmall">Editing User</h4>
               <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
                <form>
                  <div class="form-group">
                  <input class="form-control mb-2" id="editUsername" type="text" placeholder="enter name" value="" size="30">
                  <input class="form-control mb-2" id="editPassword" type="text" placeholder="enter password" value="" size="30">
                </div>
                <div class="form-group">
                  <select class="form-control" id="editRole">
                             <option value="Admin">Admin</option>
                             <option value="Engineering">Engineering</option>
                             <option value="Reference">Reference</option>
                             <option value="Upload">Upload</option>
                  </select>
                </div>
                  <input id="editingUserId" type="hidden">
                </form>
            </div>
            <div class="modal-footer">
               <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
               <button class="btn btn-primary" type="button" onclick="saveUserEdit();">Save changes</button>
            </div>
         </div>
      </div>
   </div>
   <!-- END MODAL -->

   <!-- =============== VENDOR SCRIPTS ===============-->
   <!-- MODERNIZR-->
   <script src="app/vendor/modernizr/modernizr.custom.js"></script>
   <!-- JQUERY-->
   <script src="app/vendor/jquery/dist/jquery.js"></script>
   <!-- BOOTSTRAP-->
   <script src="app/vendor/popper.js/dist/umd/popper.js"></script>
   <script src="app/vendor/bootstrap/dist/js/bootstrap.js"></script>
   <!-- STORAGE API-->
   <script src="app/vendor/js-storage/js.storage.js"></script>
   <!-- JQUERY EASING-->
   <script src="app/vendor/jquery.easing/jquery.easing.js"></script>
   <!-- ANIMO-->
   <script src="app/vendor/animo/animo.js"></script>
   <!-- SCREENFULL-->
   <script src="app/vendor/screenfull/dist/screenfull.js"></script>
   <!-- LOCALIZE-->
   <script src="app/vendor/jquery-localize/dist/jquery.localize.js"></script>
   <!-- SWEET ALERT-->
   <script src="app/vendor/sweetalert/dist/sweetalert.min.js"></script>
   <!-- =============== PAGE VENDOR SCRIPTS ===============-->
   <!-- Datatables-->
   <script src="app/vendor/datatables.net/js/jquery.dataTables.js"></script>
   <script src="app/vendor/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
   <script src="app/vendor/datatables.net-buttons/js/dataTables.buttons.js"></script>
   <script src="app/vendor/datatables.net-buttons-bs/js/buttons.bootstrap.js"></script>
   <script src="app/vendor/datatables.net-buttons/js/buttons.colVis.js"></script>
   <script src="app/vendor/datatables.net-buttons/js/buttons.flash.js"></script>
   <script src="app/vendor/datatables.net-buttons/js/buttons.html5.js"></script>
   <script src="app/vendor/datatables.net-buttons/js/buttons.print.js"></script>
   <script src="app/vendor/datatables.net-keytable/js/dataTables.keyTable.js"></script>
   <script src="app/vendor/datatables.net-responsive/js/dataTables.responsive.js"></script>
   <script src="app/vendor/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
   <script src="app/vendor/jszip/dist/jszip.js"></script>
   <script src="app/vendor/pdfmake/build/pdfmake.js"></script>
   <script src="app/vendor/pdfmake/build/vfs_fonts.js"></script>
   <!-- =============== APP SCRIPTS ===============-->
   <script src="js/app.js"></script>

   <script type="text/javascript">
    var saveUser = function (username, role, password, userId) {
      if(username.trim().length < 1) {
        $.bootstrapGrowl("Username is required", {
          type: 'danger',
          align: 'right'
        });
        return;
      }
      if(password.trim().length < 1) {
        $.bootstrapGrowl("Password is required", {
          type: 'danger',
          align: 'right'
        });
        return;
      }
      if(password.trim().length > 32) {
        $.bootstrapGrowl("Password must be 32 characters or less", {
          type: 'danger',
          align: 'right'
        });
        return;
      }
      $.ajax({
  			type:"post",
  			url:"./api/addUser.php",
  			data: {
          username: username,
          role: role,
          userId: userId,
          password: password
        },
  			success: function(data){
  				if(data == 0){
            $.bootstrapGrowl("Something happened, please try again", {
              type: 'danger',
              align: 'right'
            });
  			  } else if(data == 2){
            $.bootstrapGrowl("Name already exists", {
              type: 'danger',
              align: 'right'
            });
  			  } else {
            if(userId < 0) {
              $.bootstrapGrowl("Successfully Added", {
                type: 'success',
                align: 'right'
              });
              $('#addUsername').val('');
              $('#addPassword').val('');
              $('#selectRole').val('empty').trigger('change');
            } else {
              $.bootstrapGrowl("Successfully Edited", {
                type: 'success',
                align: 'right'
              });
            }

            $('#allUsers').DataTable().ajax.reload();
          }
		    }
      });
    };
    var addNewUser = function () {
      var username = $('#addUsername').val();
      var role     = $('#selectRole').val();
      var password = $('#addPassword').val();

      if (role == "empty") {
        $.bootstrapGrowl("Must select a role first", {
          type: 'danger',
          align: 'right'
        });
        return;
      }
      saveUser(username, role, password, -1);
    };

    var editRow = function (userId, username, role, password) {
      $('#editUsername').val(username);
      $('#editPassword').val(password);
      $('#editingUserId').val(userId);
      $('#editRole').val(role);
      $('#editUserModal').modal('show');
    };

    var saveUserEdit = function () {
      var name      = $('#editUsername').val();
      var password  = $('#editPassword').val();
      var role      = $('#editRole').val();
      var id        = $('#editingUserId').val();
      saveUser(name, role, password, id);
      $('#editUserModal').modal('hide');
    };

    var deleteUser = function (userid) {
      swal({
        title: "Are you sure?",
        text: "You will not be able to recover this user!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#F1651E",
        confirmButtonText: "Yes, delete!",
        closeOnConfirm: true
      },
      function() {
        $.ajax({
          type:"post",
          url:"./api/deleteUser.php",
          data: {
            userid: userid
          },
          success: function(data){
            if(data == 0){
              $.bootstrapGrowl("Name not found, try again", {
                type: 'danger',
                align: 'right'
              });
            } else {
              $.bootstrapGrowl("Successfully Deleted User", {
                type: 'success',
                align: 'right'
              });
              $('#allUsers').DataTable().ajax.reload();
            }
          }
        });

      });
    };

    $(function() {
      table = $('#allUsers').DataTable({
        "order": [[ 1, "asc" ]],
        "ajax": "./api/getUsers.php",
        "columns": [
          { "data": "id" },
          { "data": "name" },
          { "data": "role" },
          { "data": "password" },
          {
            mRender: function (data, type, row) {
                return '<button class="btn btn-primary btn-xs" type="button" onclick="editRow(\''+row['id']+'\', \''+row['name']+'\', \''+row['role']+'\', \''+row['password']+'\')">EDIT</button>';
            }, "orderable": false
          },
          {
            mRender: function (data, type, row) {
                return '<button class="btn btn-danger btn-xs" type="button" onclick="deleteUser(\''+row['id']+'\')">DELETE</button>'
            }, "orderable": false
          }

        ]
      });

      table.column('#DBID_col').visible(false);

       $(".preloadScreen").fadeOut("slow");
    });


   </script>


</body>

</html>
