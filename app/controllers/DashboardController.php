<?php
declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        Helpers::redirect('/leads');
    }
}
