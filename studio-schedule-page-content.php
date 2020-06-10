        <div class="container">
            <!-- row -->
            <div class="row tm-content-row">
                <div class="col-12 tm-block-col">
                    <div class="tm-bg-primary-dark tm-block tm-block-taller tm-block-scroll">
                        <h2 class="tm-block-title"><?php echo($currentAction);?></h2>
                        <form action="studio-schedule.php" method="post" enctype="multipart/form-data" style="margin:0px; padding:0px " id="form">
					        <table class="table" id="scheduleTable">
                                <tbody>
                                    <?php
                                    if(isset($userSiteID) && (!($userSiteID=='')))
                                    {
                                        echo($scheduleTable);
                                    ?>
                                    <?php
                                    } else {
                                    ?>
                                    <tr>
                                        <td>Please Select Your Studio to Display Your Schedule</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <input type="hidden" id="userSiteKey" name="userSiteKey" value="<?php echo($allStudios[$userSiteID][1]);?>" />
			            </form>
                    </div>
                </div>
            </div>
        </div>