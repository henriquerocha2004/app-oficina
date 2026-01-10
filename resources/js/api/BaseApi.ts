export async function fetchWithErrorHandling<T>(url: string, options: RequestInit): Promise<T> {
    const res = await fetch(url, options);
    const text = await res.text();

    if (!res.ok) {
        let message = `HTTP ${res.status}`;
        try {
            const data = text ? JSON.parse(text) : null;
            message = data?.message || message;
        } catch { }
        throw new Error(message);
    }

    try {
        return (text ? JSON.parse(text) : {}) as T;
    } catch {
        throw new Error('Resposta inválida do servidor');
    }
}

export const jsonHeaders = () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken || '',
    };
};