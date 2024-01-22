<?php

$conn = new mysqli ("", "", "", "");

if (!$conn){
    die (mysqli_error($conn));
}

?>