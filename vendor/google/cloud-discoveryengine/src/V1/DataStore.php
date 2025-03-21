<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/discoveryengine/v1/data_store.proto

namespace Google\Cloud\DiscoveryEngine\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * DataStore captures global settings and configs at the DataStore level.
 *
 * Generated from protobuf message <code>google.cloud.discoveryengine.v1.DataStore</code>
 */
class DataStore extends \Google\Protobuf\Internal\Message
{
    /**
     * Immutable. The full resource name of the data store.
     * Format:
     * `projects/{project}/locations/{location}/collections/{collection_id}/dataStores/{data_store_id}`.
     * This field must be a UTF-8 encoded string with a length limit of 1024
     * characters.
     *
     * Generated from protobuf field <code>string name = 1 [(.google.api.field_behavior) = IMMUTABLE];</code>
     */
    protected $name = '';
    /**
     * Required. The data store display name.
     * This field must be a UTF-8 encoded string with a length limit of 128
     * characters. Otherwise, an INVALID_ARGUMENT error is returned.
     *
     * Generated from protobuf field <code>string display_name = 2 [(.google.api.field_behavior) = REQUIRED];</code>
     */
    protected $display_name = '';
    /**
     * Immutable. The industry vertical that the data store registers.
     *
     * Generated from protobuf field <code>.google.cloud.discoveryengine.v1.IndustryVertical industry_vertical = 3 [(.google.api.field_behavior) = IMMUTABLE];</code>
     */
    protected $industry_vertical = 0;
    /**
     * The solutions that the data store enrolls. Available solutions for each
     * [industry_vertical][google.cloud.discoveryengine.v1.DataStore.industry_vertical]:
     * * `MEDIA`: `SOLUTION_TYPE_RECOMMENDATION` and `SOLUTION_TYPE_SEARCH`.
     * * `SITE_SEARCH`: `SOLUTION_TYPE_SEARCH` is automatically enrolled. Other
     *   solutions cannot be enrolled.
     *
     * Generated from protobuf field <code>repeated .google.cloud.discoveryengine.v1.SolutionType solution_types = 5;</code>
     */
    private $solution_types;
    /**
     * Output only. The id of the default
     * [Schema][google.cloud.discoveryengine.v1.Schema] asscociated to this data
     * store.
     *
     * Generated from protobuf field <code>string default_schema_id = 7 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     */
    protected $default_schema_id = '';
    /**
     * Immutable. The content config of the data store. If this field is unset,
     * the server behavior defaults to
     * [ContentConfig.NO_CONTENT][google.cloud.discoveryengine.v1.DataStore.ContentConfig.NO_CONTENT].
     *
     * Generated from protobuf field <code>.google.cloud.discoveryengine.v1.DataStore.ContentConfig content_config = 6 [(.google.api.field_behavior) = IMMUTABLE];</code>
     */
    protected $content_config = 0;
    /**
     * Output only. Timestamp the
     * [DataStore][google.cloud.discoveryengine.v1.DataStore] was created at.
     *
     * Generated from protobuf field <code>.google.protobuf.Timestamp create_time = 4 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     */
    protected $create_time = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $name
     *           Immutable. The full resource name of the data store.
     *           Format:
     *           `projects/{project}/locations/{location}/collections/{collection_id}/dataStores/{data_store_id}`.
     *           This field must be a UTF-8 encoded string with a length limit of 1024
     *           characters.
     *     @type string $display_name
     *           Required. The data store display name.
     *           This field must be a UTF-8 encoded string with a length limit of 128
     *           characters. Otherwise, an INVALID_ARGUMENT error is returned.
     *     @type int $industry_vertical
     *           Immutable. The industry vertical that the data store registers.
     *     @type array<int>|\Google\Protobuf\Internal\RepeatedField $solution_types
     *           The solutions that the data store enrolls. Available solutions for each
     *           [industry_vertical][google.cloud.discoveryengine.v1.DataStore.industry_vertical]:
     *           * `MEDIA`: `SOLUTION_TYPE_RECOMMENDATION` and `SOLUTION_TYPE_SEARCH`.
     *           * `SITE_SEARCH`: `SOLUTION_TYPE_SEARCH` is automatically enrolled. Other
     *             solutions cannot be enrolled.
     *     @type string $default_schema_id
     *           Output only. The id of the default
     *           [Schema][google.cloud.discoveryengine.v1.Schema] asscociated to this data
     *           store.
     *     @type int $content_config
     *           Immutable. The content config of the data store. If this field is unset,
     *           the server behavior defaults to
     *           [ContentConfig.NO_CONTENT][google.cloud.discoveryengine.v1.DataStore.ContentConfig.NO_CONTENT].
     *     @type \Google\Protobuf\Timestamp $create_time
     *           Output only. Timestamp the
     *           [DataStore][google.cloud.discoveryengine.v1.DataStore] was created at.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Discoveryengine\V1\DataStore::initOnce();
        parent::__construct($data);
    }

    /**
     * Immutable. The full resource name of the data store.
     * Format:
     * `projects/{project}/locations/{location}/collections/{collection_id}/dataStores/{data_store_id}`.
     * This field must be a UTF-8 encoded string with a length limit of 1024
     * characters.
     *
     * Generated from protobuf field <code>string name = 1 [(.google.api.field_behavior) = IMMUTABLE];</code>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Immutable. The full resource name of the data store.
     * Format:
     * `projects/{project}/locations/{location}/collections/{collection_id}/dataStores/{data_store_id}`.
     * This field must be a UTF-8 encoded string with a length limit of 1024
     * characters.
     *
     * Generated from protobuf field <code>string name = 1 [(.google.api.field_behavior) = IMMUTABLE];</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;

        return $this;
    }

    /**
     * Required. The data store display name.
     * This field must be a UTF-8 encoded string with a length limit of 128
     * characters. Otherwise, an INVALID_ARGUMENT error is returned.
     *
     * Generated from protobuf field <code>string display_name = 2 [(.google.api.field_behavior) = REQUIRED];</code>
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * Required. The data store display name.
     * This field must be a UTF-8 encoded string with a length limit of 128
     * characters. Otherwise, an INVALID_ARGUMENT error is returned.
     *
     * Generated from protobuf field <code>string display_name = 2 [(.google.api.field_behavior) = REQUIRED];</code>
     * @param string $var
     * @return $this
     */
    public function setDisplayName($var)
    {
        GPBUtil::checkString($var, True);
        $this->display_name = $var;

        return $this;
    }

    /**
     * Immutable. The industry vertical that the data store registers.
     *
     * Generated from protobuf field <code>.google.cloud.discoveryengine.v1.IndustryVertical industry_vertical = 3 [(.google.api.field_behavior) = IMMUTABLE];</code>
     * @return int
     */
    public function getIndustryVertical()
    {
        return $this->industry_vertical;
    }

    /**
     * Immutable. The industry vertical that the data store registers.
     *
     * Generated from protobuf field <code>.google.cloud.discoveryengine.v1.IndustryVertical industry_vertical = 3 [(.google.api.field_behavior) = IMMUTABLE];</code>
     * @param int $var
     * @return $this
     */
    public function setIndustryVertical($var)
    {
        GPBUtil::checkEnum($var, \Google\Cloud\DiscoveryEngine\V1\IndustryVertical::class);
        $this->industry_vertical = $var;

        return $this;
    }

    /**
     * The solutions that the data store enrolls. Available solutions for each
     * [industry_vertical][google.cloud.discoveryengine.v1.DataStore.industry_vertical]:
     * * `MEDIA`: `SOLUTION_TYPE_RECOMMENDATION` and `SOLUTION_TYPE_SEARCH`.
     * * `SITE_SEARCH`: `SOLUTION_TYPE_SEARCH` is automatically enrolled. Other
     *   solutions cannot be enrolled.
     *
     * Generated from protobuf field <code>repeated .google.cloud.discoveryengine.v1.SolutionType solution_types = 5;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getSolutionTypes()
    {
        return $this->solution_types;
    }

    /**
     * The solutions that the data store enrolls. Available solutions for each
     * [industry_vertical][google.cloud.discoveryengine.v1.DataStore.industry_vertical]:
     * * `MEDIA`: `SOLUTION_TYPE_RECOMMENDATION` and `SOLUTION_TYPE_SEARCH`.
     * * `SITE_SEARCH`: `SOLUTION_TYPE_SEARCH` is automatically enrolled. Other
     *   solutions cannot be enrolled.
     *
     * Generated from protobuf field <code>repeated .google.cloud.discoveryengine.v1.SolutionType solution_types = 5;</code>
     * @param array<int>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setSolutionTypes($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::ENUM, \Google\Cloud\DiscoveryEngine\V1\SolutionType::class);
        $this->solution_types = $arr;

        return $this;
    }

    /**
     * Output only. The id of the default
     * [Schema][google.cloud.discoveryengine.v1.Schema] asscociated to this data
     * store.
     *
     * Generated from protobuf field <code>string default_schema_id = 7 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     * @return string
     */
    public function getDefaultSchemaId()
    {
        return $this->default_schema_id;
    }

    /**
     * Output only. The id of the default
     * [Schema][google.cloud.discoveryengine.v1.Schema] asscociated to this data
     * store.
     *
     * Generated from protobuf field <code>string default_schema_id = 7 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     * @param string $var
     * @return $this
     */
    public function setDefaultSchemaId($var)
    {
        GPBUtil::checkString($var, True);
        $this->default_schema_id = $var;

        return $this;
    }

    /**
     * Immutable. The content config of the data store. If this field is unset,
     * the server behavior defaults to
     * [ContentConfig.NO_CONTENT][google.cloud.discoveryengine.v1.DataStore.ContentConfig.NO_CONTENT].
     *
     * Generated from protobuf field <code>.google.cloud.discoveryengine.v1.DataStore.ContentConfig content_config = 6 [(.google.api.field_behavior) = IMMUTABLE];</code>
     * @return int
     */
    public function getContentConfig()
    {
        return $this->content_config;
    }

    /**
     * Immutable. The content config of the data store. If this field is unset,
     * the server behavior defaults to
     * [ContentConfig.NO_CONTENT][google.cloud.discoveryengine.v1.DataStore.ContentConfig.NO_CONTENT].
     *
     * Generated from protobuf field <code>.google.cloud.discoveryengine.v1.DataStore.ContentConfig content_config = 6 [(.google.api.field_behavior) = IMMUTABLE];</code>
     * @param int $var
     * @return $this
     */
    public function setContentConfig($var)
    {
        GPBUtil::checkEnum($var, \Google\Cloud\DiscoveryEngine\V1\DataStore\ContentConfig::class);
        $this->content_config = $var;

        return $this;
    }

    /**
     * Output only. Timestamp the
     * [DataStore][google.cloud.discoveryengine.v1.DataStore] was created at.
     *
     * Generated from protobuf field <code>.google.protobuf.Timestamp create_time = 4 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     * @return \Google\Protobuf\Timestamp|null
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    public function hasCreateTime()
    {
        return isset($this->create_time);
    }

    public function clearCreateTime()
    {
        unset($this->create_time);
    }

    /**
     * Output only. Timestamp the
     * [DataStore][google.cloud.discoveryengine.v1.DataStore] was created at.
     *
     * Generated from protobuf field <code>.google.protobuf.Timestamp create_time = 4 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     * @param \Google\Protobuf\Timestamp $var
     * @return $this
     */
    public function setCreateTime($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\Timestamp::class);
        $this->create_time = $var;

        return $this;
    }

}

