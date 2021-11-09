<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Dependency\Console;

interface InputOutputInterface
{
    /**
     * @var int
     */
    public const QUIET = 1;

    /**
     * @var int
     */
    public const NORMAL = 2;

    /**
     * @var int
     */
    public const VERBOSE = 4;

    /**
     * @var int
     */
    public const VERY_VERBOSE = 8;

    /**
     * @var int
     */
    public const DEBUG = 16;

    /**
     * Writes a message to the output.
     *
     * @param array<string>|string $messages The message as an iterable of strings or a single string
     * @param bool $newline Whether to add a newline
     * @param int $options A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public function write($messages, bool $newline = false, int $options = 0): void;

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param array<string>|string $messages The message as an iterable of strings or a single string
     * @param int $options A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public function writeln($messages, int $options = 0): void;

    /**
     * Asks a question.
     *
     * @param string $question
     * @param string|null $default
     * @param callable|null $validator
     *
     * @return mixed
     */
    public function ask(string $question, ?string $default = null, ?callable $validator = null);

    /**
     * Asks for confirmation.
     *
     * @param string $question
     * @param bool $default
     *
     * @return bool
     */
    public function confirm(string $question, bool $default = true);

    /**
     * Asks a choice question.
     *
     * @param string $question
     * @param array $choices
     * @param string|int|null $default
     *
     * @return mixed
     */
    public function choice(string $question, array $choices, $default = null);
}
