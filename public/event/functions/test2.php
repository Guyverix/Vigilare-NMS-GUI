<script src="/js/jquery/jquery-1.7.1.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        if("<?php echo $timezone; ?>".length==0){
            var visitortime = new Date();
            var visitortimezone = "GMT " + -visitortime.getTimezoneOffset()/60;
            $.ajax({
                type: "GET",
                url: "./timezone.php",
                data: 'time='+ visitortimezone,
                success: function(){
//                    location.reload();
                }
            });
        }
    });
</script>



<?php

echo "BEGIN <br><br>";
session_start();
$timezone = $_SESSION['time'];

echo "HERE ". $timezone . "\n";

?>
