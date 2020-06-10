<?php 
/************************************************************************************
* Author:                                                                           *
* Date	: 5/2/2020                                                                  *
*************************************************************************************
* Populates tblTemplateClasses abd tblStudioSchedule based on information contained *
* in CSV input file in format:                                                      *
* siteID,Day,Time,Class,Teacher,HomeStudio,ZoomLink,MeetingID,NationalYN            *
*                                                                                   *
* This routine should only need to be executed one time.                            *
* Executing this routine on a populated database -WILL DESTROY- configurations set  *
* since initial load.                                                               *
*************************************************************************************/
	die('This is a DESTRUCTIVE PROCESS and has already been run.<br>You should not need to run this process again.');
	
	date_default_timezone_set('America/New_York'); 
	require_once('log_db-functions.php'); 
	require_once('log_validate-functions.php');
	require_once('conn.php');
		
	// Receive File and Process
	if(isset($_POST['action']) && $_POST['action'] == 'submitform')
	{
		//recieve the variables
		$errors = false;						

		if(isset($_POST['upload']) && $_FILES['userfile']['size'] > 0)
		{
			// Check the file and ensure it's correct
			$fileName = $_FILES['userfile']['name'];
			$userfile = $fileName;
			$tmpName  = $_FILES['userfile']['tmp_name'];
			$fileSize = $_FILES['userfile']['size'];
			$fileType = $_FILES['userfile']['type'];

			$fp      = fopen($tmpName, 'r');

			if(!get_magic_quotes_gpc())
			{
				$fileName = addslashes($fileName);
			}

			$fileName = str_replace(' ', '_', $fileName);

			$allowedExtensions = array("csv"); 
			foreach ($_FILES as $file) { 
				if ($file['tmp_name'] > '') { 
				  if (!in_array(end(explode(".", 
						strtolower($file['name']))), 
						$allowedExtensions)) { 
				   $erruserfile = $file['name'].' is an invalid file type.  Upload CSV\'s only.';
				   $errors=true;
				  } 
				} 
			} //end foreach
			
			// get handle to file and begin processing
			$data = fgetcsv($fp, 1000, ",");

			$num = count($data);
			
			// File should only have 9 columns
			if (!($num==9))
			{
				$erruserfile = 'Unrecognized File Format.';
				$errors = true;
			} else {

				//echo('Good File');
			}

		} else {
			$erruserfile = "No File Selected.";
			$errors=true;
		}
		
		// No errors, file looks good to process
		if (!$errors) 
		{	
			$lastClass = 'NEW_RUN';

			//echo('Starting Loop...<br>');
			
			$luCorporate_query = "SELECT id FROM tblStudios WHERE sdoName = 'Corporate'";

			$luCorporate_result = $conn->query($luCorporate_query) or die($conn->error.'<br>'.$luCorporate_query);
			
			if ($luCorporate_result->num_rows > 0)
			{
				// if we found the id for the Corporate Studio
				$row 	  	 = $luCorporate_result->fetch_assoc(); // only one record per studio
				$corporateID = $row['id'];
				
			} else {
				// if not we have larger issues
				die('Fatal Error: Could not find Corporate Site ID');
			}
			
			while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) 
			{	
				// skip header row
				if (!($data[0]=='siteID'))
				{
					$siteID 	= $data[0];
					$day 		= $data[1];
					$time 		= $data[2];
					$class	 	= $data[3];
					$teacher 	= $data[4];
					$homeStudio	= $data[5];
					$zoomLink 	= $data[6];
					$meetingID	= $data[7];
					$nationalYN	= $data[8];
					
					if ($nationalYN == 'Y')
					{
					// If the class is part of the National Schedule, we assign it to Corporate
					// and then share it out to every other studio on the Studio Schdeule
						
						// Add the new class to the Template Classes
						$template_query = sprintf("INSERT INTO tblTemplateClasses (tplSiteID, tplWeekDay, tplClassTime, tplClassName, tplTeacher, tplHostingStudioID, tplZoomLink, tplMeetingID, tplShareWith, tplNationalYN) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
										sanitize($siteID, 'text'),
										sanitize($day, 'text'),
										sanitize($time, 'time'),
										sanitize($class, 'text'),
										sanitize($teacher, 'text'),
										sanitize($corporateID, 'int'),
										sanitize($zoomLink, 'text'),
										sanitize($meetingID, 'text'),
										sanitize("ALL", 'text'),
										sanitize($nationalYN, 'text'));

						$template_result = $conn->query($template_query) or die($conn->error.'<br>'.$template_query);

						$classID = $conn->insert_id;

						//Now we need to share this class with all studios that are not Corporate
						$luStudio_query = "SELECT id FROM tblStudios WHERE sdoName <> 'Corporate'";
						
						$luStudio_result = $conn->query($luStudio_query) or die($conn->error.'<br>'.$luStudio_query);
						
						while ($studio_row = $luStudio_result->fetch_assoc())
						{
							// And add the National Schedule class to the Studio's Schedule
							$schedule_query = sprintf("INSERT INTO tblStudioSchedule (stsStudioID, stsClassID) VALUES (%s, %s)",
											sanitize($studio_row['id'], 'int'),
											sanitize($classID, 'int'));

							$schedule_result = $conn->query($schedule_query) or die($conn->error.'<br>'.$schedule_query);
						}						
						
					} else { // For classes that are not part of the National Schedule
					
						if (!($lastClass == $day.$time.$class.$teacher))
						{
						// If we have a new class
							if ($lastClass == 'NEW_RUN')
							{							
							// If this is our first time through, delete any old data
								$truncateTemplates_query = "TRUNCATE `tblTemplateClasses`;"; // Clear data from previous runs.

								$truncate_result = $conn->query($truncateTemplates_query) or die($conn->error);

								$truncateSchedule_query = "TRUNCATE `tblStudioSchedule`;"; // Clear data from previous runs.

								$truncate_result = $conn->query($truncateSchedule_query) or die($conn->error);
							}

							$lastClass = $day.$time.$class.$teacher;
							// set lastClass to current class so we don't do this again until we have an actual new class
							// then lookup the id of the Hosting Studio
							$lu_query = sprintf('SELECT id FROM tblStudios WHERE sdoName = %s',
									  sanitize($homeStudio, 'text'));

							$lu_result = $conn->query($lu_query) or die($conn->error.'<br>'.$lu_query);

							if ($lu_result->num_rows > 0)
							{
							// if we found the id for the Hosting Studio											
								$row 	  		 = $lu_result->fetch_assoc(); // only one record per studio
								$hostingStudioID = $row['id'];

							} else {
								// We somehow have a studio for which we did not account
								// This should not happen, but in case it does we're adding it to the list
								$newstudio_query = sprintf("INSERT INTO tblStudios (sdoSiteID, sdoName) VALUES (%s, %s)",
												 sanitize($siteID, 'text'),
												 sanitize($homeStudio, 'text'));
								die('ADD STUDIO FROM New Class:'.$newstudio_query.'<br>LU_Query:'.$lu_query.'<br>');

								/*$newstudio_result = $conn->query($newstudio_query) or die($conn->error);

								$hostingStudioID = $conn->insert_id;*/

							} //end if (mysqli_num_rows($lu_result) > 0)

							// Add the new class to the Template Classes
							$template_query = sprintf("INSERT INTO tblTemplateClasses (tplSiteID, tplWeekDay, tplClassTime, tplClassName, tplTeacher, tplHostingStudioID, tplZoomLink, tplMeetingID, tplShareWith, tplNationalYN) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
											sanitize($siteID, 'text'),
											sanitize($day, 'text'),
											sanitize($time, 'time'),
											sanitize($class, 'text'),
											sanitize($teacher, 'text'),
											sanitize($hostingStudioID, 'int'),
											sanitize($zoomLink, 'text'),
											sanitize($meetingID, 'text'),
											sanitize("ALL", 'text'),
											sanitize($nationalYN, 'text'));

							$template_result = $conn->query($template_query) or die($conn->error.'<br>'.$template_query);

							$classID = $conn->insert_id;
							
							// And add the new class to the Studio's Schedule
							$schedule_query = sprintf("INSERT INTO tblStudioSchedule (stsStudioID, stsClassID) VALUES (%s, %s)",
											sanitize($hostingStudioID, 'int'),
											sanitize($classID, 'int'));

							$schedule_result = $conn->query($schedule_query) or die($conn->error.'<br>'.$schedule_query);
							
						} else { // if (!($lastClass == $day.$time.$class.$teacher))
							// We're not at a new class, so additional entries are being shared
							// so we add them to the Studio's Schedule
							// but first we need the Studio ID from the siteID
							$luStudio_query = sprintf('SELECT id FROM tblStudios WHERE sdoSiteID = %s',
									  sanitize($siteID, 'text'));

							$luStudio_result = $conn->query($luStudio_query) or die($conn->error.'<br>'.$luStudio_query);

							if ($luStudio_result->num_rows > 0)
							{	
							// if we found the id for the Studio										
								$row 	  = $luStudio_result->fetch_assoc(); // only one record per studio
								$studioID = $row['id'];

								$schedule_query = sprintf("INSERT INTO tblStudioSchedule (stsStudioID, stsClassID) VALUES (%s, %s)",
												sanitize($studioID, 'int'),
												sanitize($classID, 'int'));

								$schedule_result = $conn->query($schedule_query) or die($conn->error.'<br>'.$schedule_query);

							} else {
								// Again, we should never hit this else, but if we do it's because the sharing studio was not found, 
								// so we need to add it first
								$newstudio_query = sprintf("INSERT INTO tblStudios (sdoSiteID, sdoName) VALUES (%s, %s)",
												 sanitize($siteID, 'text'),
												 sanitize('UNKNOWN STUDIO', 'text'));
								die('ADD STUDIO FROM Shared Class:'.$newstudio_query.'<br>LU_Query:'.$luStudio_query.'<br>');

								/*$newstudio_result = $conn->query($newstudio_query) or die($conn->error);

								$sharedStudioID = $conn->insert_id;

								$schedule_query = sprintf("INSERT INTO tblStudioSchedule (stsStudioID, stsClassID) VALUES (%s, %s)",
												sanitize($sharedStudioID, 'int'),
												sanitize($classID, 'int'));

								$schedule_result = $conn->query($schedule_query) or die($conn->error.'<br>'.$schedule_query);*/

							} // end if (mysqli_num_rows($luStudio_result) > 0)

						} // end if !($lastClass = $day.$time.$class.$teacher)
					
					} // end if ($nationalYN == 'Y')

				} //end if (!($data[0]='siteID'))

			} //wend (($data = fgetcsv($fp, 1000, ",")) !== FALSE) 

			fclose($fp);

			header("Location: upload-templates-completed.php"); 

		} // end if (!$errors)

	}//end if(isset($_POST['action']) && $_POST['action'] == 'submitform') 
?>