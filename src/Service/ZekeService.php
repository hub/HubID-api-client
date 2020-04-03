<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  09-03-2019
 */

namespace Hub\HubAPI\Service;

use InvalidArgumentException;

class ZekeService extends TokenRefreshingService
{
    /**
     * Use this to send VEN to any user.
     *
     * @param string $senderUserEmail    email address of the VEN sender registered within the Hub Culture platform.
     * @param string $recipientUserEmail email address of the VEN receiver registered within the Hub Culture platform.
     * @param string $amount             amount of VEN to be sent. The amount will be rounded to the nearest decimal.
     * @param string $message            [optional] message to the receiver.
     *
     * @return array
     */
    public function sendVen($senderUserEmail, $recipientUserEmail, $amount, $message = 'Some VEN via API')
    {
        $senderUserEmail = trim($senderUserEmail);
        if (empty($senderUserEmail)) {
            throw new InvalidArgumentException('Sender user email cannot be empty!');
        }
        $recipientUserEmail = trim($recipientUserEmail);
        if (empty($recipientUserEmail)) {
            throw new InvalidArgumentException('Sender user email cannot be empty!');
        }
        $amount = intval($amount);
        if ($amount <= 0) {
            throw new InvalidArgumentException('You must enter a valid VEN amount greater than zero(0).');
        }

        $data = array(
            'from' => $senderUserEmail,
            'to' => $recipientUserEmail,
            'amount' => $amount,
            'message' => trim($message),
        );

        return $this->createResponse(
            $this->postJson("/zeke/sendven", $data)
        );
    }
}
