<?php

require 'helpers/dbConnection.php';
require 'helpers/functions.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $title     = Clean($_POST['title']);
    $content = Clean($_POST['content']);
    $blogdate = date('Y-m-d', strtotime($_POST['blogdate']));
    $test_arr  = explode('-', $blogdate);
    // echo $new_date;

    # Validate ...... 

    $errors = [];
    #validate date
    if (empty($blogdate)) {
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

        //  $sql = "insert into users (title,content,image,date) values ('$title','$content','$disPath','$date')";
         $sql = "insert into blogs (title,content,image,blogdate) values ('$title','$content','$FinalName','$blogdate')";


        $op =  mysqli_query($con,$sql);

         if($op){
             echo 'Raw Inserted';
         }else{
             echo 'Error Try Again '. mysqli_error($con);
         }
     # Close Connection .... 
      mysqli_close($con); 
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create New Blog</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>

<div class="container">
    <h2>Create New Blog</h2>

    <form  method="post" enctype="multipart/form-data">

        <div class="form-group">
            <label for="exampleInputName">Title</label>
            <input type="text" class="form-control" required id="exampleInputName" aria-describedby="" name="title"
                   placeholder="Enter Title">
        </div>


        <div class="form-group">
            <label for="exampleInputEmail">Content</label>
            <input type="text" class="form-control" required id="exampleInputEmail1" aria-describedby="emailHelp"
                   name="content" placeholder="Enter Content">
        </div>
        <div class="form-group">
        Select Image to upload:
        <input type="file" name="image" id="fileToUpload">
        </div>
        <div class="form-group">
        <label for="start">Choose date:</label>
        <input type="date" name="blogdate"  id="dateOfBirth" name="dateOfBirth" placeholder="MM/DD/YYYY" required>

        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>


</body>

</html>