<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\PHPCPD;

/**
 * A map of exact clones.
 *
 * @since     Class available since Release 1.1.0
 */
class CodeCloneMap implements \Countable, \Iterator
{
    /**
     * @var CodeClone[] The clones in the clone map
     */
    protected $clones = [];

    /**
     * @var CodeClone[] The clones in the clone map, stored by ID
     */
    protected $clonesById = [];

    /**
     * @var int Current position while iterating the clone map
     */
    protected $position = 0;

    /**
     * @var int Number of duplicate lines in the clone map
     */
    protected $numDuplicateLines = 0;

    /**
     * @var int Number of lines analyzed
     */
    protected $numLines = 0;

    /**
     * Adds a clone to the map.
     *
     * @param CodeClone $clone
     */
    public function addClone(CodeClone $clone)
    {
        $id = $clone->getId();

        if (!isset($this->clonesById[$id])) {
            $this->clones[]        = $clone;
            $this->clonesById[$id] = $clone;
        } else {
            $existClone = $this->clonesById[$id];

            foreach ($clone->getFiles() as $file) {
                $existClone->addFile($file);
            }
        }

        $this->numDuplicateLines += $clone->getSize();
    }

    /**
     * Returns the clones stored in this map.
     *
     * @return CodeClone[]
     */
    public function getClones()
    {
        return $this->clones;
    }

    /**
     * Returns the percentage of duplicated code lines in the project.
     *
     * @return string
     */
    public function getPercentage()
    {
        if ($this->numLines > 0) {
            $percent = ($this->numDuplicateLines / $this->numLines) * 100;
        } else {
            $percent = 100;
        }

        return sprintf('%01.2F%%', $percent);
    }

    /**
     * Returns the number of lines analyzed.
     *
     * @return int
     */
    public function getNumLines()
    {
        return $this->numLines;
    }

    /**
     * Sets the number of physical source code lines in the project.
     *
     * @param int $numLines
     */
    public function setNumLines($numLines)
    {
        $this->numLines = $numLines;
    }

    /**
     * Returns the number of clones stored in this map.
     */
    public function count()
    {
        return count($this->clones);
    }

    /**
     * Rewinds the Iterator to the first element.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Checks if there is a current element after calls to rewind() or next().
     *
     * @return bool
     */
    public function valid()
    {
        return $this->position < count($this->clones);
    }

    /**
     * Returns the key of the current element.
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Returns the current element.
     *
     * @return CodeClone
     */
    public function current()
    {
        return $this->clones[$this->position];
    }

    /**
     * Moves forward to next element.
     */
    public function next()
    {
        $this->position++;
    }
}
