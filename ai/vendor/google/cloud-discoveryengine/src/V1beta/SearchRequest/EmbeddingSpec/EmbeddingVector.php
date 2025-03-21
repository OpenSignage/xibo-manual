<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/discoveryengine/v1beta/search_service.proto

namespace Google\Cloud\DiscoveryEngine\V1beta\SearchRequest\EmbeddingSpec;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Embedding vector.
 *
 * Generated from protobuf message <code>google.cloud.discoveryengine.v1beta.SearchRequest.EmbeddingSpec.EmbeddingVector</code>
 */
class EmbeddingVector extends \Google\Protobuf\Internal\Message
{
    /**
     * Embedding field path in schema.
     *
     * Generated from protobuf field <code>string field_path = 1;</code>
     */
    protected $field_path = '';
    /**
     * Query embedding vector.
     *
     * Generated from protobuf field <code>repeated float vector = 2;</code>
     */
    private $vector;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $field_path
     *           Embedding field path in schema.
     *     @type array<float>|\Google\Protobuf\Internal\RepeatedField $vector
     *           Query embedding vector.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Discoveryengine\V1Beta\SearchService::initOnce();
        parent::__construct($data);
    }

    /**
     * Embedding field path in schema.
     *
     * Generated from protobuf field <code>string field_path = 1;</code>
     * @return string
     */
    public function getFieldPath()
    {
        return $this->field_path;
    }

    /**
     * Embedding field path in schema.
     *
     * Generated from protobuf field <code>string field_path = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setFieldPath($var)
    {
        GPBUtil::checkString($var, True);
        $this->field_path = $var;

        return $this;
    }

    /**
     * Query embedding vector.
     *
     * Generated from protobuf field <code>repeated float vector = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getVector()
    {
        return $this->vector;
    }

    /**
     * Query embedding vector.
     *
     * Generated from protobuf field <code>repeated float vector = 2;</code>
     * @param array<float>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setVector($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::FLOAT);
        $this->vector = $arr;

        return $this;
    }

}


