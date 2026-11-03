<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Internship;
use App\Models\JobListing;
use App\Models\ReportExport;
use App\Models\School;
use App\Models\User;
use App\Models\UserReport;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    private const TYPES = [
        'users' => [
            'title' => 'User Growth',
            'type' => 'Users',
            'summary' => 'New registrations, account roles, and status for the selected period.',
        ],
        'verification' => [
            'title' => 'Verification Summary',
            'type' => 'Verification',
            'summary' => 'Institution account verification queue, approvals, and rejections.',
        ],
        'jobs' => [
            'title' => 'Job Posting Analytics',
            'type' => 'Jobs',
            'summary' => 'Job listings, applicants, and total views.',
        ],
        'schools' => [
            'title' => 'School Registration',
            'type' => 'School',
            'summary' => 'School records, verification status, and regional distribution.',
        ],
        'companies' => [
            'title' => 'Company Activity',
            'type' => 'Company',
            'summary' => 'Company profiles and verification status.',
        ],
        'internships' => [
            'title' => 'Internship Placement',
            'type' => 'Internship',
            'summary' => 'Internship listings, schedules, and status.',
        ],
    ];

    public function index(Request $request): View
    {
        [$from, $to] = $this->filters($request);

        $reports = collect(self::TYPES)
            ->map(function (array $definition, string $slug) use ($from, $to) {
                return [
                    ...$definition,
                    'slug' => $slug,
                    'id' => 'RPT-'.strtoupper($slug),
                    'records' => $this->countRecords($slug, $from, $to),
                    'period' => $this->periodLabel($from, $to),
                ];
            })
            ->values()
            ->all();

        return view('admin.reports.index', [
            'reports' => $reports,
            'recent' => array_slice($reports, 0, 4),
            'exports' => ReportExport::with('user')
                ->latest()
                ->paginate(10, ['*'], 'exports_page')
                ->withQueryString(),
            'userReports' => UserReport::with('user')
                ->when($request->query('report_status'), fn ($q, $s) => $q->where('status', $s))
                ->latest()
                ->paginate(15, ['*'], 'ur_page')
                ->withQueryString(),
            'userReports' => UserReport::with('user')
                ->when($request->query('report_status'), fn ($q, $s) => $q->where('status', $s))
                ->latest()
                ->paginate(15, ['*'], 'ur_page')
                ->withQueryString(),
            'activeType' => $request->query('type', ''),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
            'totals' => [
                'Users' => User::count(),
                'Jobs' => JobListing::count(),
                'Schools' => School::count(),
                'Companies' => Company::count(),
            ],
        ]);
    }

    public function show(Request $request, string $type): View
    {
        $report = $this->buildReport($type, $request);

        return view('admin.reports.show', [
            'report' => $report,
            'rows' => array_slice($report['rows'], 0, 25),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ]);
    }

    /**
     * Update status laporan dari user (hanya super_admin/admin).
     */
    public function updateUserReport(Request $request, UserReport $userReport): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,investigating,resolved,closed'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $userReport->update($validated);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function export(Request $request, string $type): StreamedResponse
    {
        $report = $this->buildReport($type, $request);

        return response()->streamDownload(function () use ($report, $request) {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, array_values($report['columns']));

            foreach ($report['rows'] as $row) {
                fputcsv($stream, array_map(
                    fn (string $column) => $this->csvSafe($row[$column] ?? ''),
                    array_keys($report['columns'])
                ));
            }

            fclose($stream);
            $this->recordExport($request, $report, 'csv');
        }, $report['filename'].'.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(Request $request, string $type): Response
    {
        $report = $this->buildReport($type, $request);

        $dompdf = new Dompdf;
        $dompdf->loadHtml(view('admin.reports.pdf', ['report' => $report])->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $this->recordExport($request, $report, 'pdf');

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$report['filename'].'.pdf"',
        ]);
    }

    public function exportXlsx(Request $request, string $type): StreamedResponse
    {
        $report = $this->buildReport($type, $request);
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report');

        $lastColumn = Coordinate::stringFromColumnIndex(count($report['columns']));
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValueExplicit('A1', $report['title'], DataType::TYPE_STRING);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValueExplicit('A2', $report['period'].' · '.$report['id'], DataType::TYPE_STRING);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);

        $headerRow = 4;
        foreach (array_values($report['columns']) as $index => $label) {
            $sheet->setCellValueExplicit(
                Coordinate::stringFromColumnIndex($index + 1).$headerRow,
                $label,
                DataType::TYPE_STRING
            );
        }

        $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:{$lastColumn}{$headerRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        $rowNumber = $headerRow + 1;
        foreach ($report['rows'] as $row) {
            foreach (array_keys($report['columns']) as $index => $column) {
                $sheet->setCellValueExplicit(
                    Coordinate::stringFromColumnIndex($index + 1).$rowNumber,
                    (string) ($row[$column] ?? ''),
                    DataType::TYPE_STRING
                );
            }
            $rowNumber++;
        }

        $sheet->freezePane('A'.($headerRow + 1));
        $sheet->setAutoFilter("A{$headerRow}:{$lastColumn}".($rowNumber - 1));
        foreach (range(1, count($report['columns'])) as $index) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index))->setWidth(22);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            function () use ($writer, $report, $request) {
                $writer->save('php://output');
                $this->recordExport($request, $report, 'xlsx');
            },
            $report['filename'].'.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return [
            isset($validated['from']) ? Carbon::parse($validated['from'])->startOfDay() : null,
            isset($validated['to']) ? Carbon::parse($validated['to'])->endOfDay() : null,
        ];
    }

    private function applyRange(Builder $query, ?Carbon $from, ?Carbon $to): Builder
    {
        return $query
            ->when($from, fn (Builder $q) => $q->where('created_at', '>=', $from))
            ->when($to, fn (Builder $q) => $q->where('created_at', '<=', $to));
    }

    private function countRecords(string $type, ?Carbon $from, ?Carbon $to): int
    {
        $query = match ($type) {
            'users' => User::query(),
            'verification' => User::whereIn('role', ['school', 'university', 'company']),
            'jobs' => JobListing::query(),
            'schools' => School::query(),
            'companies' => Company::query(),
            'internships' => Internship::query(),
            default => abort(404),
        };

        return $this->applyRange($query, $from, $to)->count();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildReport(string $type, Request $request): array
    {
        if (! array_key_exists($type, self::TYPES)) {
            abort(404);
        }

        [$from, $to] = $this->filters($request);
        $definition = self::TYPES[$type];

        [$stats, $columns, $rows] = match ($type) {
            'users' => $this->usersReport($from, $to),
            'verification' => $this->verificationReport($from, $to),
            'jobs' => $this->jobsReport($from, $to),
            'schools' => $this->schoolsReport($from, $to),
            'companies' => $this->companiesReport($from, $to),
            'internships' => $this->internshipsReport($from, $to),
        };

        return [
            ...$definition,
            'slug' => $type,
            'id' => 'RPT-'.strtoupper($type),
            'stats' => $stats,
            'columns' => $columns,
            'rows' => $rows,
            'totalRows' => count($rows),
            'period' => $this->periodLabel($from, $to),
            'from' => $from?->toDateString(),
            'to' => $to?->toDateString(),
            'filename' => strtolower($definition['title']).'-'.now()->format('Ymd-His'),
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function recordExport(Request $request, array $report, string $format): void
    {
        ReportExport::create([
            'user_id' => $request->user()?->id,
            'type' => $report['slug'],
            'format' => $format,
            'period_from' => $report['from'],
            'period_to' => $report['to'],
            'rows_count' => $report['totalRows'],
            'file_name' => $report['filename'].'.'.$format,
        ]);
    }

    /**
     * @return array{0: array<string, int>, 1: array<string, string>, 2: array<int, array<string, string>>}
     */
    private function usersReport(?Carbon $from, ?Carbon $to): array
    {
        $query = $this->applyRange(User::query(), $from, $to);

        $stats = [
            'Total Users' => (clone $query)->count(),
            'Active' => (clone $query)->where('status', 'active')->count(),
            'Pending' => (clone $query)->where('status', 'pending')->count(),
            'Admins' => (clone $query)->whereIn('role', ['super_admin', 'admin'])->count(),
        ];

        $rows = $query->latest()->get(['name', 'email', 'role', 'status', 'created_at'])
            ->map(fn (User $user) => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'created_at' => $user->created_at?->format('d M Y') ?? '-',
            ])->all();

        return [$stats, [
            'name' => 'Name',
            'email' => 'Email',
            'role' => 'Role',
            'status' => 'Status',
            'created_at' => 'Registered',
        ], $rows];
    }

    /**
     * @return array{0: array<string, int>, 1: array<string, string>, 2: array<int, array<string, string>>}
     */
    private function verificationReport(?Carbon $from, ?Carbon $to): array
    {
        $query = $this->applyRange(
            User::whereIn('role', ['school', 'university', 'company']),
            $from,
            $to
        );

        $stats = [
            'Total Requests' => (clone $query)->count(),
            'Pending' => (clone $query)->where('status', 'pending')->count(),
            'Approved' => (clone $query)->where('status', 'active')->count(),
            'Rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];

        $rows = $query->latest()->get(['name', 'email', 'role', 'status', 'created_at'])
            ->map(fn (User $user) => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'created_at' => $user->created_at?->format('d M Y') ?? '-',
            ])->all();

        return [$stats, [
            'name' => 'Name',
            'email' => 'Email',
            'role' => 'Role',
            'status' => 'Status',
            'created_at' => 'Submitted',
        ], $rows];
    }

    /**
     * @return array{0: array<string, int>, 1: array<string, string>, 2: array<int, array<string, string>>}
     */
    private function jobsReport(?Carbon $from, ?Carbon $to): array
    {
        $query = $this->applyRange(JobListing::query(), $from, $to);

        $stats = [
            'Total Jobs' => (clone $query)->count(),
            'Active' => (clone $query)->where('is_active', true)->count(),
            'Applicants' => (int) (clone $query)->sum('applicants_count'),
            'Total Views' => (int) (clone $query)->sum('total_views'),
        ];

        $rows = $query->latest()->get(['title', 'company', 'type', 'status', 'applicants_count', 'created_at'])
            ->map(fn (JobListing $job) => [
                'title' => $job->title,
                'company' => $job->company,
                'type' => $job->type,
                'status' => $job->status,
                'applicants_count' => (string) $job->applicants_count,
                'created_at' => $job->created_at?->format('d M Y') ?? '-',
            ])->all();

        return [$stats, [
            'title' => 'Title',
            'company' => 'Company',
            'type' => 'Type',
            'status' => 'Status',
            'applicants_count' => 'Applicants',
            'created_at' => 'Posted',
        ], $rows];
    }

    /**
     * @return array{0: array<string, int>, 1: array<string, string>, 2: array<int, array<string, string>>}
     */
    private function schoolsReport(?Carbon $from, ?Carbon $to): array
    {
        $query = $this->applyRange(School::query(), $from, $to);

        $stats = [
            'Total Schools' => (clone $query)->count(),
            'Active' => (clone $query)->where('status', 'Active')->count(),
            'Pending' => (clone $query)->where('status', 'Pending')->count(),
            'Students' => (int) (clone $query)->sum('total_students'),
        ];

        $rows = $query->latest()->get(['name', 'npsn', 'city', 'province', 'status', 'created_at'])
            ->map(fn (School $school) => [
                'name' => $school->name,
                'npsn' => $school->npsn,
                'city' => $school->city ?? '-',
                'province' => $school->province ?? '-',
                'status' => $school->status,
                'created_at' => $school->created_at?->format('d M Y') ?? '-',
            ])->all();

        return [$stats, [
            'name' => 'Name',
            'npsn' => 'NPSN',
            'city' => 'City',
            'province' => 'Province',
            'status' => 'Status',
            'created_at' => 'Registered',
        ], $rows];
    }

    /**
     * @return array{0: array<string, int>, 1: array<string, string>, 2: array<int, array<string, string>>}
     */
    private function companiesReport(?Carbon $from, ?Carbon $to): array
    {
        $query = $this->applyRange(Company::query(), $from, $to);

        $stats = [
            'Total Companies' => (clone $query)->count(),
            'Active' => (clone $query)->where('status', 'Active')->count(),
            'Pending' => (clone $query)->where('status', 'Pending')->count(),
            'Employees' => (int) (clone $query)->sum('total_employees'),
        ];

        $rows = $query->latest()->get(['name', 'industry', 'city', 'status', 'created_at'])
            ->map(fn (Company $company) => [
                'name' => $company->name,
                'industry' => $company->industry ?? '-',
                'city' => $company->city ?? '-',
                'status' => $company->status,
                'created_at' => $company->created_at?->format('d M Y') ?? '-',
            ])->all();

        return [$stats, [
            'name' => 'Name',
            'industry' => 'Industry',
            'city' => 'City',
            'status' => 'Status',
            'created_at' => 'Registered',
        ], $rows];
    }

    /**
     * @return array{0: array<string, int>, 1: array<string, string>, 2: array<int, array<string, string>>}
     */
    private function internshipsReport(?Carbon $from, ?Carbon $to): array
    {
        $query = $this->applyRange(Internship::query(), $from, $to);

        $stats = [
            'Total Internships' => (clone $query)->count(),
            'Active' => (clone $query)->where('status', 'active')->count(),
            'Completed' => (clone $query)->where('status', 'completed')->count(),
            'Inactive' => (clone $query)->where('status', 'inactive')->count(),
        ];

        $rows = $query->latest()->get(['title', 'company', 'status', 'start_date', 'end_date', 'created_at'])
            ->map(fn (Internship $internship) => [
                'title' => $internship->title,
                'company' => $internship->company,
                'status' => $internship->status,
                'start_date' => $internship->start_date ?? '-',
                'end_date' => $internship->end_date ?? '-',
                'created_at' => $internship->created_at?->format('d M Y') ?? '-',
            ])->all();

        return [$stats, [
            'title' => 'Title',
            'company' => 'Company',
            'status' => 'Status',
            'start_date' => 'Start',
            'end_date' => 'End',
            'created_at' => 'Posted',
        ], $rows];
    }

    private function periodLabel(?Carbon $from, ?Carbon $to): string
    {
        if (! $from && ! $to) {
            return 'All time';
        }

        return ($from?->format('d M Y') ?? 'Beginning').' - '.($to?->format('d M Y') ?? 'Now');
    }
}
