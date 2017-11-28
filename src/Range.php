<?php
/**
 * This file is part of the ramsey/http-range library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ramsey\Http\Range;

use Psr\Http\Message\RequestInterface;
use Ramsey\Http\Range\Exception\NoRangeException;

/**
 * Represents an HTTP Range request header
 *
 * @link https://tools.ietf.org/html/rfc7233 RFC 7233: HTTP Range Requests
 */
class Range
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var mixed
     */
    private $size;

    /**
     * @var UnitFactoryInterface
     */
    private $unitFactory;

    /**
     * @param RequestInterface $request
     * @param mixed $size The total size of the entity for which a range is requested
     * @param UnitFactoryInterface $unitFactory
     */
    public function __construct(
        RequestInterface $request,
        $size,
        UnitFactoryInterface $unitFactory = null
    ) {
        $this->request = $request;
        $this->size = $size;

        if ($unitFactory === null) {
            $unitFactory = new UnitFactory();
        }

        $this->unitFactory = $unitFactory;
    }

    /**
     * Returns the HTTP request object
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the total size of the entity for which the range is requested
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Returns the unit factory used by this range
     *
     * @return UnitFactoryInterface
     */
    public function getUnitFactory()
    {
        return $this->unitFactory;
    }

    /**
     * Returns the unit parsed for this range request
     *
     * @return UnitInterface
     * @throws NoRangeException if a range request is not present in the current request
     */
    public function getUnit()
    {
        $rangeHeader = $this->getRequest()->getHeader('Range');

        if (empty($rangeHeader)) {
            throw new NoRangeException();
        }

        // Use only the first Range header found, for now.
        return $this->getUnitFactory()->getUnit(trim($rangeHeader[0]), $this->getSize());
    }
}
