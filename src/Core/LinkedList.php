<?php

namespace Kedniko\Vivy\Core;

final class LinkedList
{
    public Node|null $head;

    public Node|null $tail;

    public Node|null $current;

    public Node|null $prev;

    public int $length = 0;

    /**
     * Iteration started
     * */
    private bool $advance = false;


    public function __construct(array $items = [])
    {
        $this->head = null;
        $this->tail = null;
        $this->current = null;
        $this->prev = null;

        if ($items !== []) {
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

    public function toArray(): array
    {
        $items = [];
        $this->each(function ($item) use (&$items): void {
            $items[] = $item;
        });

        return $items;
    }

    public function fromArray(array $items)
    {
        foreach ($items as $item) {
            $this->append(new Node($item));
        }

        return $this;
    }

    /**
     * @param Node|mixed $node
     * 
     * @return void
     */
    public function prepend($node): void
    {
        if (!$node instanceof Node) {
            $node = new Node($node);
        }
        $node->next = $this->head;
        $this->head = $node;
        $this->current = $this->head;
        $node->prev = null;
    }

    /**
     * @param Node|mixed $node
     * 
     * @return void
     */
    public function append($node): void
    {
        $this->incrementLength();

        if (!$node instanceof Node) {
            $node = new Node($node);
        }

        if (!$this->tail instanceof \Kedniko\Vivy\Core\Node) {
            $this->prepend($node);
            $this->tail = $node;

            return;
        }

        $this->tail->next = $node;
        $node->next = null;
        $node->prev = $this->tail;

        $this->tail = $node;

        if (!$this->current instanceof \Kedniko\Vivy\Core\Node) {
            $this->current = $node;
        }
    }

    /**
     * @param  Node|mixed  $node
     */
    public function appendAfterCurrent($node): void
    {
        $this->decrementLength();

        if (!$node instanceof Node) {
            $node = new Node($node);
        }
        if (!$this->current instanceof \Kedniko\Vivy\Core\Node) {
            $this->prepend($node);
            $this->tail = $node;

            return;
        }

        $node->next = $this->current->next;

        if ($this->current->next instanceof \Kedniko\Vivy\Core\Node) {
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
        if (!$this->advance) {
            return $this->current instanceof \Kedniko\Vivy\Core\Node;
        }

        return $this->current->next instanceof \Kedniko\Vivy\Core\Node;
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

        if ($this->current->prev instanceof \Kedniko\Vivy\Core\Node) {
            $this->current->prev->next = $this->current->next;
        }

        if ($this->current->next instanceof \Kedniko\Vivy\Core\Node) {
            $this->current->next->prev = $this->current->prev;
        }

        $this->current = $this->current->next;
        $this->advance = false;
    }

    public function remove(callable $filterCallback, $removeOnlyOne = false): void
    {
        $prev = null;
        $node = $this->head;
        while ($node instanceof \Kedniko\Vivy\Core\Node) {
            $mustRemove = $filterCallback($node->data);
            if ($mustRemove) {
                if ($node === $this->head) {
                    $this->head = $node->next;
                } elseif ($node === $this->tail) {
                    $this->tail = $prev;
                    if ($prev instanceof Node) {
                        $prev->next = $node->next;
                    }
                } elseif ($prev instanceof Node) {
                    $prev->next = $node->next;
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
