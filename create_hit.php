<?php
    require_once(__DIR__.'/Turk50/Turk50.php');
    include(__DIR__.'/aws-credentials.php');

    //Replaces the default keys with user inputted keys
    function updateKey ($keyName) {
	global $keys;
        if (!empty($_POST[$keyName])) {
            $keys[$keyName] = $_POST[$keyName];
        }
    }

   if (!empty($_POST)) {
        updateKey("AWSAccessKeyId");
        updateKey("AWSSecretAccessKeyId");
        updateKey("AWSAccessKeyIdMturk");
        updateKey("AWSSecretAccessKeyIdMturk");

        $turk50 = new Turk50($keys["AWSAccessKeyIdMturk"], $keys["AWSSecretAccessKeyIdMturk"]);

        //prepare Question
        $Question = '<QuestionForm xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2005-10-01/QuestionForm.xsd">
        <Question>
            <QuestionIdentifier>Comment for'.$_POST["forumURL"].'</QuestionIdentifier>
            <DisplayName>Comment Relating A Forum</DisplayName>
            <IsRequired>true</IsRequired>
            <QuestionContent>
              <Text>
                I need someone to post a RELEVANT comment on this forum thread:'.
                $_POST["forumURL"].'
                Do not post anything short like "Great post!" or "I agree!" - this needs to be relevant content that a person on the forum would actually post.
              </Text>
            </QuestionContent>
            <AnswerSpecification>
                <FreeTextAnswer>
                  <Constraints>
                    <Length minLength="10" maxLength="10"/>
                  </Constraints>
                </FreeTextAnswer>
            </AnswerSpecification>
          </Question>
          </QuestionForm>';
          
        //prepare Request
        $Request = array(
         "HITTypeId" => "3SJ5GB440G78X7LNWVFO7IHWBXP4Q1",
         "Question" => $Question,
         "MaxAssignments" => "2",
         "LifetimeInSeconds" => "172800",
         "RequesterAnnotation" => $_POST["forumURL"]
        );

        // invoke CreateHIT
        $CreateHITResponse = $turk50->CreateHIT($Request);
        
    }
  
?>
