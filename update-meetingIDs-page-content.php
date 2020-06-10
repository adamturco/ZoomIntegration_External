        <div class="container">
            <!-- row -->
            <div class="row tm-content-row">
                <div class="col-12 tm-block-col">
                    <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                        <h2 class="tm-block-title"><?php echo($currentAction);?></h2>
                        <form action="update-meetingIDs.php" method="post" enctype="multipart/form-data" style="margin:0px; padding:0px " id="form">
					        <table class="table">
                                <tbody>
                                    <?php
                                    if(isset($_POST['action']) && $_POST['action'] == 'submitform')
                                    {
                                        echo($resultsTable);
                                    ?>
                                    <?php
                                    } else {
                                    ?>
                                    <tr>
                                        <td><label for="fromDate">Update Classes From</label></td>
                                        <td colspan="2"><input name="fromDate" type="date" id="fromDate" size="10" value="<?php echo date('Y-m-d');?>" maxlength="10"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="reviseExistingIDsYN">Revise Existing Meeting IDs?</label></td>
                                        <td colspan="2"><input name="reviseExistingIDsYN" type="checkbox" id="reviseExistingIDsYN" value="Y" maxlength="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="forSpecificClass">Restrict to Specific Class</label></td>
                                        <td colspan="2">
                                            <select id="forSpecificClass" name="forSpecificClass" >
                                                <?php echo($forSpecificClassOptions);?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="forSpecificStudio">Restrict to Specific Studio</label></td>
                                        <td colspan="2">
                                            <select id="forSpecificStudio" name="forSpecificStudio" >
                                                <?php echo($forSpecificStudioOptions);?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td><div align="right" style="padding-right:25px">
                                            <input type="hidden" id="action" name="action" value="submitform" />
                                            <input type="submit" id="upload" name="upload" value="Populate Meeting ID's" />
                                            <?php if(!isset($_POST['action'])) {?>
                                            <input type="reset" id="reset" name="reset" value="Reset" />
                                            <?php } ?>
                                        </div></td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
			            </form>
                    </div>
                </div>
            </div>
        </div>