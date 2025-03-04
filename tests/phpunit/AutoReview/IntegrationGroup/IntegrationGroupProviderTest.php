<?php
/**
 * This code is licensed under the BSD 3-Clause License.
 *
 * Copyright (c) 2017, Maks Rafalko
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * * Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

declare(strict_types=1);

namespace Infection\Tests\AutoReview\IntegrationGroup;

use function class_exists;
use Infection\Tests\Console\E2ETest;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use function sprintf;

/**
 * @covers \Infection\Tests\AutoReview\IntegrationGroup\IntegrationGroupProvider
 */
final class IntegrationGroupProviderTest extends TestCase
{
    /**
     * @dataProvider \Infection\Tests\AutoReview\IntegrationGroup\IntegrationGroupProvider::ioTestCaseTupleProvider
     */
    public function test_io_test_case_classes_provider_is_valid(string $testCaseClassName, string $fileWithIoOperations): void
    {
        $this->assertTrue(
            class_exists($testCaseClassName, true),
            sprintf('Expected "%s" to be a class.', $testCaseClassName)
        );

        $testCaseReflection = new ReflectionClass($testCaseClassName);

        $this->assertInstanceOf(
            TestCase::class,
            $testCaseReflection->newInstanceWithoutConstructor()
        );

        $this->assertFalse(
            $testCaseReflection->isAbstract(),
            sprintf(
                'Expected "%s" to be an actual test case, not a base (abstract) one.',
                $testCaseClassName
            )
        );

        $this->assertFileExists($fileWithIoOperations);
    }

    public function test_it_finds_e2e_test(): void
    {
        foreach (IntegrationGroupProvider::ioTestCaseTupleProvider() as $tuple) {
            if ($tuple[0] === E2ETest::class) {
                $this->addToAssertionCount(1);

                return;
            }
        }

        $this->fail('IntegrationGroupProvider could not find E2ETest, a known test case without a class but with a lot of IO');
    }
}
