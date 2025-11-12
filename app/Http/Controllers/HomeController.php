<?php

namespace App\Http\Controllers;

use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct(private readonly LoanService $loanService)
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $user = Auth::user()->load(['loans.installments']);

        $loanSummary = $this->loanService->summarizeLoans($user);

        return view('home', [
            'user' => $user,
            'loanSummary' => $loanSummary,
        ]);
    }
}

