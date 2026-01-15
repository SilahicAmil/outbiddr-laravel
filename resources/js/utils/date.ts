/**
 * Date formatting utilities using EST timezone
 */

const EST_TIMEZONE = 'America/New_York';

/**
 * Format a date string to EST datetime (e.g., "1/15/2026, 10:30:00 AM")
 */
export function formatDateTime(dateString: string): string {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString('en-US', {
        timeZone: EST_TIMEZONE,
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
        hour12: true,
    });
}

/**
 * Format a date string to EST date only (e.g., "1/15/2026")
 */
export function formatDate(dateString: string): string {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        timeZone: EST_TIMEZONE,
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
    });
}

/**
 * Format a date string to EST with short format (e.g., "Jan 15, 2026 10:30 AM")
 */
export function formatDateTimeShort(dateString: string): string {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString('en-US', {
        timeZone: EST_TIMEZONE,
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
}

/**
 * Format a date string to relative time (e.g., "2 hours ago")
 */
export function formatRelativeTime(dateString: string): string {
    if (!dateString) return '';

    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    if (diffInSeconds < 60) return 'just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} min ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;

    return formatDate(dateString);
}
