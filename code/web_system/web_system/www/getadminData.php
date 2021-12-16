<?php
require_once ("config.php");

if (! (isset($_GET['pageNumber']))) {
    $pageNumber = 1;
} else {
    $pageNumber = $_GET['pageNumber'];
}

$perPageCount = 10;

$sql = "SELECT * FROM feedback  order by id desc";

if ($result = mysqli_query($link, $sql)) {
    $rowCount = mysqli_num_rows($result);
    mysqli_free_result($result);
}

$pagesCount = ceil($rowCount / $perPageCount);

$lowerLimit = ($pageNumber - 1) * $perPageCount;

$sqlQuery = " SELECT * FROM feedback  ORDER BY id desc limit " . ($lowerLimit) . " ,  " . ($perPageCount) . " ";
$results = mysqli_query($link, $sqlQuery);

?>

<table class="table" id='feedbacksTable'>
    <tr>
        <th> <input type='button' value='Delete' id='deletefb'><br><br></th>
        <th>System name</th>
        <th>Name</th>
        <th>Email</th>
        <th>Comments</th>     
        <th>Post time</th>  
    </tr>
    <?php foreach ($results as $data) { ?>
    <tr id='tr_<?php echo $data["id"];  ?>'>
        <td><input type="checkbox" id='del_<?php echo $data["id"];  ?>' name="delete[]" value="<?php echo $data["id"]; ?>"></td>
        <td><?php if($data["appname"] == "1") {echo "School reopen simulation";} elseif($data["appname"] == "2") {echo "Medical Resource Deficiencies Dashboard";} else{echo "Health Risk prediction";}?>
        </td>
        <td><?php echo $data["gname"]; ?></td>
        <td><?php echo $data["email"]; ?></td>
        <td><?php echo $data["comment"]; ?></td>  
        <td><?php echo date_format(date_create($data["posttime"]), 'm-d-Y h:i:s'); ?></td>           
    </tr>
    <?php
    }
    ?>
</table>

<div style="height: 30px;"></div>
<table width="50%" align="center">
    <tr>

        <td valign="top" align="left"></td>


        <td valign="top" align="center">

            <?php
	for ($i = 1; $i <= $pagesCount; $i ++) {
    if ($i == $pageNumber) {
        ?>
            <a href="javascript:void(0);" class="current"><?php echo $i ?></a>
            <?php
    } else {
        ?>
            <a href="javascript:void(0);" class="pages"
                onclick="showRecords('<?php echo $perPageCount;  ?>', '<?php echo $i; ?>');"><?php echo $i ?></a>
            <?php
    } // endIf
} // endFor

?>
        </td>
        <td align="right" valign="top">
            Page <?php echo $pageNumber; ?> of <?php echo $pagesCount; ?>
        </td>
    </tr>
</table>
<script>
$(document).ready(function() {

    $('#deletefb').click(function() {

        var post_arr = [];

        // Get checked checkboxes
        $('#feedbacksTable input[type=checkbox]').each(function() {
            if (jQuery(this).is(":checked")) {
                var id = this.id;
                var splitid = id.split('_');
                var postid = splitid[1];

                post_arr.push(postid);

            }
        });

        if (post_arr.length > 0) {

            var isDelete = confirm("Do you really want to delete records?");
            if (isDelete == true) {
                // AJAX Request
                $.ajax({
                        url: 'deletefeedback.php',
                        type: 'POST',
                        data: {
                            post_id: post_arr
                        },
                        success: function(response) {
                            $.each(post_arr, function(i, l) {
                                $("#tr_" + l).remove();
                            });
                        }
                    })
            }
        }
    });

});
</script>