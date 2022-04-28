<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Builder\Visitor;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class CloneNodeWithClearPositionVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    protected const POSITION_ATTRIBUTES = [
        'startLine',
        'startTokenPos',
        'endLine',
        'endTokenPos',
        'origNode',
    ];

    /**
     * @param \PhpParser\Node $node
     *
     * @return \PhpParser\Node<\PhpParser\Node>|int|null
     */
    public function leaveNode(Node $node)
    {
        $attributes = [];
        foreach ($node->getAttributes() as $key => $attribute) {
            if (in_array($key, static::POSITION_ATTRIBUTES, true)) {
                continue;
            }

            if ($key === 'comments') {
                $attributes[$key] = [];
                /** @var \PhpParser\Comment\Doc $comment */
                foreach ($attribute as $comment) {
                    $attributes[$key][] = new Doc($comment->getText());
                }

                continue;
            }

            $attributes[$key] = $attribute;
        }

        $node->setAttributes($attributes);

        return $node;
    }

    /**
     * @param \PhpParser\Node $origNode
     *
     * @return \PhpParser\Node|int|null
     */
    public function enterNode(Node $origNode)
    {
        return clone $origNode;
    }
}
