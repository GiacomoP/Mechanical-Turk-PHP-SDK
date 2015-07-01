<?php
/**
 * Copyright (c) 2015 Giacomo Persichini
 *
 * Amazon Mechanical Turk is a product of Amazon Inc.
 *
 * Distributed under the MIT license.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
namespace Amazon;

use Amazon\Exceptions\OperationResultException;

/**
 * Class that handles a response coming from Amazon API.
 *
 * @package Amazon
 * @author  Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
class Response {

    /**
     * @var Request The request that produced this Response. 
     */
    private $request;

    /**
     * @var \SimpleXMLElement The raw response from Amazon.
     */
    private $data;

    /**
     * Returns a Response.
     *
     * @param Request           $request
     * @param \SimpleXMLElement $rawResponse
     */
    public function __construct(Request $request, \SimpleXMLElement $rawResponse) {
        $this->request = $request;
        $this->data = $rawResponse;
    }

    /**
     * Returns the Response data.
     *
     * @return \SimpleXMLElement
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Returns the interested object after an operation, stripping all the
     * Response data that's not needed to a RequesterEntity.
     *
     * Exception will be thrown if the result was impossible to retrieve correctly.
     *
     * @param string $resultObjectName
     *
     * @return \SimpleXMLElement|bool Returns the result object, or simply 'true' on success.
     *
     * @throws OperationResultException
     */
    public function retrieveResult($resultObjectName) {
        if (!property_exists($this->data, $resultObjectName)) {
            throw new OperationResultException('The Result object was missing from the Response.');
        }

        /** @var \SimpleXMLElement $resultObj */
        $resultObj = $this->data->$resultObjectName;
        if (!property_exists($resultObj, 'Request')) {
            throw new OperationResultException(
                    'The Request information were missing from the Result object.'
            );
        }

        /** @var \SimpleXMLElement $requestObj */
        $requestObj = $this->data->$resultObjectName->Request;
        if (!filter_var($requestObj->IsValid, FILTER_VALIDATE_BOOLEAN)) {
            if (property_exists($requestObj, 'Errors')
                && property_exists($requestObj->Errors, 'Error')
            ) {
                $error = $requestObj->Errors->Error;
                throw new OperationResultException($error->Message, $error->Code);
            } else {
                throw new OperationResultException('An unknown error made the Response invalid.');
            }
        }

        // Prepare the output
        $output = clone $this->data->$resultObjectName;
        unset($output->Request);
        if ($output->children()) {
            return $output;
        }
        return true;
    }
}
