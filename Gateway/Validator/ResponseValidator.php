<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Validator;

use Magento\Merchantesolutions\Gateway\Http\Data\Response;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class ResponseValidator
 * @package Magento\Merchantesolutions\Gateway\Validator
 */
class ResponseValidator extends AbstractValidator
{
    /**
     * @var ResultInterfaceFactory
     */
    protected $resultFactory;

    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * ResponseValidator constructor.
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        SubjectReader $subjectReader
    ) {
        $this->resultFactory = $resultFactory;
        $this->subjectReader = $subjectReader;
        parent::__construct($resultFactory);
    }

    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject)
    {
        $response = $this->subjectReader->readResponseObject($validationSubject);
        $responseBody = $response->getBody();

        $isValid = true;
        $fails = [];
        $errorCodes = [];

        $statements = [
            [
                in_array($responseBody[Response::ERROR_CODE], ['000', '085']),
                __('The transaction has failed. ' . $responseBody[Response::AUTH_RESPONSE_TEXT]),
                $responseBody[Response::ERROR_CODE],
            ],
        ];

        foreach ($statements as $statementResult) {
            if (!$statementResult[0]) {
                $isValid = false;
                $fails[] = $statementResult[1];
                $errorCodes[] = $statementResult[2];
            }
        }

        return $this->createResult($isValid, $fails, $errorCodes);
    }
}