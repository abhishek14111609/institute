<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ValidationController extends Controller
{
    /**
     * AJAX check for common fields like email, username, etc.
     */
    public function validateField(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        $table = $request->get('table');
        $ignoreId = $request->get('ignore_id');
        $ruleString = $request->get('rules', 'nullable'); // e.g., 'required|email'

        if (!$field) {
            return response()->json(['valid' => true]);
        }

        // Parse rules
        $rules = explode('|', $ruleString);
        
        // Add unique constraint if requested or if it's a known identifier
        if ($table && in_array($field, ['email', 'username', 'roll_number', 'employee_id', 'code', 'name'])) {
            $unique = Rule::unique($table, $field);
            if ($ignoreId) {
                // If checking Users table for Student/Teacher, we need to map the ID
                if ($table === 'users' && $request->has('user_id')) {
                    $unique->ignore($request->get('user_id'));
                } else {
                    $unique->ignore($ignoreId);
                }
            }
            // For school-scoped models, ensure we only check within the same school
            if (!in_array($table, ['users'])) {
                $unique->where('school_id', auth()->user()->school_id);
            }
            $rules[] = $unique;
        }

        $validator = Validator::make([$field => $value], [
            $field => $rules
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => $validator->errors()->first($field)
            ]);
        }

        return response()->json(['valid' => true]);
    }
}
