<?php

use CaptainHook\App\Exception\ActionFailed;
use fperezco\CaptainhookConventionalBranchCommits\validators\BranchNameValidator;
use PHPUnit\Framework\TestCase;

class BranchNameValidatorTest extends TestCase
{
    /**
     * @dataProvider validBranchNameProvider
     */
    public function testValidBranchNameExceptionIsNotThrown($validBranchName)
    {
        $validator = new BranchNameValidator($validBranchName);

        try {
            $validator->validate();
        } catch (ActionFailed $e) {
            $this->fail("Expected no exception, but got: " . $e->getMessage());
        }
        $this->addToAssertionCount(1);
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
            ['feature/new-issue-blalbal'],
            ['bugfix/IDB-89-fix-bug'],
            ['hotfix/IDB-89-fix-bug'],
            ['chore/IDB-89-fix-bug'],
            ['release/RD-56'],
            ['bugfix/123']
        ];
    }

    /**
     * @dataProvider invalidBranchNameProvider
     */
    public function testInvalidBranchNameExceptionIsThrown($invalidBranchName)
    {
        $this->expectException(ActionFailed::class);
        $validator = new BranchNameValidator($invalidBranchName);
        $validator->validate();
    }

    public function invalidBranchNameProvider(): array
    {
        return [
            ['invalid-branch-name'],
            ['feature/RTG_2345_new_user'],
            ['RTG_2345_new_user'],
            ['ID-2345-new_user'],
            ['bugfix/'],
            ['test/'],
            [''],
        ];
    }


    /**
     * @dataProvider customBranchNameProvider
     */
    public function testCustomBranchNamePattern($branchName, $shouldThrowException)
    {
        $customPattern = '/^(custom|pattern)\/[A-Za-z0-9-]+$/';
        $validator = new BranchNameValidator($branchName, $customPattern);

        if ($shouldThrowException) {
            $this->expectException(ActionFailed::class);
        }

        $validator->validate();

        if (!$shouldThrowException) {
            $this->assertTrue(true);
        }
    }

    public function customBranchNameProvider(): array
    {
        return [
            ['custom/branch-123', false], // Valid, no se espera excepción
            ['pattern/branch-456', false], // Valid, no se espera excepción
            ['invalid/branch', true],      // Invalid, debería lanzar una excepción
            ['custom/', true],             // Invalid, debería lanzar una excepción
            ['pattern/', true],            // Invalid, debería lanzar una excepción
            ['custom-branch-123', true],   // Invalid, debería lanzar una excepción
        ];
    }


    public function testInvalidBranchNameMessageWithDefaultPattern()
    {
        $invalidBranchName = 'invalid-branch-name';
        $validator = new BranchNameValidator($invalidBranchName);

        try {
            $validator->validate();
            $this->fail("Expected exception not thrown");
        } catch (ActionFailed $e) {
            $this->assertSame(BranchNameValidator::MESSAGE_INVALID_BRANCH_NAME, $e->getMessage());
        }
    }

    public function testInvalidBranchNameWithCustomPattern()
    {
        $invalidBranchName = 'invalid-branch-name';
        $customPattern = '/^(custom|pattern)\/[A-Za-z0-9-]+$/';
        $validator = new BranchNameValidator($invalidBranchName, $customPattern);

        try {
            $validator->validate();
            $this->fail("Expected exception not thrown");
        } catch (ActionFailed $e) {
            $expectedMessage = "Error: branch: 'invalid-branch-name' must follow the pattern '$customPattern'.";
            $this->assertSame($expectedMessage, $e->getMessage());
        }
    }

    public function testBranchNameWithoutTicketCodeRequiredThrowsException()
    {
        $invalidBranchName = 'feature/invalid-branch-name';
        $validator = new BranchNameValidator(
            $invalidBranchName,
            null,
            true
        );

        $this->expectException(ActionFailed::class);
        $this->expectExceptionMessage("Error: branch: '$invalidBranchName' must include a ticket code following the pattern '/[A-Z]+-[0-9]+/'.");

        $validator->validate();
    }

    public function testBranchNameWithTicketCodeNotLaunchException(){

        $validBranchName = 'feature/RTG-2345-new-user';
        $validator = new BranchNameValidator($validBranchName, null, true);

        try {
            $validator->validate();
        } catch (ActionFailed $e) {
            $this->fail("Expected no exception, but got: " . $e->getMessage());
        }
        $this->addToAssertionCount(1);
    }

    public function testBranchNameWithCustomTicketCodePattern()
    {
        $validBranchName = 'feature/CUST-1234-new-feature';
        $customTicketCodePattern = '/CUST-[0-9]{4}/';
        $validator = new BranchNameValidator(
            $validBranchName,
            null,
            true,
            $customTicketCodePattern
        );

        try {
            $validator->validate();
        } catch (ActionFailed $e) {
            $this->fail("Expected no exception, but got: " . $e->getMessage());
        }
        $this->addToAssertionCount(1);
    }
}