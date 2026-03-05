<?php
require __DIR__ . '/../app/db.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/repositories/SettingsRepository.php';
require __DIR__ . '/../app/repositories/SocialServiceRepository.php';
require __DIR__ . '/../app/services/PeakerrClient.php';
require __DIR__ . '/../app/services/SmsManClient.php';

$settings = new SettingsRepository();
$config = app_config();

$peakerrClient = new PeakerrClient($config['peakerr']);
$peakerrServices = $peakerrClient->services();
if (is_array($peakerrServices) && !isset($peakerrServices['error']) && !isset($peakerrServices['success'])) {
    $socialRepo = new SocialServiceRepository();
    $count = $socialRepo->upsertFromPeakerr($peakerrServices);
    $settings->set('peakerr_services_last_sync', date('c'));
    echo "Peakerr sync: {$count} services updated\n";
} else {
    echo "Peakerr sync failed\n";
}

$smsClient = new SmsManClient($config['smsman']);
$smsClient->getCountries();
$smsClient->getPrices(0, true);

echo "SMS-Man cache warmed\n";
