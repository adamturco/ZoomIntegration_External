<?php
$name = $_POST['name'];
include('con.php');
if($name == "update_message"){
$id = $_POST['id'];
$message = $_POST['message'];
   $sql = "UPDATE update_message SET message ='$message' WHERE id='1'";
if ($conn->query($sql) === TRUE) {
    echo "Message Updated Succesfully";
} else {
    echo "Error updating record: " . $conn->error;
} 
}
if($name == "update_email"){
    $message = $_POST['message'];
    $message2 = $_POST['message2'];
    $message3 = $_POST['message3'];
    $message4 = $_POST['message4'];
    $message5 = $_POST['message5'];
    $message6 = $_POST['message6'];
    $sql = "UPDATE update_email SET message ='$message',message1 = '$message2',benifits = '$message3',member='$message4',class='$message5',t_message='$message6' WHERE id='1'";
if ($conn->query($sql) === TRUE) {
    echo "Email Updated Succesfully";
} else {
    echo "Error updating record: " . $conn->error;
} 
}
?>