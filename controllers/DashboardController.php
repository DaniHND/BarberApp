<?php
class DashboardController extends BaseController {

    // GET /  |  GET /dashboard
    public function index(): void {
        $this->auth();

        $pageTitle = 'Dashboard';
        $activeNav = 'dashboard';

        $this->render('dashboard/index', compact('pageTitle', 'activeNav'));
    }
}
