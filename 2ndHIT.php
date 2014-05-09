<?php
    require_once(__DIR__.'/Turk50/Turk50.php');
    include(__DIR__.'/aws-credentials.php');
    require(__DIR__.'/vendor/autoload.php');
    
    use Aws\Sqs\SqsClient;
    
    $client = SqsClient::factory(array(
        "key" => $keys["AWSAccessKeyId"],
        "secret" => $keys["AWSSecretAccessKeyId"],
        "region" => "ap-northeast-1"
    ));

    $result = $client->receiveMessage(array(
        "QueueUrl" => $queueUrl
    ));

    foreach ($result->getPath('Messages/*/Body') as $messageBody) {
        // Find HITId in body and check all assignments complete
        $decodedMessageBody = json_decode($messageBody);
    };

    $HITId = $decodedMessageBody->Events[0]->HITId;

        $turk50 = new Turk50($keys["AWSAccessKeyIdMturk"], $keys["AWSSecretAccessKeyIdMturk"], array("trace" => TRUE));

        //prepare Request
        $Request = array(
         "HITId" => $HITId
        );

        // invoke CreateHIT
        $RegResponse = $turk50->GetAssignmentsForHIT($Request);
	echo "<br />";
	print_r($RegResponse);
	echo "<br />";

	$totalNumResults = $RegResponse->GetAssignmentsForHITResult->TotalNumResults;
	print($totalNumResults);
	$assignmentCount = 0;

	while ($assignmentCount < $totalNumResults) {
		print($RegResponse->GetAssignmentsForHITResult->Assignment[$assignmentCount]->Answer);
		echo "<br />";
		$assignmentCount++;
	}
	
?>
