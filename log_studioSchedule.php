<?php

    date_default_timezone_set('America/New_York'); 
	require_once('log_db-functions.php'); 
	require_once('log_validate-functions.php');
	require_once('conn.php');

    $currentAction = 'Please Select Your Studio to Display Your Schedule';

    // Build option list for Studio select
    $studioList_query = "SELECT * FROM tblStudios ORDER BY sdoName ASC";
    
    $studioList_result = $conn->query($studioList_query);
    
    $studioList = '';

    while ($studioList_row = $studioList_result->fetch_assoc())
    {
        $studioList .= '<a class="dropdown-item" href="'.$_SERVER['PHP_SELF'].'?siteid='.$studioList_row['sdoSiteID'].'">'.stripslashes($studioList_row['sdoName']).'</a>';
        $allStudios[$studioList_row['sdoSiteID']] = array(stripslashes($studioList_row['sdoName']),$studioList_row['id']);
        $allStudioIDs[$studioList_row['id']] = stripslashes($studioList_row['sdoName']);
    }
    
    if (isset($_GET['siteid']))
    {
        $userSiteID = check_input($_GET['siteid']);
        $scheduleTable = '<thead>
                                <tr>
                                    <th scope="col">DAY</th>
                                    <th scope="col">TIME</th>
                                    <th scope="col">CLASS</th>
                                    <th scope="col">INSTRUCTOR</th>
                                    <th scope="col">HOSTING STUDIO</th>
                                    <th scope="col">MEETING ID</th>
                                    <th scope="col">NATIONAL SCHEDULE?</th>
                                </tr>
                            </thead>';
        $currentAction = 'Schedule for Honor Yoga '.$allStudios[$userSiteID][0].' ('.$userSiteID.')';
        
        $getStudioSchedule_query = "SELECT * FROM tblTemplateClasses WHERE 1 ORDER BY FIELD(tplWeekDay, 'SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'), tplClassTime ASC, tplClassName ASC";
        
        $getStudioSchedule_result = $conn->query($getStudioSchedule_query);
        
        while ($getStudioSchedule_row = $getStudioSchedule_result->fetch_assoc())
        {
            $checkForClassOnSchedule_query = sprintf("SELECT * FROM tblStudioSchedule WHERE stsStudioID = %s and stsClassID = %s",
                                             sanitize($allStudios[$userSiteID][1], 'int'),
                                             sanitize($getStudioSchedule_row['id'], 'int'));
            $checkForClassOnSchedule_result = $conn->query($checkForClassOnSchedule_query);
            
            $available = ($checkForClassOnSchedule_result->num_rows ==0) ? ' class="available"' : '';
            
            $scheduleTable .= '<tr'.$available.' id="row'.$getStudioSchedule_row['id'].'~'.$allStudios[$userSiteID][1].'"><td>'.$getStudioSchedule_row['tplWeekDay'].'</td><td>'.$getStudioSchedule_row['tplClassTime'].'</td><td>'.$getStudioSchedule_row['tplClassName'].'</td><td>'.$getStudioSchedule_row['tplTeacher'].'</td><td>'.$allStudioIDs[$getStudioSchedule_row['tplHostingStudioID']].'</td><td>'.$getStudioSchedule_row['tplMeetingID'].'</td><td>'.$getStudioSchedule_row['tplNationalYN'].'</td></tr>';
            
        } // wend while ($getStudioSchedule_row = $getStudioSchedule_result->fetch_assoc())
        
    } // end if (isset($_GET['siteid']))
?>