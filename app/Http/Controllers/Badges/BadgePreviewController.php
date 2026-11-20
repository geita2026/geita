<?php

namespace App\Http\Controllers\Badges;

use App\Http\Controllers\Controller;
use App\Models\ExhibitionBadges\CompanyMember;
use App\Models\setting\DefaultSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BadgePreviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── Check if live test mode is ON ─────────────────────────────────────
    private function isLiveTestMode(): bool
    {
        return (bool) (DefaultSettings::limit(1)->value('badge_live_test') ?? false);
    }

    // ── Browser preview — blade with qr-code-styling JS ───────────────────
    public function show($member_id)
    {
        // Gate: only accessible if live test mode is ON
        if (!$this->isLiveTestMode()) {
            return back()->with('fail', 'Badge Live Test Mode is currently OFF. Enable it in Default Settings to use this preview.');
        }

        $badge = $this->fetchBadge($member_id);

        if (!$badge) {
            return back()->with('fail', 'Member not found: ' . $member_id);
        }

        return view('Badges.NewBadge', [
            'memberId'        => $badge->member_id,
            'badgeName'       => trim($badge->first_name . ' ' . $badge->last_name),
            'badgeCompany'    => trim($badge->associateCompany ?? $badge->companyName ?? ''),
            'badgeGroup'      => trim($badge->group_name ?? 'EXHIBITOR'),
            'badgeGroupColor' => $badge->group_background_color ?? '#13487e',
            'badgeTextColor'  => $badge->text_color ?? '#ffffff',
            'isPdf'           => false,
        ]);
    }

    // ── PDF download — server-side QR (JS can't run in DomPDF) ────────────
    public function pdf($member_id)
    {
        $badge = $this->fetchBadge($member_id);

        if (!$badge) {
            return back()->with('fail', 'Member not found: ' . $member_id);
        }

        $svg    = $this->buildSvg($badge, withQr: true);
        $svgB64 = base64_encode($svg);

        $pdf = Pdf::loadView('Badges.NewBadge', [
                    'svgB64'   => $svgB64,
                    'memberId' => $badge->member_id,
                    'isPdf'    => true,
                  ])
                  ->setPaper([0, 0, 226.77, 311.81], 'portrait'); // ≈ 8cm × 11cm

        return $pdf->stream('badge_' . $member_id . '.pdf');
    }

    // ── Core SVG builder ───────────────────────────────────────────────────
    private function buildSvg($badge, bool $withQr = true): string
    {
        $svg = file_get_contents(public_path('ditf26.svg'));

        // ── Allow SVG elements that bleed outside the viewBox to show ─────
        $svg = preg_replace('/<svg\b([^>]*)>/', '<svg$1 overflow="visible">', $svg, 1);

        // ── Remove sample name letters (y=184.31) ─────────────────────────
        $svg = preg_replace(
            '/<text[^>]*translate\([^)]*\s184\.31\)[^>]*>[\s\S]*?<\/text>/',
            '',
            $svg
        );

        // ── Remove sample group letters (y=223.12) ────────────────────────
        $svg = preg_replace(
            '/<text[^>]*translate\([^)]*\s223\.12\)[^>]*>[\s\S]*?<\/text>/',
            '',
            $svg
        );

        // ── Member name ───────────────────────────────────────────────────
        $name = strtoupper(trim($badge->first_name . ' ' . $badge->last_name));
        $svg  = str_replace('</svg>',
            '<text text-anchor="middle" x="379.8" y="184.31"
                font-family="\'Times New Roman\', serif" font-weight="700"
                font-size="15.65" fill="#1b1b1b" letter-spacing="0.5">'
            . htmlspecialchars($name) . '</text>' . PHP_EOL . '</svg>',
            $svg
        );

        // ── Company name (small, between name and group strip) ────────────
        $company = strtoupper(trim($badge->associateCompany ?? $badge->companyName ?? ''));
        if ($company) {
            $svg = str_replace('</svg>',
                '<text text-anchor="middle" x="379.8" y="193.5"
                    font-family="Arial, sans-serif" font-size="8" fill="#444444">'
                . htmlspecialchars($company) . '</text>' . PHP_EOL . '</svg>',
                $svg
            );
        }

        // ── Group strip label ─────────────────────────────────────────────
        $group = strtoupper($badge->group_name ?? 'EXHIBITOR');
        $svg   = str_replace('</svg>',
            '<text text-anchor="middle" x="379.8" y="223.12"
                font-family="\'Times New Roman\', serif" font-weight="700"
                font-size="21.44" fill="#ffffff" letter-spacing="1">'
            . htmlspecialchars($group) . '</text>' . PHP_EOL . '</svg>',
            $svg
        );

        // ── Server-side QR (PDF only) ─────────────────────────────────────
        if ($withQr) {
            $qrSvg   = (string) QrCode::format('svg')->size(156)->margin(0)
                                       ->generate('SAMS|' . $badge->member_id);
            $qrInner = preg_replace('/<\?xml[^?]*\?>\s*/', '', $qrSvg);
            $qrInner = preg_replace('/<svg[^>]*>([\s\S]*?)<\/svg>/i', '$1', $qrInner);

            $svg = str_replace('</svg>',
                '<g transform="translate(310.94, 298.46)">' . $qrInner . '</g>' . PHP_EOL . '</svg>',
                $svg
            );
        }

        return $svg;
    }

    // ── DB fetch — no member_image column (avoids memory crash) ───────────
    private function fetchBadge($member_id)
    {
        return CompanyMember::leftJoin('companies', 'company_members.company_id', '=', 'companies.companyID')
            ->leftJoin('badge_groups', 'company_members.member_group', '=', 'badge_groups.group_id')
            ->where('company_members.member_id', $member_id)
            ->select([
                'company_members.member_id',
                'company_members.first_name',
                'company_members.last_name',
                'company_members.associateCompany',
                'companies.companyName',
                'badge_groups.group_name',
                'badge_groups.group_background_color',
                'badge_groups.text_color',
            ])
            ->first();
    }
}
