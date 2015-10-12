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
 * Class that holds the Amazon's API operation results this framework support.
 *
 * @package Amazon
 * @author  Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
abstract class OperationResults {

    /**#@+
     * @const string Operation result block names on Amazon.
     */
    const GET_ACCOUNT_BALANCE_RESULT = 'GetAccountBalanceResult';
    const BLOCK_WORKER_RESULT = 'BlockWorkerResult';
    const UNBLOCK_WORKER_RESULT = 'UnblockWorkerResult';
    const GET_BLOCKED_WORKERS_RESULT = 'GetBlockedWorkersResult';
    const NOTIFY_WORKERS_RESULT = 'NotifyWorkersResult';
    /**#@-*/
}
