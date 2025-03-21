<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/discoveryengine/v1/site_search_engine_service.proto

namespace Google\Cloud\DiscoveryEngine\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Request message for
 * [SiteSearchEngineService.GetTargetSite][google.cloud.discoveryengine.v1.SiteSearchEngineService.GetTargetSite]
 * method.
 *
 * Generated from protobuf message <code>google.cloud.discoveryengine.v1.GetTargetSiteRequest</code>
 */
class GetTargetSiteRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Required. Full resource name of
     * [TargetSite][google.cloud.discoveryengine.v1.TargetSite], such as
     * `projects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/siteSearchEngine/targetSites/{target_site}`.
     * If the caller does not have permission to access the
     * [TargetSite][google.cloud.discoveryengine.v1.TargetSite], regardless of
     * whether or not it exists, a PERMISSION_DENIED error is returned.
     * If the requested [TargetSite][google.cloud.discoveryengine.v1.TargetSite]
     * does not exist, a NOT_FOUND error is returned.
     *
     * Generated from protobuf field <code>string name = 1 [(.google.api.field_behavior) = REQUIRED, (.google.api.resource_reference) = {</code>
     */
    protected $name = '';

    /**
     * @param string $name Required. Full resource name of
     *                     [TargetSite][google.cloud.discoveryengine.v1.TargetSite], such as
     *                     `projects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/siteSearchEngine/targetSites/{target_site}`.
     *
     *                     If the caller does not have permission to access the
     *                     [TargetSite][google.cloud.discoveryengine.v1.TargetSite], regardless of
     *                     whether or not it exists, a PERMISSION_DENIED error is returned.
     *
     *                     If the requested [TargetSite][google.cloud.discoveryengine.v1.TargetSite]
     *                     does not exist, a NOT_FOUND error is returned. Please see
     *                     {@see SiteSearchEngineServiceClient::targetSiteName()} for help formatting this field.
     *
     * @return \Google\Cloud\DiscoveryEngine\V1\GetTargetSiteRequest
     *
     * @experimental
     */
    public static function build(string $name): self
    {
        return (new self())
            ->setName($name);
    }

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $name
     *           Required. Full resource name of
     *           [TargetSite][google.cloud.discoveryengine.v1.TargetSite], such as
     *           `projects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/siteSearchEngine/targetSites/{target_site}`.
     *           If the caller does not have permission to access the
     *           [TargetSite][google.cloud.discoveryengine.v1.TargetSite], regardless of
     *           whether or not it exists, a PERMISSION_DENIED error is returned.
     *           If the requested [TargetSite][google.cloud.discoveryengine.v1.TargetSite]
     *           does not exist, a NOT_FOUND error is returned.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Discoveryengine\V1\SiteSearchEngineService::initOnce();
        parent::__construct($data);
    }

    /**
     * Required. Full resource name of
     * [TargetSite][google.cloud.discoveryengine.v1.TargetSite], such as
     * `projects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/siteSearchEngine/targetSites/{target_site}`.
     * If the caller does not have permission to access the
     * [TargetSite][google.cloud.discoveryengine.v1.TargetSite], regardless of
     * whether or not it exists, a PERMISSION_DENIED error is returned.
     * If the requested [TargetSite][google.cloud.discoveryengine.v1.TargetSite]
     * does not exist, a NOT_FOUND error is returned.
     *
     * Generated from protobuf field <code>string name = 1 [(.google.api.field_behavior) = REQUIRED, (.google.api.resource_reference) = {</code>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Required. Full resource name of
     * [TargetSite][google.cloud.discoveryengine.v1.TargetSite], such as
     * `projects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/siteSearchEngine/targetSites/{target_site}`.
     * If the caller does not have permission to access the
     * [TargetSite][google.cloud.discoveryengine.v1.TargetSite], regardless of
     * whether or not it exists, a PERMISSION_DENIED error is returned.
     * If the requested [TargetSite][google.cloud.discoveryengine.v1.TargetSite]
     * does not exist, a NOT_FOUND error is returned.
     *
     * Generated from protobuf field <code>string name = 1 [(.google.api.field_behavior) = REQUIRED, (.google.api.resource_reference) = {</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;

        return $this;
    }

}

