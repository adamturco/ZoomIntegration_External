<?php
/************************************************************************************
* Author:                                                                           *
* Date	: 5/4/2020                                                                  *
*************************************************************************************
* Compares classes in the 'classes' table against the template classes and attempts *
* to match based on studio (site_id = tplSiteID), day of week (WEEKDAY(start_date)  *
* = tplWeekDay), and class start time (TIME(start_date) = tplClassTime). If a match *
* is found, the tplMeetingID is assigned to meeting_id.                             *
*                                                                                   *
* Options:                                                                          *
*                                                                                   *
*	Update Classes From User-input Date                                             *
* 		Process records only on or after provided date, do not update historic.     *
*                                                                                   *
*  	Revise Existing ID's YN                                                         *
*		Updates existing Meeting ID's in the classes table. Useful when Zoom        *
*		links change and are no longer valid. May be modified to include the        *
*		options below.                                                              *
*                                                                                   *
* 		For a Specific Class														*
*			Update all existing ID's for a selected class.							*
*                                                                                   *
*		For a Specific Studio														*
*			Updates all existing ID's for s specific studio.						*
*                                                                                   *
* This procedure will need to run whenever new classes are added to the system, or  *
* meeting ID's change for existing classes.                                         *
*************************************************************************************/

	function dayNumber($weekday)
	{
		switch($weekday)
		{
			case "Monday":
				$value = 0;
				break;
			case "Tuesday":
				$value = 1;
				break;
			case "Monday":
				$value = 2;
				break;
			case "Monday":
				$value = 3;
				break;
			case "Monday":
				$value = 4;
				break;
			case "Monday":
				$value = 5;
				break;
			case "Monday":
				$value = 6;
		}
        return $value;
	}
    
    function dayName($daynumber)
    {
        switch($daynumber)
        {
            case 0:
                $value = "Monday";
                break;
            case 1:
                $value = "Tuesday";
                break;
            case 2:
                $value = "Wednesday";
                break;
            case 3:
                $value = "Thursday";
                break;
            case 4:
                $value = "Friday";
                break;
            case 5:
                $value = "Saturday";
                break;
            case 6:
                $value = "Sunday";
                break;
        }
        return $value;
    }

    function trimClassName($classToTrim)
    {
        if (strpos($classToTrim, ' - ') === false)
        {
            $value = $classToTrim;
        } else {
            $explodeValue = explode(' - ', $classToTrim);
            $value        = trim($explodeValue[0]);
        }
        return $value;
    }
	
	date_default_timezone_set('America/New_York'); 
	require_once('log_db-functions.php'); 
	require_once('log_validate-functions.php');
	require_once('conn.php');

    $currentAction = 'Update Meeting Links';

    // Build option list for Specific Class box
    $specificClass_query = "SELECT DISTINCT * FROM tblTemplateClasses ORDER BY FIELD(tplWeekDay, 'SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'), tplClassTime ASC";
    
    $specificClass_result = $conn->query($specificClass_query);
    
    $forSpecificClassOptions = '<option value="">-Select if Desired-</option>';

    while ($specificClass_row = $specificClass_result->fetch_assoc())
    {
        $forSpecificClassOptions .= '<option value="'.$specificClass_row['tplClassName'].'~'.$specificClass_row['tplWeekDay'].'~'.$specificClass_row['tplClassTime'].'">'.$specificClass_row['tplWeekDay'].' '.$specificClass_row['tplClassName'].' at '.$specificClass_row['tplClassTime'].'</option>';
    }

    // Build option list for Specific Studio box
    $specificStudio_query = "SELECT * FROM tblStudios ORDER BY sdoName ASC";
    
    $specificStudio_result = $conn->query($specificStudio_query);
    
    $forSpecificStudioOptions = '<option value="">-Select if Desired-</option>';

    while ($specificStudio_row = $specificStudio_result->fetch_assoc())
    {
        $forSpecificStudioOptions .= '<option value="'.$specificStudio_row['sdoSiteID'].'">'.stripslashes($specificStudio_row['sdoName']).'</option>';
    }

	// Receive Input and Process
	if(isset($_POST['action']) && $_POST['action'] == 'submitform')
	{
		//recieve the variables
		$errors = false;
        $currentAction = 'Results';
        $resultsTable = '<thead><tr><th>Processing Started '.date("l, F jS, Y, H:i:s").'</th></tr></thead>';
		
		$fromDate 			 = check_input($_POST['fromDate']);
		$reviseExistingIDsYN = check_input($_POST['reviseExistingIDsYN']);
		$forSpecificClass 	 = check_input($_POST['forSpecificClass']);
		$forSpecificStudio 	 = check_input($_POST['forSpecificStudio']);
        
        if ($reviseExistingIDsYN == ''){$reviseExistingIDsYN='N';}
		
		// No errors, looks good to process
		if (!$errors) 
		{
			// Build the query based on the options selected
			$where_clause = '';
			
			if (!($fromDate==''))
			{
            // Only look at classes past x date
				$where_clause .= sprintf(" WHERE date > %s",
								sanitize($fromDate, 'date'));
			}
			if ($reviseExistingIDsYN == 'N')
			{
            // look at ALL classes? Or just the ones with blank Meeting ID's?
				$where_clause .= " AND ISNULL(meeting_id)";
			}
			if (!($forSpecificClass==''))
			{
            // look for ALL classes to match, or just a particular class?
				$specifics = explode('~',$forSpecificClass);
				
				$forSpecificName = $specifics[0].'%';
				$forSpecificDay	 = dayNumber($specifics[1]);
				$forSpecificTime = $specifics[2];
				
				$where_clause .= sprintf(" AND class_name LIKE %s AND WEEKDAY(start_date) = %s AND TIME(start_date) = %s",
								sanitize($forSpecificName, 'text'),
								sanitize($forSpecificDay, 'text'),
								sanitize($forSpecificTime, 'time'));
			}
			if (!($forSpecificStudio==''))
			{
            // look at ALL studios, or just a single studio?
				$where_clause .= sprintf(" AND site_id = %s",
								sanitize($forSpecificStudio, 'text'));
			}
			
			$classes_query = "SELECT * FROM classes".$where_clause;
            
            $classes_result = $conn->query($classes_query) or die($conn->error.'<br>'.$classes_query);
            
            $resultsTable .= '<tr><td>Options Selected: '.$where_clause.'</td></tr>';
            
            $classCtr=0;
            $matchCtr=0;
            while($classes_row = $classes_result->fetch_assoc())
            {
            // Loop through the classes table and try to find classes that match
                $classRowID = $classes_row['id'];
                $dayOfWeek  = dayName(date('N', strtotime($classes_row['start_date']))-1);
                $siteID     = $classes_row['site_id'];
                $className  = trimClassName($classes_row['class_name']).'%';
                $classTime  = date('H:i:s', strtotime($classes_row['start_date']));
                                
                $luStudio_query = sprintf("SELECT * FROM tblStudios WHERE sdoSiteID = %s",
                                  sanitize($siteID, 'text'));
                
                $luStudio_result = $conn->query($luStudio_query) or die($conn->error.'<br>'.$luStudio_query);
                
                $luStudio_row    = $luStudio_result->fetch_assoc();
                
                ++$classCtr;
                                
                if ($luStudio_result->num_rows > 0)
                {
                    $studioKey = $luStudio_row['id'];
                } else {
                    // if we can't find the studio we can't continue
                    die('Fatal Error: Studio ID not found: '.$luStudio_query);
                }
                // Attempt to find the class on the schedule
                $luSchedule_query = sprintf("SELECT * FROM tblTemplateClasses, tblStudioSchedule WHERE stsStudioID = %s AND stsClassID = tblTemplateClasses.id AND tplSiteID = %s AND tplWeekDay = %s AND tplClassTime = %s AND tplClassName LIKE %s ORDER BY tplClassTime ASC",
                                    sanitize($studioKey, 'int'),
                                    sanitize($siteID, 'text'),
                                    sanitize($dayOfWeek, 'text'),
                                    sanitize($classTime, 'time'),
                                    sanitize($className, 'text'));
                
                $luSchedule_result = $conn->query($luSchedule_query) or die($conn->error.'<br>'.$luSchedule_query);
                                
                if ($luSchedule_result->num_rows > 0)
                {
                // We found a match! Now add the Meeting_ID and update the classes table    
                    $luSchedule_row = $luSchedule_result->fetch_assoc(); // should only be one result. If not, we take the first one only.
                    
                    $meetingID = $luSchedule_row['tplMeetingID'];
                    
                    $updateClasses_query = sprintf("UPDATE classes SET meeting_id = %s WHERE id = %s LIMIT 1",
                                           sanitize($meetingID, 'text'),
                                           sanitize($classRowID, 'int'));
                    
                    $updateClasses_result = $conn->query($updateClasses_query) or die($conn->error.'<br>'.$updateClasses_query);
                    
                    $resultsTable .= '<tr><td>Updated: '.$dayOfWeek.' '.$siteID.' '.$className.' '.$classTime.' '.$meetingID.'</td></tr>';
                    
                    ++$matchCtr;
                } else {
                    
                    //$resultsTable .= '<tr><td>No Match: '.$classes_row['site_id'].' '.$classes_row['class_name'].' '.$classes_row['start_date'].' '.$classes_row['meeting_id'].' '.'</td></tr>';
                }
                
            } // wend while($classes_row = $classes_result->fetch_assoc()) 
            
            $resultsTable .= '<tr><td>Checked '.$classCtr.' Classes. Inserted '.$matchCtr.' Meeting ID\'s</td></tr>';
            $resultsTable .= '<thead><tr><th>Process Completed.</th></tr></thead>';
			
		} // end if (!$errors)
	
	} // end if(isset($_POST['action']) && $_POST['action'] == 'submitform')
    
?>