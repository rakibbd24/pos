<?php
/**
 * WebhookIgnoreDeliveryReport
 *
 * PHP version 7.2
 *
 * @category Class
 * @package  Infobip
 * @author   Infobip Support
 * @link     https://www.infobip.com
 */

/**
 * Infobip Client API Libraries OpenAPI Specification
 *
 * OpenAPI specification containing public endpoints supported in client API libraries.
 *
 * Contact: support@infobip.com
 *
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * Do not edit the class manually.
 */

namespace Infobip\Model;

use ArrayAccess;
use Infobip\ObjectSerializer;

/**
 * WebhookIgnoreDeliveryReport Class Doc Comment
 *
 * @category Class
 * @package  Infobip
 * @author   Infobip Support
 * @link     https://www.infobip.com
 * @implements \ArrayAccess<TKey, TValue>
 * @template TKey int|null
 * @template TValue mixed|null
 */
class WebhookIgnoreDeliveryReport implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'WebhookIgnoreDeliveryReport';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'bulkId' => 'string',
        'messageId' => 'string',
        'to' => 'string',
        'sentAt' => '\DateTime',
        'doneAt' => '\DateTime',
        'smsCount' => 'int',
        'callbackData' => 'string',
        'price' => '\Infobip\Model\WebhookIgnorePrice',
        'status' => '\Infobip\Model\MessageStatus',
        'error' => '\Infobip\Model\MessageError',
        'browserLink' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'bulkId' => null,
        'messageId' => null,
        'to' => null,
        'sentAt' => 'date-time',
        'doneAt' => 'date-time',
        'smsCount' => 'int32',
        'callbackData' => null,
        'price' => null,
        'status' => null,
        'error' => null,
        'browserLink' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'bulkId' => 'bulkId',
        'messageId' => 'messageId',
        'to' => 'to',
        'sentAt' => 'sentAt',
        'doneAt' => 'doneAt',
        'smsCount' => 'smsCount',
        'callbackData' => 'callbackData',
        'price' => 'price',
        'status' => 'status',
        'error' => 'error',
        'browserLink' => 'browserLink'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'bulkId' => 'setBulkId',
        'messageId' => 'setMessageId',
        'to' => 'setTo',
        'sentAt' => 'setSentAt',
        'doneAt' => 'setDoneAt',
        'smsCount' => 'setSmsCount',
        'callbackData' => 'setCallbackData',
        'price' => 'setPrice',
        'status' => 'setStatus',
        'error' => 'setError',
        'browserLink' => 'setBrowserLink'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'bulkId' => 'getBulkId',
        'messageId' => 'getMessageId',
        'to' => 'getTo',
        'sentAt' => 'getSentAt',
        'doneAt' => 'getDoneAt',
        'smsCount' => 'getSmsCount',
        'callbackData' => 'getCallbackData',
        'price' => 'getPrice',
        'status' => 'getStatus',
        'error' => 'getError',
        'browserLink' => 'getBrowserLink'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }





    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['bulkId'] = $data['bulkId'] ?? null;
        $this->container['messageId'] = $data['messageId'] ?? null;
        $this->container['to'] = $data['to'] ?? null;
        $this->container['sentAt'] = $data['sentAt'] ?? null;
        $this->container['doneAt'] = $data['doneAt'] ?? null;
        $this->container['smsCount'] = $data['smsCount'] ?? null;
        $this->container['callbackData'] = $data['callbackData'] ?? null;
        $this->container['price'] = $data['price'] ?? null;
        $this->container['status'] = $data['status'] ?? null;
        $this->container['error'] = $data['error'] ?? null;
        $this->container['browserLink'] = $data['browserLink'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets bulkId
     *
     * @return string|null
     */
    public function getBulkId()
    {
        return $this->container['bulkId'];
    }

    /**
     * Sets bulkId
     *
     * @param string|null $bulkId The ID that uniquely identifies a list of email messages. This is either defined by user in the request or auto generated.
     *
     * @return self
     */
    public function setBulkId($bulkId)
    {
        $this->container['bulkId'] = $bulkId;

        return $this;
    }

    /**
     * Gets messageId
     *
     * @return string|null
     */
    public function getMessageId()
    {
        return $this->container['messageId'];
    }

    /**
     * Sets messageId
     *
     * @param string|null $messageId The ID that uniquely identifies the email sent to the recipient.
     *
     * @return self
     */
    public function setMessageId($messageId)
    {
        $this->container['messageId'] = $messageId;

        return $this;
    }

    /**
     * Gets to
     *
     * @return string|null
     */
    public function getTo()
    {
        return $this->container['to'];
    }

    /**
     * Sets to
     *
     * @param string|null $to Destination email address.
     *
     * @return self
     */
    public function setTo($to)
    {
        $this->container['to'] = $to;

        return $this;
    }

    /**
     * Gets sentAt
     *
     * @return \DateTime|null
     */
    public function getSentAt()
    {
        return $this->container['sentAt'];
    }

    /**
     * Sets sentAt
     *
     * @param \DateTime|null $sentAt Send date and time. Has the following format: `yyyy-MM-dd'T'HH:mm:ss.SSSZ`.
     *
     * @return self
     */
    public function setSentAt($sentAt)
    {
        $this->container['sentAt'] = $sentAt;

        return $this;
    }

    /**
     * Gets doneAt
     *
     * @return \DateTime|null
     */
    public function getDoneAt()
    {
        return $this->container['doneAt'];
    }

    /**
     * Sets doneAt
     *
     * @param \DateTime|null $doneAt Delivery date and time.
     *
     * @return self
     */
    public function setDoneAt($doneAt)
    {
        $this->container['doneAt'] = $doneAt;

        return $this;
    }

    /**
     * Gets smsCount
     *
     * @return int|null
     */
    public function getSmsCount()
    {
        return $this->container['smsCount'];
    }

    /**
     * Sets smsCount
     *
     * @param int|null $smsCount The number of emails sent.
     *
     * @return self
     */
    public function setSmsCount($smsCount)
    {
        $this->container['smsCount'] = $smsCount;

        return $this;
    }

    /**
     * Gets callbackData
     *
     * @return string|null
     */
    public function getCallbackData()
    {
        return $this->container['callbackData'];
    }

    /**
     * Sets callbackData
     *
     * @param string|null $callbackData Callback data sent through `callbackData` field in fully featured email.
     *
     * @return self
     */
    public function setCallbackData($callbackData)
    {
        $this->container['callbackData'] = $callbackData;

        return $this;
    }

    /**
     * Gets price
     *
     * @return \Infobip\Model\WebhookIgnorePrice|null
     */
    public function getPrice()
    {
        return $this->container['price'];
    }

    /**
     * Sets price
     *
     * @param \Infobip\Model\WebhookIgnorePrice|null $price Sent email price.
     *
     * @return self
     */
    public function setPrice($price)
    {
        $this->container['price'] = $price;

        return $this;
    }

    /**
     * Gets status
     *
     * @return \Infobip\Model\MessageStatus|null
     */
    public function getStatus()
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param \Infobip\Model\MessageStatus|null $status Indicates whether the email is successfully sent, not sent, delivered, not delivered, waiting for delivery or any other possible status.
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets error
     *
     * @return \Infobip\Model\MessageError|null
     */
    public function getError()
    {
        return $this->container['error'];
    }

    /**
     * Sets error
     *
     * @param \Infobip\Model\MessageError|null $error Indicates whether the error occurred during the query execution.
     *
     * @return self
     */
    public function setError($error)
    {
        $this->container['error'] = $error;

        return $this;
    }

    /**
     * Gets browserLink
     *
     * @return string|null
     */
    public function getBrowserLink()
    {
        return $this->container['browserLink'];
    }

    /**
     * Sets browserLink
     *
     * @param string|null $browserLink Contains the link to the HTML sent to recipient. This will be present only if the _view in browser_ feature is used in the email.
     *
     * @return self
     */
    public function setBrowserLink($browserLink)
    {
        $this->container['browserLink'] = $browserLink;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}
