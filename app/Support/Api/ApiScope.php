<?php

namespace App\Support\Api;

final class ApiScope
{
    public const WEBSITE_READ = 'website:read';

    public const CMS_WRITE = 'cms:write';

    public const AKADEMIK_WRITE = 'akademik:write';

    public const KEUANGAN_WRITE = 'keuangan:write';

    public const ALL = [
        self::WEBSITE_READ,
        self::CMS_WRITE,
        self::AKADEMIK_WRITE,
        self::KEUANGAN_WRITE,
    ];

    public const STAFF_ROLES = [
        'super_admin',
        'kepala_madrasah',
        'wakamad_kurikulum',
        'wakamad_kesiswaan',
        'wakamad_humas',
        'wakamad_sarpras',
        'tata_usaha',
        'bendahara',
        'guru',
        'guru_bk',
        'wali_kelas',
        'pustakawan',
        'editor_berita',
    ];

    /**
     * @return array<int, string>
     */
    public static function forRole(string $role): array
    {
        $scopes = [self::WEBSITE_READ];

        if (in_array($role, ['super_admin', 'wakamad_humas', 'editor_berita', 'kepala_madrasah', 'tata_usaha', 'guru'], true)) {
            $scopes[] = self::CMS_WRITE;
        }

        if (in_array($role, ['super_admin', 'kepala_madrasah', 'wakamad_kurikulum', 'wakamad_kesiswaan', 'wali_kelas', 'guru'], true)) {
            $scopes[] = self::AKADEMIK_WRITE;
        }

        if (in_array($role, ['super_admin', 'bendahara', 'tata_usaha'], true)) {
            $scopes[] = self::KEUANGAN_WRITE;
        }

        return array_values(array_unique($scopes));
    }

    public static function isStaff(string $role): bool
    {
        return in_array($role, self::STAFF_ROLES, true);
    }
}
