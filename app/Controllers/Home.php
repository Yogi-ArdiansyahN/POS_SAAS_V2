<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home/index');
    }

    public function indexAdmin()
    {
        return view('admin/dashboard');
    }

    public function indexStaff()
    {
        return view('staff/dashboard');
    }

    public function indexMember()
    {
        return view('member/dashboard');
    }
}
