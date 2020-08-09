<?php
print_r($_FILES);
$nombre = $_FILES['file']['name'];
$guardado = $_FILES['file']['tmp_name'];

//if (!file_exists('archivos')){
//    mkdir('archivos',0777,true);
//}

if(!rename ("$guardado","assets/images" . $nombre)) {
//if (move_uploaded_file($guardado, 'assets/images' . $nombre)) {
    echo "Archivo guardado";
}
/*
if (isset($_FILES['file'])) {
    //include_once("db_connect.php");
    //foreach($_FILES['file']['name'] as $key=>$val){


    $originalName = $_FILES['file']['name'];
    $ext = '.' . pathinfo($originalName, PATHINFO_EXTENSION);
    $generatedName = md5($_FILES['file']['tmp_name']) . $ext;
    $filePath = $path . $generatedName;
    if (!is_writable($path)) {
        echo json_encode(array(
            'status' => false,
            'msg'    => 'Destination directory not writable.'
        ));
        exit;
    }
    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        echo json_encode(array(
            'status' => true,
            'generatedName' => $generatedName
        ));
        
    } else {
        echo json_encode(array(
            'status' => false,
            'generatedName' => $generatedName
        ));
    }
    
} else {
    echo json_encode(array('status' => false, 'msg' => 'No file uploaded.'));
    exit;
}*/
    //$upload_dir = "upload/";
    //$upload_file = $upload_dir . $_FILES['file']['name'][$key];
    //$filename = $_FILES['file']['name'][$key];
    //if (move_uploaded_file($_FILES['file']['tmp_name'][$key], $upload_file)) {
        //$insert_sql = "INSERT INTO images(file_name) VALUES ('" . $filename . "')";
        //mysqli_query($conn, $insert_sql) or die("database error: " . mysqli_error($conn));
    //}
    //}*/
echo 'File uploaded and saved in database successfully.';
