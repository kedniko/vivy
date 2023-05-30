<?php

namespace Kedniko\Vivy\Core;

final class LinkedList
{
    /** @var Node|null */
    public $head;

    /** @var Node|null */
    public $tail;

    /** @var Node|null */
    public $current;

    /** @var Node|null */
    public $prev;

    /**
     * @var int
     */
    public $length = 0;

    /**
     * Iteration started
     * */
    private bool $advance = false;

    /**
     * @param  array  $items
     */
    public function __construct($items = [])
    {
        if ($items) {
            $this->fromArray($items);
        }
    }

    private function incrementLength(): void
    {
        $this->length++;
    }

    private function decrementLength(): void
    {
        if ($this->length > 0) {
            $this->length--;
        }
    }

    public function isEmpty(): bool
    {
        return $this->length === 0;
    }

    public function length()
    {
        return $this->length;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        $items = [];
        $this->each(function ($item) use (&$items): void {
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
    public function prepend($node): void
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
    public function append($node): void
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
    public function appendAfterCurrent($node): void
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

    public function hasNext(): bool
    {
        if ($this->advance === false) {
            return $this->current !== null;
        }

        return $this->current->next !== null;
    }

    public function each(callable $callback): void
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

    public function rewind(): void
    {
        $this->advance = false;
        $this->current = $this->head;
    }

    public function getCurrent()
    {
        return $this->current->data;
    }

    public function getNext()
    {
        if ($this->advance) {
            $this->advanceCurrent();
        }

        $this->advance = true;

        return $this->current->data;
    }

    private function advanceCurrent(): void
    {
        $this->current = $this->current->next;
    }

    public function removeCurrent(): void
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

    public function remove(callable $filterCallback, $removeOnlyOne = false): void
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
                $node = null;

                $this->decrementLength();
            } else {
                $prev = $node;
                $node = $node->next;
            }
        }
    }
}
