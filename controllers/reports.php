<?php
require_once 'config.php';

$date = $_GET['date'] ?? date('Y-m-d');

// Daily ticket activity
$ticket_activity = $pdo->prepare("
    SELECT t.*, c.name as customer_name, u.name as technician_name,
           COUNT(tu.id) as update_count
    FROM tickets t
    LEFT JOIN customers c ON t.customer_id = c.id
    LEFT JOIN users u ON t.assigned_to = u.id
    LEFT JOIN ticket_updates tu ON t.id = tu.ticket_id AND DATE(tu.created_at) = ?
    WHERE DATE(t.created_at) = ? OR EXISTS (
        SELECT 1 FROM ticket_updates tu2 WHERE tu2.ticket_id = t.id AND DATE(tu2.created_at) = ?
    )
    GROUP BY t.id
    ORDER BY t.created_at DESC
");
$ticket_activity->execute([$date, $date, $date]);
$tickets = $ticket_activity->fetchAll();

// Daily parts sales
$parts_sales = $pdo->prepare("
    SELECT tp.*, u.name as technician_name, t.ticket_number, c.name as customer_name
    FROM ticket_parts tp
    LEFT JOIN users u ON tp.added_by = u.id
    LEFT JOIN tickets t ON tp.ticket_id = t.id
    LEFT JOIN customers c ON t.customer_id = c.id
    WHERE DATE(tp.created_at) = ?
    ORDER BY tp.created_at DESC
");
$parts_sales->execute([$date]);
$parts = $parts_sales->fetchAll();

// Daily summary by technician
$tech_summary = $pdo->prepare("
    SELECT u.name as technician_name,
           COALESCE(ticket_counts.tickets_worked, 0) as tickets_worked,
           COALESCE(parts_counts.parts_sold, 0) as parts_sold,
           COALESCE(parts_counts.parts_revenue, 0) as parts_revenue,
           COALESCE(hours_counts.hours_logged, 0) as hours_logged
    FROM users u
    LEFT JOIN (
        SELECT technician_id, COUNT(DISTINCT ticket_id) as tickets_worked
        FROM ticket_updates 
        WHERE DATE(created_at) = ?
        GROUP BY technician_id
    ) ticket_counts ON u.id = ticket_counts.technician_id
    LEFT JOIN (
        SELECT added_by, COUNT(*) as parts_sold, SUM(sell_price) as parts_revenue
        FROM ticket_parts 
        WHERE DATE(created_at) = ?
        GROUP BY added_by
    ) parts_counts ON u.id = parts_counts.added_by
    LEFT JOIN (
        SELECT technician_id, SUM(hours_logged) as hours_logged
        FROM ticket_updates 
        WHERE DATE(created_at) = ?
        GROUP BY technician_id
    ) hours_counts ON u.id = hours_counts.technician_id
    WHERE u.role IN ('admin', 'manager', 'technician')
    AND (ticket_counts.tickets_worked > 0 OR parts_counts.parts_sold > 0)
    ORDER BY parts_revenue DESC
");
$tech_summary->execute([$date, $date, $date]);
$summary = $tech_summary->fetchAll();

renderModernPage(
    'Reports - SupportTracker',
    'Daily Report - ' . date('M j, Y', strtotime($date)),
    'reports.php',
    compact('tickets', 'parts', 'summary', 'date'),
    ''
);
?>