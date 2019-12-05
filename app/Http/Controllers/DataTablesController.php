<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\User;
use Illuminate\View\View;
use Yajra\Datatables\Datatables;

class DataTablesController extends Controller
{
    /**
     * Displays datatables front end view
     *
     * @return View
     */
    public function datatable()
    {
        return view('datatables.index');
    }

    /**
     * Process datatables ajax request.
     *
     * @return JsonResponse
     */

    public function getPosts()
    {
        return \DataTables::of(User::query())->make(true);
    }
}
