<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi;

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    /** @dataProvider verifiedAdditionalModels */
    public function testVerifiedAdditionalModelKeepsExactCase(string $name): void
    {
        $this->assertSame($name, (new Model($name))->asString());
    }

    /** @return array<int, array{string}> */
    public function verifiedAdditionalModels(): array
    {
        return [
            ['ComboManualItem'],
            ['ComboManualName'],
            ['GoodGroup'],
            ['HospitalBlock'],
            ['MedicalCards'],
            ['PartyAccount'],
            ['PartyAccountDoc'],
            ['StoreDocument'],
            ['StoreDocumentOperation'],
            ['Suppliers'],
            ['Unit'],
            ['anonymousClient'],
            ['fiscalTaxSystems'],
            ['goodTag'],
        ];
    }

    public function testAsStringWithValidModel(): void
    {
        $this->assertEquals(
            'invoice',
            (
            new Model('invoice')
            )->asString()
        );
    }

    public function testAsStringWithInvalidModel(): void
    {
        $this->expectException(\Exception::class);
        (new Model('invalidModel'))->asString();
    }
}
