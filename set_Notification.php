<?php
    require_once(__DIR__.'/Turk50/Turk50.php');
    include(__DIR__.'/aws-credentials.php');

// Creates a HitType and prints new ID

        
    $Notification = array(
        "Destination" => $queueUrl,
        "Transport" => "SQS",
        "Version" => "2006-05-05",
	"EventType" => array("HITExpired", "HITReviewable")
     );
        
    $request = array(
        "HITTypeId" => $HITTypeId1,
        "Notification" => $Notification,
        "Active" => TRUE
    );

    $SetNotificationresponse = $turk50->SetHITTypeNotification($request);
    $LastResponse = $turk50->__getLastResponse();
    echo "<br /> The Last Request was:<br />";
	print($turk50->__getLastRequest());
    echo "<br /> The Last Response was:<br />";
    print($LastResponse); 

?>
