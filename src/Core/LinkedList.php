<?php

namespace Kedniko\Vivy\Core;

final class LinkedList
{
    /** @var Node|null */
    public $head = null;

    /** @var Node|null */
    public $tail = null;

    /** @var Node|null */
    public $current = null;

    /** @var Node|null */
    public $prev = null;

    /**
     * @var int
     */
    public $length = 0;

    /**
     * Iteration started
     *
     * @var bool
     * */
    private $advance = false;

    /**
     * @param  array  $items
     */
    public function __construct($items = [])
    {
        if ($items) {
            $this->fromArray($items);
        }
    }

    private function incrementLength()
    {
        $this->length++;
    }

    private function decrementLength()
    {
        if ($this->length > 0) {
            $this->length--;
        }
    }

    public function isEmpty()
    {
        return $this->length === 0;
    }

    public function length()
    {
        return $this->length;
    }

    public function toArray()
    {
        $items = [];
        $this->each(function ($item) use (&$items) {
            $items[] = $item;
        });

        return $items;
    }

    /**
     * @param  array  $items
     */
    public function fromArray($items)
    {
        foreach ($items as $item) {
            $this->append(new Node($item));
        }

        return $this;
    }

    /**
     * @param  Node|mixed  $node
     */
    public function prepend($node)
    {
        if (! $node instanceof Node) {
            $node = new Node($node);
        }
        $node->next = $this->head;
        $this->head = $node;
        $this->current = $this->head;
        $node->prev = null;
    }

    /**
     * @param  Node|mixed  $node
     */
    public function append($node)
    {
        $this->incrementLength();

        if (! $node instanceof Node) {
            $node = new Node($node);
        }

        if ($this->tail === null) {
            $this->prepend($node);
            $this->tail = $node;

            return;
        }

        $this->tail->next = $node;
        $node->next = null;
        $node->prev = $this->tail;

        $this->tail = $node;

        if ($this->current === null) {
            $this->current = $node;
        }
    }

    /**
     * @param  Node|mixed  $node
     */
    public function appendAfterCurrent($node)
    {
        $this->decrementLength();

        if (! $node instanceof Node) {
            $node = new Node($node);
        }
        if ($this->current === null) {
            $this->prepend($node);
            $this->tail = $node;

            return;
        }

        $node->next = $this->current->next;

        if ($this->current->next) {
            $this->current->next->prev = $node;
        }

        $this->current->next = $node;

        $node->prev = $this->current;

        if ($this->current === $this->tail) {
            $this->tail = $node;
        }
    }

    public function hasNext()
    {
        if ($this->advance === false) {
            return $this->current !== null;
        }

        return $this->current->next !== null;
    }

    public function each(callable $callback)
    {
        $this->rewind();
        while ($this->hasNext()) {
            $type = $this->getNext();
            if ($callback($type) === false) {
                break;
            }
        }
        $this->rewind();
    }

    // public function find($cb)
    // {
    // 	return $this->each(function ($item) use ($cb) {
    // 		if ($cb($item) === true) {
    // 			return $item;
    // 		}
    // 		return null;
    // 	});
    // }

    public function rewind()
    {
        $this->advance = false;
        $this->current = $this->head;
    }

    public function getCurrent()
    {
        $data = $this->current->data;

        return $data;
    }

    public function getNext()
    {
        if ($this->advance) {
            $this->advanceCurrent();
        }

        $this->advance = true;

        $data = $this->current->data;

        return $data;
    }

    private function advanceCurrent()
    {
        $this->current = $this->current->next;
    }

    public function removeCurrent()
    {
        $this->decrementLength();

        if ($this->current === $this->head) {
            $this->head = $this->current->next;
        }
        if ($this->current === $this->tail) {
            $this->tail = $this->current->prev;
        }

        if ($this->current->prev) {
            $this->current->prev->next = $this->current->next;
        }

        if ($this->current->next) {
            $this->current->next->prev = $this->current->prev;
        }

        $this->current = $this->current->next;
        $this->advance = false;
    }

    public function remove(callable $filterCallback, $removeOnlyOne = false)
    {
        $prev = null;
        $node = $this->head;
        while ($node !== null) {
            $mustRemove = $filterCallback($node->data);
            if ($mustRemove) {
                if ($node === $this->head) {
                    $this->head = $node->next;
                } elseif ($node === $this->tail) {
                    $this->tail = $prev;
                    if ($prev instanceof Node) {
                        $prev->next = $node->next;
                    }
                } else {
                    if ($prev instanceof Node) {
                        $prev->next = $node->next;
                    }
                }
                if ($removeOnlyOne) {
                    return;
                }
                $node = $node->next;
                $node = null;

                $this->decrementLength();
            } else {
                $prev = $node;
                $node = $node->next;
            }
        }
    }
}
