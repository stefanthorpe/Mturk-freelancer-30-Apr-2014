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

    $splitComment = explode(" ", $RegResponse->GetAssignmentsForHITResult->Assignment[0]->Answer, 2);
    
    $questionText = "These three choices are comments in response to the question written at this Traffic Planet forum page:/n"
    .$splitComment[0]."/n Please choose which comment is the most RELEVANT to the topic in the forum and also which sounds like the most NORMAL English with good grammar:/n";
    
	$totalNumResults = $RegResponse->GetAssignmentsForHITResult->TotalNumResults;
	print($totalNumResults);
	$assignmentCount = 0;

	while ($assignmentCount < $totalNumResults) {
	    $answer = explode(" ", $RegResponse->GetAssignmentsForHITResult->Assignment[$assignmentCount]->Answer, 2);
		$questionText .= "Comment ".$assignmentCount + 1;
		$questionText .= "/n>".$answer[1];
		$questionText .= "/n/n";
		$assignmentCount++;
	}
	
	$Question = '<QuestionForm xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2005-10-01/QuestionForm.xsd">
        <Question>
            <QuestionIdentifier>Review</QuestionIdentifier>
            <DisplayName>Review Forum Comment</DisplayName>
            <IsRequired>true</IsRequired>
            <QuestionContent>
              <Text>
               '.$questionText.'
              </Text>
            </QuestionContent>
            <AnswerSpecification>
                <FreeTextAnswer>
                  <Constraints>
                    <Length minLength="1" maxLength="1"/>
                  </Constraints>
                </FreeTextAnswer>
            </AnswerSpecification>
          </Question>
          </QuestionForm>';
          
        //prepare Request
        $Request = array(
         "HITTypeId" => "new type id",
         "Question" => $Question,
         "MaxAssignments" => "2",
         "LifetimeInSeconds" => "172800",
         "RequesterAnnotation" => $_POST["forumURL"]
        );

        // invoke CreateHIT
        $CreateHITResponse = $turk50->CreateHIT($Request);
?>
