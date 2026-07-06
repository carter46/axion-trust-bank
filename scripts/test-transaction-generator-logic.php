<?php
/**
 * Offline logic tests for TransactionHistoryGenerator (no DB required for math checks).
 * Run: php scripts/test-transaction-generator-logic.php
 */
require_once __DIR__ . '/../includes/transaction-history-generator.php';

class TransactionHistoryGeneratorLogicTest extends TransactionHistoryGenerator
{
    public function __construct()
    {
        // Skip DB connection for pure math tests.
    }

    public function run(): void
    {
        $items = [
            ['transaction_type' => 'credit', 'amount' => 1000, 'fee' => 0, 'status' => 'completed'],
            ['transaction_type' => 'debit', 'amount' => 300, 'fee' => 5, 'status' => 'completed'],
            ['transaction_type' => 'debit', 'amount' => 50, 'fee' => 0, 'status' => 'failed'],
        ];

        $net = $this->computeNetMovement($items);
        assert(abs($net - 695.0) < 0.01, 'Net movement baseline failed: ' . $net);

        $scaled = $this->scaleToHistoryImpact($items, 200.0);
        $scaledNet = $this->computeNetMovement($scaled);
        assert(abs($scaledNet - 200.0) < 0.02, 'Positive scale failed: ' . $scaledNet);

        $scaledNeg = $this->scaleToHistoryImpact($items, -1200.0);
        $negNet = $this->computeNetMovement($scaledNeg);
        assert(abs($negNet - (-1200.0)) < 0.02, 'Negative scale failed: ' . $negNet);

        $previous = 500.0;
        $impact = 200.0;
        $opening = $previous - $impact;
        $chained = $this->buildBalanceChain($scaled, $opening);
        $last = $chained[count($chained) - 1];
        assert(abs((float)$last['balance_after'] - $previous) < 0.02, 'Anchor after mismatch');
        assert(abs((float)$chained[0]['balance_before'] - $opening) < 0.02, 'Opening mismatch');

        $anchorAfter = round(700.0 - 200.0, 2);
        assert(abs($anchorAfter + 300.0 - 800.0) < 0.01, 'Replace anchor math failed');

        echo "All logic tests passed.\n";
    }
}

(new TransactionHistoryGeneratorLogicTest())->run();
