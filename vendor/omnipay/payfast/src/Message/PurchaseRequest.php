<?php

namespace Omnipay\PayFast\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * PayFast Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://www.payfast.co.za/eng/';
    protected $testEndpoint = 'https://sandbox.payfast.co.za/eng/';
    protected $APIEndpoint  = 'https://api.payfast.co.za/subscriptions/';

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getMerchantKey()
    {
        return $this->getParameter('merchantKey');
    }

    public function setMerchantKey($value)
    {
        return $this->setParameter('merchantKey', $value);
    }

    public function getPassphrase()
    {
        return $this->getParameter('passphrase');
    }

    public function setPassphrase($value)
    {
        return $this->setParameter('passphrase', $value);
    }

    public function getPdtKey()
    {
        return $this->getParameter('pdtKey');
    }

    public function setPdtKey($value)
    {
        return $this->setParameter('pdtKey', $value);
    }

    public function getCustomStr1()
    {
        return $this->getParameter('customStr1');
    }

    public function setCustomStr1($value)
    {
        return $this->setParameter('customStr1', $value);
    }

    public function getCustomStr2()
    {
        return $this->getParameter('customStr2');
    }

    public function setCustomStr2($value)
    {
        return $this->setParameter('customStr2', $value);
    }

    public function getCustomStr3()
    {
        return $this->getParameter('customStr3');
    }

    public function setCustomStr3($value)
    {
        return $this->setParameter('customStr3', $value);
    }
    public function getCustomStr4()
    {
        return $this->getParameter('customStr4');
    }

    public function setCustomStr4($value)
    {
        return $this->setParameter('customStr4', $value);
    }

    public function getCustomStr5()
    {
        return $this->getParameter('customStr5');
    }

    public function setCustomStr5($value)
    {
        return $this->setParameter('customStr5', $value);
    }

    public function getCustomInt1()
    {
        return $this->getParameter('customInt1');
    }

    public function setCustomInt1($value)
    {
        return $this->setParameter('customInt1', $value);
    }

    public function getCustomInt2()
    {
        return $this->getParameter('customInt2');
    }

    public function setCustomInt2($value)
    {
        return $this->setParameter('customInt2', $value);
    }

    public function getCustomInt3()
    {
        return $this->getParameter('customInt3');
    }

    public function setCustomInt3($value)
    {
        return $this->setParameter('customInt3', $value);
    }

    public function getCustomInt4()
    {
        return $this->getParameter('customInt4');
    }

    public function setCustomInt4($value)
    {
        return $this->setParameter('customInt4', $value);
    }

    public function getCustomInt5()
    {
        return $this->getParameter('customInt5');
    }

    public function setCustomInt5($value)
    {
        return $this->setParameter('customInt5', $value);
    }

    public function getPaymentMethod()
    {
        return $this->getParameter('paymentMethod');
    }

    public function setPaymentMethod($value)
    {
        return $this->setParameter('paymentMethod', $value);
    }

    public function getSubscriptionType()
    {
        return $this->getParameter('subscriptionType');
    }

    public function getFrequencyUnit()
    {
        return $this->getParameter('frequencyUnit');
    }

    public function getInstallments()
    {
        return $this->getParameter('installments');
    }

    public function getTransactionId()
    {
        return $this->getParameter('transactionId');
    }

    public function getIsRecur()
    {
        return $this->getParameter('isRecur');
    }

    public function getBillingDate()
    {
        return $this->getParameter('billingDate');
    }

    public function getRecurringAmount()
    {
        return $this->getParameter('recurringAmount');
    }

    public function getFrequency()
    {
        return $this->getParameter('frequency');
    }

    public function getCycles()
    {
        return $this->getParameter('cycles');
    }

    public function getCardTransactionType()
    {
        return $this->getParameter('cardTransactionType');
    }

    public function setSubscriptionType($value)
    {
        return $this->setParameter('subscriptionType', $value);
    }

    public function setFrequencyUnit($value)
    {
        return $this->setParameter('frequencyUnit', $value);
    }

    public function setInstallments($value)
    {
        return $this->setParameter('installments', $value);
    }

    public function setTransactionId($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    public function setIsRecur($value)
    {
        return $this->setParameter('isRecur', $value);
    }

    public function setBillingDate($value)
    {
        return $this->setParameter('billingDate', $value);
    }

    public function setRecurringAmount($value)
    {
        return $this->setParameter('recurringAmount', $value);
    }

    public function setFrequency($value)
    {
        return $this->setParameter('frequency', $value);
    }

    public function setCycles($value)
    {
        return $this->setParameter('cycles', $value);
    }

    public function setCardTransactionType($value)
    {
        return $this->setParameter('cardTransactionType', $value);
    }

    public function getData()
    {
        $this->validate('amount', 'description');
        $data = array();

        // aiden - data for api token payment

        if ($this->getCardTransactionType() == 'continuous') {
          $data['header'] = [
            'version' => 'v1',
            'merchant-id' => $this->getMerchantId(),
            'timestamp' => date("Y-m-d\TH:i:s")
          ];
          $data['body'] = [
            'amount' => (int)($this->getAmount() * 100),
            // aiden - signature error when there's a trailing slash
            'item_name' => trim($this->getDescription()),
            'passphrase' => $this->getParameter('passphrase')
          ];
          $data['header']['signature'] = $this->generateAPISignature($data);
          $headerStrings = array();
          foreach ($data['header'] as $key => $value) {
            array_push($headerStrings, $key.': '.$value);
          }
          $data['header'] = $headerStrings;
          unset($data['body']['passphrase']);
          return $data;
        }

        $data = array();
        $data['merchant_id'] = $this->getMerchantId();
        $data['merchant_key'] = $this->getMerchantKey();
        $data['return_url'] = $this->getReturnUrl();
        $data['cancel_url'] = $this->getCancelUrl();
        $data['notify_url'] = $this->getNotifyUrl();

        if ($this->getCard()) {
            $data['name_first'] = $this->getCard()->getFirstName();
            $data['name_last'] = $this->getCard()->getLastName();
            $data['email_address'] = $this->getCard()->getEmail();
        }

        $data['m_payment_id'] = $this->getTransactionId();
        $data['amount'] = $this->getAmount();
        // aiden - signature error when there's a trailing slash
        $data['item_name'] = trim($this->getDescription());
        $data['custom_int1'] = $this->getCustomInt1();
        $data['custom_int2'] = $this->getCustomInt2();
        $data['custom_int3'] = $this->getCustomInt3();
        $data['custom_int4'] = $this->getCustomInt4();
        $data['custom_int5'] = $this->getCustomInt5();
        $data['custom_str1'] = $this->getCustomStr1();
        $data['custom_str2'] = $this->getCustomStr2();
        $data['custom_str3'] = $this->getCustomStr3();
        $data['custom_str4'] = $this->getCustomStr4();
        $data['custom_str5'] = $this->getCustomStr5();

        /**
         * Allow overriding the Payment Method
         */
        if ($this->getPaymentMethod()) {
            $data['payment_method'] = $this->getPaymentMethod();
        }

        /**
         * Subscription billing options.
         */
        if (1 == $this->getSubscriptionType()) {
            $data['subscription_type'] = $this->getSubscriptionType();
            $data['billing_date'] = $this->getBillingDate();
            $data['recurring_amount'] = $this->getRecurringAmount();
            $data['frequency'] = $this->getFrequency();
            $data['cycles'] = $this->getCycles();
        }
        if (2 == $this->getSubscriptionType()) {
            $data['subscription_type'] = $this->getSubscriptionType();
        }

        // aiden
        if ($this->getIsRecur() == 1) {
          $data['subscription_type'] = '2'; // 2 = adhoc
          // $data['frequency'] = '3'; // hardcoded for 'monthly', limited PayFast options
          // $data['cycles'] = $this->getInstallments();
        }

        $data['passphrase'] = $this->getParameter('passphrase');
        $data['signature'] = $this->generateSignature($data);
        unset($data['passphrase']);

        return $data;
    }

    protected function generateSignature($data)
    {
        $fields = array();

        // specific order required by PayFast
        // @see https://developers.payfast.co.za/documentation/#checkout-page
        foreach (array('merchant_id', 'merchant_key', 'return_url', 'cancel_url', 'notify_url', 'name_first',
                     'name_last', 'email_address', 'cell_number',
                    /**
                     * Transaction Details
                     */
                    'm_payment_id', 'amount', 'item_name', 'item_description',
                    /**
                     * Custom return data
                     */
                    'custom_int1', 'custom_int2', 'custom_int3', 'custom_int4', 'custom_int5',
                    'custom_str1', 'custom_str2', 'custom_str3', 'custom_str4', 'custom_str5',
                    /**
                     * Email confirmation
                     */
                    'email_confirmation', 'confirmation_address',
                    /**
                     * Payment Method
                     */
                    'payment_method',
                    /**
                     * Subscriptions
                     */
                    'subscription_type', 'billing_date', 'recurring_amount', 'frequency', 'cycles',
                    /**
                     * Passphrase for md5 signature generation
                     */
                    'passphrase') as $key) {
            if (!empty($data[$key])) {
                $fields[$key] = $data[$key];
            }
        }

        return md5(http_build_query($fields));
    }

    protected function generateAPISignature($data)
    {
        // aiden - different signature format for API payments
        $fields = array();
        foreach (array('amount', 'item_name', 'merchant-id', 'passphrase', 'timestamp', 'version') as $key) {
            if (!empty($data['header'][$key])) {
                $fields[$key] = $data['header'][$key];
            } elseif (!empty($data['body'][$key])) {
                $fields[$key] = $data['body'][$key];
            }
        }

        return md5(http_build_query($fields));
    }

    public function sendData($data)
    {
        if ($this->getCardTransactionType() == 'continuous') {
          $endpoint = $this->getEndpoint();
        } else {
          $endpoint = $this->getEndpoint().'process';
        }
        return $this->response = new PurchaseResponse($this, $data, $endpoint);
    }

    public function getEndpoint()
    {
        // aiden - conditional endpoint for api payments
        if ($this->getCardTransactionType() == 'continuous') {
          $ep = $this->APIEndpoint.$this->getCardReference().'/adhoc';
          if ($this->getTestMode()) {
            $ep = $ep.'?testing=true';
          }
          return $ep;
        }
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
