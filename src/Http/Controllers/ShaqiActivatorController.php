<?php

namespace Shaqi\BotbleActivator\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Botble\Base\Supports\Core;
use Botble\Setting\Facades\Setting;
use Illuminate\Filesystem\Filesystem;
use Botble\Base\Events\LicenseActivated;
use Botble\Base\Events\LicenseActivating;
use Illuminate\Contracts\Session\Session;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;
use Botble\Setting\Http\Requests\LicenseSettingRequest;


class ShaqiActivatorController extends SettingController
{

    private string $cacheLicenseKeyName = '45d0da541764682476f822028d945a46270ba404';

 
    public function __construct(
        private readonly Filesystem $files,
        private readonly Session $session
    ) {
        
    }

    public function getVerifyLicense(Request $request, Core $core)
    {
        $data = [
            'activated_at' => Carbon::now()->format('M d Y'),
            'licensed_to' => 'Ishtiaq Ahmed',
        ];
        return $this
            ->httpResponse()
            ->setMessage('Your license is activated.')->setData($data);
    }

    public function activateLicense(LicenseSettingRequest $request, Core $core)
    {
        $client = $request->input('buyer');
        $license = $request->input('purchase_code');

        LicenseActivating::dispatch($license, $client);

        $data['status'] = true;
        $data['lic_response'] = 'Congratulations your license is activated! since ' . date('d/m/Y');

        $this->session->forget("license:{$this->getLicenseCacheKey()}:last_checked_date");

        LicenseActivated::dispatch($license, $client);

        Setting::forceSet('licensed_to', $client)->save();

        $data =  [
            'activated_at' =>  Carbon::now()->format('M d Y'),
            'licensed_to' => $client,
        ];

        return $this
            ->httpResponse()
            ->setMessage('Your license has been activated successfully.')
            ->setData($data);
    }

    public function deactivateLicense(BaseHttpResponse $response, Core $core)
    {
        return $response->setError()->setMessage('This is a Nulled version! no license to deactivate.');
    }

    public function getLicenseCacheKey(): string
    {
        return $this->cacheLicenseKeyName;
    }

}
