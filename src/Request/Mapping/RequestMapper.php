<?php

declare(strict_types=1);

namespace App\Request\Mapping;

use App\Request\Mapping\Attribute\JsonPayload;
use App\Request\Mapping\Attribute\SourceAttribute;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestMapper
{
    public function __construct(
        private DenormalizerInterface $serializer,
        private ValidatorInterface $validator,
        private RequestDataExtractor $extractor
    ) {
    }

    /**
     * @param class-string $targetClass
     *
     * @throws InvalidDataException
     */
    public function mapRequestToObject(
        ServerRequestInterface $request,
        string $targetClass,
        SourceAttribute $dataSourceAttribute
    ): object {
        $data = $this->extractor->getDataForRequestObjectFromRequest(
            $request,
            $dataSourceAttribute
        );

        $ref = new ReflectionClass($targetClass);
        $usePreMappingValidation = $ref->implementsInterface(RawDataValidation::class);

        if ($usePreMappingValidation) {
            // pre mapping validation
            $getConstraint = $ref->getMethod('getConstraint');
            $violations = $this->validator->validate(
                $data,
                $getConstraint->invoke(null)
            );
            if ($violations->count() > 0) {
                throw new InvalidDataException($violations);
            }
        }

        if ($ref->implementsInterface(ManualDataMapping::class)) {
            $obj = $ref->getMethod('mapFromRequestData')->invoke(null, $data);
        } else {
            $obj = $this->denormalize($data, $targetClass, $dataSourceAttribute);
        }
        if (!$usePreMappingValidation) {
            $violations = $this->validator->validate($obj);

            if ($violations->count() > 0) {
                throw new InvalidDataException($violations);
            }
        }

        return $obj;
    }

    /**
     * @param array<mixed> $data
     */
    private function denormalize(array $data, string $class, SourceAttribute $dataSourceAttribute): object
    {
        try {
            if (!empty($data)) {
                $disableTypeEnforcement = !$dataSourceAttribute instanceof JsonPayload;
                $dto = $this->serializer->denormalize($data, $class, null, [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => $disableTypeEnforcement]);
            } else {
                $dto = new $class();
            }

            return $dto;
        } catch (NotNormalizableValueException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        }
    }
}
