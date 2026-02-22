<?php

declare(strict_types=1);

namespace Symkit\FaqBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symkit\FaqBundle\Entity\Faq;

final class FaqTest extends TestCase
{
    public function testCodeRegexConstraint(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
        $faq = new Faq();
        $faq->setTitle('Test');
        $faq->setCode('invalid code');
        $violations = $validator->validate($faq, null, ['create']);
        self::assertGreaterThan(0, $violations->count(), 'Code with space should violate regex');
        $codeViolation = null;
        foreach ($violations as $v) {
            if (str_contains((string) $v->getPropertyPath(), 'code')) {
                $codeViolation = $v;
                break;
            }
        }
        self::assertNotNull($codeViolation);
    }

    public function testValidCodePasses(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
        $faq = new Faq();
        $faq->setTitle('Test');
        $faq->setCode('valid-code-123');
        $violations = $validator->validate($faq, null, ['create']);
        $codeViolations = [];
        foreach ($violations as $v) {
            if (str_contains((string) $v->getPropertyPath(), 'code')) {
                $codeViolations[] = $v;
            }
        }
        self::assertCount(0, $codeViolations);
    }
}
