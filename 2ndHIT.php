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
    //var_dump($result);

    foreach ($result->getPath('Messages/*/Body') as $messageBody) {
        // Find HITId in body and check all assignments complete
        $decodedMessageBody = json_decode($messageBody);
    };
    //	var_dump($decodedMessageBody);
	//echo "<br/>";

    $resultArray = $result->toArray();
    //print_r($resultArray);
    echo "<br/>";
$receiptHandle = $resultArray['Messages'][0]['ReceiptHandle'];
print_r($receiptHandle);

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
    $totalNumResults = $RegResponse->GetAssignmentsForHITResult->TotalNumResults;

    if ($totalNumResults > 0) {

        $splitComment = explode(" ", $RegResponse->GetAssignmentsForHITResult->Assignment[0]->Answer, 2);
        
        $questionText = 'These three choices are comments in response to the question written at this Traffic Planet forum page. Please choose which comment is the most RELEVANT to the topic in the forum and also which sounds like the most NORMAL English with good grammar:
                          ';
	    $answerText = '<SelectionAnswer>
                      <StyleSuggestion>radiobutton</StyleSuggestion>
                      <Selections>
                      ';
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
            $answerText .= '  <Selection>
                          <SelectionIdentifier>Comment';
	    $answerText .= $assignmentCount + 1;
            $answerText .= '</SelectionIdentifier>
                          <Text>Comment ';
	    $answerText .= $assignmentCount + 1;
            $answerText .= '</Text>
                        </Selection>
                        ';
		    $assignmentCount++;
	    }
    
        $answerText .= '</Selections>  
                    </SelectionAnswer>
                    ';
//print($answerText);
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
                    '.$answerText.'
                </AnswerSpecification>
              </Question>
              </QuestionForm>';
    //print $Question;
            //prepare Request
            $Request = array(
             "HITTypeId" => $HITTypeId2,
             "Question" => $Question,
             "MaxAssignments" => "1",
             "LifetimeInSeconds" => "7200",
             "RequesterAnnotation" => $_POST["forumURL"]
            );

            // invoke CreateHIT
            $CreateHITResponse = $turk50->CreateHIT($Request);
    //print_r($CreateHITResponse);
    };

$deleteMessage = $client->deleteMessage(array(
	"QueueUrl" => $queueUrl,
	"ReceiptHandle" => $receiptHandle
  ));
?>

