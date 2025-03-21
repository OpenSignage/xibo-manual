<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/discoveryengine/v1beta/site_search_engine_service.proto

namespace Google\Cloud\DiscoveryEngine\V1beta;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Request message for
 * [SiteSearchEngineService.BatchVerifyTargetSites][google.cloud.discoveryengine.v1beta.SiteSearchEngineService.BatchVerifyTargetSites]
 * method.
 *
 * Generated from protobuf message <code>google.cloud.discoveryengine.v1beta.BatchVerifyTargetSitesRequest</code>
 */
class BatchVerifyTargetSitesRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Required. The parent resource shared by all TargetSites being verified.
     * `projects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/siteSearchEngine`.
     *
     * Generated from protobuf field <code>string parent = 1 [(.google.api.field_behavior) = REQUIRED, (.google.api.resource_reference) = {</code>
     */
    protected $parent = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $parent
     *           Required. The parent resource shared by all TargetSites being verified.
     *           `projects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/siteSearchEngine`.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Discoveryengine\V1Beta\SiteSearchEngineService::initOnce();
        parent::__construct($data);
    }

    /**
     * Required. The parent resource shared by all TargetSites being verified.
     * `projects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/siteSearchEngine`.
     *
     * Generated from protobuf field <code>string parent = 1 [(.google.api.field_behavior) = REQUIRED, (.google.api.resource_reference) = {</code>
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Required. The parent resource shared by all TargetSites being verified.
     * `projects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/siteSearchEngine`.
     *
     * Generated from protobuf field <code>string parent = 1 [(.google.api.field_behavior) = REQUIRED, (.google.api.resource_reference) = {</code>
     * @param string $var
     * @return $this
     */
    public function setParent($var)
    {
        GPBUtil::checkString($var, True);
        $this->parent = $var;

        return $this;
    }

}

