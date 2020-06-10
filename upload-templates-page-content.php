<div id="content">
	<div id="columnleftdouble">

		<form action="upload-templates.php" method="post" enctype="multipart/form-data" style="margin:0px; padding:0px " id="form">
					
	<?php if(!$erruserfile=='' )
			{
				if ($erruserfile=='Unrecognized File Format.')
				{
					echo('
				<table width="587" border="0" cellspacing="0" cellpadding="0">	
					<tr>
						<td>Expected Class Template CSV Input File in format<br>
						siteID,Day,Time,Class,Teacher,HomeStudio,ZoomLink,MeetingID,NationalYN<br><br>
						Received "'.$userfile.'" file in format<br>
						'.implode(",", $data).'
						</td>
					</tr>
				</table>');
				}
			} ?>
		<table width="587" border="0" cellspacing="0" cellpadding="0">	
		<?php 
		if(!$erruserfile=='' )
			{
				echo('
				<tr>
					<td width="57">&nbsp;</td>
					<td width="179">&nbsp;</td>
					<td width="351"><span class="redtxterror">'.$erruserfile.'</span></td>
				</tr>');
			}
			
		?>	
				<tr>
					<td width="57">&nbsp;</td>
					<td width="179">Upload Class Template CSV Input File: <span class="red_star">*</span> </td>
					<td width="351"><input type="hidden" name="MAX_FILE_SIZE" value="10000000">
							<input class="input3<?php if(!$erruserfile==''){echo('r');} ?>" name="userfile" type="file" id="userfile"> 
					</td>
				</tr>
		 
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td><div align="right" style="padding-right:25px">
						<input type="hidden" id="action" name="action" value="submitform" />
						<input type="submit" id="upload" name="upload" value="Upload" />
						<?php if(!isset($_POST['action'])) {?>
						<input type="reset" id="reset" name="reset" value="Reset" />
						<?php } ?>
					</div></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="main_d"><div align="right" style="padding-right:25px">Your IP Address is Logged as: <?php echo($ip); ?></div></td>
				</tr>
			</table>
		</form>
	</div>
			
		

</div>
