<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/aiplatform/v1/feature_online_store_service.proto

namespace Google\Cloud\AIPlatform\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The request message for
 * [FeatureOnlineStoreService.SearchNearestEntities][google.cloud.aiplatform.v1.FeatureOnlineStoreService.SearchNearestEntities].
 *
 * Generated from protobuf message <code>google.cloud.aiplatform.v1.SearchNearestEntitiesRequest</code>
 */
class SearchNearestEntitiesRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Required. FeatureView resource format
     * `projects/{project}/locations/{location}/featureOnlineStores/{featureOnlineStore}/featureViews/{featureView}`
     *
     * Generated from protobuf field <code>string feature_view = 1 [(.google.api.field_behavior) = REQUIRED, (.google.api.resource_reference) = {</code>
     */
    private $feature_view = '';
    /**
     * Required. The query.
     *
     * Generated from protobuf field <code>.google.cloud.aiplatform.v1.NearestNeighborQuery query = 2 [(.google.api.field_behavior) = REQUIRED];</code>
     */
    private $query = null;
    /**
     * Optional. If set to true, the full entities (including all vector values
     * and metadata) of the nearest neighbors are returned; otherwise only entity
     * id of the nearest neighbors will be returned. Note that returning full
     * entities will significantly increase the latency and cost of the query.
     *
     * Generated from protobuf field <code>bool return_full_entity = 3 [(.google.api.field_behavior) = OPTIONAL];</code>
     */
    private $return_full_entity = false;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $feature_view
     *           Required. FeatureView resource format
     *           `projects/{project}/locations/{location}/featureOnlineStores/{featureOnlineStore}/featureViews/{featureView}`
     *     @type \Google\Cloud\AIPlatform\V1\NearestNeighborQuery $query
     *           Required. The query.
     *     @type bool $return_full_entity
     *           Optional. If set to true, the full entities (including all vector values
     *           and metadata) of the nearest neighbors are returned; otherwise only entity
     *           id of the nearest neighbors will be returned. Note that returning full
     *           entities will significantly increase the latency and cost of the query.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Aiplatform\V1\FeatureOnlineStoreService::initOnce();
        parent::__construct($data);
    }

    /**
     * Required. FeatureView resource format
     * `projects/{project}/locations/{location}/featureOnlineStores/{featureOnlineStore}/featureViews/{featureView}`
     *
     * Generated from protobuf field <code>string feature_view = 1 [(.google.api.field_behavior) = REQUIRED, (.google.api.resource_reference) = {</code>
     * @return string
     */
    public function getFeatureView()
    {
        return $this->feature_view;
    }

    /**
     * Required. FeatureView resource format
     * `projects/{project}/locations/{location}/featureOnlineStores/{featureOnlineStore}/featureViews/{featureView}`
     *
     * Generated from protobuf field <code>string feature_view = 1 [(.google.api.field_behavior) = REQUIRED, (.google.api.resource_reference) = {</code>
     * @param string $var
     * @return $this
     */
    public function setFeatureView($var)
    {
        GPBUtil::checkString($var, True);
        $this->feature_view = $var;

        return $this;
    }

    /**
     * Required. The query.
     *
     * Generated from protobuf field <code>.google.cloud.aiplatform.v1.NearestNeighborQuery query = 2 [(.google.api.field_behavior) = REQUIRED];</code>
     * @return \Google\Cloud\AIPlatform\V1\NearestNeighborQuery|null
     */
    public function getQuery()
    {
        return $this->query;
    }

    public function hasQuery()
    {
        return isset($this->query);
    }

    public function clearQuery()
    {
        unset($this->query);
    }

    /**
     * Required. The query.
     *
     * Generated from protobuf field <code>.google.cloud.aiplatform.v1.NearestNeighborQuery query = 2 [(.google.api.field_behavior) = REQUIRED];</code>
     * @param \Google\Cloud\AIPlatform\V1\NearestNeighborQuery $var
     * @return $this
     */
    public function setQuery($var)
    {
        GPBUtil::checkMessage($var, \Google\Cloud\AIPlatform\V1\NearestNeighborQuery::class);
        $this->query = $var;

        return $this;
    }

    /**
     * Optional. If set to true, the full entities (including all vector values
     * and metadata) of the nearest neighbors are returned; otherwise only entity
     * id of the nearest neighbors will be returned. Note that returning full
     * entities will significantly increase the latency and cost of the query.
     *
     * Generated from protobuf field <code>bool return_full_entity = 3 [(.google.api.field_behavior) = OPTIONAL];</code>
     * @return bool
     */
    public function getReturnFullEntity()
    {
        return $this->return_full_entity;
    }

    /**
     * Optional. If set to true, the full entities (including all vector values
     * and metadata) of the nearest neighbors are returned; otherwise only entity
     * id of the nearest neighbors will be returned. Note that returning full
     * entities will significantly increase the latency and cost of the query.
     *
     * Generated from protobuf field <code>bool return_full_entity = 3 [(.google.api.field_behavior) = OPTIONAL];</code>
     * @param bool $var
     * @return $this
     */
    public function setReturnFullEntity($var)
    {
        GPBUtil::checkBool($var);
        $this->return_full_entity = $var;

        return $this;
    }

}

