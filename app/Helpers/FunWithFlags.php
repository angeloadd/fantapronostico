<?php

declare(strict_types=1);

namespace App\Helpers;

use Locale;

final class FunWithFlags
{
    /**
     * Complete FIFA to ISO2 exception map.
     */
    private const array FIFA_TO_ISO_2 = [
        'AFG' => 'AF', 'ALB' => 'AL', 'ALG' => 'DZ', 'AND' => 'AD', 'ANG' => 'AO',
        'AIA' => 'AI', 'ATG' => 'AG', 'ARG' => 'AR', 'ARM' => 'AM', 'ARU' => 'AW',
        'ASA' => 'AS', 'AUS' => 'AU', 'AUT' => 'AT', 'AZE' => 'AZ', 'BAH' => 'BS',
        'BHR' => 'BH', 'BAN' => 'BD', 'BRB' => 'BB', 'BLR' => 'BY', 'BEL' => 'BE',
        'BLZ' => 'BZ', 'BER' => 'BM', 'BHU' => 'BT', 'BOL' => 'BO', 'BIH' => 'BA',
        'BOT' => 'BW', 'BRA' => 'BR', 'VGB' => 'VG', 'BRU' => 'BN', 'BUL' => 'BG',
        'BFA' => 'BF', 'BDI' => 'BI', 'CAM' => 'KH', 'CMR' => 'CM', 'CAN' => 'CA',
        'CPV' => 'CV', 'CAY' => 'KY', 'CTA' => 'CF', 'CHA' => 'TD', 'CHI' => 'CL',
        'CHN' => 'CN', 'TPE' => 'TW', 'COL' => 'CO', 'COM' => 'KM', 'CGO' => 'CG',
        'COK' => 'CK', 'CRC' => 'CR', 'CRO' => 'HR', 'CUB' => 'CU', 'CUW' => 'CW',
        'CYP' => 'CY', 'CZE' => 'CZ', 'DEN' => 'DK', 'DJI' => 'DJ', 'DMA' => 'DM',
        'DOM' => 'DO', 'COD' => 'CD', 'ECU' => 'EC', 'EGY' => 'EG', 'SLV' => 'SV',
        'EQG' => 'GQ', 'ERI' => 'ER', 'EST' => 'EE', 'SWZ' => 'SZ', 'ETH' => 'ET',
        'FRO' => 'FO', 'FIJ' => 'FJ', 'FIN' => 'FI', 'FRA' => 'FR', 'GAB' => 'GA',
        'GAM' => 'GM', 'GEO' => 'GE', 'GER' => 'DE', 'GHA' => 'GH', 'GIB' => 'GI',
        'GRE' => 'GR', 'GRL' => 'GL', 'GRN' => 'GD', 'GUM' => 'GU', 'GUA' => 'GT',
        'GUI' => 'GN', 'GNB' => 'GW', 'GUY' => 'GY', 'HAI' => 'HT', 'HON' => 'HN',
        'HKG' => 'HK', 'HUN' => 'HU', 'ISL' => 'IS', 'IND' => 'IN', 'IDN' => 'ID',
        'IRN' => 'IR', 'IRQ' => 'IQ', 'IRL' => 'IE', 'ISR' => 'IL', 'ITA' => 'IT',
        'CIV' => 'CI', 'JAM' => 'JM', 'JPN' => 'JP', 'JOR' => 'JO', 'KAZ' => 'KZ',
        'KEN' => 'KE', 'PRK' => 'KP', 'KOR' => 'KR', 'KSA' => 'SA', 'KUW' => 'KW',
        'KGZ' => 'KG', 'LAO' => 'LA', 'LVA' => 'LV', 'LBN' => 'LB', 'LES' => 'LS',
        'LBR' => 'LR', 'LBY' => 'LY', 'LIE' => 'LI', 'LTU' => 'LT', 'LUX' => 'LU',
        'MAC' => 'MO', 'MAD' => 'MG', 'MAW' => 'MW', 'MAS' => 'MY', 'MDV' => 'MV',
        'MLI' => 'ML', 'MLT' => 'MT', 'MTN' => 'MR', 'MAU' => 'MU', 'MEX' => 'MX',
        'MDA' => 'MD', 'MNG' => 'MN', 'MNE' => 'ME', 'MSR' => 'MS', 'MAR' => 'MA',
        'MOZ' => 'MZ', 'MYA' => 'MM', 'NAM' => 'NA', 'NEP' => 'NP', 'NED' => 'NL',
        'NCA' => 'NI', 'NIG' => 'NE', 'NGA' => 'NG', 'MKD' => 'MK', 'NOR' => 'NO',
        'OMA' => 'OM', 'PAK' => 'PK', 'PLE' => 'PS', 'PAN' => 'PA', 'PNG' => 'PG',
        'PAR' => 'PY', 'PER' => 'PE', 'PHI' => 'PH', 'POL' => 'PL', 'POR' => 'PT',
        'PUR' => 'PR', 'QAT' => 'QA', 'ROU' => 'RO', 'RUS' => 'RU', 'RWA' => 'RW',
        'SKN' => 'KN', 'LCA' => 'LC', 'VIN' => 'VC', 'SAM' => 'WS', 'SMR' => 'SM',
        'STP' => 'ST', 'SEN' => 'SN', 'SRB' => 'RS', 'SEY' => 'SC', 'SLE' => 'SL',
        'SGP' => 'SG', 'SVK' => 'SK', 'SVN' => 'SI', 'SOL' => 'SB', 'SOM' => 'SO',
        'RSA' => 'ZA', 'SSD' => 'SS', 'ESP' => 'ES', 'SRI' => 'LK', 'SUD' => 'SD',
        'SUR' => 'SR', 'SWE' => 'SE', 'SUI' => 'CH', 'SYR' => 'SY', 'TAI' => 'PF',
        'TJK' => 'TJ', 'TAN' => 'TZ', 'THA' => 'TH', 'TLS' => 'TL', 'TOG' => 'TG',
        'TON' => 'TO', 'TRI' => 'TT', 'TUN' => 'TN', 'TUR' => 'TR', 'TKM' => 'TM',
        'TCA' => 'TC', 'UGA' => 'UG', 'UKR' => 'UA', 'UAE' => 'AE', 'USA' => 'US',
        'URU' => 'UY', 'UZB' => 'UZ', 'VAN' => 'VU', 'VEN' => 'VE', 'VIE' => 'VN',
        'VIR' => 'VI', 'YEM' => 'YE', 'ZAM' => 'ZM', 'ZIM' => 'ZW', 'SLO' => 'SI',
    ];

    // High-performance static map for matching Alpha-3 to Alpha-2
    private const array ISO_3_TO_ISO_2 = [
        'AFG' => 'AF', 'ALB' => 'AL', 'DZA' => 'DZ', 'ASM' => 'AS', 'AND' => 'AD',
        'AGO' => 'AO', 'AIA' => 'AI', 'ATA' => 'AQ', 'ATG' => 'AG', 'ARG' => 'AR',
        'ARM' => 'AM', 'ABW' => 'AW', 'AUS' => 'AU', 'AUT' => 'AT', 'AZE' => 'AZ',
        'BHS' => 'BH', 'BHR' => 'BH', 'BGD' => 'BD', 'BRB' => 'BB', 'BLR' => 'BY',
        'BEL' => 'BE', 'BLZ' => 'BZ', 'BEN' => 'BJ', 'BMU' => 'BM', 'BTN' => 'BT',
        'BOL' => 'BO', 'BES' => 'BQ', 'BIH' => 'BA', 'BWA' => 'BW', 'BVT' => 'BV',
        'BRA' => 'BR', 'IOT' => 'IO', 'BRN' => 'BN', 'BGR' => 'BG', 'BFA' => 'BF',
        'BDI' => 'BI', 'CPV' => 'CV', 'KHM' => 'KH', 'CMR' => 'CM', 'CAN' => 'CA',
        'CYM' => 'KY', 'CAF' => 'CF', 'TCD' => 'TD', 'CHL' => 'CL', 'CHN' => 'CN',
        'CXR' => 'CX', 'CCK' => 'CC', 'COL' => 'CO', 'COM' => 'KM', 'COD' => 'CD',
        'COG' => 'CG', 'COK' => 'CK', 'CRI' => 'CR', 'HRV' => 'HR', 'CUB' => 'CU',
        'CUW' => 'CW', 'CYP' => 'CY', 'CZE' => 'CZ', 'CIV' => 'CI', 'DNK' => 'DK',
        'DJI' => 'DJ', 'DMA' => 'DM', 'DOM' => 'DO', 'ECU' => 'EC', 'EGY' => 'EG',
        'SLV' => 'SV', 'GNQ' => 'GQ', 'ERI' => 'ER', 'EST' => 'EE', 'SWZ' => 'SZ',
        'ETH' => 'ET', 'FLK' => 'FK', 'FRO' => 'FO', 'FJI' => 'FJ', 'FIN' => 'FI',
        'FRA' => 'FR', 'GUF' => 'GF', 'PYF' => 'PF', 'ATF' => 'TF', 'GAB' => 'GA',
        'GMB' => 'GM', 'GEO' => 'GE', 'DEU' => 'DE', 'GHA' => 'GH', 'GIB' => 'GI',
        'GRC' => 'GR', 'GRL' => 'GL', 'GRD' => 'GD', 'GLP' => 'GP', 'GUM' => 'GU',
        'GTM' => 'GT', 'GGY' => 'GG', 'GIN' => 'GN', 'GNB' => 'GW', 'GUY' => 'GY',
        'HTI' => 'HT', 'HMD' => 'HM', 'VAT' => 'VA', 'HND' => 'HN', 'HKG' => 'HK',
        'HUN' => 'HU', 'ISL' => 'IS', 'IND' => 'IN', 'IDN' => 'ID', 'IRN' => 'IR',
        'IRQ' => 'IQ', 'IRL' => 'IE', 'IMN' => 'IM', 'ISR' => 'IL', 'ITA' => 'IT',
        'JAM' => 'JM', 'JPN' => 'JP', 'JEY' => 'JE', 'JOR' => 'JO', 'KAZ' => 'KZ',
        'KEN' => 'KE', 'KIR' => 'KI', 'PRK' => 'KP', 'KOR' => 'KR', 'KWT' => 'KW',
        'KGZ' => 'KG', 'LAO' => 'LA', 'LVA' => 'LV', 'LBN' => 'LB', 'LSO' => 'LS',
        'LBR' => 'LR', 'LBY' => 'LY', 'LIE' => 'LI', 'LTU' => 'LT', 'LUX' => 'LU',
        'MAC' => 'MO', 'MKD' => 'MK', 'MDG' => 'MG', 'MWI' => 'MW', 'MYS' => 'MY',
        'MDV' => 'MV', 'MLI' => 'ML', 'MLT' => 'MT', 'MHL' => 'MH', 'MTQ' => 'MQ',
        'MRT' => 'MR', 'MUS' => 'MU', 'MYT' => 'YT', 'MEX' => 'MX', 'FSM' => 'FM',
        'MDA' => 'MD', 'MCO' => 'MC', 'MNG' => 'MN', 'MNE' => 'ME', 'MSR' => 'MS',
        'MAR' => 'MA', 'MOZ' => 'MZ', 'MMR' => 'MM', 'NAM' => 'NA', 'NRU' => 'NR',
        'NPL' => 'NP', 'NLD' => 'NL', 'NCL' => 'NC', 'NZL' => 'NZ', 'NIC' => 'NI',
        'NER' => 'NE', 'NGA' => 'NG', 'NIU' => 'NU', 'NFK' => 'NF', 'MNP' => 'MP',
        'NOR' => 'NO', 'OMN' => 'OM', 'PAK' => 'PK', 'PLW' => 'PW', 'PSE' => 'PS',
        'PAN' => 'PA', 'PNG' => 'PG', 'PRY' => 'PY', 'PER' => 'PE', 'PHL' => 'PH',
        'PCN' => 'PN', 'POL' => 'PL', 'PRT' => 'PT', 'PRI' => 'PR', 'QAT' => 'QA',
        'REU' => 'RE', 'ROU' => 'RO', 'RUS' => 'RU', 'RWA' => 'RW', 'BLM' => 'BL',
        'SHN' => 'SH', 'KNA' => 'KN', 'LCA' => 'LC', 'MAF' => 'MF', 'SPM' => 'PM',
        'VCT' => 'VC', 'WSM' => 'WS', 'SMR' => 'SM', 'STP' => 'ST', 'SAU' => 'SA',
        'SEN' => 'SN', 'SRB' => 'RS', 'SYC' => 'SC', 'SLE' => 'SL', 'SGP' => 'SG',
        'SXM' => 'SX', 'SVK' => 'SK', 'SVN' => 'SI', 'SLB' => 'SB', 'SOM' => 'SO',
        'ZAF' => 'ZA', 'SGS' => 'GS', 'SSD' => 'SS', 'ESP' => 'ES', 'LKA' => 'LK',
        'SDN' => 'SD', 'SUR' => 'SR', 'SJM' => 'SJ', 'SWE' => 'SE', 'CHE' => 'CH',
        'SYR' => 'SY', 'TWN' => 'TW', 'TJK' => 'TJ', 'TZA' => 'TZ', 'THA' => 'TH',
        'TLS' => 'TL', 'TGO' => 'TG', 'TKL' => 'TK', 'TON' => 'TO', 'TTO' => 'TT',
        'TUN' => 'TN', 'TUR' => 'TR', 'TKM' => 'TM', 'TCA' => 'TC', 'TUV' => 'TV',
        'UGA' => 'UG', 'UKR' => 'UA', 'ARE' => 'AE', 'GBR' => 'GB', 'UMI' => 'UM',
        'USA' => 'US', 'URY' => 'UY', 'UZB' => 'UZ', 'VUT' => 'VU', 'VEN' => 'VE',
        'VNM' => 'VN', 'VGB' => 'VG', 'VIR' => 'VI', 'WLF' => 'WF', 'ESH' => 'EH',
        'YEM' => 'YE', 'ZMB' => 'ZM', 'ZWE' => 'ZW',
    ];

    /** Subdivision flags that cannot be derived from ISO alpha-2 */
    private const array SUBDIVISION_FLAGS = [
        'ENG' => '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
        'SCO' => '🏴󠁧󠁢󠁳󠁣󠁴󠁿',
        'WAL' => '🏴󠁧󠁢󠁷󠁬󠁳󠁿',
        'NIR' => '🇬🇧',
    ];

    /** @var ?array<string, string> */
    private static ?array $flagsMap = null;

    /**
     * Accepts ISO alpha-2 ('FR'), FIFA ('FRA'), ISO alpha-3 ('FRA'),
     * or English country name ('France').
     */
    public static function getFlag(string $code): string
    {
        $upper = mb_strtoupper(mb_trim($code));

        if (isset(self::SUBDIVISION_FLAGS[$upper])) {
            return self::SUBDIVISION_FLAGS[$upper];
        }

        // 2-letter ISO alpha-2 → direct regional indicator conversion
        if (2 === mb_strlen($upper) && ctype_alpha($upper)) {
            return self::iso2ToEmoji($upper);
        }

        // 3-letter code → resolve to ISO alpha-2 via FIFA then ISO3 tables
        $iso2 = self::FIFA_TO_ISO_2[$upper] ?? self::ISO_3_TO_ISO_2[$upper] ?? null;
        if ($iso2) {
            return self::iso2ToEmoji($iso2);
        }

        // English name fallback (e.g. 'France' → looks up computed map)
        self::$flagsMap ??= self::computeCountryNamesToFlags();

        return self::$flagsMap[mb_strtolower($upper)] ?? '🏴‍☠️';
    }

    private static function iso2ToEmoji(string $iso2): string
    {
        return mb_chr(0x1F1E6 + ord($iso2[0]) - ord('A'))
             .mb_chr(0x1F1E6 + ord($iso2[1]) - ord('A'));
    }

    private static function computeCountryNamesToFlags(): array
    {
        $flagMap = [];

        for ($i = 65; $i <= 90; $i++) {
            for ($j = 65; $j <= 90; $j++) {
                $code = chr($i).chr($j);
                $name = Locale::getDisplayRegion('-'.$code, 'en');

                if ($name && $name !== $code) {
                    $flagEmoji = mb_chr($i % 32 + 0x1F1E5).mb_chr($j % 32 + 0x1F1E5);
                    $flagMap[mb_strtolower($name)] = $flagEmoji;
                }
            }
        }

        return $flagMap;
    }
}
