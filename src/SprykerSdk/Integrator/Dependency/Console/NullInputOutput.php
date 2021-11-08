<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Dependency\Console;

class NullInputOutput implements InputOutputInterface
{
    /**
     * Writes a message to the output.
     *
     * @param string|array<string> $messages The message as an iterable of strings or a single string
     * @param bool $newline Whether to add a newline
     * @param int $options A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     *
     * @return void
     */
    public function write($messages, bool $newline = false, int $options = 0): void
    {
        echo $messages;
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array<string> $messages The message as an iterable of strings or a single string
     * @param int $options A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     *
     * @return void
     */
    public function writeln($messages, int $options = 0): void
    {
        echo $messages . '<br>';
    }

    /**
     * Asks a question.
     *
     * @param string $question
     * @param string|null $default
     * @param callable|null $validator
     *
     * @return mixed
     */
    public function ask(string $question, ?string $default = null, ?callable $validator = null)
    {
        return $default;
    }

    /**
     * Asks for confirmation.
     *
     * @param string $question
     * @param bool $default
     *
     * @return bool
     */
    public function confirm(string $question, bool $default = true): bool
    {
        return $default;
    }

    /**
     * Asks a choice question.
     *
     * @param string $question
     * @param array $choices
     * @param string|int|null $default
     *
     * @return mixed
     */
    public function choice(string $question, array $choices, $default = null)
    {
        return $default;
    }
}
