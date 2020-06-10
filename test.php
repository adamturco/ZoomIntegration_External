           
             <?php
include('con.php');
echo $sql = "SELECT * FROM classes WHERE date >='2020-04-26' AND date <= '2020-04-30' and site_id = '173983' ORDER BY start_date";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $i = 1;
    while($row = $result->fetch_assoc()) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
        
    }
} else {
    echo "0 results";
}
               ?>