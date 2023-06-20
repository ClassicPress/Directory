<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Remote;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\RemoteException;

interface DownloaderInterface
{
    /**
     * @throws RemoteException
     */
    public function downloadFile(string $uri): FileContents;
}
