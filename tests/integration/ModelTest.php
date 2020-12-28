<?php

namespace Tests\integration;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerUrl\url;
use function Otis22\VetmanagerRestApi\uri;
use function Otis22\VetmanagerRestApi\byApiKey;
use function getenv;

class ModelTest extends TestCase
{
    public function testGetInvoice(): void
    {
        $this->assertIsModelWorking('invoice');
    }

    public function testGetClient(): void
    {
        $this->assertIsModelWorking('client');
    }

    public function testGetCity(): void
    {
        $this->assertIsModelWorking('city');
    }

    public function testGetAdmission(): void
    {
        $this->assertIsModelWorking('admission');
    }

    public function testGetBreed(): void
    {
        $this->assertIsModelWorking('breed');
    }

    public function testGetCassa(): void
    {
        $this->assertIsModelWorking('cassa');
    }

    public function testGetCassaclose(): void
    {
        $this->assertIsModelWorking('cassaclose');
    }

    public function testGetCassarashod(): void
    {
        $this->assertIsModelWorking('cassarashod');
    }

    public function testGetCityType(): void
    {
        $this->assertIsModelWorking('cityType');
    }

    public function testGetClientPhone(): void
    {
        $this->assertIsModelWorking('clientPhone');
    }

    public function testGetClinics(): void
    {
        $this->assertIsModelWorking('clinics');
    }

    public function testGetClinicsToClients(): void
    {
        $this->assertIsModelWorking('clinicsToClients');
    }

    public function testGetClinicsToDocuments(): void
    {
        $this->assertIsModelWorking('clinicsToDocuments');
    }

    public function testGetClosingOfInvoices(): void
    {
        $this->assertIsModelWorking('closingOfInvoices');
    }

    public function testGetComboManualItem(): void
    {
        $this->assertIsModelWorking('comboManualItem');
    }

    public function testGetComboManualName(): void
    {
        $this->assertIsModelWorking('comboManualName');
    }

    public function testGetDiagnoses(): void
    {
        $this->assertIsModelWorking('diagnoses');
    }

    public function testGetDoctorsResponsible(): void
    {
        $this->assertIsModelWorking('doctorsResponsible');
    }

    public function testGetFailedHook(): void
    {
        $this->assertIsModelWorking('failedHook');
    }

    public function testGetFiscalRegister(): void
    {
        $this->assertIsModelWorking('fiscalRegister');
    }

    public function testGetFiscalRegisterData(): void
    {
        $this->assertIsModelWorking('fiscalRegisterData');
    }

    public function testGetGood(): void
    {
        $this->assertIsModelWorking('good');
    }

    public function testGetGoodGroup(): void
    {
        $this->assertIsModelWorking('goodGroup');
    }

    public function testGetGoodSaleParam(): void
    {
        $this->assertIsModelWorking('goodSaleParam');
    }

    public function testGetHospitalBlock(): void
    {
        $this->assertIsModelWorking('hospitalBlock');
    }

    public function testGetHospital(): void
    {
        $this->assertIsModelWorking('hospital');
    }

    public function testGetInvoiceDocument(): void
    {
        $this->assertIsModelWorking('invoiceDocument');
    }

    public function testGetLastTime(): void
    {
        $this->assertIsModelWorking('lastTime');
    }

    public function testGetMedicalCards(): void
    {
        $this->assertIsModelWorking('medicalCards');
    }

    public function testGetPartyAccount(): void
    {
        $this->assertIsModelWorking('partyAccount');
    }

    public function testGetPartyAccountDoc(): void
    {
        $this->assertIsModelWorking('partyAccountDoc');
    }

    public function testGetPayment(): void
    {
        $this->assertIsModelWorking('payment');
    }

    public function testGetPet(): void
    {
        $this->assertIsModelWorking('pet');
    }

    public function testGetPetType(): void
    {
        $this->assertIsModelWorking('petType');
    }

    public function testGetProperties(): void
    {
        $this->assertIsModelWorking('properties');
    }

    public function testGetReport(): void
    {
        $this->assertIsModelWorking('report');
    }

    public function testGetRole(): void
    {
        $this->assertIsModelWorking('role');
    }

    public function testGetServicePrice(): void
    {
        $this->assertIsModelWorking('servicePrice');
    }

    public function testGetStoreDocument(): void
    {
        $this->assertIsModelWorking('storeDocument');
    }

    public function testGetStoreDocumentOperation(): void
    {
        $this->assertIsModelWorking('storeDocumentOperation');
    }

    public function testGetStores(): void
    {
        $this->assertIsModelWorking('stores');
    }

    public function testGetStreet(): void
    {
        $this->assertIsModelWorking('street');
    }

    public function testGetSuppliers(): void
    {
        $this->assertIsModelWorking('suppliers');
    }

    public function testGetTimesheet(): void
    {
        $this->assertIsModelWorking('timesheet');
    }

    public function testGetTimesheetTypes(): void
    {
        $this->assertIsModelWorking('timesheetTypes');
    }

    public function testGetUnit(): void
    {
        $this->assertIsModelWorking('unit');
    }

    public function testGetUser(): void
    {
        $this->assertIsModelWorking('user');
    }

    public function testGetUserCalls(): void
    {
        $this->assertIsModelWorking('userCalls');
    }

    public function testGetUserConfig(): void
    {
        $this->assertIsModelWorking('userConfig');
    }

    public function testGetUserPosition(): void
    {
        $this->assertIsModelWorking('userPosition');
    }

    private function assertIsModelWorking(string $modelName): void
    {
        $client = new Client(
            [
                'base_uri' => url(
                    strval(
                        getenv('TEST_DOMAIN_NAME')
                    )
                )->asString()
            ]
        );
        $request = $client->request(
            'GET',
            uri($modelName)->asString(),
            [
                'headers' => byApiKey(
                    strval(
                        getenv("TEST_API_KEY")
                    )
                )->asKeyValue()
            ]
        );
        $json = json_decode(
            strval(
                $request->getBody()
            )
        );
        $this->assertTrue($json->success);
    }
}
