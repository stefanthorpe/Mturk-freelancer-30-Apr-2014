<?php
    require_once(__DIR__.'/Turk50/Turk50.php');

    if (!empty($_POST)) {
        $AWSAccessKeyId = "AWSAccessKeyIdHere";
        $AWSSecretAccessKeyId = "AWSSecretAccessKeyIdHere";

        $turk50 = new Turk50($AWSAccessKeyId, $AWSSecretAccessKeyId);

        //prepare Question
        $Question = '<QuestionForm xmlns="http://mechanicalturk.amazonaws.com/AWSMechanicalTurkDataSchemas/2005-10-01/QuestionForm.xsd">
        <Question>
            <QuestionIdentifier>forumComment</QuestionIdentifier>
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
         "Title" => "Forum Comment",
         "Description" => "Draft a comment for a forum thread",
         "Question" => $Question,
         "Reward" => array("Amount" => "1.00", "CurrencyCode" => "USD"),
         "AssignmentDurationInSeconds" => "60",
         "LifetimeInSeconds" => "172,800",
         "QualificationRequirement" => $QualificationRequirement
        );

        // invoke CreateHIT
        $CreateHITResponse = $turk50->CreateHIT($Request);
    }
?>
