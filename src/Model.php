<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi;

use ElegantBro\Interfaces\Stringify;

final class Model implements Stringify
{
    /**
     * @var string
     */
    private string $model;
    /**
     * @var string[]
     */
    private array $validModels = [
        'admission' => '',
        'breed' => '',
        'cassa' => '',
        'cassaclose' => '',
        'cassarashod' => '',
        'city' => '',
        'cityType' => '',
        'client' => '',
        'clientPhone' => '',
        'clinics' => '',
        'clinicsToClients' => '',
        'clinicsToDocuments' => '',
        'clinicsToUsers' => '',
        'closingOfInvoices' => '',
        'comboManualItem' => '',
        'comboManualName' => '',
        'departmentToDocument' => '',
        'departments' => '',
        'diagnoses' => '',
        'doctorsResponsible' => '',
        'failedHook' => '',
        'fiscalRegister' => '',
        'fiscalRegisterData' => '',
        'good' => '',
        'goodGroup' => '',
        'goodSaleParam' => '',
        'hospital' => '',
        'hospitalBlock' => '',
        'invoice' => '',
        'invoiceDocument' => '',
        'lastTime' => '',
        'medicalCards' => '',
        'partyAccount' => '',
        'partyAccountDoc' => '',
        'payment' => '',
        'pet' => '',
        'petType' => '',
        'properties' => '',
        'report' => '',
        'role' => '',
        'servicePrice' => '',
        'storeDocument' => '',
        'storeDocumentOperation' => '',
        'stores' => '',
        'street' => '',
        'suppliers' => '',
        'timesheet' => '',
        'timesheetTypes' => '',
        'unit' => '',
        'user' => '',
        'userCalls' => '',
        'userConfig' => '',
        'userPosition' => ''
    ];

    /**
     * Model constructor.
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        return isset($this->validModels[$this->model]) ? $this->model : throw new \InvalidArgumentException("Model is not valid");
    }
}
