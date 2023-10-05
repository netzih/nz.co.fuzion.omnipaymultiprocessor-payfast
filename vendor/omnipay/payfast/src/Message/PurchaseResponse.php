<?php

namespace Omnipay\PayFast\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * PayFast Purchase Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $redirectUrl;
    protected $header;
    protected $body;

    public function __construct(RequestInterface $request, $data, $redirectUrl)
    {
        parent::__construct($request, $data);
        $this->redirectUrl = $redirectUrl;
        if (!empty($data['body'])) {
          $this->header = $data['header'];
          $this->body = $data['body'];
        }
    }

    public function isSuccessful()
    {
        // aiden - bit weird to do this here but it determines this function's required return value
        if ($this->header) {
          $ch = curl_init( $this->redirectUrl );
          curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
          curl_setopt( $ch, CURLOPT_HEADER, false );
          curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 1 );
          curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
          curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
          // For the body values such as amount, item_name, & item_description
          curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($this->body));
          curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
          curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->header);

          // Execute and close cURL
          $response = curl_exec( $ch );
          curl_close( $ch );
          $response = json_decode($response);
          if ($response->code == 200) {
            return true;
          } else {
            return false;
          }
        }
        return false;
    }

    public function isRedirect()
    {
        //aiden - don't do redirect when using API
        if ($this->header) {
          return false;
        }
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->getData();
    }
}
