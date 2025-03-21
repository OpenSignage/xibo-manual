<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/discoveryengine/v1beta/search_service.proto

namespace Google\Cloud\DiscoveryEngine\V1beta\SearchResponse\Summary;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Citation metadata.
 *
 * Generated from protobuf message <code>google.cloud.discoveryengine.v1beta.SearchResponse.Summary.CitationMetadata</code>
 */
class CitationMetadata extends \Google\Protobuf\Internal\Message
{
    /**
     * Citations for segments.
     *
     * Generated from protobuf field <code>repeated .google.cloud.discoveryengine.v1beta.SearchResponse.Summary.Citation citations = 1;</code>
     */
    private $citations;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type array<\Google\Cloud\DiscoveryEngine\V1beta\SearchResponse\Summary\Citation>|\Google\Protobuf\Internal\RepeatedField $citations
     *           Citations for segments.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Discoveryengine\V1Beta\SearchService::initOnce();
        parent::__construct($data);
    }

    /**
     * Citations for segments.
     *
     * Generated from protobuf field <code>repeated .google.cloud.discoveryengine.v1beta.SearchResponse.Summary.Citation citations = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getCitations()
    {
        return $this->citations;
    }

    /**
     * Citations for segments.
     *
     * Generated from protobuf field <code>repeated .google.cloud.discoveryengine.v1beta.SearchResponse.Summary.Citation citations = 1;</code>
     * @param array<\Google\Cloud\DiscoveryEngine\V1beta\SearchResponse\Summary\Citation>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setCitations($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Cloud\DiscoveryEngine\V1beta\SearchResponse\Summary\Citation::class);
        $this->citations = $arr;

        return $this;
    }

}


