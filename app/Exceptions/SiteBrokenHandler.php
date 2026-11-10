<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Throwable;

class SiteBrokenHandler
{
    /**
     * If the exception is a QueryException caused by a missing table
     * (e.g. after a full database reset), render the friendly
     * "website rusak" page instead of crashing. Otherwise return null
     * so the framework's default exception handling takes over.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function render(Throwable $e, Request $request)
    {
        if ($e instanceof QueryException && $this->isMissingTable($e->getMessage())) {
            return Response::view('errors.site-broken', ['code' => 500]);
        }

        return null;
    }

    private function isMissingTable(string $message): bool
    {
        return str_contains($message, "doesn't exist")
            || str_contains($message, 'does not exist')
            || str_contains($message, 'Base table or view not found');
    }
}
