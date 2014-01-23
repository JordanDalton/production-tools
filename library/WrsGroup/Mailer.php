<?php
/**
 * A wrapper for Zend_Mail that facilitates sending an e-mail by using a domain
 * object for the e-mail
 *
 * @category WrsGroup
 * @author Eugene Morgan
 */
class WrsGroup_Mailer
{
    /**
     * Sends an e-mail using an e-mail domain object (not a Zend_Mail object)
     *
     * @param Model_Mail $mail A mail object
     */
    public function send($mail)
    {
        $mailService = new Zend_Mail();

        $recipientHolders = array(
            'to' => $mail->getToRecipients(),
            'cc' => $mail->getCcRecipients(),
            'bcc' => $mail->getBccRecipients(),
        );

        foreach ($recipientHolders as $type => $recordSet) {
            if (!$recordSet) {
                continue;
            }
            $method = 'add' . ucfirst($type);
            foreach ($recordSet as $emailAddress) {
                if ($emailAddress->name) {
                    $mailService->$method($emailAddress->address,
                                          $emailAddress->name);
                } else {
                    $mailService->$method($emailAddress->address);
                }
            }
        }
        if (!count($mailService->getRecipients())) {
            $msg = 'The e-mail entity did not include any recipients.';
            throw new InvalidArgumentException($msg);
        }

        $from = $mail->getFrom();
        $mailService->setFrom($from->address, $from->name);
        $mailService->setSubject($mail->subject);
        $mailService->setBodyHtml($mail->bodyHtml);
        $mailService->setBodyText($mail->bodyText);

        // Set reply to
        if ($mail->replyToAddress) {
            $mailService->addHeader('Reply-To', $mail->replyToAddress);
        }

        if (count($mail->getAttachments())) {
            foreach ($mail->getAttachments() as $attachment) {
                $mailService->addAttachment($attachment);
            }
        }
        $mailService->send();
    }
}