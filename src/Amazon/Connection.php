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

/**
 * Class that holds the access keys to Amazon API.
 *
 * @package Amazon
 * @author  Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
class Connection {

    /**
     * @var string The access key to access Amazon API.
     */
    private $accessKey;

    /**
     * @var string The secret key to access Amazon API.
     */
    private $secretKey;

    /**
     * @var bool If this connection is to the sandbox mode or not.
     */
    private $sandbox;

    /**
     * Returns a new Connection that uses the given API keys.
     *
     * @param string $accessKey The API access key.
     * @param string $secretKey The API secret key.
     * @param bool   $isSandbox [optional] If the sandbox mode is active.
     *      Defaults to false (production mode).
     */
    public function __construct($accessKey, $secretKey, $isSandbox = false) {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->sandbox = $isSandbox;
    }

    /**
     * Sets the access key.
     *
     * @param string $accessKey
     */
    public function setAccessKey($accessKey) {
        $this->accessKey = $accessKey;
    }

    /**
     * Returns the access key.
     *
     * @return string
     */
    public function getAccessKey() {
        return $this->accessKey;
    }

    /**
     * Sets the secret key.
     *
     * @param string $secretKey
     */
    public function setSecretKey($secretKey) {
        $this->secretKey = $secretKey;
    }

    /**
     * Returns the secret key.
     *
     * @return string
     */
    public function getSecretKey() {
        return $this->secretKey;
    }

    /**
     * Sets the sandbox mode. If false, the mode will be production.
     *
     * @param bool $isSandbox
     */
    public function setSandbox($isSandbox) {
        $this->sandbox = $isSandbox;
    }

    /**
     * Returns the mode for this Connection. If true is returned, the active
     * mode is sandbox. If false is returned, the Connection is to the production
     * service.
     *
     * @return bool
     */
    public function isSandbox() {
        return $this->sandbox;
    }
}
