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
namespace Amazon\Entities;

use Amazon\Operations;
use Amazon\OperationResults;
use Amazon\Request;

/**
 * Represents an Account on Amazon Mechanical Turk.
 *
 * @package Amazon
 * @author  Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
class Account extends AbstractEntity {

    /**
     * Returns the available balance for this Account.
     *
     * @return Price
     *
     * @throws OperationResultException
     * @throws RequestException
     */
    public function getBalance() {
        $operationName = Operations::GET_ACCOUNT_BALANCE;
        $operationResultName = OperationResults::GET_ACCOUNT_BALANCE_RESULT;

        $request = new Request($operationName);
        /** @var \SimpleXMLElement $operationResult */
        $operationResult = $request->execute()->retrieveResult($operationResultName);

        return Price::parseFromXml($operationResult->AvailableBalance);
    }
}
