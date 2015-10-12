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
use Amazon\PaginatedRequest;
use Amazon\Request;

use Amazon\Exceptions\InvalidStateException;

/**
 * Represents a Worker on Amazon Mechanical Turk.
 *
 * @package Amazon
 * @author  Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
class Worker extends AbstractEntity {

    /**
     * @var string Amazon's Worker Id.
     */
    private $id;

    /**
     * @var bool If the current Worker has been proved to be a valid one in Amazon.
     */
    private $validated;

    /**
     * @var bool If the current Worker is blocked to do the Account work.
     */
    private $blocked;

    /**
     * @var string The reason the Worker has been blocked.
     */
    private $blockReason;

    /**
     * Returns a Worker.
     *
     * Workers are identifiable only through their Worker Id, and can be verified
     * as valid workers only after one API call.
     *
     * @param string $workerId Amazon's Worker Id.
     */
    public function __construct($workerId = null) {
        if (!is_null($workerId)) {
            $this->id = $workerId;
            $this->validated = false;
        }
    }

    /**
     * Caches the Worker to the latest available state, making the due API calls
     * to populate its properties.
     *
     * @TODO
     */
    public function fetch() {
        // Use private cache methods to look for the Worker in the available collections.
    }

    /**
     * Blocks this Worker.
     *
     * The Worker does not see the reason you blocked them. The reason is only
     * for your records.
     *
     * @param string $reason A message explaining the reason for blocking the Worker.
     *
     * @return bool Returns 'true' on success.
     *
     * @throws \InvalidArgumentException If the reason is shorter than 1 character.
     * @throws OperationResultException
     * @throws RequestException
     */
    public function block($reason) {
        if (strlen($reason) < 1) {
            throw new \InvalidArgumentException('The reason to block the worker must be longer than 1 character.');
        }
        $operationName = Operations::BLOCK_WORKER;
        $operationResultName = OperationResults::BLOCK_WORKER_RESULT;
        $data = array(
            'WorkerId' => $this->id,
            'Reason' => $reason
        );

        $request = new Request($operationName, $data);
        /** @var \SimpleXMLElement $operationResult */
        $operationResult = $request->execute()->retrieveResult($operationResultName);

        $this->validated = true;
        $this->blocked = true;
        return $operationResult;
    }

    /**
     * Unblocks this Worker.
     *
     * The Worker does not see the reason you unblocked them. The reason is only
     * for your records.
     *
     * @param string $reason [optional] A message explaining the reason for unblocking the Worker.
     *
     * @return bool Returns 'true' on success.
     *
     * @throws OperationResultException
     * @throws RequestException
     */
    public function unblock($reason = '') {
        $operationName = Operations::UNBLOCK_WORKER;
        $operationResultName = OperationResults::UNBLOCK_WORKER_RESULT;
        $data = array(
            'WorkerId' => $this->id,
            'Reason' => $reason
        );

        $request = new Request($operationName, $data);
        /** @var \SimpleXMLElement $operationResult */
        $operationResult = $request->execute()->retrieveResult($operationResultName);

        $this->validated = true;
        $this->blocked = false;
        return $operationResult;
    }

    /**
     * Sends a notification message to the Worker.
     *
     * @param string $subject The subject for the notification email.
     * @param string $message The message. HTML markup is not allowed.
     *
     * @return bool Returns 'true' on success.
     *
     * @throws \InvalidArgumentException    If length constraints are not respected.
     * @throws OperationResultException     If the Worker has never worked for the Account.
     */
    public function sendMessage($subject, $message) {
        $maxConstraints = array(
            'subject' => 200,
            'message' => 4096
        );

        if (strlen($subject) < 1) {
            throw new \InvalidArgumentException('The subject of the message must '
                                              . 'be longer than 1 character.');
        } elseif (strlen($subject) > $maxConstraints['subject']) {
            throw new \InvalidArgumentException('The subject of the message can '
                                              . "contain max {$maxConstraints['subject']} characters.");
        }

        if (strlen($message) < 1) {
            throw new \InvalidArgumentException('The subject of the message must '
                                              . 'be longer than 1 character.');
        } elseif (strlen($message) > $maxConstraints['message']) {
            throw new \InvalidArgumentException('The subject of the message can '
                                              . "contain max {$maxConstraints['message']} characters.");
        }

        $operationName = Operations::NOTIFY_WORKERS;
        $operationResultName = OperationResults::NOTIFY_WORKERS_RESULT;
        $data = array(
            'WorkerId.1' => $this->id,
            'Subject' => $subject,
            'MessageText' => $message
        );

        $request = new Request($operationName, $data);
        /** @var \SimpleXMLElement $operationResult */
        $operationResult = $request->execute()->retrieveResult($operationResultName);

        if ($operationResult === true) {
            $this->validated = true;
        } elseif (property_exists($operationResult, 'NotifyWorkersFailureStatus')) {
            $reason = $operationResult->NotifyWorkersFailureStatus->NotifyWorkersFailureMessage;
            throw new OperationResultException("It was impossible to send the message: {$reason}");
        }

        return $operationResult;
    }

    /**
     * Returns the Worker's Id.
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Returns if the Worker has been validated already or not.
     *
     * @return bool
     */
    public function isValidated() {
        return $this->validated;
    }

    /**
     * Returns if the Worker has been blocked to do your work.
     *
     * @return bool
     *
     * @throws InvalidStateException If the Worker hasn't been validated and the
     *      block state is thus unknown, or not cached yet.
     */
    public function isBlocked() {
        if (!$this->validated) {
            throw new InvalidStateException('The Worker object has never been '
                                          . 'synced with Amazon API, the block state is unknown.');
        } else {
            return $this->blocked;
        }
    }

    /**
     * Returns the reason used to block the Worker.
     *
     * @return type
     *
     * @throws InvalidStateException If the Worker hasn't been validated, or
     * it's not blocked.
     */
    public function getBlockReason() {
        if (!$this->validated) {
            throw new InvalidStateException('The Worker object has never been '
                                          . 'synced with Amazon API, the block state is unknown.');
        } elseif (!$this->blocked) {
            throw new InvalidStateException('The Worker has not been blocked, impossible to return a reason.');
        } else {
            return $this->blockReason;
        }
    }

    /**
     * Returns this Worker as an associative array.
     *
     * @return array
     */
    public function toArray() {
        $array = parent::toArray();
        $array['id'] = $this->id;
        $array['validated'] = $this->validated;
        $array['blocked'] = $this->blocked;
        $array['blockReason'] = $this->blockReason;
        return $array;
    }

    /**
     * Returns all blocked workers.
     *
     * @param int|null $max [optional] The maximum number of blocked Workers to return.
     *
     * @return Worker[]
     *
     * @throws \InvalidArgumentException
     * @throws OperationResultException
     * @throws RequestException
     */
    public static function getBlockedWorkers($max = null) {
        $operationName = Operations::GET_BLOCKED_WORKERS;
        $operationResultName = OperationResults::GET_BLOCKED_WORKERS_RESULT;

        if (!is_null($max) && !is_int($max)) {
            throw new \InvalidArgumentException('$max expects an integer.');
        }

        /** @var Worker[] $workers */
        $workers = array();
        $request = new PaginatedRequest($operationName, $operationResultName);
        $request->setMaxRecords($max);
        do {
            $operationResult = $request->fetchNextPage()->retrieveResult($operationResultName);
            foreach ($operationResult->WorkerBlock as $block) {
                $worker = self::parseFromXml($block);
                array_push($workers, $worker);
            }
        } while ($request->hasNextPage());

        return $workers;
    }

    /**
     * Caches a Worker from the blocked Workers collection.
     *
     * @param string $workerId The Worker Id to match.
     *
     * @return Worker|bool Returns false if the Worker hasn't been found.
     *
     * @throws OperationResultException
     * @throws RequestException
     */
    private static function cacheWorkerBlock($workerId) {
        $operationName = Operations::GET_BLOCKED_WORKERS;
        $operationResultName = OperationResults::GET_BLOCKED_WORKERS_RESULT;

        $request = new PaginatedRequest($operationName, $operationResultName);
        do {
            $operationResult = $request->fetchNextPage()->retrieveResult($operationResultName);
            foreach ($operationResult->WorkerBlock as $block) {
                if ($block->WorkerId == $workerId) {
                    return self::parseFromXml($block);
                }
            }
        } while ($request->hasNextPage());

        return false;
    }

    /**
     * Returns a validated Worker from an XML response.
     *
     * @param \SimpleXMLElement $xml     A WorkerBlock Amazon entity.
     * @param bool              $blocked [optional] If the WorkerBlock is related
     *      to a block. Defaults to true.
     *
     * @return Worker
     */
    private static function parseFromXml(\SimpleXMLElement $xml, $blocked = true) {
        $obj = new static();
        $obj->id = (string) $xml->WorkerId;
        $obj->blocked = $blocked;
        $obj->blockReason = (string) $xml->Reason;
        $obj->validated = true;
        return $obj;
    }
}
