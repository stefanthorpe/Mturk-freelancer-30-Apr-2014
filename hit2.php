<?php
    require_once(__DIR__.'/Turk50/Turk50.php');
    
    $result = json_decode($HTTP_RAW_POST_DATA, true);
    if (!empty($result)) {
    
        $AWSAccessKeyId = "   ";
        $AWSSecretAccessKeyId = "  ";

        $turk50 = new Turk50(AWSAccessKeyId, AWSSecretAccessKeyId);

        //prepare Question
        $Question = '<Question>
            <QuestionIdentifier>forumComment</QuestionIdentifier>
            <DisplayName>Comment Relating A Forum</DisplayName>
            <IsRequired>true</IsRequired>
            <QuestionContent>
              <Text>
                I need someone to post a RELEVANT comment on this forum thread:'.
                $result['Message'].'
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
          </Question>';
          
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
