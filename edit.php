<?php

require 'helpers/dbConnection.php';
require 'helpers/functions.php';

$id = $_GET['id'];

$sql = "select * from blogs where id = $id";
$op  = mysqli_query($con, $sql);
$data = mysqli_fetch_assoc($op);
$blogdate = date('Y-m-d', strtotime($_POST['blogdate']));
$test_arr  = explode('-', $blogdate);

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $title     = Clean($_POST['title']);
    $content = Clean($_POST['content']);


    # Validate ...... 

    $errors = [];
        #validate date
    if (!empty($blogdate)) {
            $errors['date'] = "Date  Required";
    }
    else if (checkdate($test_arr[0], $test_arr[1], $test_arr[2])) {
        $errors['date'] = "Date  Not Valid";
    }

    # validate title .... 
    if (empty($title)) {
        $errors['title'] = "Title  Required";
    }
    else if (is_numeric($title))
    {
         $errors['title']= "title Cannot Be Numbers Only Must Be String";
    }
    # validate content

    if (empty($content)) {
        $errors['content'] = "Content Required";
    } elseif (strlen($content) > 50) {
        $errors['content'] = "Length Must be smaller than 50 chars";
    }
    if (!empty($_FILES['image']['name'])) {
    
        $imageName    = $_FILES['image']['name'];
        $imageTemPath = $_FILES['image']['tmp_name'];
        $imageSize    = $_FILES['image']['size'];
        $imageType    = $_FILES['image']['type'];
    
        $typesInfo  =  explode('/', $imageType);    
        $extension  =  strtolower(end($typesInfo));      
        $allowedType = ['png','jpg','jpeg'];  
    
        if (in_array($extension, $allowedType)) {
    
            # Create Final Name ... 
            $FinalName = time() . rand() . '.' . $extension;
    
            $disPath = 'uploads/' . $FinalName;
    
            if (move_uploaded_file($imageTemPath, $disPath)) {
    
                echo 'Image Uploaded <br>';
            } else {
                echo 'Error Try Again';
            }
        }else{
            echo 'InValid Extension';
        }
    } else {
        $errors['Image']= "Required";
    }


    # Check ...... 
    if (count($errors) > 0) {
        // print errors .... 

        foreach ($errors as $key => $value) {
            # code...

            echo '* ' . $key . ' : ' . $value . '<br>';
        }
    } else {

         # DB OP ......... 

         $sql = "update blogs set title='$title' , content = '$content' , image = '$disPath' , blogdate='$blogdate' where  id = $id";

        $op =  mysqli_query($con, $sql);

        if ($op) {
            $message =  'Raw updated';
            # Set Message to Session

            $_SESSION['Message'] = $message;

            header("location: index.php");
        } else {
            echo 'Error Try Again ' . mysqli_error($con);
        }
        # Close Connection .... 
        mysqli_close($con);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Blog</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>

<div class="container">
    <h2>Edit Blog</h2>

    <form  method="post" enctype="multipart/form-data">

        <div class="form-group">
            <label for="exampleInputName">Title</label>
            <input type="text" class="form-control" required id="exampleInputName" aria-describedby="" name="title"
            value="<?php echo $data['title'] ?>"      placeholder="Enter Title">
        </div>


        <div class="form-group">
            <label for="exampleInputEmail">Content</label>
            <input type="text" class="form-control" required id="exampleInputEmail1" aria-describedby="emailHelp"
            value="<?php echo $data['content'] ?>"        name="content" placeholder="Enter Content">
        </div>
        <div class="form-group">
        <label for="start">Choose date:</label>
        <input type="date" name="blogdate" value="<?php echo $data['blogdate'] ?>"   id="dateOfBirth" name="dateOfBirth" placeholder="MM/DD/YYYY" required>
        </div>
        <div class="form-group">
        Select Image to upload:
        <input type="file" name="image" id="fileToUpload">
        </div>


        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>


</body>

</html>