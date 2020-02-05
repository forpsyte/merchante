<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Model;

/**
 * Class TaxCodes
 * @package Magento\Merchantesolutions\Model
 */
class TaxCodes
{
    const ValueAddedTaxOfZeroRate = "VATB";
    const Provincial = "PROV";
    const NationalTax = "NATI";
    const StateTax = "STAT";
    const WithholdingTax = "WITH";
    const CapitalGainTax = "KAPA";
    const InterimProfitTax = "INPO";
    const StampDuty = "STAM";
    const WealthTax = "WTAX";
    const InheritanceTax = "INHT";
    const SolidaritySurcharge = "SOSU";
    const TaxCredit = "CTAX";
    const Equalisation = "EQUL";
    const GiftTax = "GIFT";
    const ConsumptionTax = "COAX";
    const AlternativeMinimumTax = "ALMI";
    const LocalTax = "LOCL";
    const NationalFederalTax = "COUN";
    const PaymentLevyTax = "LEVY";
    const StockExchangeTax = "STEX";
    const TransactionTax = "TRAX";
    const TransferTax = "TRAN";
    const ValueAddedTax = "VATA";
    const LocalBrokerCommission = "LOCO";
    const ExecutingBrokerCommission = "EXEC";
    const EUTaxRetention = "EUTR";
    const Aktiengewinn1 = "AKT1";
    const Aktiengewinn2 = "AKT2";
    const Zwischengewinn = "ZWIS";
    const CustomsTax = "CUST";
    const Other = "OTHR";
    const Mietgewinn = "MIET";
    const GermanLocalTax3 = "LOTE";
    const GermanLocalTax4 = "LYDT";
    const GermanLocalTax2 = "LIDT";
    const WithholdingOfForeignTax = "WITF";
    const WithholdingOfLocalTax = "WITL";
    const CapitalLossCredit = "NKAP";

    /**
     * @var array
     */
    protected $taxCodes = [
        self::ValueAddedTaxOfZeroRate, self::Provincial, self::NationalTax, self::StateTax, self::WithholdingTax,
        self::CapitalGainTax, self::InterimProfitTax, self::StampDuty, self::WealthTax, self::InheritanceTax,
        self::SolidaritySurcharge, self::TaxCredit, self::Equalisation, self::GiftTax, self::ConsumptionTax,
        self::AlternativeMinimumTax, self::LocalTax, self::NationalFederalTax, self::PaymentLevyTax,
        self::StockExchangeTax, self::TransactionTax, self::TransferTax, self::ValueAddedTax,
        self::LocalBrokerCommission, self::ExecutingBrokerCommission, self::EUTaxRetention, self::Aktiengewinn1,
        self::Aktiengewinn2, self::Zwischengewinn, self::CustomsTax, self::Other, self::Mietgewinn,
        self::GermanLocalTax3, self::GermanLocalTax4, self::GermanLocalTax2, self::WithholdingOfForeignTax,
        self::WithholdingOfLocalTax, self::CapitalLossCredit
    ];

    /**
     * @return array
     */
    public function getTaxCodes()
    {
        return $this->taxCodes;
    }

    /**
     * @param $code
     * @return bool
     */
    public function isValidTaxCode($code)
    {
        return in_array($code, $this->getTaxCodes());
    }
}
