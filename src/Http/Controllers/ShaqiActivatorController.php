<?php

namespace Shaqi\BotbleActivator\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Botble\Base\Supports\Core;
use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Botble\Base\Events\LicenseActivated;
use Botble\Base\Events\LicenseActivating;
use Illuminate\Contracts\Session\Session;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;
use Botble\Setting\Http\Requests\LicenseSettingRequest;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ShaqiActivatorController extends SettingController
{

    private string $licenseFilePath;

    private string $cacheLicenseKeyName = '45d0da541764682476f822028d945a46270ba404';

 

    public function __construct(
        private readonly Filesystem $files,
        private readonly Session $session
    ) {
        $this->licenseFilePath = storage_path('.license');
        $this->skipLicenseReminderFilePath = storage_path('framework/license-reminder-latest-time.txt');
    }

    public function getVerifyLicense(Request $request, Core $core)
    {
        $activatedAt = Carbon::createFromTimestamp(filectime($core->getLicenseFilePath()));
        $data = [
            'activated_at' => $activatedAt->format('M d Y'),
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

        $this->files->put($this->licenseFilePath, Arr::get($data, 'lic_response'), true);

        $this->session->forget("license:{$this->getLicenseCacheKey()}:last_checked_date");

        $this->skipLicenseReminder();
        // $this->clearLicenseReminder();

        LicenseActivated::dispatch($license, $client);

        Setting::forceSet('licensed_to', $client)->save();

        $activatedAt = Carbon::createFromTimestamp(filectime($core->getLicenseFilePath()));

        $data =  [
            'activated_at' => $activatedAt->format('M d Y'),
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

    public function skipLicenseReminder(): bool
    {
        $ttl = Carbon::now()->addDays(3);
        $this->files->put(
            $this->skipLicenseReminderFilePath,
            encrypt($ttl->toIso8601String())
        );
        return true;
    }

    public function clearLicenseReminder(): void
    {
        if (!$this->files->exists($this->skipLicenseReminderFilePath)) {
            return;
        }
        $this->files->delete($this->skipLicenseReminderFilePath);
    }
}
