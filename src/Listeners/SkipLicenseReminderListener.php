<?php

namespace Shaqi\BotbleActivator\Listeners;

use Exception;
use Carbon\Carbon;
use Shaqi\BotbleActivator\Plugin;
use Botble\Base\Facades\BaseHelper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Session\Session;

class SkipLicenseReminderListener 
{
    private string $skipLicenseReminderFilePath;

    public function __construct(
        private readonly Filesystem $files,
        private readonly Session $session
    ) {
        $this->skipLicenseReminderFilePath = storage_path('framework/license-reminder-latest-time.txt');
    }

    public function handle(): void
    {
        try {
            Plugin::activated();
            $this->clearLicenseReminder();
        } catch (Exception $exception) {
            BaseHelper::logError($exception);
        }
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