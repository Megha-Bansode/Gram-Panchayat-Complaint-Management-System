<?php
/**
 * FILE: includes/citizen_helpers.php
 * MODULE: Citizen Module - Shared Helpers
 * PURPOSE: Centralised status label and badge class functions
 *          to avoid code duplication across citizen pages.
 *          Handbook §12 - No duplicated code.
 * USAGE:   require_once '../includes/citizen_helpers.php';
 */

function status_label(string $status): string
{
    $map = [
        'pending'     => 'Pending',
        'assigned'    => 'Assigned',
        'in_progress' => 'In Progress',
        'resolved'    => 'Resolved',
    ];
    return $map[$status] ?? htmlspecialchars(ucfirst(str_replace('_', ' ', $status)));
}

function status_badge_class(string $status): string
{
    $map = [
        'pending'     => 'badge-status-pending',
        'assigned'    => 'badge-status-assigned',
        'in_progress' => 'badge-status-inprogress',
        'resolved'    => 'badge-status-resolved',
    ];
    return $map[$status] ?? 'badge-status-default';
}
