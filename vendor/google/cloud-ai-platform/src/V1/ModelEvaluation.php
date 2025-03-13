<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/aiplatform/v1/model_evaluation.proto

namespace Google\Cloud\AIPlatform\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * A collection of metrics calculated by comparing Model's predictions on all of
 * the test data against annotations from the test data.
 *
 * Generated from protobuf message <code>google.cloud.aiplatform.v1.ModelEvaluation</code>
 */
class ModelEvaluation extends \Google\Protobuf\Internal\Message
{
    /**
     * Output only. The resource name of the ModelEvaluation.
     *
     * Generated from protobuf field <code>string name = 1 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     */
    private $name = '';
    /**
     * The display name of the ModelEvaluation.
     *
     * Generated from protobuf field <code>string display_name = 10;</code>
     */
    private $display_name = '';
    /**
     * Points to a YAML file stored on Google Cloud Storage describing the
     * [metrics][google.cloud.aiplatform.v1.ModelEvaluation.metrics] of this
     * ModelEvaluation. The schema is defined as an OpenAPI 3.0.2 [Schema
     * Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     *
     * Generated from protobuf field <code>string metrics_schema_uri = 2;</code>
     */
    private $metrics_schema_uri = '';
    /**
     * Evaluation metrics of the Model. The schema of the metrics is stored in
     * [metrics_schema_uri][google.cloud.aiplatform.v1.ModelEvaluation.metrics_schema_uri]
     *
     * Generated from protobuf field <code>.google.protobuf.Value metrics = 3;</code>
     */
    private $metrics = null;
    /**
     * Output only. Timestamp when this ModelEvaluation was created.
     *
     * Generated from protobuf field <code>.google.protobuf.Timestamp create_time = 4 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     */
    private $create_time = null;
    /**
     * All possible
     * [dimensions][google.cloud.aiplatform.v1.ModelEvaluationSlice.Slice.dimension]
     * of ModelEvaluationSlices. The dimensions can be used as the filter of the
     * [ModelService.ListModelEvaluationSlices][google.cloud.aiplatform.v1.ModelService.ListModelEvaluationSlices]
     * request, in the form of `slice.dimension = <dimension>`.
     *
     * Generated from protobuf field <code>repeated string slice_dimensions = 5;</code>
     */
    private $slice_dimensions;
    /**
     * Points to a YAML file stored on Google Cloud Storage describing
     * [EvaluatedDataItemView.data_item_payload][] and
     * [EvaluatedAnnotation.data_item_payload][google.cloud.aiplatform.v1.EvaluatedAnnotation.data_item_payload].
     * The schema is defined as an OpenAPI 3.0.2 [Schema
     * Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     * This field is not populated if there are neither EvaluatedDataItemViews nor
     * EvaluatedAnnotations under this ModelEvaluation.
     *
     * Generated from protobuf field <code>string data_item_schema_uri = 6;</code>
     */
    private $data_item_schema_uri = '';
    /**
     * Points to a YAML file stored on Google Cloud Storage describing
     * [EvaluatedDataItemView.predictions][],
     * [EvaluatedDataItemView.ground_truths][],
     * [EvaluatedAnnotation.predictions][google.cloud.aiplatform.v1.EvaluatedAnnotation.predictions],
     * and
     * [EvaluatedAnnotation.ground_truths][google.cloud.aiplatform.v1.EvaluatedAnnotation.ground_truths].
     * The schema is defined as an OpenAPI 3.0.2 [Schema
     * Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     * This field is not populated if there are neither EvaluatedDataItemViews nor
     * EvaluatedAnnotations under this ModelEvaluation.
     *
     * Generated from protobuf field <code>string annotation_schema_uri = 7;</code>
     */
    private $annotation_schema_uri = '';
    /**
     * Aggregated explanation metrics for the Model's prediction output over the
     * data this ModelEvaluation uses. This field is populated only if the Model
     * is evaluated with explanations, and only for AutoML tabular Models.
     *
     * Generated from protobuf field <code>.google.cloud.aiplatform.v1.ModelExplanation model_explanation = 8;</code>
     */
    private $model_explanation = null;
    /**
     * Describes the values of
     * [ExplanationSpec][google.cloud.aiplatform.v1.ExplanationSpec] that are used
     * for explaining the predicted values on the evaluated data.
     *
     * Generated from protobuf field <code>repeated .google.cloud.aiplatform.v1.ModelEvaluation.ModelEvaluationExplanationSpec explanation_specs = 9;</code>
     */
    private $explanation_specs;
    /**
     * The metadata of the ModelEvaluation.
     * For the ModelEvaluation uploaded from Managed Pipeline, metadata contains a
     * structured value with keys of "pipeline_job_id", "evaluation_dataset_type",
     * "evaluation_dataset_path", "row_based_metrics_path".
     *
     * Generated from protobuf field <code>.google.protobuf.Value metadata = 11;</code>
     */
    private $metadata = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $name
     *           Output only. The resource name of the ModelEvaluation.
     *     @type string $display_name
     *           The display name of the ModelEvaluation.
     *     @type string $metrics_schema_uri
     *           Points to a YAML file stored on Google Cloud Storage describing the
     *           [metrics][google.cloud.aiplatform.v1.ModelEvaluation.metrics] of this
     *           ModelEvaluation. The schema is defined as an OpenAPI 3.0.2 [Schema
     *           Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     *     @type \Google\Protobuf\Value $metrics
     *           Evaluation metrics of the Model. The schema of the metrics is stored in
     *           [metrics_schema_uri][google.cloud.aiplatform.v1.ModelEvaluation.metrics_schema_uri]
     *     @type \Google\Protobuf\Timestamp $create_time
     *           Output only. Timestamp when this ModelEvaluation was created.
     *     @type array<string>|\Google\Protobuf\Internal\RepeatedField $slice_dimensions
     *           All possible
     *           [dimensions][google.cloud.aiplatform.v1.ModelEvaluationSlice.Slice.dimension]
     *           of ModelEvaluationSlices. The dimensions can be used as the filter of the
     *           [ModelService.ListModelEvaluationSlices][google.cloud.aiplatform.v1.ModelService.ListModelEvaluationSlices]
     *           request, in the form of `slice.dimension = <dimension>`.
     *     @type string $data_item_schema_uri
     *           Points to a YAML file stored on Google Cloud Storage describing
     *           [EvaluatedDataItemView.data_item_payload][] and
     *           [EvaluatedAnnotation.data_item_payload][google.cloud.aiplatform.v1.EvaluatedAnnotation.data_item_payload].
     *           The schema is defined as an OpenAPI 3.0.2 [Schema
     *           Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     *           This field is not populated if there are neither EvaluatedDataItemViews nor
     *           EvaluatedAnnotations under this ModelEvaluation.
     *     @type string $annotation_schema_uri
     *           Points to a YAML file stored on Google Cloud Storage describing
     *           [EvaluatedDataItemView.predictions][],
     *           [EvaluatedDataItemView.ground_truths][],
     *           [EvaluatedAnnotation.predictions][google.cloud.aiplatform.v1.EvaluatedAnnotation.predictions],
     *           and
     *           [EvaluatedAnnotation.ground_truths][google.cloud.aiplatform.v1.EvaluatedAnnotation.ground_truths].
     *           The schema is defined as an OpenAPI 3.0.2 [Schema
     *           Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     *           This field is not populated if there are neither EvaluatedDataItemViews nor
     *           EvaluatedAnnotations under this ModelEvaluation.
     *     @type \Google\Cloud\AIPlatform\V1\ModelExplanation $model_explanation
     *           Aggregated explanation metrics for the Model's prediction output over the
     *           data this ModelEvaluation uses. This field is populated only if the Model
     *           is evaluated with explanations, and only for AutoML tabular Models.
     *     @type array<\Google\Cloud\AIPlatform\V1\ModelEvaluation\ModelEvaluationExplanationSpec>|\Google\Protobuf\Internal\RepeatedField $explanation_specs
     *           Describes the values of
     *           [ExplanationSpec][google.cloud.aiplatform.v1.ExplanationSpec] that are used
     *           for explaining the predicted values on the evaluated data.
     *     @type \Google\Protobuf\Value $metadata
     *           The metadata of the ModelEvaluation.
     *           For the ModelEvaluation uploaded from Managed Pipeline, metadata contains a
     *           structured value with keys of "pipeline_job_id", "evaluation_dataset_type",
     *           "evaluation_dataset_path", "row_based_metrics_path".
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Aiplatform\V1\ModelEvaluation::initOnce();
        parent::__construct($data);
    }

    /**
     * Output only. The resource name of the ModelEvaluation.
     *
     * Generated from protobuf field <code>string name = 1 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Output only. The resource name of the ModelEvaluation.
     *
     * Generated from protobuf field <code>string name = 1 [(.google.api.field_behavior) = OUTPUT_ONLY];</code>
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
     * The display name of the ModelEvaluation.
     *
     * Generated from protobuf field <code>string display_name = 10;</code>
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * The display name of the ModelEvaluation.
     *
     * Generated from protobuf field <code>string display_name = 10;</code>
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
     * Points to a YAML file stored on Google Cloud Storage describing the
     * [metrics][google.cloud.aiplatform.v1.ModelEvaluation.metrics] of this
     * ModelEvaluation. The schema is defined as an OpenAPI 3.0.2 [Schema
     * Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     *
     * Generated from protobuf field <code>string metrics_schema_uri = 2;</code>
     * @return string
     */
    public function getMetricsSchemaUri()
    {
        return $this->metrics_schema_uri;
    }

    /**
     * Points to a YAML file stored on Google Cloud Storage describing the
     * [metrics][google.cloud.aiplatform.v1.ModelEvaluation.metrics] of this
     * ModelEvaluation. The schema is defined as an OpenAPI 3.0.2 [Schema
     * Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     *
     * Generated from protobuf field <code>string metrics_schema_uri = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setMetricsSchemaUri($var)
    {
        GPBUtil::checkString($var, True);
        $this->metrics_schema_uri = $var;

        return $this;
    }

    /**
     * Evaluation metrics of the Model. The schema of the metrics is stored in
     * [metrics_schema_uri][google.cloud.aiplatform.v1.ModelEvaluation.metrics_schema_uri]
     *
     * Generated from protobuf field <code>.google.protobuf.Value metrics = 3;</code>
     * @return \Google\Protobuf\Value|null
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    public function hasMetrics()
    {
        return isset($this->metrics);
    }

    public function clearMetrics()
    {
        unset($this->metrics);
    }

    /**
     * Evaluation metrics of the Model. The schema of the metrics is stored in
     * [metrics_schema_uri][google.cloud.aiplatform.v1.ModelEvaluation.metrics_schema_uri]
     *
     * Generated from protobuf field <code>.google.protobuf.Value metrics = 3;</code>
     * @param \Google\Protobuf\Value $var
     * @return $this
     */
    public function setMetrics($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\Value::class);
        $this->metrics = $var;

        return $this;
    }

    /**
     * Output only. Timestamp when this ModelEvaluation was created.
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
     * Output only. Timestamp when this ModelEvaluation was created.
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

    /**
     * All possible
     * [dimensions][google.cloud.aiplatform.v1.ModelEvaluationSlice.Slice.dimension]
     * of ModelEvaluationSlices. The dimensions can be used as the filter of the
     * [ModelService.ListModelEvaluationSlices][google.cloud.aiplatform.v1.ModelService.ListModelEvaluationSlices]
     * request, in the form of `slice.dimension = <dimension>`.
     *
     * Generated from protobuf field <code>repeated string slice_dimensions = 5;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getSliceDimensions()
    {
        return $this->slice_dimensions;
    }

    /**
     * All possible
     * [dimensions][google.cloud.aiplatform.v1.ModelEvaluationSlice.Slice.dimension]
     * of ModelEvaluationSlices. The dimensions can be used as the filter of the
     * [ModelService.ListModelEvaluationSlices][google.cloud.aiplatform.v1.ModelService.ListModelEvaluationSlices]
     * request, in the form of `slice.dimension = <dimension>`.
     *
     * Generated from protobuf field <code>repeated string slice_dimensions = 5;</code>
     * @param array<string>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setSliceDimensions($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->slice_dimensions = $arr;

        return $this;
    }

    /**
     * Points to a YAML file stored on Google Cloud Storage describing
     * [EvaluatedDataItemView.data_item_payload][] and
     * [EvaluatedAnnotation.data_item_payload][google.cloud.aiplatform.v1.EvaluatedAnnotation.data_item_payload].
     * The schema is defined as an OpenAPI 3.0.2 [Schema
     * Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     * This field is not populated if there are neither EvaluatedDataItemViews nor
     * EvaluatedAnnotations under this ModelEvaluation.
     *
     * Generated from protobuf field <code>string data_item_schema_uri = 6;</code>
     * @return string
     */
    public function getDataItemSchemaUri()
    {
        return $this->data_item_schema_uri;
    }

    /**
     * Points to a YAML file stored on Google Cloud Storage describing
     * [EvaluatedDataItemView.data_item_payload][] and
     * [EvaluatedAnnotation.data_item_payload][google.cloud.aiplatform.v1.EvaluatedAnnotation.data_item_payload].
     * The schema is defined as an OpenAPI 3.0.2 [Schema
     * Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     * This field is not populated if there are neither EvaluatedDataItemViews nor
     * EvaluatedAnnotations under this ModelEvaluation.
     *
     * Generated from protobuf field <code>string data_item_schema_uri = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setDataItemSchemaUri($var)
    {
        GPBUtil::checkString($var, True);
        $this->data_item_schema_uri = $var;

        return $this;
    }

    /**
     * Points to a YAML file stored on Google Cloud Storage describing
     * [EvaluatedDataItemView.predictions][],
     * [EvaluatedDataItemView.ground_truths][],
     * [EvaluatedAnnotation.predictions][google.cloud.aiplatform.v1.EvaluatedAnnotation.predictions],
     * and
     * [EvaluatedAnnotation.ground_truths][google.cloud.aiplatform.v1.EvaluatedAnnotation.ground_truths].
     * The schema is defined as an OpenAPI 3.0.2 [Schema
     * Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     * This field is not populated if there are neither EvaluatedDataItemViews nor
     * EvaluatedAnnotations under this ModelEvaluation.
     *
     * Generated from protobuf field <code>string annotation_schema_uri = 7;</code>
     * @return string
     */
    public function getAnnotationSchemaUri()
    {
        return $this->annotation_schema_uri;
    }

    /**
     * Points to a YAML file stored on Google Cloud Storage describing
     * [EvaluatedDataItemView.predictions][],
     * [EvaluatedDataItemView.ground_truths][],
     * [EvaluatedAnnotation.predictions][google.cloud.aiplatform.v1.EvaluatedAnnotation.predictions],
     * and
     * [EvaluatedAnnotation.ground_truths][google.cloud.aiplatform.v1.EvaluatedAnnotation.ground_truths].
     * The schema is defined as an OpenAPI 3.0.2 [Schema
     * Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.0.2.md#schemaObject).
     * This field is not populated if there are neither EvaluatedDataItemViews nor
     * EvaluatedAnnotations under this ModelEvaluation.
     *
     * Generated from protobuf field <code>string annotation_schema_uri = 7;</code>
     * @param string $var
     * @return $this
     */
    public function setAnnotationSchemaUri($var)
    {
        GPBUtil::checkString($var, True);
        $this->annotation_schema_uri = $var;

        return $this;
    }

    /**
     * Aggregated explanation metrics for the Model's prediction output over the
     * data this ModelEvaluation uses. This field is populated only if the Model
     * is evaluated with explanations, and only for AutoML tabular Models.
     *
     * Generated from protobuf field <code>.google.cloud.aiplatform.v1.ModelExplanation model_explanation = 8;</code>
     * @return \Google\Cloud\AIPlatform\V1\ModelExplanation|null
     */
    public function getModelExplanation()
    {
        return $this->model_explanation;
    }

    public function hasModelExplanation()
    {
        return isset($this->model_explanation);
    }

    public function clearModelExplanation()
    {
        unset($this->model_explanation);
    }

    /**
     * Aggregated explanation metrics for the Model's prediction output over the
     * data this ModelEvaluation uses. This field is populated only if the Model
     * is evaluated with explanations, and only for AutoML tabular Models.
     *
     * Generated from protobuf field <code>.google.cloud.aiplatform.v1.ModelExplanation model_explanation = 8;</code>
     * @param \Google\Cloud\AIPlatform\V1\ModelExplanation $var
     * @return $this
     */
    public function setModelExplanation($var)
    {
        GPBUtil::checkMessage($var, \Google\Cloud\AIPlatform\V1\ModelExplanation::class);
        $this->model_explanation = $var;

        return $this;
    }

    /**
     * Describes the values of
     * [ExplanationSpec][google.cloud.aiplatform.v1.ExplanationSpec] that are used
     * for explaining the predicted values on the evaluated data.
     *
     * Generated from protobuf field <code>repeated .google.cloud.aiplatform.v1.ModelEvaluation.ModelEvaluationExplanationSpec explanation_specs = 9;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getExplanationSpecs()
    {
        return $this->explanation_specs;
    }

    /**
     * Describes the values of
     * [ExplanationSpec][google.cloud.aiplatform.v1.ExplanationSpec] that are used
     * for explaining the predicted values on the evaluated data.
     *
     * Generated from protobuf field <code>repeated .google.cloud.aiplatform.v1.ModelEvaluation.ModelEvaluationExplanationSpec explanation_specs = 9;</code>
     * @param array<\Google\Cloud\AIPlatform\V1\ModelEvaluation\ModelEvaluationExplanationSpec>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setExplanationSpecs($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Cloud\AIPlatform\V1\ModelEvaluation\ModelEvaluationExplanationSpec::class);
        $this->explanation_specs = $arr;

        return $this;
    }

    /**
     * The metadata of the ModelEvaluation.
     * For the ModelEvaluation uploaded from Managed Pipeline, metadata contains a
     * structured value with keys of "pipeline_job_id", "evaluation_dataset_type",
     * "evaluation_dataset_path", "row_based_metrics_path".
     *
     * Generated from protobuf field <code>.google.protobuf.Value metadata = 11;</code>
     * @return \Google\Protobuf\Value|null
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    public function hasMetadata()
    {
        return isset($this->metadata);
    }

    public function clearMetadata()
    {
        unset($this->metadata);
    }

    /**
     * The metadata of the ModelEvaluation.
     * For the ModelEvaluation uploaded from Managed Pipeline, metadata contains a
     * structured value with keys of "pipeline_job_id", "evaluation_dataset_type",
     * "evaluation_dataset_path", "row_based_metrics_path".
     *
     * Generated from protobuf field <code>.google.protobuf.Value metadata = 11;</code>
     * @param \Google\Protobuf\Value $var
     * @return $this
     */
    public function setMetadata($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\Value::class);
        $this->metadata = $var;

        return $this;
    }

}

