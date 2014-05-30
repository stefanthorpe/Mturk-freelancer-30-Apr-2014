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

	 //prepare Question
        $Question = '<QuestionForm xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2005-10-01/QuestionForm.xsd">
        <Question>
            <QuestionIdentifier>Comment for'.$_POST["forumURL"].'</QuestionIdentifier>
            <DisplayName>Draft a comment for a forum/blog post</DisplayName>
            <IsRequired>true</IsRequired>
            <QuestionContent>
              <FormattedContent><![CDATA[
                <p>I need someone to write a RELEVANT comment on this post: <a href="'.$_POST["forumURL"].'">'.$_POST["forumURL"].'</a><br/>
                Do not write anything short like "Great post!" or "I agree!" - this needs to be relevant content that a reader would actually post.</p>
              ]]></FormattedContent>
            </QuestionContent>
            <AnswerSpecification>
                <FreeTextAnswer>
                  <Constraints>
                    <Length minLength="10"/>
                  </Constraints>
                </FreeTextAnswer>
            </AnswerSpecification>
          </Question>
          </QuestionForm>';
          
        //prepare Request
        $Request = array(
         "HITTypeId" => $HITTypeId1,
         "Question" => $Question,
         "MaxAssignments" => "3",
         "LifetimeInSeconds" => "7200",
         "RequesterAnnotation" => $_POST["forumURL"]
        );

        // invoke CreateHIT
        $CreateHITResponse = $turk50->CreateHIT($Request);
        print_r($CreateHITResponse);

    }
  
?>
