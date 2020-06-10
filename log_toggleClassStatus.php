<?php
    require_once('log_db-functions.php'); 
	require_once('log_validate-functions.php');
	require_once('conn.php');

    if (empty($_POST['rowid']))
    {
        // cannot proceed
    } else {
        // fields are populated
        $rowData     = explode('~',substr(check_input($_POST['rowid']), 3)); //strip out the word 'row' and parse ~
        $rowid       = $rowData[0];
        $userSiteKey = $rowData[1];

        $luStudioSchedule_query = sprintf("SELECT * FROM tblStudioSchedule WHERE stsStudioID = %s AND stsClassID = %s",
                                  sanitize($userSiteKey),
                                  sanitize($rowid));
        $luStudioSchedule_result = $conn->query($luStudioSchedule_query);

        if ($luStudioSchedule_result->num_rows >0)
        {
            $luStudioSchedule_row = $luStudioSchedule_result->fetch_assoc();

            $deleteClass_query = sprintf("DELETE FROM tblStudioSchedule WHERE id = %s",
                                 sanitize($luStudioSchedule_row['id'], 'int'));
            $deleteClass_result = $conn->query($deleteClass_query);
            
            $returnMsg = ($deleteClass_result->affected_rows() >0) ? 'Deleted':'Failed';

        } else {

            $insertClass_query = sprintf("INSERT INTO tblStudioSchedule (stsStudioID, stsClassID) VALUES (%s, %s)",
                                 sanitize($userSiteKey, 'int'),
                                 sanitize($rowid, 'int'));
            $insertClass_result = $conn->query($insertClass_query);
            
            $returnMsg = ($insertClass_result) ? 'Inserted':'Failed';

        }
        
        echo json_encode(['msg'=>$returnMsg]);

    }
?>