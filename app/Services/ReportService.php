<?php

namespace App\Services;

use App\Models\School;
use App\Models\Student;
use App\Models\Fee;
use App\Models\FeePayment;
use App\Models\Expense;
use App\Models\InventorySale;
use App\Models\Attendance;
use App\Models\Material;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    private function feeRevenueQuery($schoolId)
    {
        return FeePayment::query()->whereHas('fee', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        });
    }

    private function salesRevenueQuery($schoolId)
    {
        return InventorySale::query()
            ->where('school_id', $schoolId)
            ->where('payment_status', 'paid');
    }

    private function expenseQuery($schoolId)
    {
        return Expense::query()->where('school_id', $schoolId);
    }

    private function outstandingFeesQuery($schoolId)
    {
        return Fee::query()
            ->where('school_id', $schoolId)
            ->whereIn('status', ['pending', 'partial', 'overdue']);
    }

    /**
     * Get school dashboard statistics
     */
    public function getSchoolDashboardStats($schoolId)
    {
        $school = School::findOrFail($schoolId);
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $monthlyFeeRevenue = (float) $this->feeRevenueQuery($schoolId)
            ->whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', $currentMonth)
            ->sum('amount');

        $monthlySalesRevenue = (float) $this->salesRevenueQuery($schoolId)
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('total_amount');

        $monthlyExpenses = (float) $this->expenseQuery($schoolId)
            ->whereYear('expense_date', $currentYear)
            ->whereMonth('expense_date', $currentMonth)
            ->sum('amount');

        $totalFeeRevenue = (float) $this->feeRevenueQuery($schoolId)->sum('amount');
        $totalSalesRevenue = (float) $this->salesRevenueQuery($schoolId)->sum('total_amount');
        $pendingFees = (float) $this->outstandingFeesQuery($schoolId)
            ->sum(DB::raw('total_amount + late_fee - discount - paid_amount'));

        return [
            'total_students' => $school->students()->active()->count(),
            'total_batches' => $school->batches()->active()->count(),
            'total_teachers' => $school->teachers()->active()->count(),
            'total_classes' => $school->classes()->active()->count(),

            // Fee statistics
            'total_fees' => Fee::where('school_id', $schoolId)->sum('total_amount'),
            'collected_fees' => Fee::where('school_id', $schoolId)->sum('paid_amount'),
            'pending_fees' => $pendingFees,
            'monthly_collection' => $monthlyFeeRevenue + $monthlySalesRevenue,
            'monthly_fee_revenue' => $monthlyFeeRevenue,
            'monthly_sales_revenue' => $monthlySalesRevenue,
            'monthly_total_revenue' => $monthlyFeeRevenue + $monthlySalesRevenue,
            'monthly_net' => ($monthlyFeeRevenue + $monthlySalesRevenue) - $monthlyExpenses,
            'total_fee_revenue' => $totalFeeRevenue,
            'total_sales_revenue' => $totalSalesRevenue,
            'total_operational_revenue' => $totalFeeRevenue + $totalSalesRevenue,

            // Expense statistics
            'total_expenses' => Expense::where('school_id', $schoolId)->sum('amount'),
            'monthly_expenses' => $monthlyExpenses,

            // Subscription info
            'subscription_expires_at' => $school->subscription_expires_at,
            'days_until_expiry' => $school->subscription_expires_at
                ? Carbon::now()->diffInDays($school->subscription_expires_at, false)
                : null,
            'is_subscription_active' => $school->isSubscriptionActive(),
        ];
    }

    /**
     * Get monthly income report
     */
    public function getMonthlyIncomeReport($schoolId, $year, $month = null)
    {
        $feeQuery = Fee::where('school_id', $schoolId)
            ->whereYear('created_at', $year);

        if ($month) {
            $feeQuery->whereMonth('created_at', $month);
        }

        $fees = $feeQuery->get();
        $feeCollected = (float) $this->feeRevenueQuery($schoolId)
            ->whereYear('paid_at', $year)
            ->when($month, fn($query) => $query->whereMonth('paid_at', $month))
            ->sum('amount');

        $salesCollected = (float) $this->salesRevenueQuery($schoolId)
            ->whereYear('created_at', $year)
            ->when($month, fn($query) => $query->whereMonth('created_at', $month))
            ->sum('total_amount');

        $expenses = (float) $this->expenseQuery($schoolId)
            ->whereYear('expense_date', $year)
            ->when($month, fn($query) => $query->whereMonth('expense_date', $month))
            ->sum('amount');

        return [
            'total_generated' => $fees->sum('total_amount'),
            'fee_generated' => $fees->sum('total_amount'),
            'total_collected' => $feeCollected + $salesCollected,
            'fee_collected' => $feeCollected,
            'sales_collected' => $salesCollected,
            'total_revenue' => $feeCollected + $salesCollected,
            'total_pending' => $fees->sum(function ($fee) {
                return $fee->total_amount + $fee->late_fee - $fee->discount - $fee->paid_amount;
            }),
            'total_discount' => $fees->sum('discount'),
            'total_late_fee' => $fees->sum('late_fee'),
            'total_expenses' => $expenses,
            'net_income' => ($feeCollected + $salesCollected) - $expenses,
        ];
    }

    /**
     * Get monthly expense report by category
     */
    public function getMonthlyExpenseReport($schoolId, $year, $month)
    {
        return Expense::where('school_id', $schoolId)
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats($schoolId, $startDate, $endDate)
    {
        $attendances = Attendance::where('school_id', $schoolId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $totalRecords = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();

        return [
            'total_records' => $totalRecords,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'present_percentage' => $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 2) : 0,
        ];
    }

    /**
     * Get fee collection chart data
     */
    public function getFeeCollectionChartData($schoolId, $year)
    {
        $months = [];
        $feeRevenue = [];
        $salesRevenue = [];
        $totalRevenue = [];
        $pending = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($year, $month)->format('M');
            $months[] = $monthName;

            $monthlyFees = Fee::where('school_id', $schoolId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();

            $monthlyFeeRevenue = (float) $this->feeRevenueQuery($schoolId)
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month)
                ->sum('amount');

            $monthlySalesRevenue = (float) $this->salesRevenueQuery($schoolId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('total_amount');

            $feeRevenue[] = $monthlyFeeRevenue;
            $salesRevenue[] = $monthlySalesRevenue;
            $totalRevenue[] = $monthlyFeeRevenue + $monthlySalesRevenue;
            $pending[] = $monthlyFees->sum('total_amount') - $monthlyFees->sum('paid_amount');
        }

        return [
            'labels' => $months,
            'collected' => $feeRevenue,
            'sales' => $salesRevenue,
            'total' => $totalRevenue,
            'pending' => $pending,
        ];
    }

    /**
     * Get student enrollment trend
     */
    public function getStudentEnrollmentTrend($schoolId, $year)
    {
        $months = [];
        $enrollments = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($year, $month)->format('M');
            $months[] = $monthName;

            $count = Student::where('school_id', $schoolId)
                ->whereYear('admission_date', $year)
                ->whereMonth('admission_date', $month)
                ->count();

            $enrollments[] = $count;
        }

        return [
            'labels' => $months,
            'enrollments' => $enrollments,
        ];
    }

    /**
     * Get top fee defaulters
     */
    public function getTopDefaulters($schoolId, $limit = 5)
    {
        return Fee::where('school_id', $schoolId)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->with('student.user')
            ->select('student_id', DB::raw('SUM(total_amount + late_fee - discount - paid_amount) as balance'), DB::raw('COUNT(*) as fee_count'))
            ->groupBy('student_id')
            ->orderByDesc('balance')
            ->take($limit)
            ->get();
    }

    /**
     * Get count of overdue fees
     */
    public function getOverdueFeesCount($schoolId)
    {
        return Fee::where('school_id', $schoolId)
            ->where('status', 'overdue')
            ->count();
    }

    /**
     * Monthly income trend using actual payment records.
     */
    public function getMonthlyIncomeTrend($schoolId, $year)
    {
        $labels = [];
        $feeAmounts = [];
        $salesAmounts = [];
        $totalAmounts = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = Carbon::create($year, $month)->format('M');

            $monthlyFee = (float) $this->feeRevenueQuery($schoolId)
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month)
                ->sum('amount');

            $monthlySales = (float) $this->salesRevenueQuery($schoolId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('total_amount');

            $feeAmounts[] = $monthlyFee;
            $salesAmounts[] = $monthlySales;
            $totalAmounts[] = $monthlyFee + $monthlySales;
        }

        return [
            'labels' => $labels,
            'fee_amounts' => $feeAmounts,
            'sales_amounts' => $salesAmounts,
            'amounts' => $totalAmounts,
            'total' => array_sum($totalAmounts),
        ];
    }

    /**
     * Monthly expense trend for selected year.
     */
    public function getMonthlyExpenseTrend($schoolId, $year)
    {
        $labels = [];
        $amounts = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = Carbon::create($year, $month)->format('M');
            $amounts[] = (float) Expense::where('school_id', $schoolId)
                ->whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'amounts' => $amounts,
            'total' => array_sum($amounts),
        ];
    }

    /**
     * Student record summary chart data.
     */
    public function getStudentRecordBreakdown($schoolId)
    {
        $active = Student::where('school_id', $schoolId)->where('is_active', true)->count();
        $inactive = Student::where('school_id', $schoolId)->where('is_active', false)->count();

        return [
            'labels' => ['Active', 'Inactive'],
            'counts' => [$active, $inactive],
            'total' => $active + $inactive,
        ];
    }

    /**
     * Stock report based on uploaded study materials.
     */
    public function getStockReportData($schoolId, $year)
    {
        $byType = Material::where('school_id', $schoolId)
            ->select(DB::raw("COALESCE(NULLIF(file_type, ''), 'other') as type"), DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        $monthlyUploads = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyUploads[] = Material::where('school_id', $schoolId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
        }

        return [
            'type_labels' => $byType->pluck('type')->map(fn($type) => strtoupper($type))->values()->all(),
            'type_totals' => $byType->pluck('total')->map(fn($count) => (int) $count)->values()->all(),
            'monthly_uploads' => $monthlyUploads,
            'total_items' => Material::where('school_id', $schoolId)->count(),
        ];
    }
}
