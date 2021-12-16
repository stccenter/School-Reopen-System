<?php
// Initialize the session
session_start();
require('config.php');
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true){
    header("location: adminlogin.php");
    exit;
}

if(!isAdmin()){
    header("location: unauthorized.php");
    exit;
}


require_once __DIR__ . './adminPagination.php';

$paginationModel = new Pagination();
$pageResult = $paginationModel->getPage();
$queryString = "?";
if (isset($_GET["page"])) {
    $pn = $_GET["page"];
} else {
    $pn = 1;
}
$limit ='15';

$totalRecords = $paginationModel->getAllRecords();
$totalPages = ceil($totalRecords / $limit);

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>System</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/checkout/">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css" />
    <!-- Bootstrap core CSS -->
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

    <style>
    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }

    .content {
        height: 100%;
        overflow: auto;
        position: relative;
    }

    .card-body {
        padding: .5rem;
    }


    #chartContainer {
        height: 100%;
        width: 100%;
        /* float: left; */
        /* position: fixed; */
        margin: auto;
    }


    .user-text {
        margin-right: 20px;
        color: #ffffff;
    }

    .navbar {
        margin-bottom: 20px;
    }
    </style>
</head>

<body class="bg-white">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!--<a class="navbar-brand" href="#">ABM</a>-->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample07"
                aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav mr-auto">

                </ul>
                <span class="user-text">Welcome <?php echo $_SESSION["adminuserrole"]; ?></span>
                <a href="adminlogout.php">Log Out</a>
            </div>
        </div>
    </nav>

    <div class="col-lg-12">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row">
                            <div class="col">

                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title" id="title_1">User permission control</h3>
                                    <h3 class="card-title" id="title_2">Feedback</h3>
                                </div>
                            </div>
                            <div class="col-4 align-self-center">
                                <div class="card-tools">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            Select Panel
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" id="show_1" href="#">User permission control</a>
                                            <a class="dropdown-item" id="show_2" href="#">Feedback</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="table_1" style="overflow-x: auto; height: 800px;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> No. </th>
                                    <th>Name</th>
                                    <th>User level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count=1;
                                $userdata = array();

                                $sql = "SELECT id,username, userlevel FROM users";
                                
                                $result = mysqli_query($link,$sql);
                                

                                while($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $count; ?></td>
                                    <td><?php echo $row["username"]; ?></td>
                                    <td>
                                        <select id="updateLevel_<?php echo $row["id"]; ?>">
                                            <?php
                                    $sql1 = "SELECT id, userlevel FROM userlevel";
                                    $result1 = mysqli_query($link,$sql1);
                                    while ($rows = mysqli_fetch_assoc($result1))
                                    { 
                                    if($row['userlevel'] == $rows['id'])
                                    {
                                    ?>
                                            <option value="<?php echo $rows['id']; ?>" selected="selected">
                                                <?php echo $rows['userlevel']; ?></option>
                                            <?php
                                    }
                                    else { ?>
                                            <option value="<?php echo $rows['id']; ?>"><?php echo $rows['userlevel']; ?>
                                            </option>
                                            <?php  
                                    }
                                    }
                                    ?>
                                        </select>
                                    </td>
                                    <td><button id="updateLevel_<?php echo $row["id"]; ?>"
                                            onclick='updateUL(<?php echo $row["id"]; ?>)' type="button"
                                            class="btn btn-sm btn-outline-secondary">Update</button>
                                        <p id='UpdateMsg_<?php echo $row["id"];?>'> </p>
                                    </td>
                                </tr>
                                <?php $count++; } 
mysqli_free_result($result);
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body" id="table_2" style="overflow-x: auto; height: 800px;">
                    <div id="results"></div>
 
                        <?php
                        
                            // Close statement
                            mysqli_close($link);
                            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="../assets/dist/js/bootstrap.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.esm.js"></script>

    <script nomodule src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ionic/core/css/ionic.bundle.css" />

    <script src="https://d3js.org/d3.v4.js"></script>
    <!--Validation js-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="../assets/dist/js/canvasjs.min.js"></script>
    <script type="text/javascript"></script>
    <script>
    function updateUL(id) {
        var value = $("#updateLevel_" + id).find('option:selected').val();
        //$(".edit").removeClass("editMode");

        $.ajax({
            url: 'edituser.php',
            type: 'post',
            data: {
                value: value,
                id: id
            },
            success: function(response) {
                if (response == 1) {
                    $("#UpdateMsg_" + id).html("Saved successfully.");
                } else {
                    $("#UpdateMsg_" + id).html("Not saved.");
                }
            }
        });
    }


    function showRecords(perPageCount, pageNumber) {
        $.ajax({
            type: "GET",
            url: "getadminData.php",
            data: "pageNumber=" + pageNumber,
            cache: false,
            success: function(html) {
                $("#results").html(html);
            }
        });
    }
    
    $(document).ready(function() {
        showRecords(10, 1);
        $("#table_2").hide();
        $("#title_2").hide();
        $("#show_1").click(function() {
            $("#table_2").hide();
            $("#title_2").hide();
            $("#table_1").show();
            $("#title_1").show();
        });
        $("#show_2").click(function() {
            $("#table_1").hide();
            $("#title_1").hide();
            $("#table_2").show();
            $("#title_2").show();
        });

    
        $('.edit').click(function() {
            $(this).addClass('editMode');
            var id = this.id;
            var split_id = id.split("_");
            var field_name = split_id[0];
            var edit_id = split_id[1];
            $("#updateLevel_" + edit_id).removeAttr("hidden");
        });



    })
    </script>

</body>

</html>