<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  16-09-2018
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Model\File;
use Hub\HubAPI\Service\Model\SubProfileType;

class UserService extends TokenRefreshingService
{
    const BASE = '/user';
    const DEFAULT_PAGINATION_LIMIT = 10;

    /**
     * Use this to provision a new user in the Hub Culture platform.
     *
     * @param string      $firstName   New user's first name
     * @param string      $lastName    New user's last name
     * @param string      $email       New user's email address. This will be the login username too.
     * @param string      $password    New user's new login password
     * @param string      $phoneNumber New user's phone number
     * @param string|null $countryCode [optional] New user's country code. This must be the ISO 3166 representation.
     *                                 Ex: GB
     *
     * @return array
     */
    public function registerNewUser($firstName, $lastName, $email, $password, $phoneNumber, $countryCode = null)
    {
        $payload = array(
            'first' => $firstName,
            'last' => $lastName,
            'email' => $email,
            'password' => $password,
            'mobile' => $phoneNumber,
            'country' => $countryCode,
        );
        if (!empty($countryCode) && strlen($countryCode) === 2) {
            $payload['country'] = $countryCode;
        }

        return $this->createResponse($this->postFormData(self::BASE, $payload));
    }

    /**
     * Use this to launch a new sub account under the authenticated user account.
     * Sub accounts are secondary identities for a given user and are transferable.
     *
     * For example: A property/pavilion can be owned by a business owned by the main account holder where the main
     * account is a personal account. As per this example, it may be convenient to launch a new sub 'entity' type
     * profile.
     *
     * @param string         $accountName    Name of the sub user account. Ex: "User B's boat by the sea"
     * @param SubProfileType $profileType    Type of the profile. Valid types are 'entity' & 'bot'.
     * @param string         $profileVariant Variant of the sub profile type. A variant describes more about a given sub
     *                                       profile type. Ex: "entity__nonprofit"
     *
     * @return array
     */
    public function registerNewSubUser($accountName, SubProfileType $profileType, $profileVariant)
    {
        $payload = array(
            'name' => $accountName,
            'profile_type' => (string)$profileType,
            'profile_variant' => $profileVariant,
        );

        return $this->createResponse($this->postFormData(self::BASE . '/sub-profiles', $payload));
    }

    /**
     * This sends an email with a link to reset a user's password. The email must belong to a valid Hub Culture account.
     * The email won't be sent if the account is still pending or deleted.
     *
     * @param string $email The email address to be used to match an account and to send the email.
     *
     * @return array
     */
    public function sendForgotPasswordEmail($email)
    {
        return $this->createResponse($this->postFormData("/auth/forgot?email={$email}"));
    }

    /**
     * Use this to retrieve a user by their id.
     *
     * @param int $id User identifier.
     *
     * @return array
     */
    public function getUserById($id)
    {
        if ($id === 'me') {
            return $this->getSelf();
        }

        return $this->createResponse($this->get(self::BASE . "/{$id}"));
    }

    /**
     * Use this to upload an image to the authenticated user.
     *
     * @param string $absoluteFilePath Absolute file path to an image file. ex: /tmp/test-image.jpg
     *
     * @return array
     */
    public function uploadLogo($absoluteFilePath)
    {
        return $this->createResponse(
            $this->uploadFile(self::BASE . "/uploadLogo", new File('logo', $absoluteFilePath))
        );
    }

    /**
     * Use this to upload a secondary image to the authenticated user.
     *
     * @param string $absoluteFilePath Absolute file path to an image file. ex: /tmp/test-image.jpg
     *
     * @return array
     */
    public function uploadSecondaryLogo($absoluteFilePath)
    {
        return $this->createResponse(
            $this->uploadFile(self::BASE . "/upload-secondary-logo", new File('logo', $absoluteFilePath))
        );
    }

    /**
     * Use this to get the current authenticated user.
     *
     * @return array
     */
    public function getSelf()
    {
        return $this->createResponse($this->get(self::BASE));
    }

    /**
     * Use this to update the profile status message.
     *
     * @param string $message
     *
     * @return array
     */
    public function updateStatusMessage($message)
    {
        return $this->createResponse($this->put(self::BASE . '/status-message', array('message' => $message)));
    }

    /**
     * This returns all the friends of the current authenticated user.
     *
     * @param int $offset [optional] offset for pagination
     * @param int $limit  [optional] limit for pagination
     *
     * @return array
     * @see FriendService::getFriends()
     */
    public function getFriends($offset = 0, $limit = self::DEFAULT_PAGINATION_LIMIT)
    {
        $offset = intval($offset) === 0 ? 0 : intval($offset);
        $limit = intval($limit) === 0 ? self::DEFAULT_PAGINATION_LIMIT : intval($limit);

        return $this->createResponse($this->get("/friends?offset={$offset}&limit={$limit}"));
    }

    /**
     * This returns all the available health conditions. In order to get the current user's data,
     * Please use the @see getHealthProfile
     *
     * @return array
     */
    public function getAvailableHealthConditions()
    {
        return $this->createResponse($this->get('/health/available-conditions'));
    }

    /**
     * This returns a list of approved health practices.
     *
     * @return array
     */
    public function getHealthPractitioners()
    {
        return $this->createResponse($this->get('/health/practitioners'));
    }

    /**
     * Use this to enrol at a health practice.
     *
     * @param int $practitionerId A valid health practitioner id
     *
     * @return array
     * @see getHealthPractitioners To select a health practitioner
     */
    public function enrolAtHealthPractitioner($practitionerId)
    {
        $practitionerId = intval($practitionerId);
        return $this->createResponse($this->postFormData("/health/practitioner/{$practitionerId}/enrol"));
    }

    /**
     * Use this to un-enrol from a health practice that you are currently enrolled at.
     *
     * @param int $practitionerId A valid health practitioner id
     *
     * @return array
     * @see getHealthPractitioners To select a health practitioner
     */
    public function unEnrolFromHealthPractitioner($practitionerId)
    {
        $practitionerId = intval($practitionerId);
        return $this->createResponse($this->postFormData("/health/practitioner/{$practitionerId}/unenrol"));
    }

    /**
     * Returns any approved health condition verifications given by a practitioner.
     *
     * @param bool $onlyApproved [optional] pass false to get all the verifications including unapproved ones
     *
     * @return array
     */
    public function getHealthConditionVerifications($onlyApproved = true)
    {
        $onlyApproved = ($onlyApproved) ? 1 : 0;
        return $this->createResponse(
            $this->get('/health/condition-verifications', array('is_approved' => $onlyApproved))
        );
    }

    /**
     * Use this to attach a verification document to an existing verification request.
     *
     * @param int    $verificationRequestId A valid verification request id
     * @param string $absoluteFilePath      Absolute file path to a file. ex: /tmp/verification.docx
     *
     * @return array
     * @see  getHealthConditionVerifications To get a verification id
     */
    public function attachVerificationDocument($verificationRequestId, $absoluteFilePath)
    {
        return $this->createResponse(
            $this->uploadFile(
                "/health/condition-verifications/{$verificationRequestId}/attachment",
                new File('attachment', $absoluteFilePath)
            )
        );
    }

    /**
     * This returns the user's health data. THis data is usually submitted via the HubID health page in the site.
     *
     * @return array
     * @see https://hubculture.com/account/health Link to the HubID health page
     */
    public function getHealthProfile()
    {
        return $this->createResponse($this->get('/health/profile'));
    }

    /**
     * Use this to invite a new practitioner into the platform by their email address.
     *
     * @param string $emailAddress The emil address of the person that you are inviting
     *
     * @return array
     */
    public function inviteHealthPractitioner($emailAddress)
    {
        return $this->createResponse(
            $this->postFormData('/health/invite-practitioner', array('email' => $emailAddress))
        );
    }

    /**
     * Use this to update the health profile of the current authenticated user.
     *
     * @param string      $bloodType                  Blood type. Ex: A+
     * @param string|null $medications                [optional] Any medication that the user is taking.
     * @param array       $existingHealthConditionIds [optional] Valid health condition ids.
     * @param int|null    $primaryPractitionerId      [optional] Passing a valid practitioner id will enrol you in
     *                                                their practice and you will be able to communicate with the
     *                                                practitioner. You may also use the enrol method later.
     *
     * @return array
     * @see getHealthPractitioners To select a health practitioner
     * @see getAvailableHealthConditions to get the condition ids
     * @see enrolAtPractitioner to enrol at a health practice
     */
    public function setHealthProfile(
        $bloodType,
        $medications = null,
        array $existingHealthConditionIds = array(),
        $primaryPractitionerId = null
    ) {
        $payload = array();
        $validBloodType = array('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-');
        if (in_array($bloodType, $validBloodType)) {
            $payload['health_blood_type'] = $bloodType;
        }
        if (!empty($medications)) {
            $payload['health_medications'] = $medications;
        }
        if (!empty($existingHealthConditionIds)) {
            $payload['health_conditions'] = implode(',', $existingHealthConditionIds);
        }
        if (!is_null($primaryPractitionerId) && intval($primaryPractitionerId) > 0) {
            $payload['primary_health_practitioner'] = $primaryPractitionerId;
        }

        return $this->createResponse($this->put('/health/profile', $payload));
    }
}
