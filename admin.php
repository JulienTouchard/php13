<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] === "ROLE_USER"){
    header("Location: ./index.php");
}
echo "success admin";