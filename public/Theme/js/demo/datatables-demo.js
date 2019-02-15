// Call the dataTables jQuery plugin
$(document).ready(function() {
  var i = 1;
  while ($('#dataTable-' +  i) != null && i< 50) {
    $('#dataTable-' +  i).DataTable();
    i++;
  }
});
