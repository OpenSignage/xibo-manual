<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/discoveryengine/v1beta/schema.proto

namespace GPBMetadata\Google\Cloud\Discoveryengine\V1Beta;

class Schema
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Google\Api\FieldBehavior::initOnce();
        \GPBMetadata\Google\Api\Resource::initOnce();
        \GPBMetadata\Google\Protobuf\Struct::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
0google/cloud/discoveryengine/v1beta/schema.proto#google.cloud.discoveryengine.v1betagoogle/api/resource.protogoogle/protobuf/struct.proto"�
Schema0
struct_schema (2.google.protobuf.StructH 
json_schema (	H 
name (	B�A:��A�
%discoveryengine.googleapis.com/SchemaPprojects/{project}/locations/{location}/dataStores/{data_store}/schemas/{schema}iprojects/{project}/locations/{location}/collections/{collection}/dataStores/{data_store}/schemas/{schema}B
schemaB�
\'com.google.cloud.discoveryengine.v1betaBSchemaProtoPZQcloud.google.com/go/discoveryengine/apiv1beta/discoveryenginepb;discoveryenginepb�DISCOVERYENGINE�#Google.Cloud.DiscoveryEngine.V1Beta�#Google\\Cloud\\DiscoveryEngine\\V1beta�&Google::Cloud::DiscoveryEngine::V1betabproto3'
        , true);

        static::$is_initialized = true;
    }
}

