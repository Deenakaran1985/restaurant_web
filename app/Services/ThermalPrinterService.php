<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\KdsTicket;
use Illuminate\Support\Facades\Log;

class ThermalPrinterService
{
    // ESC/POS Command Byte Constants for 80mm Network LAN Thermal Printers
    const ESC = "\x1B";
    const GS  = "\x1D";
    const NUL = "\x00";

    const INIT         = self::ESC . "@";
    const ALIGN_LEFT   = self::ESC . "a" . "\x00";
    const ALIGN_CENTER = self::ESC . "a" . "\x01";
    const ALIGN_RIGHT  = self::ESC . "a" . "\x02";
    
    const TEXT_NORMAL  = self::ESC . "!" . "\x00";
    const TEXT_BOLD    = self::ESC . "E" . "\x01";
    const TEXT_BOLD_OFF= self::ESC . "E" . "\x00";
    const TEXT_DOUBLE  = self::ESC . "!" . "\x30";
    
    const CUT_PAPER    = self::GS . "V" . "\x41" . "\x03";
    const BEEPER       = self::ESC . "B" . "\x03" . "\x02"; // 3 acoustic beeps for Kitchen KOT

    /**
     * Send ESC/POS Bytes over LAN TCP Socket (Default Port 9100)
     */
    public static function sendToSocket(string $ip, int $port, string $payload): bool
    {
        try {
            $socket = @fsockopen($ip, $port, $errno, $errstr, 2.0);
            if (!$socket) {
                // If physical LAN printer is unreachable or running in local dev simulation, log receipt cleanly
                Log::info("Thermal Printer Offline at {$ip}:{$port}. [Hardware Simulator Backup Activated]\n" . self::stripEscCommands($payload));
                self::saveToLocalSimulator($ip, $payload);
                return true; // Return true so POS cashier workflow is not blocked
            }

            fwrite($socket, $payload);
            fclose($socket);
            Log::info("ESC/POS Receipt successfully dispatched over TCP Socket to printer: {$ip}:{$port}");
            return true;
        } catch (\Exception $e) {
            Log::error("Thermal Printer Socket Exception on {$ip}:{$port}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Print Kitchen Order Ticket (KOT) with Acoustic Buzzer Chime
     */
    public static function printKot(KdsTicket $ticket, string $printerIp = '192.168.32.151'): bool
    {
        $order = $ticket->order;
        if (!$order) return false;

        $buffer  = self::INIT;
        $buffer .= self::ALIGN_CENTER . self::TEXT_DOUBLE . "KITCHEN ORDER TICKET\n" . self::TEXT_NORMAL;
        $buffer .= self::TEXT_BOLD . "Ticket: #{$ticket->ticket_number}\n";
        $buffer .= "Table: " . ($order->table ? "T-" . $order->table->table_number : "Takeaway / POS") . "\n";
        $buffer .= "Time: " . now()->format('d-M-Y H:i:s') . "\n" . self::TEXT_BOLD_OFF;
        $buffer .= self::ALIGN_LEFT . str_repeat("-", 42) . "\n";
        
        $buffer .= sprintf("%-5s | %-32s\n", "QTY", "ITEM & SPECIAL NOTES");
        $buffer .= str_repeat("-", 42) . "\n";

        foreach ($order->items as $item) {
            $buffer .= self::TEXT_BOLD . sprintf("%-5s | %-32s\n", $item->quantity . "x", substr($item->item_name, 0, 32)) . self::TEXT_BOLD_OFF;
            if ($item->special_instructions) {
                $buffer .= sprintf("      >>> %s\n", $item->special_instructions);
            }
        }

        $buffer .= str_repeat("-", 42) . "\n";
        $buffer .= self::ALIGN_CENTER . "Station: {$ticket->station_name}\n";
        $buffer .= "\n\n\n" . self::CUT_PAPER . self::BEEPER;

        return self::sendToSocket($printerIp, 9100, $buffer);
    }

    /**
     * Print Kitchen Order Receipt Directly from POS Order (When KDS Display Screen is Optional/Disabled)
     */
    public static function printOrderKot(Order $order, string $printerIp = '192.168.32.151'): bool
    {
        $buffer  = self::INIT;
        $buffer .= self::ALIGN_CENTER . self::TEXT_DOUBLE . "KITCHEN ORDER RECEIPT\n" . self::TEXT_NORMAL;
        $buffer .= " [ THERMAL LAN DIRECT PRINT MODE ]\n";
        $buffer .= self::TEXT_BOLD . "Order #: {$order->order_number}\n";
        $buffer .= "Table:   " . ($order->table ? "T-" . $order->table->table_number : strtoupper($order->order_type)) . "\n";
        $buffer .= "Waiter:  " . ($order->waiter->name ?? "Tablet POS Terminal") . "\n";
        $buffer .= "Time:    " . now()->format('d-M-Y H:i:s') . "\n" . self::TEXT_BOLD_OFF;
        $buffer .= self::ALIGN_LEFT . str_repeat("-", 42) . "\n";
        
        $buffer .= sprintf("%-5s | %-32s\n", "QTY", "DISH / SPECIAL PREP INSTRUCTIONS");
        $buffer .= str_repeat("-", 42) . "\n";

        foreach ($order->items as $item) {
            $buffer .= self::TEXT_BOLD . sprintf("%-5s | %-32s\n", $item->quantity . "x", substr($item->item_name, 0, 32)) . self::TEXT_BOLD_OFF;
            if ($item->special_instructions) {
                $buffer .= sprintf("      *** %s ***\n", $item->special_instructions);
            }
        }

        $buffer .= str_repeat("-", 42) . "\n";
        $buffer .= self::ALIGN_CENTER . "Action: PREPARE IMMEDIATELY\n";
        $buffer .= "Automated by Antigravity Thermal Socket Engine\n";
        $buffer .= "\n\n\n" . self::CUT_PAPER . self::BEEPER;

        return self::sendToSocket($printerIp, 9100, $buffer);
    }

    /**
     * Print Customer Tax Invoice Receipt (80mm Width)
     */
    public static function printCustomerReceipt(Invoice $invoice, string $printerIp = '192.168.32.150'): bool
    {
        $order = $invoice->order;
        $buffer  = self::INIT;
        $buffer .= self::ALIGN_CENTER . self::TEXT_DOUBLE . "ANTIGRAVITY DINING\n" . self::TEXT_NORMAL;
        $buffer .= "Fine Dining & Gourmet POS Suite\n";
        $buffer .= "Host IP: 192.168.32.249:8107\n";
        $buffer .= "GST/VAT Tax Reg: #AGY-99887766\n";
        $buffer .= str_repeat("=", 42) . "\n";
        
        $buffer .= self::ALIGN_LEFT;
        $buffer .= sprintf("Invoice #: %-28s\n", $invoice->invoice_number);
        $buffer .= sprintf("Date:      %-28s\n", $invoice->created_at->format('d-M-Y h:i A'));
        $buffer .= sprintf("Cashier:   %-28s\n", $invoice->cashier->name ?? 'Main POS Terminal');
        if ($order && $order->table) {
            $buffer .= sprintf("Table:     %-28s\n", "T-" . $order->table->table_number);
        }
        $buffer .= str_repeat("-", 42) . "\n";
        
        // Items Header
        $buffer .= sprintf("%-20s %5s %15s\n", "ITEM", "QTY", "AMOUNT");
        $buffer .= str_repeat("-", 42) . "\n";

        if ($order) {
            foreach ($order->items as $item) {
                $buffer .= sprintf("%-20s %5s %15s\n", substr($item->item_name, 0, 20), $item->quantity, number_format($item->subtotal, 2));
            }
        }

        $buffer .= str_repeat("-", 42) . "\n";
        $buffer .= sprintf("%-25s %16s\n", "Subtotal:", "INR " . number_format($invoice->subtotal, 2));
        $buffer .= sprintf("%-25s %16s\n", "GST & VAT Tax (5.0%):", "INR " . number_format($invoice->tax_total, 2));
        $buffer .= sprintf("%-25s %16s\n", "Discount:", "- INR " . number_format($invoice->discount_total, 2));
        $buffer .= self::TEXT_BOLD;
        $buffer .= sprintf("%-25s %16s\n", "GRAND TOTAL:", "INR " . number_format($invoice->grand_total, 2));
        $buffer .= self::TEXT_BOLD_OFF;
        $buffer .= str_repeat("=", 42) . "\n";
        
        $buffer .= self::ALIGN_CENTER . "Thank You for Dining With Us!\n";
        $buffer .= "Powered by Google Antigravity Advanced Coding\n";
        $buffer .= "\n\n\n" . self::CUT_PAPER;

        return self::sendToSocket($printerIp, 9100, $buffer);
    }

    /**
     * Save Simulated Receipt Transcript to storage for Zero-Risk Hardware Development
     */
    private static function saveToLocalSimulator(string $ip, string $payload)
    {
        $logPath = storage_path('logs/thermal_receipts.log');
        $cleanText = "=== [MOCKED ESC/POS LAN PRINTER ON IP {$ip}:9100] ===\n" . self::stripEscCommands($payload) . "\n=======================================================\n\n";
        file_put_contents($logPath, $cleanText, FILE_APPEND);
    }

    /**
     * Strip non-printable ESC/POS byte formatting for human-readable logs
     */
    private static function stripEscCommands(string $payload): string
    {
        return preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $payload);
    }
}
