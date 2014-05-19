+<?php
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
//	echo "<br />";
//	print_r($RegResponse);
//	echo "<br />";

    $splitComment = explode(" ", $RegResponse->GetAssignmentsForHITResult->Assignment[0]->Answer, 2);
    
    $questionText = 'These three choices are comments in response to the question written at this Traffic Planet forum page. Please choose which comment is the most RELEVANT to the topic in the forum and also which sounds like the most NORMAL English with good grammar:
';
    
	$totalNumResults = $RegResponse->GetAssignmentsForHITResult->TotalNumResults;
	print($totalNumResults);
	$assignmentCount = 0;
//print_r($RegResponse);
	while ($assignmentCount < $totalNumResults) {
	$xml =simplexml_load_string($RegResponse->GetAssignmentsForHITResult->Assignment[$assignmentCount]->Answer);
	print_r($xml);
	    $answer = explode(">", $RegResponse->GetAssignmentsForHITResult->Assignment[$assignmentCount]->Answer, 2);
		$questionText .= "Comment ";
		$questionText .= $assignmentCount + 1;
		$questionText .= ":" . $xml->Answer->FreeText;
		$questionText .= '
';
		$assignmentCount++;
	}
$questionText;	
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
                <SelectionAnswer>
                  <StyleSuggestion>radiobutton</StyleSuggestion>
                  <Selections>
                    <Selection>
                      <SelectionIdentifier>Comment1</SelectionIdentifier>
                      <Text>Comment 1</Text>
                    </Selection>
                    <Selection>
                      <SelectionIdentifier>Comment2</SelectionIdentifier>
                      <Text>Comment 2</Text>
                    </Selection>
                    <Selection>
                      <SelectionIdentifier>Comment3</SelectionIdentifier>
                      <Text>Comment 3</Text>
                    </Selection>
                  </Selections>  
                </SelectionAnswer>
            </AnswerSpecification>
          </Question>
          </QuestionForm>';
//print $Question;
        //prepare Request
        $Request = array(
         "HITTypeId" => "3H63IRCKX3GKJ5IKZ7A1UD8NUG18E4",
         "Question" => $Question,
         "MaxAssignments" => "2",
         "LifetimeInSeconds" => "172800",
         "RequesterAnnotation" => $_POST["forumURL"]
        );

        // invoke CreateHIT
        $CreateHITResponse = $turk50->CreateHIT($Request);
print_r($CreateHITResponse);
?>
