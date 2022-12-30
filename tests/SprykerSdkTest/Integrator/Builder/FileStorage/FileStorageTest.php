<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdkTest\Integrator\Builder\FileStorage;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Integrator\Builder\FileStorage\FileStorage;

class FileStorageTest extends TestCase
{
    /**
     * @return void
     */
    public function testAddFileShouldAddFile(): void
    {
        //Arrange
        $fileStorage = new FileStorage();

        //Act
        $fileStorage->addFile('someClass.php');
        $files = $fileStorage->flush();

        //Assert
        $this->assertSame(['someClass.php'], $files);
    }

    /**
     * @return void
     */
    public function testAddUniqueFileShouldAddFileWhenSameFilesAdded(): void
    {
        //Arrange
        $fileStorage = new FileStorage();

        //Act
        $fileStorage->addFile('someClass.php');
        $fileStorage->addFile('someClass.php');
        $files = $fileStorage->flush();

        //Assert
        $this->assertSame(['someClass.php'], $files);
    }

    /**
     * @return void
     */
    public function testFlushShouldClearStorage(): void
    {
        //Arrange
        $fileStorage = new FileStorage();

        //Act
        $fileStorage->addFile('someClass.php');
        $fileStorage->flush();
        $files = $fileStorage->flush();

        //Assert
        $this->assertEmpty($files);
    }
}
