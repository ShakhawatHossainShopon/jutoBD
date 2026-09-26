<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid Order ID");

// Fetch order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) die("Order not found");

// Fetch items
$itemStmt = $pdo->prepare("SELECT oi.*, p.name AS product_name, p.uid AS product_uid FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$grandTotal = 0;
foreach($items as $item) {
    $grandTotal += ($item['locked_price'] * $item['quantity']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #INV-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f1f5f9; 
            color: #0f172a; 
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
        }
        .invoice-box { 
            max-width: 850px; 
            margin: 40px auto; 
            padding: 50px 60px; 
            background: white; 
            border-radius: 2px; 
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1); 
        }
        
        @media print {
            @page { margin: 0; size: A4 portrait; }
            body { background-color: white; margin: 0; padding: 0; }
            .invoice-box { box-shadow: none; margin: 0 auto; padding: 40px; width: 100%; max-width: 100%; }
            .print-hide { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Controls (Hidden on Print) -->
    <div class="text-center mt-10 print-hide">
        <button onclick="window.print()" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all mr-3">
            <svg class="w-5 h-5 inline-block mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Invoice
        </button>
        <button onclick="window.close()" class="bg-slate-200 text-slate-700 px-8 py-3 rounded-xl font-bold hover:bg-slate-300 transition-all">Close</button>
    </div>

    <!-- Printable Area -->
    <div class="invoice-box">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-slate-100 pb-10 mb-10">
            <div>
                <!-- Brand / Logo Area -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white font-extrabold text-xl">
                        YM
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">YourStore</h1>
                </div>
                <p class="text-slate-500 font-medium text-[13px] leading-relaxed">
                    123 E-commerce Avenue<br>
                    Banani, Dhaka 1213, Bangladesh<br>
                    Phone: +880 1711-000000<br>
                    Email: support@yourstore.com
                </p>
            </div>
            <div class="text-right">
                <h2 class="text-[40px] font-extrabold text-slate-200 uppercase tracking-widest mb-1 leading-none">INVOICE</h2>
                <p class="text-slate-800 font-extrabold text-lg mb-2">#INV-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></p>
                <div class="inline-block bg-slate-50 border border-slate-100 rounded-lg px-4 py-2 mt-2 text-left">
                    <p class="text-slate-400 font-bold text-[10px] uppercase tracking-widest mb-0.5">Date of Issue</p>
                    <p class="text-slate-800 font-bold text-sm"><?= date("d M Y", strtotime($order['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Bill To -->
        <div class="flex justify-between mb-12">
            <div class="max-w-xs">
                <p class="text-[11px] font-extrabold text-blue-500 uppercase tracking-widest mb-3">Billed To</p>
                <h3 class="text-xl font-extrabold text-slate-900 mb-2"><?= htmlspecialchars($order['name']) ?></h3>
                <p class="text-slate-500 text-[14px] font-medium leading-relaxed mb-2"><?= nl2br(htmlspecialchars($order['address'])) ?><br><?= htmlspecialchars($order['city']) ?></p>
                <p class="text-slate-500 text-[14px] font-medium leading-relaxed">
                    <strong class="text-slate-700">P:</strong> <?= htmlspecialchars($order['phone']) ?><br>
                    <?php if(!empty($order['email'])): ?>
                    <strong class="text-slate-700">E:</strong> <?= htmlspecialchars($order['email']) ?>
                    <?php endif; ?>
                </p>
            </div>
            <div class="text-right flex flex-col items-end">
                <p class="text-[11px] font-extrabold text-blue-500 uppercase tracking-widest mb-3">Payment Info</p>
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-5 py-4 w-64 text-left">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Method</p>
                    <p class="text-[14px] font-extrabold text-slate-800 mb-3"><?= htmlspecialchars($order['payment_method']) ?></p>
                    
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status</p>
                    <?php if(stripos($order['payment_method'], 'cash on delivery') !== false): ?>
                        <p class="text-[13px] font-bold text-orange-600 bg-orange-50 inline-block px-2 py-0.5 rounded border border-orange-100">Unpaid (COD)</p>
                    <?php else: ?>
                        <p class="text-[13px] font-bold text-emerald-600 bg-emerald-50 inline-block px-2 py-0.5 rounded border border-emerald-100">Paid in Full</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-left mb-10">
            <thead>
                <tr class="border-b-2 border-slate-800 text-slate-900">
                    <th class="py-4 text-[12px] uppercase tracking-widest font-extrabold w-1/2">Item Description</th>
                    <th class="py-4 text-[12px] uppercase tracking-widest font-extrabold text-center">Qty</th>
                    <th class="py-4 text-[12px] uppercase tracking-widest font-extrabold text-right">Unit Price</th>
                    <th class="py-4 text-[12px] uppercase tracking-widest font-extrabold text-right">Total</th>
                </tr>
            </thead>
            <tbody class="text-slate-600">
                <?php foreach($items as $item): 
                    $lineTotal = $item['locked_price'] * $item['quantity'];
                    $attrs = [];
                    if($item['color']) $attrs[] = "Color: " . $item['color'];
                    if($item['size']) $attrs[] = "Size: " . $item['size'];
                    $attrStr = implode(" &bull; ", $attrs);
                ?>
                <tr class="border-b border-slate-100">
                    <td class="py-5 pr-4">
                        <p class="font-extrabold text-slate-800 text-[15px] mb-1"><?= htmlspecialchars($item['product_name']) ?></p>
                        <div class="flex items-center gap-3 text-[12px] font-medium text-slate-500">
                            <span class="bg-slate-100 px-2 py-0.5 rounded text-slate-600 font-bold">UID: <?= $item['product_uid'] ?></span>
                            <?php if($attrStr): ?>
                                <span><?= $attrStr ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-5 text-center font-bold text-slate-800"><?= $item['quantity'] ?></td>
                    <td class="py-5 text-right font-medium text-slate-500">৳<?= number_format($item['locked_price'], 2) ?></td>
                    <td class="py-5 text-right font-extrabold text-slate-900">৳<?= number_format($lineTotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="flex justify-end mb-16">
            <div class="w-72">
                <div class="flex justify-between py-2 text-[14px] text-slate-500 font-bold">
                    <span>Subtotal</span>
                    <span class="text-slate-800">৳<?= number_format($grandTotal, 2) ?></span>
                </div>
                <div class="flex justify-between py-2 text-[14px] text-slate-500 font-bold mb-2">
                    <span>Shipping Fee</span>
                    <span class="text-slate-800">৳0.00</span>
                </div>
                <div class="flex justify-between py-4 text-[20px] font-extrabold text-blue-600 border-t-2 border-slate-800">
                    <span>Grand Total</span>
                    <span>৳<?= number_format($grandTotal, 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t-2 border-slate-100 pt-8 flex justify-between items-end">
            <div>
                <h4 class="text-slate-900 font-extrabold text-[16px] mb-1">Thank you for your business!</h4>
                <p class="text-slate-500 text-[13px] font-medium">If you have any questions about this invoice, please contact support.</p>
            </div>
            <div class="text-right">
                <!-- Signature Placeholder -->
                <div class="w-32 h-12 border-b border-slate-300 mb-2 mx-auto"></div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Authorized Signature</p>
            </div>
        </div>
        
    </div>
</body>
</html>
