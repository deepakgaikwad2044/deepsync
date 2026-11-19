<?php

namespace App\Controllers;

class AdminRbacPageController
{
    public function index(): void
    {
        view("admin.rbac.index");
    }
}
