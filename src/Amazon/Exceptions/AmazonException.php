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
namespace Amazon\Exceptions;

/**
 * An exception caused by an error coming from Amazon.
 *
 * @package Amazon
 * @author  Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
class AmazonException extends SDKException {

    /**
     * @var string The message regarding this error.
     */
    protected $message;

    /**
     * @var string Amazon's error code.
     */
    protected $code;

    /**
     * Returns a new Amazon exception.
     *
     * @param string $message The message.
     * @param string $code    [optional] Amazon's error code.
     */
    public function __construct($message, $code = null) {
        $this->message = $message;
        $this->code = $code;
        parent::__construct($this->message, -1, null);
    }

    /**
     * Returns this Exception's Amazon error code.
     *
     * @return string
     */
    public function getAmazonErrorCode() {
        return $this->code;
    }
}