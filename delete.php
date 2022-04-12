<?php 

// delete raw from db  ..... 

require 'helpers/dbConnection.php';

$id = $_GET['id'];

if(filter_var($id,FILTER_VALIDATE_INT)){
// code .... 

$sql = "delete from blogs where id = $id"; 

$op = mysqli_query($con,$sql); 

if($op){
    $message = 'Raw Removed';

}else{
    $message = 'Error Try Again';
}


}else{
    $message = 'invalid ID';
}

 # Set Message to Session
 
 $_SESSION['Message'] = $message; 

header("location: index.php"); 

?>