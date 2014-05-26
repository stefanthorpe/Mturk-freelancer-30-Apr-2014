+<?php
    require_once(__DIR__.'/Turk50/Turk50.php');
    include(__DIR__.'/aws-credentials.php');
    require(__DIR__.'/vendor/autoload.php');
    require(__DIR__.'/vendor/phpmailer/phpmailer/PHPMailerAutoload.php');
    
    use Aws\Sqs\SqsClient;
    
    $client = SqsClient::factory(array(
        "key" => $keys["AWSAccessKeyId"],
        "secret" => $keys["AWSSecretAccessKeyId"],
        "region" => "ap-northeast-1"
    ));

    $result = $client->receiveMessage(array(
        "QueueUrl" => $queueUrl
    ));
//  var_dump($result);

    foreach ($result->getPath('Messages/*/Body') as $messageBody) {
        // Find HITId in body and check all assignments complete
        $decodedMessageBody = json_decode($messageBody);
    };
//	var_dump($decodedMessageBody);
//  echo "<br/>";

    $resultArray = $result->toArray();
//  print_r($resultArray);
    echo "<br/>";
	$receiptHandle = $resultArray['Messages'][0]['ReceiptHandle'];
//  print_r($receiptHandle);

    $HITId = $decodedMessageBody->Events[0]->HITId;
    $MessageHITTypeId = $decodedMessageBody->Events[0]->HITTypeId;
	echo "<br />";
	print($MessageHITTypeId);
echo "<br />";
print($HITTypeId2);
    $turk50 = new Turk50($keys["AWSAccessKeyIdMturk"], $keys["AWSSecretAccessKeyIdMturk"], array("trace" => TRUE));

     //prepare Request
     $Request = array(
         "HITId" => $HITId
     );

     $RegResponse = $turk50->GetAssignmentsForHIT($Request);
//	echo "<br />";
//  print_r($RegResponse);
//  echo "<br />";

	if ($MessageHITTypeId == $HITTypeId1) {
	echo "hit one loop";
        // invoke CreateHIT

        $totalNumResults = $RegResponse->GetAssignmentsForHITResult->TotalNumResults;

        if ($totalNumResults > 0) {

            //$splitComment = explode(" ", $RegResponse->GetAssignmentsForHITResult->Assignment[0]->Answer, 2);
            
            $questionText = 'These three choices are comments in response to the question written at this Traffic Planet forum page. Please choose which comment is the most RELEVANT to the topic in the forum and also which sounds like the most NORMAL English with good grammar:
                              ';
	        $answerText = '<SelectionAnswer>
                          <StyleSuggestion>radiobutton</StyleSuggestion>
                          <Selections>
                          ';
	        $assignmentCount = 0;
    //      print_r($RegResponse);
	        while ($assignmentCount < $totalNumResults) {
	           if ($totalNumResults = 1){ 
			$xml =simplexml_load_string($RegResponse->GetAssignmentsForHITResult->Assignment->Answer);
		}else{
			 $xml =simplexml_load_string($RegResponse->GetAssignmentsForHITResult->Assignment[$assignmentCount]->Answer);
		}
    //          print_r($xml);
	            $answer = explode(">", $RegResponse->GetAssignmentsForHITResult->Assignment[$assignmentCount]->Answer, 2);
		        $questionText .= "Comment ";
		        $questionText .= $assignmentCount + 1;
		        $questionText .= ": " . $xml->Answer->FreeText;
		        $questionText .= '
                                     ';
                $answerText .= '  <Selection>
                              <SelectionIdentifier>Comment';
	            $answerText .= $assignmentCount + 1;
		$answerText .= ": " . $xml->Answer->FreeText;
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
    //      print($answerText);
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
    //      print $Question;

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
    //      print_r($CreateHITResponse);
        };
    } elseif($MessageHITTypeId == $HITTypeId2) {
         $xml =simplexml_load_string($RegResponse->GetAssignmentsForHITResult->Assignment->Answer);
//       print_r($xml);
	$mail = new PHPMailer;
// $mail->SMTPDebug = 2;
$mail->isSMTP();                                     
$mail->Host = 'smtp.gmail.com';  
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'stefan.thorpe@gmail.com';                 // SMTP username
$mail->Password = $emailPassword;                           // SMTP password
$mail->Port = 587;
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

$mail->From = 'stefan.thorpe@gmail.com';
$mail->FromName = 'Stefan Thorpe';
$mail->addAddress('stefan.thorpe@gmail.com');     // Add a recipient

$mail->Subject = 'Here is the subject';
$mail->Body    = 'The forum comment for ';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
	
    };


//  $deleteMessage = $client->deleteMessage(array(
//	"QueueUrl" => $queueUrl,
//	"ReceiptHandle" => $receiptHandle
//  ));
?>

