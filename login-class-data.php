<?php
    # when 'submit class info' button clicked on login screen, verify teacher password and populate lab numbers
    session_start();
    # database credentials
    $db_server = $_SESSION['db_server'];
    $db_username = $_SESSION['db_username'];
    $db_password = $_SESSION['db_password'];
    $db_database = $_SESSION['db_database'];
    # database variables
    $teachers_table = $_SESSION['teachers_table'];
    $crn_column = $_SESSION['crn_column'];
    $teacher_password_column = $_SESSION['teacher_password_column'];
    $labs_table = $_SESSION['labs_table'];
    $lab_id_column = $_SESSION['lab_id_column'];
    # fetch variables from javascript
    $crn = $_POST['crn'];
    $teacher_password = $_POST['teacher_password'];
    # connect to database
    $conn = new mysqli($db_server, $db_username, $db_password, $db_database);
    if($conn->connect_error){
        die("Connection Failed");
    }
    # fetch teacher info from teachers table
    $stmt = $conn->prepare("SELECT $teacher_password_column FROM $teachers_table WHERE $crn_column=?");
    $stmt->bind_param('s', $crn);
    $stmt->execute();
    $result = $stmt->get_result();
    $output = "";
    if($result->num_rows > 0){
        $fetch = $result->fetch_assoc()[$teacher_password_column];
        # verify teacher password
        if($fetch != $teacher_password){
            $output = "Teacher_Error";
            echo $output ? $output : $output;
            return;
        }
    }else{
        $output = "";
    }
    # fetch lab info from labs table
    $stmt = $conn->prepare("SELECT $lab_id_column FROM $labs_table WHERE $crn_column=?");
    $stmt->bind_param('s', $crn);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            # create one long string of lab ids separated by @s
            $output .= "$row[$lab_id_column]" . '@';
        }
    }else{
        $output = "";
    }
    $conn->close();
    echo $output ? "$output" : "Lab_Error";
?>