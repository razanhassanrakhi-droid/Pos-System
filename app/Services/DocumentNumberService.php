<?php

namespace App\Services;

class DocumentNumberService
{
    /**
     * Set this to true in config or settings to enable branch codes in document numbers.
     */
    protected static $enableBranchCodes = false;

    /**
     * Generate sequential document numbers: PREFIX-[BRANCH]-YYYYMMDD-SEQUENCE
     */
    public static function generateDailyNumber($prefix, $modelClass, $dateField = 'created_at', $numberField = 'invoice_number', $branchId = null)
    {
        $today = now();
        $dateString = $today->format('Ymd');
        
        $branchSegment = '';
        if (self::$enableBranchCodes && $branchId) {
            $branchSegment = '-BR' . str_pad($branchId, 2, '0', STR_PAD_LEFT);
        }

        // Search for records today matching prefix
        $latestRecord = $modelClass::whereDate($dateField, $today->toDateString())
            ->where($numberField, 'LIKE', $prefix . $branchSegment . '-' . $dateString . '-%')
            ->orderBy($numberField, 'desc')
            ->first();

        $sequence = 1;
        if ($latestRecord) {
            $lastNumber = $latestRecord->$numberField;
            $parts = explode('-', $lastNumber);
            $lastSeq = end($parts);
            if (is_numeric($lastSeq)) {
                $sequence = (int)$lastSeq + 1;
            }
        }

        return $prefix . $branchSegment . '-' . $dateString . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate sequential numbers without date: PREFIX-[BRANCH]-SEQUENCE
     */
    public static function generateStaticNumber($prefix, $modelClass, $numberField = 'customer_number', $branchId = null)
    {
        $branchSegment = '';
        if (self::$enableBranchCodes && $branchId) {
            $branchSegment = '-BR' . str_pad($branchId, 2, '0', STR_PAD_LEFT);
        }

        $latestRecord = $modelClass::where($numberField, 'LIKE', $prefix . $branchSegment . '-%')
            ->orderBy($numberField, 'desc')
            ->first();

        $sequence = 1;
        if ($latestRecord) {
            $lastNumber = $latestRecord->$numberField;
            $parts = explode('-', $lastNumber);
            $lastSeq = end($parts);
            if (is_numeric($lastSeq)) {
                $sequence = (int)$lastSeq + 1;
            }
        }

        return $prefix . $branchSegment . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate continuous infinite numbers without date: PREFIX-[BRANCH]-SEQUENCE
     * Uses ID to ensure it never resets.
     */
    public static function generateContinuousNumber($prefix, $modelClass, $numberField = 'invoice_number', $branchId = null)
    {
        $branchSegment = '';
        if (self::$enableBranchCodes && $branchId) {
            $branchSegment = '-BR' . str_pad($branchId, 2, '0', STR_PAD_LEFT);
        }

        $latestRecord = $modelClass::orderBy('id', 'desc')->first();
        $sequence = 1;
        if ($latestRecord) {
            $lastNumber = $latestRecord->$numberField;
            $parts = explode('-', $lastNumber);
            $lastSeq = end($parts);
            if (is_numeric($lastSeq)) {
                $sequence = (int)$lastSeq + 1;
            } else {
                $sequence = $latestRecord->id + 1;
            }
        }

        return $prefix . $branchSegment . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
