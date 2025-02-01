<?php

use fperezco\CaptainhookConventionalBranchCommits\BranchNameValidator;
use PHPUnit\Framework\TestCase;

class BranchNameValidatorTest extends TestCase
{
    /**
     * @dataProvider validBranchNameProvider
     */
    public function testValidBranchName($validBranchName)
    {
        $validator = new BranchNameValidator();
        $this->assertTrue($validator->__invoke($validBranchName));
    }

    /**
     * @dataProvider invalidBranchNameProvider
     */
    public function testInvalidBranchName($invalidBranchName)
    {
        $validator = new BranchNameValidator();
        $this->assertFalse($validator->__invoke($invalidBranchName));
    }

    public function validBranchNameProvider(): array
    {
        return [
            ['develop'],
            ['master'],
            ['main'],
            ['feature/RTG-2345-new-user'],
            ['feature/RT-23'],
            ['feature/A-2'],
            ['bugfix/IDB-89-fix-bug'],
            ['hotfix/IDB-89-fix-bug'],
            ['chore/IDB-89-fix-bug'],
            ['release/RD-56'],
        ];
    }

    public function invalidBranchNameProvider(): array
    {
        return [
            ['invalid-branch-name'],
            ['feature/RTG_2345_new_user'],
            ['RTG_2345_new_user'],
            ['ID-2345-new_user'],
            ['feature/invalid'],
            ['bugfix/123'],
            ['test/'],
            [''],
        ];
    }
}