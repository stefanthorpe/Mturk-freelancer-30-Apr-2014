<?php
    require_once(__DIR__.'/Turk50/Turk50.php');
    include(__DIR__.'/aws-credentials.php');

// Creates a HitType and prints new ID

    $turk50 = new Turk50($AWSAccessKeyId, $AWSSecretAccessKeyId, array("trace" => TRUE));
        
    $Notification = array(
        "Destination" => "Sqs End point",
        "Transport" => "SQS",
        "Version" => "2006-05-05",
        "EventType" => "HITReviewable",
        "EventType" => "HITExpired"
     );
        
    $request = array(
        "HITTypeId" => "3SJ5GB440G78X7LNWVFO7IHWBXP4Q1",
        "Notification" => $Notification,
        "Active" => TRUE
    );
    
    $SetNotificationresponse = $turk50->SetHITTypeNotification($request);
    $LastResponse = $turk50->__getLastResponse();
    print($LastResponse["HITTypeId"]); 

?>
