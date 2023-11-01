<?php

namespace App\Http\Controllers\API;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;

class ReCaptcha
{
    public const CLIENT_API = 'https://www.google.com/recaptcha/api.js';

    public const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * The recaptcha secret key.
     *
     * @var string
     */
    protected string $secret;

    /**
     * The recaptcha sitekey key.
     *
     * @var string
     */
    protected string $sitekey;

    /**
     * @var Client
     */
    protected Client $http;

    /**
     * NoCaptcha.
     *
     * @param string $secret
     * @param string $sitekey
     */
    public function __construct(string $secret, string $sitekey)
    {
        $this->secret = $secret;
        $this->sitekey = $sitekey;
        $this->http = new Client(['timeout' => 2.0]);
    }

    /**
     * Render HTML captcha.
     *
     * @param array $attributes
     * @param string $lang
     *
     * @return string
     */
    public function display(array $attributes = [], $text = 'Submit')
    {
        $lang = app()->getLocale();
        $html = '<script src="'.$this->getJsLink($lang).'" async defer></script>'."\n";
        $html .= '<button type="submit" id="register-submit" class="btn btn-orange btn-block btn-animate btn-animate-vertical g-recaptcha" data-badge="inline" data-sitekey="'.$this->sitekey.'" data-callback="registerFormSubmit" onclick="reCaptcha()">'."\n";
        $html .= '<span><i class="icon fa fa-user-plus" aria-hidden="true"></i> '.$text.' </span>'."\n";
        $html .= '</button>';

        return $html;
    }

    /**
     * Verify no-captcha response.
     *
     * @param string $response
     * @param string|null $clientIp
     *
     * @return bool
     */
    public function verifyResponse(string $response, string $clientIp = null): bool
    {
        if (empty($response)) {
            return false;
        }

        $response = $this->sendRequestVerify([
            'secret'   => $this->secret,
            'response' => $response,
            'remoteip' => $clientIp,
        ]);

        return isset($response['success']) && $response['success'] === true;
    }

    /**
     * Verify no-captcha response by Symfony Request.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function verifyRequest(Request $request): bool
    {
        return $this->verifyResponse(
            $request->get('g-recaptcha-response'),
            $request->getClientIp()
        );
    }

    /**
     * Get recaptcha js link.
     *
     * @param string|null $lang
     *
     * @return string
     */
    public function getJsLink(string $lang = null): string
    {
        return $lang ? static::CLIENT_API.'?hl='.$lang : static::CLIENT_API;
    }

    /**
     * Send verify request.
     *
     * @param array $query
     *
     * @return array
     */
    protected function sendRequestVerify(array $query = []): array
    {
        $response = $this->http->request('POST', static::VERIFY_URL, [
            'form_params' => $query,
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Build HTML attributes.
     *
     * @param array $attributes
     *
     * @return string
     */
    protected function buildAttributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            $html[] = $key.'="'.$value.'"';
        }

        return count($html) ? ' '.implode(' ', $html) : '';
    }
}
