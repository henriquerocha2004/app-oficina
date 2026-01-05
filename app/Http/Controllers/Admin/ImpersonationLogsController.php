<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\SearchDTO;
use App\Http\Controllers\Controller;
use App\Services\Admin\ImpersonationLogService;
use Inertia\Inertia;
use Inertia\Response;

class ImpersonationLogsController extends Controller
{
    public function __construct(
        private ImpersonationLogService $logService
    ) {
    }

    /**
     * Display impersonation logs.
     */
    public function index(): Response
    {
        $searchDTO = SearchDTO::fromRequest(request()->all());
        $logs = $this->logService->list($searchDTO);

        return Inertia::render('admin/impersonation-logs/Index', [
            'logs' => $logs,
            'filters' => $searchDTO->filters,
        ]);
    }
}
