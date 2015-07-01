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
 * Class that handles Amazon API pages logic while requesting data.
 *
 * @package Amazon
 * @author  Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
class PaginatedRequest {

    /**
     * @var string The operation to perform.
     */
    private $operation;

    /**
     * @var string The expected operation result name.
     */
    private $operationResult;

    /**
     * @var int The page number to use for the next Request.
     */
    private $pageNumber;

    /**
     * @var int The page size to use for the next Request.
     */
    private $pageSize;

    /**
     * @var int The maximum number of records to fetch.
     */
    private $maxRecords;

    /**
     * @var int The number of records already fetched.
     */
    private $recordsFetched;

    /**
     * @var int The records left to fetch from the collection.
     */
    private $recordsLeft;

    /**
     * Returns a PaginatedRequest for Requests with pages operations.
     *
     * @param string $operation       An operation from Operations.
     * @param string $operationResult An operation result from OperationResults.
     */
    public function __construct($operation, $operationResult) {
        $this->operation = $operation;
        $this->operationResult = $operationResult;

        $this->pageNumber = 0;
        $this->pageSize = AmazonSDK::MAX_PAGE_SIZE;
        $this->maxRecords = null;
        $this->recordsFetched = 0;
        $this->recordsLeft = null;
    }

    /**
     * Sets the maximum number of records to fetch.
     *
     * Setting a maximum number will automatically adjust the requests to meet
     * this amount with the lowest possible number of API calls.
     *
     * Responses will also contain only this maximum number of records and
     * never more than that.
     *
     * @param int|null $maxNumber The max number. 'null' for unlimited records.
     *
     * @throws \InvalidArgumentException
     */
    public function setMaxRecords($maxNumber) {
        if (!is_null($maxNumber) && !is_int($maxNumber)) {
            throw new \InvalidArgumentException('$maxNumber expects an integer, or null.');
        }

        $this->maxRecords = $maxNumber;
        $this->pageSize =
                $this->maxRecords < AmazonSDK::MAX_PAGE_SIZE ? $this->maxRecords : AmazonSDK::MAX_PAGE_SIZE;
    }

    /**
     * Executes a Request to get the next page for the current operation.
     *
     * @return Response
     *
     * @throws OperationResultException
     * @throws RequestException
     */
    public function fetchNextPage() {
        if (!$this->hasNextPage()) {
            return false;
        }
        /*
         * If there are less records left than the page size, we are at the
         * last page. Lower the page size to the records left just to be precise.
         */
        if (!is_null($this->recordsLeft) && $this->recordsLeft < $this->pageSize) {
            $this->pageSize = $this->recordsLeft;
        }
        // Lower page size even more if max will be reached at the next Request.
        if (!is_null($this->maxRecords) && $this->pageSize + $this->recordsFetched > $this->maxRecords) {
            $this->pageSize = $this->maxRecords - $this->recordsFetched;
        }
        $this->pageNumber++;
        $request = new Request($this->operation, $this->getRequestData());
        /** @var Response $response */
        $response = $request->execute();
        /** @var \SimpleXMLElement $result */
        $result = $response->retrieveResult($this->operationResult);

        // Proceed to update the references.
        $allRecords = $result->TotalNumResults;
        $recordsThisPage = $result->NumResults;
        $this->recordsFetched += $result->NumResults;
        $this->recordsLeft =
                is_null($this->recordsLeft) ? $allRecords - $recordsThisPage : $this->recordsLeft - $recordsThisPage;

        return $response;
    }

    /**
     * Checks if the current PaginatedRequest can fetch the next page.
     *
     * @return bool
     */
    public function hasNextPage() {
        return (is_null($this->recordsLeft) || $this->recordsLeft > 0)
                && (is_null($this->maxRecords) || $this->recordsFetched < $this->maxRecords);
    }

    /**
     * Builds the extra data for the paginated Request.
     *
     * @return array
     */
    private function getRequestData() {
        return array(
            'PageNumber' => $this->pageNumber,
            'PageSize' => $this->pageSize
        );
    }
}
