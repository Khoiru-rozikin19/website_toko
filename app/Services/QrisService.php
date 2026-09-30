<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrisService
{
    /**
     * Default static QRIS payload (DANA Bisnis RZ Store).
     */
    public const DEFAULT_STATIC_QRIS = '00020101021126570011ID.DANA.WWW011893600915302634402802090263440280303UMI51440014ID.CO.QRIS.WWW0215ID10265391682640303UMI5204481453033605802ID5908rz store6015Kab. Ogan Komer610532159630485BE';

    /**
     * Convert static QRIS string into dynamic QRIS string with specific amount.
     */
    public static function makeDynamic(int|float $amount, ?string $staticQris = null): string
    {
        $payload = $staticQris ?? self::DEFAULT_STATIC_QRIS;

        // 1. Remove existing CRC16 tag at the end (tag 63 + 04 + 4 hex chars)
        $payloadWithoutCrc = preg_replace('/6304[0-9A-Fa-f]{4}$/', '', $payload);

        // 2. Change Point of Initiation Method from Static (010211) to Dynamic (010212)
        $payloadWithoutCrc = str_replace('010211', '010212', $payloadWithoutCrc);

        // 3. Format Tag 54 (Transaction Amount)
        // Tag 54 + Length (2 digits) + Amount value
        $amountStr = (string) (int) $amount;
        $tag54Length = sprintf('%02d', strlen($amountStr));
        $tag54 = '54'.$tag54Length.$amountStr;

        // 4. Inject Tag 54 before Tag 58 (Country Code '5802ID')
        // Or if Tag 58 is present, split and inject
        if (str_contains($payloadWithoutCrc, '5802ID')) {
            $parts = explode('5802ID', $payloadWithoutCrc, 2);
            $newPayload = $parts[0].$tag54.'5802ID'.$parts[1];
        } else {
            $newPayload = $payloadWithoutCrc.$tag54;
        }

        // 5. Append Tag 6304 for Checksum calculation
        $dataToSign = $newPayload.'6304';

        // 6. Calculate CRC16-CCITT checksum
        $crc = self::calculateCrc16($dataToSign);

        return $dataToSign.$crc;
    }

    /**
     * Calculate CRC16-CCITT (0xFFFF, 0x1021) for EMVCo QRIS specification.
     */
    public static function calculateCrc16(string $data): string
    {
        $crc = 0xFFFF;
        $len = strlen($data);

        for ($i = 0; $i < $len; $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(sprintf('%04X', $crc));
    }

    /**
     * Generate SVG QR Code string for dynamic QRIS.
     */
    public static function generateQrSvg(string $qrisString, int $size = 280): string
    {
        return QrCode::size($size)
            ->format('svg')
            ->margin(1)
            ->errorCorrection('M')
            ->generate($qrisString);
    }
}
