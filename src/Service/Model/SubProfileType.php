<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service\Model;

/**
 * This is a enum class which represents Hub Culture sub user profile types.
 *
 * @package Hub\HubAPI\Service\Model
 */
final class SubProfileType
{
    const PROFILE_TYPE_BOT = 'bot';
    const PROFILE_TYPE_ENTITY = 'entity';

    /**
     * @var string
     */
    private $type;

    /**
     * Private constructor to be invoked by public accessors of this enum class.
     *
     * @param string $type Type of the profile
     */
    private function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the bot type.
     *
     * @see SubProfileType::PROFILE_TYPE_BOT
     * @return SubProfileType
     */
    public static function bot()
    {
        return new self(self::PROFILE_TYPE_BOT);
    }

    /**
     * Returns the entity type.
     *
     * @see SubProfileType::PROFILE_TYPE_ENTITY
     * @return SubProfileType
     */
    public static function entity()
    {
        return new self(self::PROFILE_TYPE_ENTITY);
    }

    /**
     * Returns the string representation of the profile type enum value.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }
}
