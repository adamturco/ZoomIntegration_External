	<div>
		<footer class="tm-footer row tm-mt-small">
            <div class="col-12 font-weight-light">
                <p class="text-center text-white mb-0 px-4 small">
                    Copyright &copy; Honor Yoga <b><?php echo(date('Y')); ?></b> All rights reserved. 
                    
                </p>
            </div>
        </footer>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- https://jquery.com/download/ -->
    <script src="js/moment.min.js"></script>
    <!-- https://momentjs.com/ -->
    <script src="js/Chart.min.js"></script>
    <!-- http://www.chartjs.org/docs/latest/ -->
    <script src="js/bootstrap.min.js"></script>
    <!-- https://getbootstrap.com/ -->
    <script src="js/tooplate-scripts.js"></script>
    <script>       
        // DOM is ready
        $(function () {
            $(document).on("click", "#scheduleTable tbody tr", function(e) {
                var rowid = $(this).closest("tr").attr("id");
                $(this).toggleClass("available");
            $.ajax({
                url: "./log_toggleClassStatus.php",
                method: "POST",
                dataType: "json",
                data: { rowid:rowid },
                success: function(data) {
                  //alert(data.msg);
                }
              });
            });
           
        });
    </script>
</body>

</html>