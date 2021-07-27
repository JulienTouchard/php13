<?php
require('func.php');
require('pdo.php');

if (!empty($_POST['submitted'])) {
    $errors = [];
    // traitement du formulaire
    foreach ($_POST as $key => $value) {
        $_POST[$key] = xss($value);
    }
    // valid text
    $errors = validText($errors, $_POST['pwd'], 'pwd', 5, 8);
    $errors = validText($errors, $_POST['name'], 'name', 5, 10);
    // valid email
    $errors = validEmail($errors, $_POST['email']);
    // valid checkbox
    if (empty($_POST['mentions'])) {
        $errors['mentions'] = "Conditions obligatoires";
    }
    // verif image
    if ($_FILES['avatar']['size'] === 0) {
        $errors['avatar'] = "Image obligatoire";
    } else {
        $tabTypImg = ["image/jpg","image/jpeg","image/png","image/webp"];
        $boolImg = false;
        for ($i=0; $i <count($tabTypImg) ; $i++) { 
            if($_FILES['avatar']['type'] === $tabTypImg[$i]){
                $boolImg = true;
            }
        }
        $boolImg ? : $errors['avatar'] = "Le format n'est pas bon";

        /* if ($_FILES['avatar']['type'] === "image/jpg" || $_FILES['avatar']['type'] === "image/jpeg" || $_FILES['avatar']['type'] === "image/png" || $_FILES['avatar']['type'] === "image/webp") {
            //success
            var_dump("success",getimagesize($_FILES['avatar']['tmp_name']));
            // commit        
        } else {
            $errors['avatar'] = "Le format n'est pas bon";
        } */


        if ($_FILES['avatar']['size'] >= 2000000) {
            $errors['avatar'] = "Le fichier fait plus de 2 Mo";
        }
    }

    if (count($errors) === 0) {

        // traitement pdo
        $sql = "INSERT INTO user (email,pwd,name,avatar,created_at)
        VALUES (:email,:pwd,:name,:avatar,NOW())";
        $query = $pdo->prepare($sql);
        $query->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $query->bindValue(':pwd', $_POST['pwd'], PDO::PARAM_STR);
        $query->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
        $query->bindValue(':avatar', "../asset/upload/" . $_FILES['avatar']['name'], PDO::PARAM_STR);

        $query->execute();
        
        if (!is_dir("../asset/upload")) {
            mkdir("../asset/upload");
        }
        move_uploaded_file($_FILES['avatar']['tmp_name'], "../asset/upload/" . $_FILES['avatar']['name']);
        // tout c'est bien passé
        header("Location: ../index.php");

    } else {
        // tout ne s'est pas bien passé
        header("Location: ../registration.php?errors=".serialize($errors)."&data=".serialize($_POST));
    }
    debug($_FILES);
    debug($errors);
    debug($_POST);
    die;
} else {
    // n'a pas acces à cette page
    header("Location: ../index.php");
}
