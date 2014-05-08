<?php
    require_once(__DIR__.'/Turk50/Turk50.php');
    include(__DIR__.'/aws-credentials.php');

// Creates a HitType and prints new ID

        $turk50 = new Turk50($keys["AWSAccessKeyIdMturk"], $keys["AWSSecretAccessKeyIdMturk"], array("trace" => TRUE));

        // require Worker_PercentAssignmentsApproved >= 90%
        $Worker_PercentAssignmentsApproved = array(
         "QualificationTypeId" => "000000000000000000L0",
         "Comparator" => "GreaterThanOrEqualTo",
         "IntegerValue" => "90"
        );
          
        //prepare Request
        $Request = array(
         "Title" => "Forum Comments",
         "Description" => "Draft a comment for a forum thread",
         "Reward" => array("Amount" => "1.00", "CurrencyCode" => "USD"),
         "AssignmentDurationInSeconds" => "60",
         "LifetimeInSeconds" => "172800",
         "AutoApprovalDelayInSeconds" => "43200",
         "QualificationRequirement" => $Worker_PercentAssignmentsApproved
        );

        // invoke CreateHIT
        $RegResponse = $turk50->RegisterHITType($Request);
        $LastResponse = $turk50->__getLastResponse();
        print($LastResponse["HITTypeId"]);
?>
