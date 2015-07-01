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

use Amazon\Exceptions\RequestException;

/**
 * Class that handles requests to Amazon API.
 *
 * @package Amazon
 * @author  Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
class Request {

    /**
     * @var Connection The Connection for this request.
     */
    private $connection;

    /**
     * @var string The operation to perform.
     */
    private $operation;

    /**
     * @var array|null Extra data to use for this Request.
     */
    private $data;

    /**
     * @var string The ID that came from Amazon after executing the Request.
     */
    private $requestId;

    /**
     * Returns a new request for the given connection and operation.
     *
     * @param string $operation An operation from Operations.
     * @param array  $data      [optional] Extra data to send.
     */
    public function __construct($operation, $data = null) {
        $this->connection = AmazonSDK::getDefaultConnection();
        $this->operation = $operation;
        $this->data = $data;
    }

    /**
     * Executes the request.
     *
     * @return Response
     *
     * @throws RequestException
     */
    public function execute() {
        $timestamp = self::unixTimeToAmazonTime(time());
        $data = array(
            'Service' => 'AWSMechanicalTurkRequester',
            'Version' => AmazonSDK::API_SUPPORTED_VERSION,
            'AWSAccessKeyId' => $this->connection->getAccessKey(),
            'Operation' => $this->operation,
            'Timestamp' => $timestamp,
            'Signature' => self::generateSignature(AmazonSDK::AMAZON_SERVICE,
                    $this->operation, $timestamp, $this->connection->getSecretKey())
        );
        // Attach extra data.
        if (count($this->data)) {
            $data = array_merge($data, $this->data);
        }

        $url = (
                $this->connection->isSandbox() ?
                    AmazonSDK::BASE_SANDBOX_URL :
                    AmazonSDK::BASE_URL
               )
               . '?' . http_build_query($data);
        $answer = simplexml_load_string(file_get_contents($url));

        // Let exceptions go up as if this method threw them.
        $this->checkForRequestErrors($answer);

        $this->requestId = $answer->OperationRequest->RequestId;
        $response = new Response($this, $answer);
        return $response;
    }

    /**
     * Looks for request-level errors in a Response and throws the
     * proper exceptions.
     *
     * @param \SimpleXMLElement $response
     *
     * @throws RequestException
     */
    private function checkForRequestErrors($response) {
        if (!property_exists($response, 'OperationRequest')) {
            throw new RequestException(
                    'It was impossible to retrieve an error message from Amazon API. '
                  . 'Please check your SDK configuration.'
            );
        }
        $operationRequest = $response->OperationRequest;
        if (!property_exists($operationRequest, 'RequestId')) {
            throw new RequestException('It was impossible to retrieve a RequestId.');
        }
        if (property_exists($operationRequest, 'Errors')
            && property_exists($operationRequest->Errors, 'Error')
        ) {
            $error = $operationRequest->Errors->Error;
            throw new RequestException($error->Message, $error->Code);
        }
    }

    /**
     * Returns a dateTime value from a PHP timestamp.
     *
     * @see http://www.w3.org/TR/xmlschema-2/#dateTime
     *
     * @param int $unixTime Number of seconds since the Unix Epoch.
     *
     * @return string The formatted GMT time.
     */
    private static function unixTimeToAmazonTime($unixTime) {
        return gmdate('Y-m-d\TH:i:s\\Z', $unixTime);
    }

    /**
     * Generates the signature requested by Amazon.
     *
     * @param string $service   The name of the service used.
     * @param string $operation The operation to perform.
     * @param string $timestamp The formatted dateTime.
     * @param string $secretKey The secret API key.
     *
     * @return string A signature to use in a request.
     */
    private static function generateSignature($service, $operation, $timestamp, $secretKey) {
        $string_to_encode = $service . $operation . $timestamp;
        $signature = base64_encode(hash_hmac('sha1', $string_to_encode, $secretKey, true));
        return $signature;
    }
}
