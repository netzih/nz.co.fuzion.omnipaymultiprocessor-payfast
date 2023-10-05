<?php

namespace Omnipay\PayFast\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * PayFast Complete Purchase Request
 *
 * We use the same return URL & class to handle both PDT (Payment Data Transfer)
 * and ITN (Instant Transaction Notification).
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function getData()
    {
        if ($this->httpRequest->query->get('pt')) {
            // this is a Payment Data Transfer request
            $data = array();
            $data['pt'] = $this->httpRequest->query->get('pt');
            $data['at'] = $this->getPdtKey();
            return $data;
        } elseif ($signature = $this->httpRequest->request->get('signature')) {
            // this is an Instant Transaction Notification request
            $data = $this->httpRequest->request->all();
            // signature is completely useless since it has no shared secret
            // aiden - not true... passphrase is used... implementing signature check
            // signature must not be posted back to the validate URL, so we unset it
            unset($data['signature']);
            $passphrase = $this->parameters->get('passphrase');
            $testsignature = md5(http_build_query($data) . '&passphrase='.urlencode( $passphrase ));
            if ($signature === $testsignature) {
              return $data;
            }
        }
        throw new InvalidRequestException('Missing ITN variables or signature mismatch');
    }

    public function sendData($data)
    {

        // aiden - not needed
        // if (isset($data['pt'])) {
        //     // validate PDT
        //     $url = $this->getEndpoint().'/query/fetch';
        //     $httpResponse = $this->httpClient->request('post', $url, [], http_build_query($data));
        //     return $this->response = new CompletePurchasePdtResponse($this, $httpResponse->getBody()->getContents());
        // } else {
            // validate ITN   aiden - and do security checks

            if ($data['token']) {
              $status = "VALID"; // skip checks if adhoc payment
            } else {
              // aiden - verify host
              $validHosts = array(
                  'www.payfast.co.za',
                  'sandbox.payfast.co.za',
                  'w1w.payfast.co.za',
                  'w2w.payfast.co.za',
              );
              $validIps = array();
              foreach( $validHosts as $pfHostname )
              {
                  $ips = gethostbynamel( $pfHostname );
                  if( $ips !== false )
                  {
                      $validIps = array_merge( $validIps, $ips );
                  }
              }
              // Remove duplicates
              $validIps = array_unique( $validIps );
              if( !in_array( $_SERVER['REMOTE_ADDR'], $validIps ) )
              {
                  $status = "INVALID";
              } else {

                $contribution = civicrm_api3('contribution', 'getsingle', array('id' => $data['m_payment_id']));

                if( abs( floatval( $contribution['total_amount'] ) - floatval( $data['amount_gross'] ) ) > 0.01 )
                {
                    $status = "INVALID";
                } else {

                  $url = $this->getEndpoint().'query/validate';

                  // aiden using code from payfast

                  $ch = curl_init();
                  // Set cURL options - Use curl_setopt for greater PHP compatibility
                  // Base settings
                  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                  curl_setopt( $ch, CURLOPT_HEADER, false );
                  curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
                  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 1 );
                  // Standard settings
                  curl_setopt( $ch, CURLOPT_URL, $url );
                  curl_setopt( $ch, CURLOPT_POST, true );
                  curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($data) );
                  // Execute CURL
                  var_dump($ch);
                  $response = curl_exec( $ch );
                  curl_close( $ch );
                  $lines = explode( "\r\n", $response );
                  $status = trim( $lines[0] );
                }
              }
            }
            // $httpResponse = $this->httpClient->request('post', $url, [], http_build_query($data));
            // $status = $httpResponse->getBody()->getContents();

            return $this->response = new CompletePurchaseItnResponse($this, $data, $status);
        // }
    }
}
