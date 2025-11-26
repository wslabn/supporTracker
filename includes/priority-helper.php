<?php
function getPriorityResponseTime($pdo, $priority, $location_id = null) {
    try {
        // Get company defaults
        $defaults = $pdo->query("SELECT priority_low_hours, priority_medium_hours, priority_high_hours, priority_urgent_hours FROM settings LIMIT 1")->fetch();
        
        // Get location overrides if they exist and location_id is provided
        $location_settings = null;
        if ($location_id) {
            $location = $pdo->prepare("SELECT priority_low_hours, priority_medium_hours, priority_high_hours, priority_urgent_hours FROM locations WHERE id = ?");
            $location->execute([$location_id]);
            $location_settings = $location->fetch();
        }
        
        // Determine which value to use (location override or company default)
        switch ($priority) {
            case 'low':
                return ($location_settings && $location_settings['priority_low_hours']) ? $location_settings['priority_low_hours'] : ($defaults['priority_low_hours'] ?? 24);
            case 'medium':
                return ($location_settings && $location_settings['priority_medium_hours']) ? $location_settings['priority_medium_hours'] : ($defaults['priority_medium_hours'] ?? 8);
            case 'high':
                return ($location_settings && $location_settings['priority_high_hours']) ? $location_settings['priority_high_hours'] : ($defaults['priority_high_hours'] ?? 2);
            case 'urgent':
                return ($location_settings && $location_settings['priority_urgent_hours']) ? $location_settings['priority_urgent_hours'] : ($defaults['priority_urgent_hours'] ?? 1);
            default:
                return 24;
        }
    } catch (Exception $e) {
        // Fallback to defaults if database error
        switch ($priority) {
            case 'urgent': return 1;
            case 'high': return 2;
            case 'medium': return 8;
            case 'low': default: return 24;
        }
    }
}

function getPriorityBadgeClass($priority) {
    switch ($priority) {
        case 'urgent':
            return 'bg-danger';
        case 'high':
            return 'bg-warning';
        case 'medium':
            return 'bg-info';
        case 'low':
        default:
            return 'bg-success';
    }
}

function formatResponseTime($hours) {
    if ($hours < 1) {
        return round($hours * 60) . ' minutes';
    } elseif ($hours == 1) {
        return '1 hour';
    } else {
        return $hours . ' hours';
    }
}
?>