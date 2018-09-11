var remainingMachineIds = [];
var originalFiltersAjaxReturn = {};
var secondTransform = [];

var selectedFilters = {
  job: '_na_',
  machine: '_na_',
  machineSn: '_na_',
  diameter: '_na_',
  material: '_na_',
  yaxes: []
};

var JOB_INDEX = 1;
var MACHINE_SN_INDEX = 2;
var MACHINE_INDEX = 4;
var PREFIX = 5;
var MODEL_NUMBER = 6;
var SUFFIX = 7;
var DIAMETER_INDEX = 11;
var MATERIAL_INDEX = 12;
var total_col = 10;

var resetFilters = function () {
  selectedFilters = {
    job: '_na_',
    machine: '_na_',
    machineSn: '_na_',
    diameter: '_na_',
    material: '_na_',
    yaxes: []
  };
  buildRemainingMachineIds();
  clearFilterDropdowns();
  for (var i=0; i<remainingMachineIds.length; i++) {
    populateFilterArrays(remainingMachineIds[i].dataArray);
  }
  selectFilterDropdowns();
  $("#filterDateEnd").datepicker("setDate", new Date());
  var oneWeekAgo = new Date();
  oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
  $("#filterDateStart").datepicker("setDate", oneWeekAgo);
};

var errorCheck = function() {
  var start = new Date($('#filterDateStart > input').val());
  var end   = new Date($('#filterDateEnd > input').val());
  if (start.getTime() > end.getTime()) {
    $.bootstrapGrowl("ERROR: start date must come before the end date", {
      type: 'danger',
      align: 'right'
    });
    return false;
  }
  if (selectedFilters.yaxes.length == 0) {
    $.bootstrapGrowl("ERROR: must select at least one y-axis", {
      type: 'danger',
      align: 'right'
    });
    return false;
  }
  if (selectedFilters.job == '_na_' && selectedFilters.machine == '_na_' && selectedFilters.machineSn == '_na_' && selectedFilters.diameter == '_na_' && selectedFilters.material == '_na_') {
    $.bootstrapGrowl("ERROR: must select at least one filter", {
      type: 'danger',
      align: 'right'
    });
    return false;
  }
  return true;
}

var drawGraph = function () {
  if (!errorCheck()) {
    return;
  }
  else {
    // Erase Previously Graphed Series
    var seriesLength = chart.series.length;
    for(var i = seriesLength -1; i > -1; i--) {
        chart.series[i].remove();
    }
    // Hide Table
    hideTable();
  }

  var machineIdsInFilter = [];
  var headerIdsInFilter = [];
  for (var i=0; i<remainingMachineIds.length; i++) {
    machineIdsInFilter.push(remainingMachineIds[i].id);
    headerIdsInFilter = headerIdsInFilter.concat(remainingMachineIds[i].file_headers);
  }
  var postData = {
    startDate: ($('#filterDateStart > input').val().length > 0 ? new Date($('#filterDateStart > input').val()).getTime() : ''),
    endDate: ($('#filterDateEnd > input').val().length > 0 ? new Date($('#filterDateEnd > input').val()).getTime() : ''),
    machineIds: machineIdsInFilter,
    headerIds: headerIdsInFilter,
    yaxes: selectedFilters.yaxes
  };

  var generateSubTitle = function(descriptions) {
    var startDateString = $('#filterDateStart > input').val().replace(/-/g, '/');
    var endDateString   = $('#filterDateEnd > input').val().replace(/-/g, '/');
    var subTitle = '';
    for (var i = 0; i < descriptions.length; i++) {
      subTitle = subTitle + descriptions[i] + ', ';
    }
    subTitle = subTitle.slice(0, -2) + ': ' + startDateString + ' - ' + endDateString;
    return subTitle;
  };

  $.ajax({
      url: './api/chartData.php',
      data: postData,
      type: "POST",
      success: function(data) {
        var hasData = false;
        chart.setTitle({ text: getJobName(selectedFilters.job) }, { text: generateSubTitle(data.descriptions) });
        for (var i = 0; i < selectedFilters.yaxes.length; i++) {
          if (data.series[selectedFilters.yaxes[i]].length > 0) hasData = true;
          if (i != data.eventIndex) {
            chart.addSeries({
              data: data.series[selectedFilters.yaxes[i]],
              name: data.descriptions[i]
            });
          } else {
            chart.addSeries({
              data: data.series[selectedFilters.yaxes[i]],
              name: 'Event',
              turboThreshold: 0,
              color: '#f05050',
            });
          }
        }
        tableReload(data.series, data.descriptions, data.eventIndex);
        if(!hasData) {
          $.bootstrapGrowl("The selected filters have no data for the time range", {
            type: 'danger',
            align: 'right'
          });
        }
      },
      cache: false
  });

};

var hideTable = function() {
  // Hide All Columns
  var column_id   = '#column_0';
  for (var i = 0; i < total_col; i++) {
    column_id = column_id.slice(0, -1) + i;
    table.column(column_id).visible(false);
  }
};

var tableReload = function(data, descriptions, eventIndex) {

  // Change Column Names
  var column_id   = '#column_0';
  table.column(column_id).visible(true);
  var col_header = table.column(column_id).header();
  $(col_header).html('Date - Time');
  for (var i = 0; i < descriptions.length; i++) {
    if (i != eventIndex) {
      column_id = column_id.slice(0, -1) + (i+1);
      table.column(column_id).visible(true);
      var col_header = table.column(column_id).header();
      $(col_header).html(descriptions[i]);
    }
  }

  // Fill Table with new data
  table.clear();
  transformData(data);
  table.rows.add(secondTransform);
  table.columns.adjust();
  table.draw();
};

var transformData = function(data) {
  var transformedData = {};
  // Removes any series that don't have data
  for (var key in data) {
    if (data[key].length === 0) {
      delete data[key];
    }
  }
  // Fills Object
  emptyArr = Array.apply(null, Array(10)).map(String.prototype.valueOf,"N/A");
  for (var i = 0; i < Object.keys(data).length; i++) {
    var index = Object.keys(data)[i];
    for (var j = 0; j < data[index].length; j++) {
      var timestamp = data[index][j][0];
      if (!(timestamp in transformedData)) {
        transformedData[timestamp] = emptyArr;
      }
      transformedData[timestamp][i+1] = data[index][j][1];
    }
  }
  // Fills secondTransform Array
  for (var timestamp in transformedData) {
    var date = new Date(Number(timestamp));
    transformedData[timestamp][0] = date.toLocaleString({ timeZone: "America/New_York" }).replace(/,/, ' - ');
    secondTransform.push(transformedData[timestamp].slice());
  }

};

var getJobName = function(jobId) {
  for(var i=0; i<originalFiltersAjaxReturn.jobs.length; i++) {
    if(originalFiltersAjaxReturn.jobs[i].laidigId == jobId) {
      return jobId + ': ' + originalFiltersAjaxReturn.jobs[i].description;
    }
  }
  return jobId;
};

var getMaterialName = function(materialId) {
  for(var i=0; i<originalFiltersAjaxReturn.materials.length; i++) {
    if(parseInt(originalFiltersAjaxReturn.materials[i].laidigId) == parseInt(materialId)) {
      return originalFiltersAjaxReturn.materials[i].laidigId + ' - ' + originalFiltersAjaxReturn.materials[i].description;
    }
  }
  return materialId;
};

var clearFilterDropdowns = function () {
  $('#filterJob').empty();
  $('#filterMachine').empty();
  $('#filterMachineSn').empty();
  $('#filterDiameter').empty();
  $('#filterMaterial').empty();
  $('#yAxesSelected').empty();

  addNotSelectedFilterOption();
};

var selectFilterDropdowns = function () {
  if (selectedFilters.job == '_na_' && $('#filterJob').children('option').length == 2) {
    selectedFilters.job = $('#filterJob').children('option')[1].value;
  }
  $('#filterJob').val(selectedFilters.job).trigger('change');
  if (selectedFilters.machine == '_na_' && $('#filterMachine').children('option').length == 2) {
    selectedFilters.machine = $('#filterMachine').children('option')[1].value;
  }
  $('#filterMachine').val(selectedFilters.machine).trigger('change');
  if (selectedFilters.machineSn == '_na_' && $('#filterMachineSn').children('option').length == 2) {
    selectedFilters.machineSn = $('#filterMachineSn').children('option')[1].value;
  }
  $('#filterMachineSn').val(selectedFilters.machineSn).trigger('change');
  if (selectedFilters.diameter == '_na_' && $('#filterDiameter').children('option').length == 2) {
    selectedFilters.diameter = $('#filterDiameter').children('option')[1].value;
  }
  $('#filterDiameter').val(selectedFilters.diameter).trigger('change');
  if (selectedFilters.material == '_na_' && $('#filterMaterial').children('option').length == 2) {
    selectedFilters.material = $('#filterMaterial').children('option')[1].value;
  }
  $('#filterMaterial').val(selectedFilters.material).trigger('change');
};

var addOptionToFilterDropdown = function(elementId, text, value) {
  if (!$('#' + elementId).find("option[value='" + value + "']").length) {
    var newOption = new Option(text, value, false, false);
    $('#' + elementId).append(newOption);
  }
};

var addNotSelectedFilterOption = function () {
  addOptionToFilterDropdown('filterJob', 'Not Selected', '_na_');
  addOptionToFilterDropdown('filterMachineSn', 'Not Selected', '_na_');
  addOptionToFilterDropdown('filterMachine', 'Not Selected', '_na_');
  addOptionToFilterDropdown('filterDiameter', 'Not Selected', '_na_');
  addOptionToFilterDropdown('filterMaterial', 'Not Selected', '_na_');
};

var populateFilterArrays = function (machineIdArray) {
  var machineSN   = machineIdArray[MACHINE_SN_INDEX] + machineIdArray[PREFIX] + machineIdArray[MODEL_NUMBER] + machineIdArray[SUFFIX];
  addOptionToFilterDropdown('filterJob', getJobName(machineIdArray[JOB_INDEX]), machineIdArray[JOB_INDEX]);
  addOptionToFilterDropdown('filterMachineSn', machineSN, machineIdArray[MACHINE_SN_INDEX]);
  addOptionToFilterDropdown('filterMachine', machineIdArray[MACHINE_INDEX], machineIdArray[MACHINE_INDEX]);
  addOptionToFilterDropdown('filterDiameter', machineIdArray[DIAMETER_INDEX], machineIdArray[DIAMETER_INDEX]);
  addOptionToFilterDropdown('filterMaterial', getMaterialName(machineIdArray[MATERIAL_INDEX]), machineIdArray[MATERIAL_INDEX]);
};

var buildRemainingMachineIds = function () {
  remainingMachineIds.length = 0;
  var machineIds = originalFiltersAjaxReturn.parsedOriginalMachineIds;
  for (var i=0; i<machineIds.length; i++) {
    if( (selectedFilters.job == '_na_' || machineIds[i].dataArray[JOB_INDEX] == selectedFilters.job) && (selectedFilters.machine == '_na_' || machineIds[i].dataArray[MACHINE_INDEX] == selectedFilters.machine) && (selectedFilters.machineSn == '_na_' || machineIds[i].dataArray[MACHINE_SN_INDEX] == selectedFilters.machineSn) && (selectedFilters.diameter == '_na_' || machineIds[i].dataArray[DIAMETER_INDEX] == selectedFilters.diameter) && (selectedFilters.material == '_na_' || machineIds[i].dataArray[MATERIAL_INDEX] == selectedFilters.material)) {
      remainingMachineIds.push(machineIds[i]);
    }
  }
};

var setFilterDropdowns = function () {
  addNotSelectedFilterOption();
  $.ajax({
      url: './api/populateFiltersInJs.php',
      type: "GET",
      success: function(data) {
        if (data.status) {
          console.log(data);
          originalFiltersAjaxReturn = data;
          originalFiltersAjaxReturn.parsedOriginalMachineIds = [];
          for(var i=0; i<data.machineIds.length; i++) {
            var machineIdArray = CSVToArray(data.machineIds[i].data)[0];
            remainingMachineIds.push({id: data.machineIds[i].id, dataArray: machineIdArray});
            originalFiltersAjaxReturn.parsedOriginalMachineIds.push({id: data.machineIds[i].id, dataArray: machineIdArray, file_headers: data.machineIds[i].file_headers.split(',')});
            populateFilterArrays(machineIdArray);
          }
          if (selectedFilters.job !== '_na_') {
            buildRemainingMachineIds();
            populateYAxisSelector();
          }
          selectFilterDropdowns();

        }
      }
  });
};

var storeFilterValues = function () {
  selectedFilters = {
    job: $('#filterJob').val(),
    machine: $('#filterMachine').val(),
    machineSn: $('#filterMachineSn').val(),
    diameter: $('#filterDiameter').val(),
    material: $('#filterMaterial').val(),
    yaxes: $('#yAxesSelected').val()
  };
};

var populateYAxisSelector = function () {
  var headerIdsToGet = [];
  for (var i=0; i<remainingMachineIds.length; i++) {
    populateFilterArrays(remainingMachineIds[i].dataArray);
    headerIdsToGet = headerIdsToGet.concat(remainingMachineIds[i].file_headers);
  }
  $.ajax({
      url: './api/populateYAxisSelect.php',
      type: "POST",
      data: {
        headers: headerIdsToGet
      },
      success: function(data) {
        if (data.status) {
          $('#yAxesSelected').empty();
          console.log(data);
          for (var i=0; i<data.headers.length; i++) {
            addOptionToFilterDropdown('yAxesSelected', data.headers[i].displayText, data.headers[i].id);
          }
          $('#yAxesSelected').val(selectedFilters.yaxes).trigger('change');

        }
      }
  });
};

$('.selecttwo').on('select2:select', function (e) {
  storeFilterValues();
  if($(this).attr('id') == 'yAxesSelected') return;
  buildRemainingMachineIds();
  clearFilterDropdowns();
  populateYAxisSelector();
  selectFilterDropdowns();
});

var chart; // must be global
// CHART
Highcharts.setOptions({
  time: { timezoneOffset: 4 * 60 }
});

$(function() {

  $('.selecttwo').each(function(i, obj) {
      $(this).select2({
        minimumResultsForSearch: Infinity,
        theme: 'bootstrap4',
        sorter: function(data) {
          return data.sort(function (a, b) {
              a = a.text.toLowerCase();
              b = b.text.toLowerCase();
              if(a == 'not selected') {
                return -1;
              } else if (a > b) {
                  return 1;
              } else if (a < b) {
                  return -1;
              }
              return 0;
          });
        }
      });
  });
  $('.date').each(function(i, obj) {
      $(this).datepicker({
        format: 'mm-dd-yyyy',
        endDate: '+1d',
        autoclose: true
      });
  });
  $("#filterDateEnd").datepicker("setDate", new Date());
  var oneWeekAgo = new Date();
  oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
  $("#filterDateStart").datepicker("setDate", oneWeekAgo);


  setFilterDropdowns();

  chart = Highcharts.chart('container', {
      chart: {
        type: 'line',
        zoomType: 'x'
      },
      tooltip: {
        formatter: function() {
          var tooltips = [];
          var date = new Date(this.x);
          var minutes = "0" + date.getMinutes();
          var seconds = "0" + date.getSeconds();
          var dateString = '<b>DateTime: </b>' + date.getMonth() + '/' + date.getDate() + '/' + date.getFullYear() + ' ' + date.getHours() + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
          tooltips.push(dateString);
          this.points.forEach(function(point) {
            var fullString;
            if (point.series.name == 'Event') {
              fullString = '<b>Event: </b>' + point.point.event;
            } else {
              fullString = '<b>' + point.series.name + ': </b>' + point.y;
            }
            tooltips.push(fullString);
          });
          return tooltips;
        },
        split: true
      },
      title: { text: '' },
      xAxis: {
          crosshair: { enabled: true },
          title: { text: 'Time' },
          type: 'datetime'
      },
      yAxis: [{ // Primary yAxis
        opposite: true
      }, { // Secondary yAxis
        labels: {
          format: '{value}',
          style: {
            color: Highcharts.getOptions().colors[2]
          }
        }
      }],
      credits: {
        enabled: false
      },
      exporting: {
        sourceWidth: 1200,
        sourceHeight: 600,
        buttons: {
            contextButton: {
                menuItems: ['downloadPDF', 'downloadPNG', 'downloadSVG', 'downloadJPEG', 'separator', 'downloadCSV', 'downloadXLS', 'viewData']
            }
        }
      }
  });

  table = $('#chartTable').DataTable({
    data: secondTransform,
    deferRender: true,
    pageLength: 100,
    bFilter: false,
    bLengthChange: false
  });
  hideTable();

  $(".preloadScreen").fadeOut("slow");
});
