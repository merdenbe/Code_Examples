window.onbeforeunload = function() {
 return (filesUploaded.length != 0) ? "Upload in progress" : null;
}

var filesUploaded = [];
var dropzoneArea = {};
var filesAdded = 0;
var filesProcessed = 0;
var maxFilesReached = false;
Dropzone.autoDiscover = false;

var updateOverallProgress = function () {
  $('#overallProgressAlert').text(filesProcessed + ' of ' + filesAdded + ' files finished processing');
};
var reorderFilePreview = function (previewElement, status) {
  if (status == 'error') {
    if($('#customFilePreview').has('.dz-complete')) {
      previewElement.insertBefore($('#customFilePreview').find('.dz-complete:first'))
    }
  }
};

var viewInfo = function(filename, backupPath, status, machineId, header, date_uploaded, job_number, date_range) {
  table2.clear().draw();
  table2.row.add(['<b>Filename</b>', filename]).draw();
  table2.row.add(['<b>Backup Path</b>', backupPath]).draw();
  table2.row.add(['<b>Status</b>', status]).draw();
  table2.row.add(['<b>Machine Id</b>', machineId]).draw();
  table2.row.add(['<b>Header</b>', header]).draw();
  table2.row.add(['<b>Date Uploaded</b>', date_uploaded]).draw();
  table2.row.add(['<b>Job Number</b>', job_number]).draw();
  table2.row.add(['<b>Date Range</b>', date_range]).draw();
  $('#viewInfoModal').modal('show');
};
 $(function() {
   var updateProgress = setInterval(function() {
     if (!maxFilesReached) {
       if (filesUploaded.length > 0) {
         if(filesUploaded.length < 5 && dropzoneArea.getQueuedFiles().length > 0) {
           dropzoneArea.processFile(dropzoneArea.getQueuedFiles()[0]);
         }
         $.ajax({
             url: './api/getFileProgress.php?backupFileId=' + filesUploaded.join(),
             type: "GET",
             success: function(data) {
               console.log(data);
               for(var x=0; x<data.length; x++) {
                 var previewElement = {};
                 $('.backupFilesId').each(function() {
                   if($(this).val() == data[x]['id']) {
                     previewElement = $(this).parent();
                     return false;
                   }
                 });
                 if(parseInt(data[x]['status']) > 0 && parseInt(data[x]['status']) < 101) {
                   previewElement.find('.progress-bar').css('width', parseInt(data[x]['status']) + '%');
                 } else {
                   filesProcessed++;
                   updateOverallProgress();
                   var index = filesUploaded.indexOf(data[x]['id']);
                   if (index > -1) {
                     filesUploaded.splice(index, 1);
                   }
                   if (dropzoneArea.getQueuedFiles().length > 0 && filesUploaded.length < 5) {
                     dropzoneArea.processFile(dropzoneArea.getQueuedFiles()[0]);
                   }
                   if(data[x]['status'].indexOf('error') > -1 || data[x]['status'].indexOf('Error') > -1) {
                     previewElement.find('.progress-bar').css('width', '0%');
                     previewElement.find('.fa-spinner').hide();
                     previewElement.find('.dataDzMessage').text(data[x]['status']);
                     previewElement.find('.fa-times').fadeIn();
                     previewElement.find('#statusThumbnail').removeClass('bg-info');
                     previewElement.find('.dataDzMessage').removeClass('text-success');
                     previewElement.find('.dataDzMessage').addClass('text-danger');
                     previewElement.find('#statusThumbnail').addClass('bg-danger');
                     reorderFilePreview(previewElement, 'error');
                   } else {
                     previewElement.find('.progress-bar').css('width', '100%');
                     previewElement.find('.fa-spinner').hide();
                     previewElement.find('.fa-check').fadeIn();
                     previewElement.find('#statusThumbnail').removeClass('bg-info');
                     previewElement.find('#statusThumbnail').addClass('bg-success');
                     previewElement.find('.dataDzMessage').text(data[x]['status']);
                     previewElement.fadeOut({duration: 6000});
                     $.bootstrapGrowl("Successfully Processed " + previewElement.find('.fullFilePath').text().match(new RegExp('.{1,25}', 'g')).join(" ") , {
                       type: 'success'
                     });
                   }
                   $('#allEntries').DataTable().ajax.reload();
                 }
               }
             }
         });
       } else if (dropzoneArea.getQueuedFiles().length > 0) {
         dropzoneArea.processFile(dropzoneArea.getQueuedFiles()[0]);
       }
     } else {
       dropzoneArea.removeAllFiles(true);
       $('#customFilePreview').empty();
     }
   }, 3000);

   var previewNode = $("#template");
   previewNode.attr('id', '');
   previewNode.css('display', '');
   var previewTemplate = previewNode.parent().html();
   previewNode.parent().empty();
   var dropzoneOptions = {
       autoProcessQueue: false,
       uploadMultiple: false,
       parallelUploads: 4,
       maxFiles: 1000,
       dictDefaultMessage: '<em class="ion-upload color-blue-grey-100 icon-2x"></em><br>Click here to upload a folder or drop files in this box', // default messages before first drop
       paramName: 'file', // The name that will be used to transfer the file
       maxFilesize: 3000, // MB
       timeout: 0,
       addRemoveLinks: false,
       acceptedFiles: '.csv',
       previewsContainer: '#customFilePreview',
       previewTemplate: previewTemplate,
       init: function() {
           this.on('addedfile', function(file) {
             if(!maxFilesReached) {
               filesAdded++;
               if (filesAdded > 999) {
                 maxFilesReached = true;
                 $('#datafileDropzone').parent().hide();
                 $('#overallProgressAlert').text('You are unable to upload more than 1000 files at a time. None of the files will be processed. Cleaning this up is very hard on the web browser so you will need to refresh this page to have the ability to upload files.');
                 $('#overallProgressAlert').addClass('alert-danger');
                 dropzoneArea.removeAllFiles(true);
                 $('#customFilePreview').empty();
               } else {
                 updateOverallProgress();
                 var fileNameToShow = file.name;
                 if(file.webkitRelativePath.length > 1) {
                   fileNameToShow = file.webkitRelativePath;
                 } else if(file.fullPath && file.fullPath.length > 1) {
                   fileNameToShow = file.fullPath;
                 }
                 $(file.previewElement.querySelector('.fullFilePath')).text(fileNameToShow);
                 $('#customFilePreview').append($(file.previewElement));
               }
             }
           });
           this.on("uploadprogress", function(file, progress) {
             $(file.previewElement.querySelector('.progress-bar')).css('width', (progress/4) + '%'); //expect upload to take 25% of the time. may need adjusting
           });
           this.on('sending', function(file, xhr, formData){
             var fileNameToShow = file.name;
             if(file.webkitRelativePath.length > 1) {
               fileNameToShow = file.webkitRelativePath;
             } else if(file.fullPath && file.fullPath.length > 1) {
               fileNameToShow = file.fullPath;
             }
             formData.append('relativePathName', fileNameToShow);
           });
           this.on("success", function(file, response) {
             $(file.previewElement.querySelector('.backupFilesId')).val(response);
             filesUploaded.push(response);
           });
           this.on("error", function(file) {
             $(file.previewElement.querySelector('.fa-spinner')).hide();
             $(file.previewElement.querySelector('.dataDzMessage')).hide();
             $(file.previewElement.querySelector('.fa-times')).fadeIn();
             $(file.previewElement.querySelector('#statusThumbnail')).removeClass('bg-info');
             $(file.previewElement.querySelector('#statusThumbnail')).addClass('bg-danger');
             reorderFilePreview($(file.previewElement), 'error');
           });
       }

   };

   if ($('#datafileDropzone').length) {
     dropzoneArea = new Dropzone('#datafileDropzone', dropzoneOptions);
   }

   table = $('#allEntries').DataTable( {
     "processing": true,
     "serverSide": true,
     "order": [[ 2, "desc" ]],
     "ajax": {
        url: "./api/getBackupsTable.php",
        type: 'POST'
     },
     "columns": [
       { "data": "filename" },
       { "data": "status" },
       { "data": "date_uploaded"},
       { "data": "job_number"},
       {
         mRender: function (data, type, row) {
           return '<button class="btn btn-primary btn-xs" type="button" onclick="viewInfo(\''+row['filename']+'\', \''+row['backup_path']+'\', \''+row['status']+'\', \''+row['machineId']+'\', \''+row['header']+'\', \''+row['date_uploaded']+'\', \''+row['job_number']+'\', \''+row['date_range'].replace(/\r?\n|\r/g," ")+'\')">View</button>';
         }, "orderable": false
       }
     ],
     "createdRow": function(row, data, dataIndex,cells){
       if(data['status'] !=  'Successfully Processed'){
         if(!data['status'].includes('% done')) {
           $(row).addClass('red');
         } else {
           $(row).addClass('blue');
         }
       }
     }
   });

   table2 = $('#infoTable').DataTable({
     "bFilter": false,
     "paging":   false,
     "ordering": false,
     "info":     false,
     "columnDefs": [{
      targets: 1,
      render: $.fn.dataTable.render.ellipsis(false, 75)
    }]
   });
 });
