<?php

namespace Tests\integration;

use GuzzleHttp\Client;

use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerApi\url;
use function Otis22\VetmanagerRestApi\uri;
use function Otis22\VetmanagerRestApi\byApiKey;

class ModelTest extends TestCase
{
    public function testGetInvoice()
    {
        $this->assertIsModelWorking('invoice');
    }

    public function testGetClient()
    {
        $this->assertIsModelWorking('client');
    }

    public function testGetCity()
    {
        $this->assertIsModelWorking('city');
    }

    public function testGetAdmission()
    {
        $this->assertIsModelWorking('admission');
    }

    public function testGetBreed()
    {
        $this->assertIsModelWorking('breed');
    }

    public function testGetCassa()
    {
        $this->assertIsModelWorking('cassa');
    }

    public function testGetCassaclose()
    {
        $this->assertIsModelWorking('cassaclose');
    }

    public function testGetCassarashod()
    {
        $this->assertIsModelWorking('cassarashod');
    }

    public function testGetCityType()
    {
        $this->assertIsModelWorking('cityType');
    }

    public function testGetClientPhone()
    {
        $this->assertIsModelWorking('clientPhone');
    }

    public function testGetClinics()
    {
        $this->assertIsModelWorking('clinics');
    }

    public function testGetClinicsToClients()
    {
        $this->assertIsModelWorking('clinicsToClients');
    }

    public function testGetClinicsToDocuments()
    {
        $this->assertIsModelWorking('clinicsToDocuments');
    }

    public function testGetClosingOfInvoices()
    {
        $this->assertIsModelWorking('closingOfInvoices');
    }

    public function testGetComboManualItem()
    {
        $this->assertIsModelWorking('comboManualItem');
    }

    public function testGetComboManualName()
    {
        $this->assertIsModelWorking('comboManualName');
    }

    public function testGetDiagnoses()
    {
        $this->assertIsModelWorking('diagnoses');
    }

    public function testGetDoctorsResponsible()
    {
        $this->assertIsModelWorking('doctorsResponsible');
    }

    public function testGetFailedHook()
    {
        $this->assertIsModelWorking('failedHook');
    }

    public function testGetFiscalRegister()
    {
        $this->assertIsModelWorking('fiscalRegister');
    }

    public function testGetFiscalRegisterData()
    {
        $this->assertIsModelWorking('fiscalRegisterData');
    }

    public function testGetGood()
    {
        $this->assertIsModelWorking('good');
    }

    public function testGetGoodGroup()
    {
        $this->assertIsModelWorking('goodGroup');
    }

    public function testGetGoodSaleParam()
    {
        $this->assertIsModelWorking('goodSaleParam');
    }

    public function testGetHospitalBlock()
    {
        $this->assertIsModelWorking('hospitalBlock');
    }

    public function testGetHospital()
    {
        $this->assertIsModelWorking('hospital');
    }

    public function testGetInvoiceDocument()
    {
        $this->assertIsModelWorking('invoiceDocument');
    }

    public function testGetLastTime()
    {
        $this->assertIsModelWorking('lastTime');
    }

    public function testGetMedicalCards()
    {
        $this->assertIsModelWorking('medicalCards');
    }

    public function testGetPartyAccount()
    {
        $this->assertIsModelWorking('partyAccount');
    }

    public function testGetPartyAccountDoc()
    {
        $this->assertIsModelWorking('partyAccountDoc');
    }

    public function testGetPayment()
    {
        $this->assertIsModelWorking('payment');
    }

    public function testGetPet()
    {
        $this->assertIsModelWorking('pet');
    }

    public function testGetPetType()
    {
        $this->assertIsModelWorking('petType');
    }

    public function testGetProperties()
    {
        $this->assertIsModelWorking('properties');
    }

    public function testGetReport()
    {
        $this->assertIsModelWorking('report');
    }

    public function testGetRole()
    {
        $this->assertIsModelWorking('role');
    }

    public function testGetServicePrice()
    {
        $this->assertIsModelWorking('servicePrice');
    }

    public function testGetStoreDocument()
    {
        $this->assertIsModelWorking('storeDocument');
    }

    public function testGetStoreDocumentOperation()
    {
        $this->assertIsModelWorking('storeDocumentOperation');
    }

    public function testGetStores()
    {
        $this->assertIsModelWorking('stores');
    }

    public function testGetStreet()
    {
        $this->assertIsModelWorking('street');
    }

    public function testGetSuppliers()
    {
        $this->assertIsModelWorking('suppliers');
    }

    public function testGetTimesheet()
    {
        $this->assertIsModelWorking('timesheet');
    }

    public function testGetTimesheetTypes()
    {
        $this->assertIsModelWorking('timesheetTypes');
    }

    public function testGetUnit()
    {
        $this->assertIsModelWorking('unit');
    }

    public function testGetUser()
    {
        $this->assertIsModelWorking('user');
    }

    public function testGetUserCalls()
    {
        $this->assertIsModelWorking('userCalls');
    }

    public function testGetUserConfig()
    {
        $this->assertIsModelWorking('userConfig');
    }

    public function testGetUserPosition()
    {
        $this->assertIsModelWorking('userPosition');
    }

    private function assertIsModelWorking($modelName)
    {
        $client = new Client(['base_uri' => url(getenv('TEST_DOMAIN_NAME'))->asString()]);
        $request = $client->request(
            'GET',
            uri($modelName)->asString(),
            ['headers' => byApiKey(getenv("TEST_API_KEY"))->asKeyValue()]
        );
        $json = json_decode(
            strval($request->getBody())
        );
        $this->assertTrue($json->success);
    }
}
