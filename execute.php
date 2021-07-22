<?php

include 'operations.php';

$operations = new Operations();

if (isset($_POST['key'])) {
    switch($_POST['key']){
        case 'addNew':
            $operations->insert();
            break;
        case 'getExistingData':
            $operations->readAll();
            break;
        case 'getRowData':
            $operations->readById();
            break;
        case 'updateRow':
            $operations->update();
            break;
        case 'deleteRow':
            $operations->delete();
            break;
        case 'logout':
            $operations->logout();
            break;    
    }
}

?>