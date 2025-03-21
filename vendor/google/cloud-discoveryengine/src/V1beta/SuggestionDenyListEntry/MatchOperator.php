<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/discoveryengine/v1beta/completion.proto

namespace Google\Cloud\DiscoveryEngine\V1beta\SuggestionDenyListEntry;

use UnexpectedValueException;

/**
 * Operator for matching with the generated suggestions.
 *
 * Protobuf type <code>google.cloud.discoveryengine.v1beta.SuggestionDenyListEntry.MatchOperator</code>
 */
class MatchOperator
{
    /**
     * Default value. Should not be used
     *
     * Generated from protobuf enum <code>MATCH_OPERATOR_UNSPECIFIED = 0;</code>
     */
    const MATCH_OPERATOR_UNSPECIFIED = 0;
    /**
     * If the suggestion is an exact match to the block_phrase, then block it.
     *
     * Generated from protobuf enum <code>EXACT_MATCH = 1;</code>
     */
    const EXACT_MATCH = 1;
    /**
     * If the suggestion contains the block_phrase, then block it.
     *
     * Generated from protobuf enum <code>CONTAINS = 2;</code>
     */
    const CONTAINS = 2;

    private static $valueToName = [
        self::MATCH_OPERATOR_UNSPECIFIED => 'MATCH_OPERATOR_UNSPECIFIED',
        self::EXACT_MATCH => 'EXACT_MATCH',
        self::CONTAINS => 'CONTAINS',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no name defined for value %s', __CLASS__, $value));
        }
        return self::$valueToName[$value];
    }


    public static function value($name)
    {
        $const = __CLASS__ . '::' . strtoupper($name);
        if (!defined($const)) {
            throw new UnexpectedValueException(sprintf(
                    'Enum %s has no value defined for name %s', __CLASS__, $name));
        }
        return constant($const);
    }
}


