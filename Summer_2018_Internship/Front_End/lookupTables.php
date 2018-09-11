<?php require_once(dirname(__FILE__)."/./api/db.php");
  if(!isset($_SESSION['name'])) header ("Location: ./index.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <title>Laidig Dashboard - </title>
   <!-- Global Styling Changes-->
   <link rel="stylesheet" href="app/css/global.css">
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
   <!-- SELECT2-->
   <link rel="stylesheet" href="app/vendor/select2/dist/css/select2.css">
   <link rel="stylesheet" href="app/vendor/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css">
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
               <div>Lookup Tables
                  <small>Add, Delete, and Edit Lookup Tables</small>
               </div>
            </div>
            <div class="container-fluid">
               <!-- DATATABLE DEMO 2-->

               <div class="card">
                 <div class="card-header">
                    <span><b>Select Lookup Table</b></span>
                 </div>
                  <div class="card-body">
                      <select class="form-control" style="width: 20%" id="selectTable" onchange="tableReload();">
                                 <option value="materials">Materials</option>
                                 <option value="parameters">Parameters</option>
                                 <option value="jobs">Jobs</option>
                      </select>
                  </div>
               </div>

               <div class="card"  style="<?php if($_SESSION['role'] != 'Admin') echo 'display:none';?>">
                 <div class="card-header">
                    <span id="addHeader"><b>Add Entries to Materials Table</b></span>
                 </div>
                  <div class="card-body">
                    <form>
                      <div class="form-group">
                              <label id="form_label3">LaidigID</label>
                              <input class="form-control" value="" type="text" id="addID" placeholder="Enter ID">
                      </div>
                      <div class="form-group">
                              <label id="form_label4">Description</label>
                              <textarea class="form-control" value="" id="addDescrip" placeholder="Enter description"></textarea>
                      </div>
                      <button class="btn btn-square btn-success" onclick="addNewEntry();" type="button">Add Entry</button>
                    </form>
                  </div>
               </div>

               <div class="card">
                 <div class="card-header">
                   <span id="tableHeader"><b>Materials Lookup Table</b></span>
                 </div>
                <div class="card-body">
                  <table id="lookupTable" class="table table-striped my-4 w-100">
                      <thead>
                          <tr>
                              <th id="DBID_col">ID</th>
                              <th id="column1">LaidigID</th>
                              <th id="column2">Description</th>
                              <th style="<?php if($_SESSION['role'] != 'Admin') echo 'display:none';?>">Edit</th>
                              <th style="<?php if($_SESSION['role'] != 'Admin') echo 'display:none';?>">Delete</th>
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
   <div class="modal fade" id="editEntryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabelSmall" aria-hidden="true">
      <div class="modal-dialog modal-sm">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myModalLabelSmall">Editing Entry</h4>
               <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
              <form>
                <div class="form-group">
                        <label id="form_label1">LaidigID</label>
                        <input class="form-control" value="" type="text" id="editId" onkeypress="return event.keyCode != 13">
                </div>
                <div class="form-group">
                        <label id="form_label2">Description</label>
                        <textarea class="form-control" value="" id="editDescrip"></textarea>
                </div>
                <input id="editingEntryId" type="hidden">
              </form>
            </div>
            <div class="modal-footer">
               <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
               <button class="btn btn-primary" type="button" onclick="saveEntry();">Save changes</button>
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
   <!-- PARSLEY-->
   <script src="app/vendor/select2/dist/js/select2.full.js"></script>
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

    var tableReload = function() {
      // Handles Tables
      var url = './api/lookup_tables.php?tbl=' + $('#selectTable').val();
      $('#lookupTable').DataTable().ajax.url(url).load();

      // Handles Text
      var choice = $('#selectTable').val().charAt(0).toUpperCase() + $('#selectTable').val().slice(1);
      $('#addHeader').text('Add Entries to ' + choice + ' Table');
      $('#tableHeader').text(choice + ' Lookup Table');
      if (choice == 'Jobs') {
        $('#column1').text('Job Number');
        $('#column2').text('Client Name');
        $('#form_label1').text('Job Number');
        $('#form_label2').text('Client Name');
        $('#form_label3').text('Job Number');
        $('#form_label4').text('Client Name');
      } else {
        $('#column1').text('LaidigID');
        $('#column2').text('Description');
        $('#form_label1').text('LaidigID');
        $('#form_label2').text('Description');
        $('#form_label3').text('LaidigID');
        $('#form_label4').text('Description');
      }
    };

    var addNewEntry = function() {
      var choice      = $('#selectTable').val().charAt(0).toUpperCase() + $('#selectTable').val().slice(1);
      if (choice == 'Jobs') {
        var label1      = 'Job Number';
        var label2      = 'Client Name';
      } else {
        var label1      = "LaidgID";
        var label2      = 'Description';
      }
      var laidigId    = $('#addID').val().trim();
      var description = $('#addDescrip').val().trim();
      if (choice == 'Parameters' && !/^[A-Z][0-9][0-9][0-9]$/.test(laidigId)) {
        $.bootstrapGrowl("A parameter must be a capital letter followed by three digits", {
          type: 'danger',
          align: 'right'
        });
        return;
      }
      if (description.length > 512) {
        $.bootstrapGrowl(label2 + " is above the 512 character limit", {
          type: 'danger',
          align: 'right'
        });
        return;
      }

      if (laidigId.length > 12) {
        $.bootstrapGrowl(label1 + " is above the 12 character limit", {
          type: 'danger',
          align: 'right'
        });
        return;
      }

      if (laidigId.trim().length < 1 || description.trim().length < 1) {
        $.bootstrapGrowl("Missing required info", {
          type: 'danger',
          align: 'right'
        });
        return;
      }

      if (laidigId[0] != 'J' && choice == 'Jobs') {
        $.bootstrapGrowl("Job Number must begin with a J", {
          type: 'danger',
          align: 'right'
        });
        return;
      }
      var url = './api/lookup_tables.php?tbl=' + $('#selectTable').val();
      $.ajax({
        type:"post",
        url: url,
        data: {
          laidigId: laidigId,
          description: description
        },
        success: function(data){
          if(data.status == 0){
            $.bootstrapGrowl("Something happened, please try again", {
              type: 'danger',
              align: 'right'
            });
          } else if(data.status == 2){
            $.bootstrapGrowl("ID already exists", {
              type: 'danger',
              align: 'right'
            });
          } else {
              $.bootstrapGrowl("Successfully Added Entry", {
                type: 'success',
                align: 'right'
              });
              $('#addID').val('');
              $('#addDescrip').val('');
          }
          $('#lookupTable').DataTable().ajax.reload();
        }
      });
    };

    var deleteEntry = function(id) {
      swal({
        title: "Are you sure?",
        text: "You will not be able to recover this entry!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#F1651E",
        confirmButtonText: "Yes, delete!",
        closeOnConfirm: true
      },
      function() {
        url = './api/lookup_tables.php?tbl=' + $('#selectTable').val() + '&id=' + id;
        $.ajax({
          type:"DELETE",
          url: url,
          success: function(data){
            if(data == 0){
              $.bootstrapGrowl("Something happened, please try again", {
                type: 'danger',
                align: 'right'
              });
            } else {
                $.bootstrapGrowl("Successfully Deleted Entry", {
                  type: 'success',
                  align: 'right'
                });
            }
            $('#lookupTable').DataTable().ajax.reload();
          }
        });
      });
    };

    var editEntry = function(id, laidigId, description) {
      $('#editId').val(laidigId);
      $('#editDescrip').val(description);
      $('#editingEntryId').val(id);
      $('#editEntryModal').modal('show');
    }

    var saveEntry = function() {
      var choice      = $('#selectTable').val().charAt(0).toUpperCase() + $('#selectTable').val().slice(1);
      if (choice == 'Jobs') {
        var label1      = 'Job Number';
        var label2      = 'Client Name';
      } else {
        var label1      = "LaidgID";
        var label2      = 'Description';
      }
      var id          = $('#editingEntryId').val();
      var laidigId    = $('#editId').val().trim();
      var description = $('#editDescrip').val().trim();
      var url         = './api/lookup_tables.php?tbl=' + $('#selectTable').val();

      if (description.length > 512) {
        $.bootstrapGrowl(label2 + " is above the 512 character limit", {
          type: 'danger',
          align: 'right'
        });
        return;
      }

      if (laidigId.length > 12) {
        $.bootstrapGrowl(label1 + " is above the 12 character limit", {
          type: 'danger',
          align: 'right'
        });
        return;
      }

      if (laidigId[0] != 'J' && choice == 'Jobs') {
        $.bootstrapGrowl("Job Number must begin with a J", {
          type: 'danger',
          align: 'right'
        });
        return;
      }

      if (choice == 'Parameters' && !/^[A-Z][0-9][0-9][0-9]$/.test(laidigId)) {
        $.bootstrapGrowl("A parameter must be a capital letter followed by three digits", {
          type: 'danger',
          align: 'right'
        });
        return;
      }

      $.ajax({
        type:"post",
        url: url,
        data: {
          laidigId: laidigId,
          description: description,
          id: id
        },
        success: function(data){
          if(data.status == 0){
            $.bootstrapGrowl("Something happened, please try again", {
              type: 'danger',
              align: 'right'
            });
          } else if(data.status == 2){
            $.bootstrapGrowl("ID already exists", {
              type: 'danger',
              align: 'right'
            });
          } else {
              $.bootstrapGrowl("Successfully Edited Entry", {
                type: 'success',
                align: 'right'
              });
          }
          $('#lookupTable').DataTable().ajax.reload();
        }
      });

      $('#editEntryModal').modal('hide');
    }


    $(function() {
      if (<?php if ($_SESSION['role'] == 'Admin') echo "false"; else echo "true";?>) {
        table1 = $('#lookupTable').DataTable({
        "ajax": "./api/lookup_tables.php?tbl=materials",
        "columns": [
          { "data": "id" },
          { "data": "laidigId" },
          { "data": "description"}
        ]
        });
        table1.column('#DBID_col').visible(false);
      } else {
        table2 = $('#lookupTable').DataTable({
          "ajax": "./api/lookup_tables.php?tbl=materials",
          "columns": [
            { "data": "id" },
            { "data": "laidigId" },
            { "data": "description"},
            {
              mRender: function (data, type, row) {
                  return '<button class="btn btn-primary btn-xs" type="button" onclick="editEntry(\''+row['id']+'\', \''+row['laidigId']+'\', \''+row['description']+'\')">EDIT</button>';
              }, "orderable": false
            },
            {
              mRender: function (data, type, row) {
                  return '<button class="btn btn-danger btn-xs" type="button" onclick="deleteEntry(\''+row['id']+'\')">DELETE</button>'
              }, "orderable": false
            }

          ]
          });
          table2.column('#DBID_col').visible(false);
      }
        $(".preloadScreen").fadeOut("slow");
    });

    $(function() {
      $('#selectTable').select2({
        theme: 'bootstrap4'
      });

      $('#selectTable').select2({
        minimumResultsForSearch: -1
        });
    });


   </script>


</body>

</html>
