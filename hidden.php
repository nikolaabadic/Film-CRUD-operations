<?php
    session_start();
    if(!isset($_SESSION['loggedIn'])){
        header('Location: index.html');
        exit();
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Film database</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
</head>
<body>

    <div class="container" style="margin-top: 30px;">

        <div id="tableManager" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Film Name</h2>
                    </div>

                    <div class="modal-body">
                        <div id="editContent">
                            <input type="text" class="form-control" id="name" placeholder="Film name"><br>
                            <input type="number" class="form-control" id="year" placeholder="Year"><br>
                            <textarea class="form-control" id="description" placeholder="Description"></textarea><br>
                            <input type="hidden" id="editRowID" value="0">
                        </div>

                        <div id="showContent" style="display:none;">
                            <h3>Year</h3>
                            <div id="yearView"></div>
                            <hr>
                            <h3>Description</h3>
                            <div id="descriptionView" style="overflow-y: scroll; height: 300px;"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="button" class="btn btn-primary" data-dismiss="modal" value="Close" id="closeBtn" style="display: none;">
                        <input type="button" id="manageBtn" onclick="manageData('addNew')" value="Save" class="btn btn-success">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <input style="float: right" type="button" class="btn btn-warning" id="logout" value="Logout">
                <h2 class="display-4">Film database</h2>

                <input style="float: right" type="button" class="btn btn-success" id="addNew" value="Add New">
                <br><br>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Name</td>
                            <td>Options</td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <script src="http://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="js/dataTables.bootstrap.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#addNew").on('click', function () {
               $(".modal-title").html("Add film");
               $("#tableManager").modal('show');
            });

            $("#logout").on('click', function(){
                $.ajax({
                    url: 'execute.php',
                    method: 'POST',
                    data: {
                        key: 'logout'
                    },
                    success: function(response){
                        window.location.href = "index.html";
                    }
                });
            });

            $("#tableManager").on('hidden.bs.modal', function () {
               $("#showContent").fadeOut();
               $("#editContent").fadeIn();
               $("#editRowID").val(0);
               $("#description").val("");
               $("#year").val("");
               $("#name").val("");
               $("#closeBtn").fadeOut();
               $("#manageBtn").attr('value', 'Add New').attr('onclick', "manageData('addNew')").fadeIn();
            });

            getExistingData(0, 50);
        });

        function deleteRow(rowID) {
            if (confirm('Are you sure you want to delete this film?')) {
                $.ajax({
                    url: 'execute.php',
                    method: 'POST',
                    dataType: 'text',
                    data: {
                        key: 'deleteRow',
                        rowID: rowID
                    }, success: function (response) {
                        location.reload();
                        alert(response);
                    }
                });
            }
        }

        function viewORedit(rowID, type) {
            $.ajax({
                url: 'execute.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    key: 'getRowData',
                    rowID: rowID
                }, success: function (response) {
                    if (type == "view") {
                        $("#showContent").fadeIn();
                        $("#editContent").fadeOut();
                        $("#descriptionView").html(response.description);
                        $("#yearView").html(response.year);
                        $("#manageBtn").fadeOut();
                        $("#closeBtn").fadeIn();
                    } else {
                        $("#showContent").fadeOut();
                        $("#editContent").fadeIn();
                        $("#editRowID").val(rowID);
                        $("#description").val(response.description);
                        $("#year").val(response.year);
                        $("#name").val(response.name);
                        $("#closeBtn").fadeOut();
                        $("#manageBtn").attr('value', 'Edit').attr('onclick', "manageData('updateRow')");
                    }

                    $(".modal-title").html(response.name);
                    $("#tableManager").modal('show');
                }
            });
        }

        function getExistingData(start, limit) {
            $.ajax({
                url: 'execute.php',
                method: 'POST',
                dataType: 'text',
                data: {
                    key: 'getExistingData',
                    start: start,
                    limit: limit
                }, success: function (response) {
                    if (response != "reachedMax") {
                        $('tbody').append(response);
                        start += limit;
                        getExistingData(start, limit);
                    } else
                        $(".table").DataTable();
                }
            });
        }

        function manageData(key) {
            var name = $("#name");
            var description = $("#description");
            var year = $("#year");
            var editRowID = $("#editRowID");

            if (isNotEmpty(name) && isNotEmpty(description) && isNotEmpty(year)) {
                $.ajax({
                   url: 'execute.php',
                   method: 'POST',
                   dataType: 'text',
                   data: {
                       key: key,
                       name: name.val(),
                       description: description.val(),
                       year: year.val(),
                       rowID: editRowID.val()
                   }, success: function (response) {
                       if (response != "success")
                           alert(response);
                       else {
                           location.reload();
                           name.val('');
                           description.val('');
                           year.val('');
                           $("#tableManager").modal('hide');
                           $("#manageBtn").attr('value', 'Add').attr('onclick', "manageData('addNew')");
                       }
                   }
                });
            }
        }

        function isNotEmpty(caller) {
            if (caller.val() == '') {
                caller.css('border', '1px solid red');
                return false;
            } else
                caller.css('border', '');

            return true;
        }
    </script>
</body>
</html>