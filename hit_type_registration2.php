<?php
    require_once(__DIR__.'/Turk50/Turk50.php');
    include(__DIR__.'/aws-credentials.php');

// Creates a HitType and prints new ID

        // require Worker_PercentAssignmentsApproved >= 90%
        $Worker_PercentAssignmentsApproved = array(
         "QualificationTypeId" => "000000000000000000L0",
         "Comparator" => "GreaterThanOrEqualTo",
         "IntegerValue" => "90"
        );
          
        //prepare Request
        $Request = array(
         "Title" => "Review Forum Comments",
         "Description" => "Review a comment for a forum thread",
         "Reward" => array("Amount" => "0.25", "CurrencyCode" => "USD"),
         "AssignmentDurationInSeconds" => "30",
         "LifetimeInSeconds" => "7200",
         "AutoApprovalDelayInSeconds" => "1200",
         "QualificationRequirement" => $Worker_PercentAssignmentsApproved
        );

        // invoke CreateHIT
        $RegResponse = $turk50->RegisterHITType($Request);
        if ($RegResponse->RegisterHITTypeResult){
                echo "<h1>This is your new HIT Type Id:</h1>";
                print($RegResponse->RegisterHITTypeResult->HITTypeId);
        }
        else{
                echo "You requested failed please see detailed response below<br />";
                print_r($RegResponse);
        }

?>
