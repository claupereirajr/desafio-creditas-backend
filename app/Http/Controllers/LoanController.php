<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanRequest;
use Illuminate\Http\Request;

class LoanController extends Controller
{

    /**
     * Calculate the option of loan a newly created resource in storage.
     * We must provide the following loan types:
     * Personal Loan. Interest Rate: 4%
     * Collateralized Loan. Interest Rate: 3%
     * Payroll Loan. Interest Rate: 2%
     * Listed below are the business rules for conceding a loan based on the person's profile:
     * Personal Loan, Collateralized Loan, Payroll
     * Income <= 3000	Yes	Yes(3)	No
     * Income > 3000 & < 5000	Yes	Yes(2)	No
     * Income => 5000	Yes	Yes(1)	Yes
     * 1 Clients under 30 y.o.
     * 2 Clients residing in SP (São Paulo state code in Brazil)
     * 3 Clients under 30 y.o. residing in SP
     *
     * OUTPUT JSON
     * {
     *  "customer": "Erikaya",
     *  "loans": [
     *      {
     *          "type": "personal",
     *          "taxes": 1
     *      }
     *   ]
     * }
     */
    public function loan(LoanRequest $request)
    {
        $data = $request->validated();
        $income = $data['customer']['income'];
        $age = $data['customer']['age'];
        $state = $data['customer']['location'];
        $loans = [];

        if ($this->isEligibleForPersonalLoan($income)) {
            $loans[] = [
                'type' => 'personal',
                'taxes' => 1
            ];
        }
        if ($this->isEligibleForCollateralizedLoan($income, $age, $state)) {
            $loans[] = [
                'type' => 'collateralized',
                'taxes' => 3
            ];
        }
        if ($this->isEligibleForPayrollLoan($income)) {
            $loans[] = [
                'type' => 'payroll',
                'taxes' => 2
            ];
        }
        return response()->json([
            'customer' => $data['customer']['name'],
            'loans' => $loans
        ]);
    }

    private function isEligibleForPersonalLoan($income)
    {
        return true; // Personal loan is available for all income levels
    }

    private function isEligibleForCollateralizedLoan($income, $age, $state)
    {
        if ($income <= 3000) {
            return $age < 30 && $state === 'SP';
        } else if ($income > 3000 && $income < 5000) {
            return $state === 'SP';
        } else { // income >= 5000
            return $age < 30;
        }
    }

    private function isEligibleForPayrollLoan($income)
    {
        return $income >= 5000;
    }
}
