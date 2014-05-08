<?php
    require_once(__DIR__.'/Turk50/Turk50.php');
    include(__DIR__.'/aws-credentials.php');
    require(__DIR__.'vendor/autoload.php');
    
    use Aws\Sqs\SqsClient;
    
    $client = SqsClient::factory(array(
        "key" => $Keys["AWSAccessKeyId"],
        "secret" => $keys["AWSSecretAccessKeyId"],
        "region" => "us-west-1"
    ));
           
    $result = $client->receiveMessage(array(
        "QueueUrl" => $queueUrl
    ));

    foreach ($result->getPath('Messages/*/Body') as $messageBody) {
        // Find HITId in body and check all assignments complete
        echo $messageBody;
    }
        
    function checkHITCompleted ($HITId) {

        $turk50 = new Turk50($keys["AWSAccessKeyIdMturk"], $keys["AWSSecretAccessKeyIdMturk"]);

        //prepare Request
        $Request = array(
         "HITId" => $HITId,
         "AssignmentStatus" => "Approved"
        );

        // invoke CreateHIT
        $RegResponse = $turk50->GetAssignmentsForHIT($Request);
        echo ($RegResponse['HITTypeId']);
   }
?>
