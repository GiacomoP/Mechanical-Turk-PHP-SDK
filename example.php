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
include 'autoload.php';

/*
 * Declare the classes you are using, they'll be automatically included.
 */
use Amazon\AmazonSDK;
use Amazon\Connection;
use Amazon\Entities\Account;
use Amazon\Entities\Worker;

/*
 * Set up the connection and set it for the SDK to use as the default one.
 * You can change the default Connection in the middle of your script, too.
 * Each request will use the default Connection it finds at that exact moment.
 */
$connection = new Connection('ACCESS_KEY', 'PRIVATE_KEY', true);
AmazonSDK::setDefaultConnection($connection);

/*
 * Get the available balance in your Account, as a Price entity.
 */
$account = new Account();
try {
    $balance = $account->getBalance();
} catch (RequestException $re) {
    // There was a request-level error!
} catch (OperationResultException $oe) {
    // There was an operation-level error!
}
print_r($balance->toArray());

/*
 * Working with Workers!
 * You can instantiate a new Worker from a given Id.
 *
 * If you want to directly jump to your first operation on the Worker you can
 * avoid the fetching step. A Worker will be flagged as 'validated' or 'not yet validated'
 * after the very first API call you'll make through its methods, since that's
 * the only way to know it.
 */
$wid = "A3D9BS7QW6ORGG";
$worker = new Worker($wid);
try {
    $worker->block('After several warnings, he continued to submit answers '
                 . 'without reading the instructions carefully.');
} catch (RequestException $re) {
    // There was a request-level error!
} catch (OperationResultException $oe) {
    // There was an operation-level error!
}
