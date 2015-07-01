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
 * Contains API information and generic methods.
 *
 * @package Amazon
 * @author Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
abstract class AmazonSDK {

    /**
     * @const string The base URL for a request in the production environment.
     */
    const BASE_URL = 'https://mechanicalturk.amazonaws.com/';

    /**
     * @const string The base URL for a request in the sandbox environment.
     */
    const BASE_SANDBOX_URL = 'https://mechanicalturk.sandbox.amazonaws.com/';

    /**
     * @const string The Amazon API we are using.
     */
    const AMAZON_SERVICE = 'AWSMechanicalTurkRequester';

    /**
     * @const string The supported version of Amazon's API.
     */
    const API_SUPPORTED_VERSION = '2014-08-15';

    /**
     * @const int The max size of a page in Amazon API.
     */
    const MAX_PAGE_SIZE = 65535;

    /**
     * @var Connection The default Connection to use.
     */
    private static $connection;

    /**
     * Sets the default Connection for the SDK to use.
     *
     * @param Connection $connection The Connection.
     */
    public static function setDefaultConnection(Connection $connection) {
       self::$connection = $connection;
    }

    /**
     * Returns the default Connection.
     *
     * @return Connection
     */
    public static function getDefaultConnection() {
        return self::$connection;
    }
}
