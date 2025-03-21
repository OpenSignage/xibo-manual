<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/discoveryengine/v1/site_search_engine.proto

namespace Google\Cloud\DiscoveryEngine\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Verification information for target sites in advanced site search.
 *
 * Generated from protobuf message <code>google.cloud.discoveryengine.v1.SiteVerificationInfo</code>
 */
class SiteVerificationInfo extends \Google\Protobuf\Internal\Message
{
    /**
     * Site verification state indicating the ownership and validity.
     *
     * Generated from protobuf field <code>.google.cloud.discoveryengine.v1.SiteVerificationInfo.SiteVerificationState site_verification_state = 1;</code>
     */
    protected $site_verification_state = 0;
    /**
     * Latest site verification time.
     *
     * Generated from protobuf field <code>.google.protobuf.Timestamp verify_time = 2;</code>
     */
    protected $verify_time = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $site_verification_state
     *           Site verification state indicating the ownership and validity.
     *     @type \Google\Protobuf\Timestamp $verify_time
     *           Latest site verification time.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Discoveryengine\V1\SiteSearchEngine::initOnce();
        parent::__construct($data);
    }

    /**
     * Site verification state indicating the ownership and validity.
     *
     * Generated from protobuf field <code>.google.cloud.discoveryengine.v1.SiteVerificationInfo.SiteVerificationState site_verification_state = 1;</code>
     * @return int
     */
    public function getSiteVerificationState()
    {
        return $this->site_verification_state;
    }

    /**
     * Site verification state indicating the ownership and validity.
     *
     * Generated from protobuf field <code>.google.cloud.discoveryengine.v1.SiteVerificationInfo.SiteVerificationState site_verification_state = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setSiteVerificationState($var)
    {
        GPBUtil::checkEnum($var, \Google\Cloud\DiscoveryEngine\V1\SiteVerificationInfo\SiteVerificationState::class);
        $this->site_verification_state = $var;

        return $this;
    }

    /**
     * Latest site verification time.
     *
     * Generated from protobuf field <code>.google.protobuf.Timestamp verify_time = 2;</code>
     * @return \Google\Protobuf\Timestamp|null
     */
    public function getVerifyTime()
    {
        return $this->verify_time;
    }

    public function hasVerifyTime()
    {
        return isset($this->verify_time);
    }

    public function clearVerifyTime()
    {
        unset($this->verify_time);
    }

    /**
     * Latest site verification time.
     *
     * Generated from protobuf field <code>.google.protobuf.Timestamp verify_time = 2;</code>
     * @param \Google\Protobuf\Timestamp $var
     * @return $this
     */
    public function setVerifyTime($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\Timestamp::class);
        $this->verify_time = $var;

        return $this;
    }

}

