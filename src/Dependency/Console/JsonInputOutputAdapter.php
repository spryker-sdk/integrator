<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Dependency\Console;

class JsonInputOutputAdapter extends SymfonyConsoleInputOutputAdapter
{
    /**
     * @var string
     */
    public const MESSAGE_LIST_KEY = 'message-list';

    /**
     * @var string
     */
    public const WARNING_LIST_KEY = 'warning-list';

    /**
     * @var array<string>
     */
    protected array $successList = [];

    /**
     * @var array<string>
     */
    protected array $warningList = [];

    /**
     * @param array<string>|string $messages
     * @param bool $newline
     * @param int $options
     *
     * @return void
     */
    public function write($messages, bool $newline = false, int $options = 0): void
    {
        $this->successList[] = is_array($messages) ? implode(' ', $messages) : $messages;
    }

    /**
     * @param array<string>|string $messages
     * @param int $options
     * @param bool $isWarning
     *
     * @return void
     */
    public function writeln($messages, int $options = 0, bool $isWarning = false): void
    {
        $this->successList[] = is_array($messages) ? implode(' ', $messages) : $messages;
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function warning(string $message): void
    {
        $this->warningList[] = $message;
    }

    public function __destruct()
    {
        $this->symfonyStyle->write((string)json_encode([
            static::MESSAGE_LIST_KEY => $this->successList,
            static::WARNING_LIST_KEY => $this->warningList,
        ]));
    }
}
