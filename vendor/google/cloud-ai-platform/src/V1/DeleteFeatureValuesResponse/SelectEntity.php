<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/aiplatform/v1/featurestore_service.proto

namespace Google\Cloud\AIPlatform\V1\DeleteFeatureValuesResponse;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Response message if the request uses the SelectEntity option.
 *
 * Generated from protobuf message <code>google.cloud.aiplatform.v1.DeleteFeatureValuesResponse.SelectEntity</code>
 */
class SelectEntity extends \Google\Protobuf\Internal\Message
{
    /**
     * The count of deleted entity rows in the offline storage.
     * Each row corresponds to the combination of an entity ID and a timestamp.
     * One entity ID can have multiple rows in the offline storage.
     *
     * Generated from protobuf field <code>int64 offline_storage_deleted_entity_row_count = 1;</code>
     */
    private $offline_storage_deleted_entity_row_count = 0;
    /**
     * The count of deleted entities in the online storage.
     * Each entity ID corresponds to one entity.
     *
     * Generated from protobuf field <code>int64 online_storage_deleted_entity_count = 2;</code>
     */
    private $online_storage_deleted_entity_count = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $offline_storage_deleted_entity_row_count
     *           The count of deleted entity rows in the offline storage.
     *           Each row corresponds to the combination of an entity ID and a timestamp.
     *           One entity ID can have multiple rows in the offline storage.
     *     @type int|string $online_storage_deleted_entity_count
     *           The count of deleted entities in the online storage.
     *           Each entity ID corresponds to one entity.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Aiplatform\V1\FeaturestoreService::initOnce();
        parent::__construct($data);
    }

    /**
     * The count of deleted entity rows in the offline storage.
     * Each row corresponds to the combination of an entity ID and a timestamp.
     * One entity ID can have multiple rows in the offline storage.
     *
     * Generated from protobuf field <code>int64 offline_storage_deleted_entity_row_count = 1;</code>
     * @return int|string
     */
    public function getOfflineStorageDeletedEntityRowCount()
    {
        return $this->offline_storage_deleted_entity_row_count;
    }

    /**
     * The count of deleted entity rows in the offline storage.
     * Each row corresponds to the combination of an entity ID and a timestamp.
     * One entity ID can have multiple rows in the offline storage.
     *
     * Generated from protobuf field <code>int64 offline_storage_deleted_entity_row_count = 1;</code>
     * @param int|string $var
     * @return $this
     */
    public function setOfflineStorageDeletedEntityRowCount($var)
    {
        GPBUtil::checkInt64($var);
        $this->offline_storage_deleted_entity_row_count = $var;

        return $this;
    }

    /**
     * The count of deleted entities in the online storage.
     * Each entity ID corresponds to one entity.
     *
     * Generated from protobuf field <code>int64 online_storage_deleted_entity_count = 2;</code>
     * @return int|string
     */
    public function getOnlineStorageDeletedEntityCount()
    {
        return $this->online_storage_deleted_entity_count;
    }

    /**
     * The count of deleted entities in the online storage.
     * Each entity ID corresponds to one entity.
     *
     * Generated from protobuf field <code>int64 online_storage_deleted_entity_count = 2;</code>
     * @param int|string $var
     * @return $this
     */
    public function setOnlineStorageDeletedEntityCount($var)
    {
        GPBUtil::checkInt64($var);
        $this->online_storage_deleted_entity_count = $var;

        return $this;
    }

}


